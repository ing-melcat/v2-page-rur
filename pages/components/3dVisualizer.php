<style>
  .model-container {
    width: 100%;
    min-height: 360px;
    height: min(56vw, 520px);
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 18px 45px rgba(15, 20, 36, 0.08);
  }

  model-viewer {
    width: 100%;
    height: 100%;
    display: block;
    background: #fff;
  }
</style>

<div class="model-container">
  <model-viewer alt="spatial rover" src="<?= e($glbPath) ?>" autoplay camera-controls
    touch-action="pan-y" disable-zoom camera-orbit="30deg 75deg 105%" min-camera-orbit="auto 75deg auto"
    max-camera-orbit="auto 75deg auto">
  </model-viewer>
</div>
