/**
 * robot_controller.cpp
 *
 * LiveKit-based robot teleoperation controller for Raspberry Pi.
 * Features:
 *  - Command ingestion via LiveKit data streams
 *  - USB camera video capture (V4L2) + GStreamer H.264 pipeline
 *  - Video track publication via LiveKit
 *  - Round-trip latency measurement via ping/pong data channels
 *  - Command-dispatch latency tracking
 *
 * Build dependencies:
 *   - livekit-sdk-cpp
 *   - libserial-dev
 *   - gstreamer-1.0, gstreamer-plugins-base, gstreamer-plugins-good,
 *     gstreamer-plugins-bad, gstreamer-plugins-ugly
 *   - pkg-config
 *
 * CMakeLists.txt snippet:
 *   find_package(PkgConfig REQUIRED)
 *   pkg_check_modules(GST REQUIRED
 *       gstreamer-1.0 gstreamer-video-1.0 gstreamer-app-1.0)
 *   target_link_libraries(robot_controller
 *       livekit serial ${GST_LIBRARIES})
 *   target_include_directories(robot_controller PRIVATE ${GST_INCLUDE_DIRS})
 */

#include <atomic>
#include <chrono>
#include <csignal>
#include <cstdlib>
#include <iomanip>
#include <iostream>
#include <map>
#include <mutex>
#include <queue>
#include <random>
#include <sstream>
#include <string>
#include <thread>
#include <unordered_map>
#include <vector>

// GStreamer
#include <gst/gst.h>
#include <gst/app/gstappsink.h>
#include <gst/video/video.h>

// LiveKit C++ SDK
#include "livekit/livekit.h"

// libserial
#include <libserial/SerialPort.h>

using namespace livekit;

// ─────────────────────────────────────────────────────────────────────────────
//  Utility
// ─────────────────────────────────────────────────────────────────────────────
namespace {

std::atomic<bool> g_running{true};

void handleSignal(int) { g_running.store(false); }

std::string getenvOrEmpty(const char *name) {
    const char *v = std::getenv(name);
    return v ? std::string(v) : std::string{};
}

std::int64_t nowEpochMs() {
    using namespace std::chrono;
    return duration_cast<milliseconds>(system_clock::now().time_since_epoch()).count();
}

std::int64_t nowEpochUs() {
    using namespace std::chrono;
    return duration_cast<microseconds>(system_clock::now().time_since_epoch()).count();
}

// ─────────────────────────────────────────────────────────────────────────────
//  Latency Tracker
//  Records round-trip times and command-processing latencies.
// ─────────────────────────────────────────────────────────────────────────────
class LatencyTracker {
public:
    struct Stats {
        double min_ms   = 0;
        double max_ms   = 0;
        double mean_ms  = 0;
        double stddev_ms = 0;
        std::size_t samples = 0;
    };

    // Record one RTT sample (milliseconds)
    void recordRtt(double rtt_ms) {
        std::lock_guard<std::mutex> lk(mutex_);
        rtt_samples_.push_back(rtt_ms);
        if (rtt_samples_.size() > kMaxSamples)
            rtt_samples_.erase(rtt_samples_.begin());
        dirty_ = true;
    }

    // Record command processing latency (time from reception to serial write)
    void recordCmdLatency(double lat_ms) {
        std::lock_guard<std::mutex> lk(mutex_);
        cmd_samples_.push_back(lat_ms);
        if (cmd_samples_.size() > kMaxSamples)
            cmd_samples_.erase(cmd_samples_.begin());
        dirty_ = true;
    }

    Stats rttStats() {
        std::lock_guard<std::mutex> lk(mutex_);
        return computeStats(rtt_samples_);
    }

    Stats cmdStats() {
        std::lock_guard<std::mutex> lk(mutex_);
        return computeStats(cmd_samples_);
    }

    void printReport() const {
        std::lock_guard<std::mutex> lk(mutex_);
        auto rtt = computeStats(rtt_samples_);
        auto cmd = computeStats(cmd_samples_);
        std::cout << std::fixed << std::setprecision(2)
                  << "[Latency] RTT  — min=" << rtt.min_ms
                  << " max=" << rtt.max_ms
                  << " mean=" << rtt.mean_ms
                  << " σ=" << rtt.stddev_ms
                  << " n=" << rtt.samples << " ms\n"
                  << "[Latency] Cmd  — min=" << cmd.min_ms
                  << " max=" << cmd.max_ms
                  << " mean=" << cmd.mean_ms
                  << " σ=" << cmd.stddev_ms
                  << " n=" << cmd.samples << " ms\n";
    }

private:
    static constexpr std::size_t kMaxSamples = 500;
    mutable std::mutex mutex_;
    std::vector<double> rtt_samples_;
    std::vector<double> cmd_samples_;
    bool dirty_ = false;

