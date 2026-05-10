<?php require_once __DIR__ . '/../../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rover RASSOR - documentation</title>
  <link rel="icon" type="image/png" href="<?= e(base_url('resources/RUR_logo_white.png')) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/toolbar/prism-toolbar.min.css">
  <link href="<?= e(base_url('styles/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(base_url('styles/style.css')) ?>" rel="stylesheet">
  <style>
    .sidebar {
      min-height: 100%;
      position: sticky;
      top: 1rem;
      padding-top: 1rem;
      border-right: 1px solid #ddd;
      background-color: #ecbf03;
      color: #0f1424;
      border-radius: 16px;
      overflow: hidden;
    }
    .content {
      padding: 2rem;
    }
    .nav-item a{
        font-family: 'Roboto';
        color: #fff;
    }
    .nav-item a:hover{
         font-family: 'Roboto';
        color: #0f1424;
    }
    .bottom-menu{
        background-color: #0f1424;
        color: #fff;
    }
    .grid-img {
    width: 100%;          
    height: 200px;        
    object-fit: contain;
    aspect-ratio: 1 / 1;
  }
    .doc-image {
      width: min(100%, 560px);
      max-height: 380px;
      object-fit: contain;
      background: #f8f9fa;
      border-radius: 12px;
      padding: .5rem;
      box-shadow: 0 12px 30px rgba(15, 20, 36, 0.08);
    }
    .printed {
      width: min(100%, 420px);
      object-fit: contain;
      background-color: #f8f9fa;
      border-radius: 12px;
      padding: .5rem;
    }
    .document-wrapper {
      border: 1px solid rgba(15, 20, 36, 0.15);
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 18px 45px rgba(15, 20, 36, 0.08);
      max-width: 800px;
      background: #fff;
    }
    .document-header {
      background: #ecbf03;
      color: #0f1424;
      padding: 12px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
    }
    .doc-btn {
      background: #0f1424;
      color: #fff;
      border: 0;
      padding: 6px 14px;
      border-radius: 8px;
    }
    #pdf-canvas {
      width: 100%;
      height: auto;
      display: block;
      background: #eee;
    }
    #btn-back-to-top {
      background-color: #0f1424;
      border-color: #0f1424;
      color: #fff;
    }
  </style>
