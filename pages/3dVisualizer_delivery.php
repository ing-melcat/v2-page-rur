<?php require_once __DIR__ . '/../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DELIVER-E | Research Unit of Robotics</title>
  <link rel="icon" type="image/png" href="<?= e(base_url('resources/RUR_logo_white.png')) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="<?= e(base_url('styles/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(base_url('styles/style.css')) ?>" rel="stylesheet">
  <style>
    .project-model-panel {
      min-height: 520px;
    }

    .project-summary {
      border-left: 4px solid #ecbf03;
      padding-left: 1rem;
    }

    .project-download-band {
      background: rgba(236, 191, 3, 0.1);
      border: 1px solid rgba(236, 191, 3, 0.24);
      border-radius: 18px;
      padding: 2rem;
    }
  </style>
</head>

<body class="store-page">
  <?php include __DIR__ . '/components/nav-bar.php'; ?>

  <main class="store-main container py-4 py-lg-5">
    <section class="rur-page-section">
      <div class="rur-hero">
        <div class="row g-4 align-items-center">
          <div class="col-lg-8">
            <span class="rur-kicker mb-3">Project Viewer</span>
            <h1 class="rur-section-title mb-2">DELIVER-E</h1>
            <p class="rur-subtitle mb-0">Self driving robot designed to deliver snacks around the university through a mobile app experience.</p>
          </div>
          <div class="col-lg-4">
            <div class="rur-methods justify-content-lg-end">
              <span class="rur-chip">Autonomous</span>
              <span class="rur-chip">Delivery</span>
              <span class="rur-chip">Mobile App</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="rur-page-section">
      <div class="rur-panel project-model-panel">
        <div class="row g-4 align-items-center">
          <div class="col-lg-7">
            <?php
              $glbPath = base_url('resources/assets/DeliveryBotv1.glb');
              include __DIR__ . '/components/3dVisualizer.php';
            ?>
          </div>
          <div class="col-lg-5">
            <div class="project-summary">
              <span class="rur-kicker mb-3">Introducing</span>
              <h2 class="rur-section-title mb-3">Delivery BOT v1</h2>
              <p class="rur-subtitle mb-0">The robot can receive a delivery request, carry the order, and move it to the requested classroom or destination.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="rur-page-section">
      <div class="project-download-band text-center">
        <h2 class="rur-section-title mb-2">Download our mobile app</h2>
        <p class="rur-subtitle">Test the robot flow and control the DELIVER-E experience from Android.</p>
        <a href="<?= e(base_url('resources/RUR_logo_white.png')) ?>" class="btn btn-primary" download>Download DELIVER-E App</a>
      </div>
    </section>

    <?php
      $page = base_url('pages/members.php');
      $section = 'deliver-e';
      $link = $page . '#' . $section;
      include __DIR__ . '/components/members_for_project_page.php';
    ?>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
      <div id="myToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <strong class="me-auto">DELIVER-E</strong>
          <small>Just now</small>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          Download our mobile app and start using our robot!
          <a href="<?= e(base_url('resources/RUR_logo_white.png')) ?>" download>click here!</a>
        </div>
      </div>
    </div>

    <?php include __DIR__ . '/components/footer.php'; ?>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      setTimeout(function () {
        const toastEl = document.getElementById('myToast');
        if (toastEl) {
          const toast = new bootstrap.Toast(toastEl);
          toast.show();
        }
      }, 5000);
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.2.0/model-viewer.min.js"></script>
</body>
</html>