    static Stats computeStats(const std::vector<double> &v) {
        if (v.empty()) return {};
        Stats s;
        s.samples = v.size();
        double sum = 0, sq = 0;
        s.min_ms = v[0]; s.max_ms = v[0];
        for (double x : v) {
            sum += x;
            sq  += x * x;
            if (x < s.min_ms) s.min_ms = x;
            if (x > s.max_ms) s.max_ms = x;
        }
        s.mean_ms = sum / s.samples;
        double var = sq / s.samples - s.mean_ms * s.mean_ms;
        s.stddev_ms = var > 0 ? std::sqrt(var) : 0.0;
        return s;
    }
};

LatencyTracker g_latency;

// ─────────────────────────────────────────────────────────────────────────────
//  Ping manager — measures RTT using LiveKit data messages
//  Protocol: sender writes "PING:<timestamp_us>" on topic "ping"
//            receiver echoes it back on topic "pong"
// ─────────────────────────────────────────────────────────────────────────────
class PingManager {
public:
    explicit PingManager(Room *room) : room_(room) {}

    // Called from the main loop periodically
    void sendPing() {
        std::string payload = "PING:" + std::to_string(nowEpochUs());
        DataPublishOptions opts;
        opts.topic = "ping";
        opts.reliable = true;
        room_->PublishData(
            reinterpret_cast<const uint8_t *>(payload.data()),
            payload.size(), opts);
    }

    // Call when a "pong" data message arrives
    void onPong(const std::string &payload) {
        // payload == "PING:<original_us>"
        if (payload.rfind("PING:", 0) != 0) return;
        try {
            std::int64_t sent_us = std::stoll(payload.substr(5));
            double rtt_ms = (nowEpochUs() - sent_us) / 1000.0;
            g_latency.recordRtt(rtt_ms);
            std::cout << "[Ping] RTT=" << std::fixed << std::setprecision(2)
                      << rtt_ms << " ms\n";
        } catch (...) {}
    }

private:
    Room *room_;
};

// ─────────────────────────────────────────────────────────────────────────────
//  Command structures & dispatcher
// ─────────────────────────────────────────────────────────────────────────────
struct RobotCommand {
    std::string subsystem;
    std::string action;
    std::map<std::string, std::string> params;
    std::int64_t timestamp_ms;
    std::int64_t received_us; // for cmd-latency measurement
};

class CommandDispatcher {
public:
    void enqueue(const RobotCommand &cmd) {
        std::lock_guard<std::mutex> lock(mutex_);
        queue_.push(cmd);
    }

    bool dequeue(RobotCommand &cmd) {
        std::lock_guard<std::mutex> lock(mutex_);
        if (queue_.empty()) return false;
        cmd = queue_.front();
        queue_.pop();
        return true;
    }

    std::size_t size() const {
        std::lock_guard<std::mutex> lock(mutex_);
        return queue_.size();
    }

private:
    std::queue<RobotCommand> queue_;
    mutable std::mutex mutex_;
};

CommandDispatcher g_dispatcher;

// ─────────────────────────────────────────────────────────────────────────────
//  Video Streamer
//  Captures from /dev/video0 via V4L2, encodes H.264 with hardware (or SW
//  fallback), and pushes encoded frames into a LiveKit VideoTrack.
//
//  GStreamer pipeline (Raspberry Pi with V4L2 H.264 HW encoder):
//    v4l2src device=/dev/videoX
//      ! video/x-raw,width=640,height=480,framerate=30/1
//      ! videoconvert
//      ! v4l2h264enc      (HW, Pi)  OR  x264enc (SW fallback)
//      ! video/x-h264,profile=baseline,level=(string)3.1
//      ! h264parse
//      ! appsink name=sink emit-signals=true sync=false
// ─────────────────────────────────────────────────────────────────────────────
class VideoStreamer {
public:
    struct Config {
        std::string device     = "/dev/video0";
        int         width      = 640;
        int         height     = 480;
        int         framerate  = 30;
        int         bitrate_kbps = 1500;
        bool        hw_encode  = true; // set false on non-Pi or if HW unavailable
    };

    explicit VideoStreamer(Room *room, Config cfg = {})
        : room_(room), cfg_(std::move(cfg)) {}

    ~VideoStreamer() { stop(); }