</head>
<body class="store-page">
  <?php include __DIR__ . '/../components/nav-bar.php'; ?>

  <main class="store-main container py-4 py-lg-5">
    <section class="rur-page-section">
      <div class="rur-hero">
        <div class="row g-4 align-items-center">
          <div class="col-lg-8">
            <span class="rur-kicker mb-3">Project Documentation</span>
            <h1 class="rur-section-title mb-2">ROVER RASSOR</h1>
            <p class="rur-subtitle mb-0">Documentation, quick start notes, and project references for the RASSOR rover collaboration.</p>
          </div>
          <div class="col-lg-4">
            <div class="rur-methods justify-content-lg-end">
              <span class="rur-chip">Rover</span>
              <span class="rur-chip">UCF</span>
              <span class="rur-chip">FSI</span>
            </div>
          </div>
        </div>
      </div>
    </section>

  <section class="rur-page-section">
  <div class="rur-panel">
    <div class="row g-4">
      <!-- Sidebar -->
      <nav class="col-md-3 col-lg-2 sidebar text-center">
        <div class="p-3 bottom-menu">
            <img src="<?= e(base_url('resources/RUR_logo_white.png')) ?>" 
                alt="Logo" 
                class="img-fluid mb-3" 
                style="max-width:120px;">
            <h5 class="fw-bold">ROVER RASSOR</h5>
            <h5 class="fw-bold">Documentation</h5>
        </div>
        <hr>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link active" href="#intro">INTRODUCTION</a></li>
          <li class="nav-item"><a class="nav-link" href="#quickstart">QUICKSTART</a></li>
          <li class="nav-item"><a class="nav-link" href="#raspberry">RASPBERRY PI</a></li>
          <li class="nav-item"><a class="nav-link" href="#printing">3D PRINTINGS</a></li>
          <li class="nav-item"><a class="nav-link" href="#livekit">LIVEKIT</a></li>
          <li class="nav-item"><a class="nav-link" href="#ros2">ROS2</a></li>
          <li class="nav-item"><a class="nav-link" href="#usage">USAGE</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <div class="col-md-9 col-lg-10 content">
        <h1 class="fw-bold text-center mb-4">Research Unit of Robotics</h1>
          <?php include __DIR__ . '/../components/last_modified.php'; ?>
        <hr>
        <h4 id="intro">ROVER RASSOR Documentation</h4>
        <p>Welcome to the documentation. Use the menu on the left to navigate through sections. <b>The following documentation corresponds 
            to the beta phase of the project.</b> This project is being developed by the <b>Research Unit of Robotics (RUR)</b>
            in collaboration with the <b>University of Central Florida (UCF)</b> for the <b>Florida Space Institute (FSI)</b>
        </p>

        <div class="container">
          <div class="row">
            <div class="col-md-6 mb-3">
              <img src="<?= e(base_url('resources/ucf.png')) ?>" alt="Image 1" class="grid-img">
            </div>
            <div class="col-md-6 mb-3">
              <img src="<?= e(base_url('resources/fsi.png')) ?>" alt="Image 2" class="grid-img">
            </div>
            <div class="col-md-6 mb-3">
              <img src="<?= e(base_url('resources/RUR_logo_white.png')) ?>" alt="Image 3" class="grid-img">
            </div>
            <div class="col-md-6 mb-3">
              <img src="<?= e(base_url('resources/unipoli.png')) ?>" alt="Image 4" class="grid-img">
            </div>
          </div>
        </div>


        <section id="quickstart">
          <hr>
          <h2 class="fw-bold">QUICK START</h2>
          <p>Start by preparing the Raspberry Pi, printing the rover structure, reviewing LiveKit communication research, and setting up the ROS2 simulation workspace.</p>
        </section>

        <section id="raspberry">
          <hr>
          <h2 class="fw-bold">RASPBERRY PI</h2>
          <p>For this project it is necessary to use a single board computer like the Raspberry Pi 4.</p>
          <h5 class="fw-bold">Prerequisites</h5>
          <ul>
            <li>Raspberry Pi Imager software. <a href="https://www.raspberrypi.com/software/" target="_blank" rel="noopener">Download</a></li>
            <li>SD Card, recommended at least 32 GB.</li>
            <li>Micro HDMI to HDMI cable adapter.</li>
            <li>Keyboard, mouse, display, and a reliable power source.</li>
          </ul>
          <div class="row g-3 mt-2">
            <?php foreach ([1, 6, 7, 14, 17, 21] as $imageNumber): ?>
              <?php $imagePath = 'resources/pi_imager/' . $imageNumber . ($imageNumber >= 17 ? '.jpeg' : '.png'); ?>
              <div class="col-md-6">
                <img src="<?= e(base_url($imagePath)) ?>" alt="Raspberry Pi setup step <?= e((string) $imageNumber) ?>" class="doc-image">
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section id="printing">
          <hr>
          <h2 class="fw-bold">3D PRINTINGS</h2>
          <p>The ROVER RASSOR structure is made up of several components that need to be 3D printed before assembly.</p>
          <h5 class="fw-bold">Main components</h5>
          <div class="row g-4">
            <?php
              $pieces = [
                ['Cycloidal Disk', 'cycloidal_disk.png', '8 pieces'],
                ['Cycloidal Pin', 'cycloidal_pin.png', '24 pieces'],
                ['Cycloidal Base', 'cycloidal_base.png', '4 pieces'],
                ['Wheel', 'wheel.png', '4 pieces'],
                ['Wheel Nut', 'wheel_nut.png', '4 pieces'],
                ['Electronics Bay', 'electronics_bay.png', 'Unknown pieces'],
              ];
            ?>
            <?php foreach ($pieces as [$pieceName, $pieceImage, $pieceCount]): ?>
              <div class="col-md-6">
                <h6 class="fw-bold"><?= e($pieceName) ?> <span class="text-muted"><?= e($pieceCount) ?></span></h6>
                <img src="<?= e(base_url('resources/printing_pieces/' . $pieceImage)) ?>" alt="<?= e($pieceName) ?>" class="printed">
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section id="livekit">
          <hr>
          <h2 class="fw-bold">LIVEKIT</h2>
          <p>LiveKit is the communication technology used for real-time video and command communication in the ROVER RASSOR project.</p>
          <div class="document-wrapper">
            <div class="document-header">
              <span class="doc-title fw-bold">LiveKit-Research.pdf</span>
              <div class="nav-controls">
                <button id="prev" class="doc-btn" type="button">Previous</button>
                <span id="page-info">Page 1</span>
                <button id="next" class="doc-btn" type="button">Next</button>
              </div>
            </div>
            <canvas id="pdf-canvas"></canvas>
          </div>
          <h5 class="fw-bold mt-4">LiveKit Source Code</h5>
          <p>The following script contains the implementation details for video streaming and command handling.</p>
          <pre class="language-cpp" data-src="<?= e(base_url('pages/projects/scripts/main_video.cpp')) ?>"></pre>
        </section>

        <section id="ros2">
          <hr>
          <h2 class="fw-bold">ROS2</h2>
          <p>ROS2 is the framework used for the rover simulation and control architecture.</p>
          <h5 class="fw-bold">Workspace Architecture</h5>
          <ul>
            <li><b>rassor_description</b>: robot description files, URDF/Xacro, launch files, and RViz configuration.</li>
            <li><b>rassor_controller</b>: control and joystick teleoperation code.</li>
          </ul>
          <h5 class="mt-3">rassor_gazebo.xacro</h5>
          <pre class="language-python" data-src="<?= e(base_url('pages/projects/scripts/unipolitov4_gazebo.xacro')) ?>"></pre>
          <h5 class="mt-3">rassor_ros2_control.xacro</h5>
          <pre class="language-python" data-src="<?= e(base_url('pages/projects/scripts/unipolitov4_ros2_control.xacro')) ?>"></pre>
          <h5 class="mt-3">display.launch.py</h5>
          <pre class="language-python" data-src="<?= e(base_url('pages/projects/scripts/display.launch.py')) ?>"></pre>
        </section>

        <section id="usage">
          <hr>
          <h2 class="fw-bold">USAGE</h2>
          <p>Use the documentation menu to move through hardware preparation, printed components, communication research, and ROS2 simulation files.</p>
        </section>
      </div>
    </div>
  </div>
  </section>

  <?php include __DIR__ . '/../components/footer.php'; ?>
  </main>
  <button type="button" class="btn btn-lg rounded-circle d-md-none" id="btn-back-to-top" style="position: fixed; bottom: 20px; right: 20px; display: none; z-index: 1000; width: 50px; height: 50px;">↑</button>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-cpp.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/toolbar/prism-toolbar.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/file-highlight/prism-file-highlight.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    const backToTopButton = document.getElementById('btn-back-to-top');

    window.addEventListener('scroll', function () {
      backToTopButton.style.display = window.scrollY > 300 ? 'block' : 'none';
    });

    backToTopButton.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    if (window.pdfjsLib) {
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

      const pdfUrl = '<?= e(base_url('pages/projects/assets/LiveKit.pdf')) ?>';
      const canvas = document.getElementById('pdf-canvas');
      const prevButton = document.getElementById('prev');
      const nextButton = document.getElementById('next');
      const pageInfo = document.getElementById('page-info');
      let pdfDoc = null;
      let pageNum = 1;

      function renderPage(num) {
        pdfDoc.getPage(num).then(function (page) {
          const viewport = page.getViewport({ scale: 1.35 });
          const context = canvas.getContext('2d');
          canvas.height = viewport.height;
          canvas.width = viewport.width;
          page.render({ canvasContext: context, viewport: viewport });
          pageInfo.textContent = 'Page ' + num + ' / ' + pdfDoc.numPages;
          prevButton.disabled = num <= 1;
          nextButton.disabled = num >= pdfDoc.numPages;
        });
      }

      pdfjsLib.getDocument(pdfUrl).promise.then(function (pdf) {
        pdfDoc = pdf;
        renderPage(pageNum);
      });

      prevButton.addEventListener('click', function () {
        if (pageNum <= 1) return;
        pageNum--;
        renderPage(pageNum);
      });

      nextButton.addEventListener('click', function () {
        if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
        pageNum++;
        renderPage(pageNum);
      });
    }
  </script>
</body>
</html>