    bool start() {
        if (!buildPipeline()) {
            std::cerr << "[Video] Failed to build GStreamer pipeline\n";
            return false;
        }

        // Create LiveKit video source + track
        VideoSourceOptions vsrc_opts;
        video_source_ = room_->CreateVideoTrackSource(vsrc_opts);
        if (!video_source_) {
            std::cerr << "[Video] Failed to create LiveKit VideoTrackSource\n";
            return false;
        }

        PublishVideoTrackOptions track_opts;
        track_opts.simulcast = false;
        video_track_ = room_->PublishVideoTrack(video_source_, track_opts);
        if (!video_track_) {
            std::cerr << "[Video] Failed to publish video track\n";
            return false;
        }
        std::cout << "[Video] Track published: " << video_track_->sid() << "\n";

        // Start pipeline
        gst_element_set_state(pipeline_, GST_STATE_PLAYING);
        running_ = true;

        // Capture thread
        capture_thread_ = std::thread([this]() { captureLoop(); });

        std::cout << "[Video] Streaming from " << cfg_.device
                  << " at " << cfg_.width << "x" << cfg_.height
                  << "@" << cfg_.framerate << "fps\n";
        return true;
    }

    void stop() {
        running_ = false;
        if (pipeline_) {
            gst_element_set_state(pipeline_, GST_STATE_NULL);
            gst_object_unref(pipeline_);
            pipeline_ = nullptr;
        }
        if (capture_thread_.joinable())
            capture_thread_.join();
    }

    // Statistics
    std::uint64_t framesPublished() const { return frames_published_.load(); }

private:
    bool buildPipeline() {
        GError *err = nullptr;

        // Choose encoder: V4L2 HW (Pi) or x264 SW
        std::string encoder_str;
        if (cfg_.hw_encode) {
            encoder_str =
                "v4l2h264enc extra-controls=\"controls,h264_profile=1,"
                "h264_level=11,video_bitrate=" +
                std::to_string(cfg_.bitrate_kbps * 1000) + "\"";
        } else {
            encoder_str =
                "x264enc tune=zerolatency speed-preset=ultrafast bitrate=" +
                std::to_string(cfg_.bitrate_kbps) +
                " key-int-max=30";
        }

        std::string pipeline_str =
            "v4l2src device=" + cfg_.device + " ! "
            "video/x-raw,width=" + std::to_string(cfg_.width) +
            ",height=" + std::to_string(cfg_.height) +
            ",framerate=" + std::to_string(cfg_.framerate) + "/1 ! "
            "videoconvert ! "
            + encoder_str + " ! "
            "video/x-h264,profile=baseline ! "
            "h264parse config-interval=1 ! "
            "appsink name=sink emit-signals=false max-buffers=2 drop=true sync=false";

        std::cout << "[Video] Pipeline: " << pipeline_str << "\n";
        pipeline_ = gst_parse_launch(pipeline_str.c_str(), &err);
        if (err) {
            std::cerr << "[Video] GStreamer parse error: " << err->message << "\n";
            g_error_free(err);
            return false;
        }

        appsink_ = gst_bin_get_by_name(GST_BIN(pipeline_), "sink");
        if (!appsink_) {
            std::cerr << "[Video] Could not find appsink element\n";
            return false;
        }
        return true;
    }

    void captureLoop() {
        while (running_) {
            GstSample *sample = gst_app_sink_try_pull_sample(
                GST_APP_SINK(appsink_), /* timeout_ns */ 100'000'000);
            if (!sample) continue;

            GstBuffer *buf = gst_sample_get_buffer(sample);
            if (!buf) { gst_sample_unref(sample); continue; }

            GstMapInfo map;
            if (gst_buffer_map(buf, &map, GST_MAP_READ)) {
                // Build a LiveKit EncodedVideoFrame and push it
                EncodedVideoFrame frame;
                frame.data       = map.data;
                frame.size       = map.size;
                frame.codec      = VideoCodec::kH264;
                frame.timestamp_us = nowEpochUs();

                // Mark keyframes (IDR NAL type 0x65)
                frame.is_key_frame =
                    map.size > 4 && (map.data[4] & 0x1F) == 5;

                video_source_->PushEncodedVideoFrame(frame);
                ++frames_published_;

                gst_buffer_unmap(buf, &map);
            }
            gst_sample_unref(sample);
        }
    }

    Room                *room_     = nullptr;
    Config               cfg_;
    GstElement          *pipeline_ = nullptr;
    GstElement          *appsink_  = nullptr;
    std::shared_ptr<VideoTrackSource> video_source_;
    std::shared_ptr<LocalVideoTrack> video_track_;
    std::atomic<bool>    running_{false};
    std::atomic<uint64_t> frames_published_{0};
    std::thread          capture_thread_;
};

// ─────────────────────────────────────────────────────────────────────────────
//  Control message handler (data streams → command queue)
// ─────────────────────────────────────────────────────────────────────────────
void handleControlMessage(std::shared_ptr<livekit::TextStreamReader> reader,
                          const std::string &participant_identity) {
    try {
        const auto info = reader->info();
        auto attr = info.attributes;

        if (attr.find("subsystem") == attr.end()) {
            std::cerr << "[Control] Missing 'subsystem' attribute from "
                      << participant_identity << "\n";
            return;
        }

        // Read full text payload (action)
        std::string payload = reader->readAll(); // blocks until stream closes

        RobotCommand cmd;
        cmd.subsystem    = attr["subsystem"];
        cmd.action       = payload;
        cmd.timestamp_ms = nowEpochMs();
        cmd.received_us  = nowEpochUs();

        if (attr.count("timestamp_ms")) {
            try { cmd.timestamp_ms = std::stoll(attr["timestamp_ms"]); }
            catch (...) {}
        }

        // Parse optional key=value params from attributes
        for (auto &[k, v] : attr) {
            if (k != "subsystem" && k != "timestamp_ms")
                cmd.params[k] = v;
        }

        std::unordered_map<std::string, int> subsystemMap = {
            {"wheels", 1}, {"front_arm", 2}, {"back_arm", 3}, {"front_drum", 4}, {"back_drum", 5}, {"autonomous_toggles", 6}
        };

        auto it = subsystemMap.find(cmd.subsystem);
        if (it != subsystemMap.end()) {
            const char *topics[] = {"", "/wheel_instructions",
                                    "/front_arm_instructions",
                                    "/back_arm_instructions",
                                    "/front_drum_instructions",
                                    "/back_drum_instructions",
                                    "/autonomous_toggles"};
            std::cout << "[Control] " << participant_identity
                      << " → subsystem=" << cmd.subsystem
                      << " action='" << cmd.action << "'"
                      << " → topic " << topics[it->second] << "\n";
            g_dispatcher.enqueue(cmd);
        } else {
            std::cerr << "[Control] Unknown subsystem: " << cmd.subsystem << "\n";
        }
    } catch (const std::exception &e) {
        std::cerr << "[Control] Error from " << participant_identity
                  << ": " << e.what() << "\n";
    }
}

// ─────────────────────────────────────────────────────────────────────────────
//  ROS Publishing Thread - Drains CMD Dispatcher => ROStopic
// ─────────────────────────────────────────────────────────────────────────────
void serialWriterLoop(const std::string &port_name,
                      LibSerial::BaudRate baud) {
    LibSerial::SerialPort serial;
    try {
        serial.Open(port_name);
        serial.SetBaudRate(baud);
        serial.SetCharacterSize(LibSerial::CharacterSize::CHAR_SIZE_8);
        serial.SetStopBits(LibSerial::StopBits::STOP_BITS_1);
        serial.SetParity(LibSerial::Parity::PARITY_NONE);
        std::cout << "[Serial] Opened " << port_name << "\n";
    } catch (const std::exception &e) {
        std::cerr << "[Serial] Cannot open " << port_name << ": " << e.what()
                  << "  — running without serial output\n";
    }

    while (g_running.load()) {
        RobotCommand cmd;
        if (!g_dispatcher.dequeue(cmd)) {
            std::this_thread::sleep_for(std::chrono::milliseconds(2));
            continue;
        }

        // Build compact serial frame:  <subsystem>|<action>\n
        std::string frame = cmd.subsystem + "|" + cmd.action + "\n";

        if (serial.IsOpen()) {
            try {
                serial.Write(frame);
            } catch (const std::exception &e) {
                std::cerr << "[Serial] Write error: " << e.what() << "\n";
            }
        } else {
            // Dry-run: just print
            std::cout << "[Serial-DRY] " << frame;
        }

        // Record command processing latency
        double lat_ms = (nowEpochUs() - cmd.received_us) / 1000.0;
        g_latency.recordCmdLatency(lat_ms);
    }

    if (serial.IsOpen()) serial.Close();
}

} // anonymous namespace

// ─────────────────────────────────────────────────────────────────────────────
//  main
// ─────────────────────────────────────────────────────────────────────────────
int main(int argc, char *argv[]) {
    // Initialise GStreamer
    gst_init(&argc, &argv);

    // Environment configuration
    std::string url       = getenvOrEmpty("LIVEKIT_URL");
    std::string token     = getenvOrEmpty("LIVEKIT_TOKEN");
    std::string serial_port = getenvOrEmpty("SERIAL_PORT");   // e.g. /dev/ttyUSB0
    std::string cam_device  = getenvOrEmpty("CAMERA_DEVICE"); // e.g. /dev/video0
    bool hw_encode = getenvOrEmpty("USE_SW_ENCODER") != "1";  // default: HW

    if (url.empty() || token.empty()) {
        std::cerr << "LIVEKIT_URL and LIVEKIT_TOKEN are required\n";
        return 1;
    }
    if (serial_port.empty()) serial_port = "/dev/ttyUSB0";
    if (cam_device.empty())  cam_device  = "/dev/video0";

    std::signal(SIGINT,  handleSignal);
#ifdef SIGTERM
    std::signal(SIGTERM, handleSignal);
#endif

    // ── LiveKit setup ────────────────────────────────────────────────────────
    livekit::initialize(livekit::LogLevel::Info, livekit::LogSink::kConsole);
    auto room = std::make_unique<Room>();

    RoomOptions options;
    options.auto_subscribe = true;
    options.dynacast       = false;

    std::cout << "[Main] Connecting to: " << url << "\n";
    if (!room->Connect(url, token, options)) {
        std::cerr << "[Main] Failed to connect to room\n";
        livekit::shutdown();
        return 1;
    }

    auto info = room->room_info();
    std::cout << "[Main] Connected to room '" << info.name
              << "', participants=" << info.num_participants << "\n";

    // ── Data channel: ping / pong ────────────────────────────────────────────
    PingManager ping_mgr(room.get());

    // Handle pong replies (data messages on topic "pong")
    room->OnDataReceived(
        [&ping_mgr](const uint8_t *data, std::size_t len,
                    const DataPacketInfo &pkt_info) {
            if (pkt_info.topic == "pong") {
                ping_mgr.onPong(std::string(reinterpret_cast<const char *>(data), len));
            } else if (pkt_info.topic == "ping") {
                // We are also a relay — echo pong back
                DataPublishOptions opts;
                opts.topic    = "pong";
                opts.reliable = true;
                // room.PublishData expects a Room pointer; store it externally if needed
            }
        });

    // ── Control stream handler ───────────────────────────────────────────────
    room->registerTextStreamHandler(
        "control",
        [](std::shared_ptr<TextStreamReader> reader,
           const std::string &participant_identity) {
            std::thread t(handleControlMessage, std::move(reader),
                          participant_identity);
            t.detach();
        });

    // ── Serial writer thread ─────────────────────────────────────────────────
    std::thread serial_thread(serialWriterLoop, serial_port,
                              LibSerial::BaudRate::BAUD_115200);

    // ── Video streamer ───────────────────────────────────────────────────────
    VideoStreamer::Config vcfg;
    vcfg.device       = cam_device;
    vcfg.width        = 640;
    vcfg.height       = 480;
    vcfg.framerate    = 30;
    vcfg.bitrate_kbps = 1500;
    vcfg.hw_encode    = hw_encode;

    VideoStreamer streamer(room.get(), vcfg);
    if (!streamer.start()) {
        std::cerr << "[Main] Video streamer failed — continuing without video\n";
    }

    // ── Main loop ────────────────────────────────────────────────────────────
    std::cout << "[Main] Robot controller ready (Ctrl-C to exit)\n";
    auto last_ping   = std::chrono::steady_clock::now();
    auto last_report = last_ping;

    while (g_running.load()) {
        auto now = std::chrono::steady_clock::now();

        // Send ping every 2 s
        if (now - last_ping >= std::chrono::seconds(2)) {
            ping_mgr.sendPing();
            last_ping = now;
        }

        // Print latency report every 10 s
        if (now - last_report >= std::chrono::seconds(10)) {
            g_latency.printReport();
            std::cout << "[Video] Frames published: "
                      << streamer.framesPublished() << "\n";
            last_report = now;
        }

        std::this_thread::sleep_for(std::chrono::milliseconds(5));
    }

    // ── Shutdown ─────────────────────────────────────────────────────────────
    std::cout << "[Main] Shutting down...\n";
    streamer.stop();
    serial_thread.join();
    room->setDelegate(nullptr);
    room.reset();
    livekit::shutdown();
    gst_deinit();
    return 0;
}