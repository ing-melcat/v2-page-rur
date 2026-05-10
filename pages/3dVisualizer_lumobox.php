<?php require_once __DIR__ . '/../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LUMOBOX | Research Unit of Robotics</title>
  <link rel="icon" type="image/png" href="<?= e(base_url('resources/RUR_logo_white.png')) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="<?= e(base_url('styles/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(base_url('styles/style.css')) ?>" rel="stylesheet">
</head>

<body class="store-page">
  <?php include __DIR__ . '/components/nav-bar.php'; ?>

  <main class="store-main container py-4 py-lg-5">
    <section class="rur-page-section">
      <div class="rur-hero">
        <div class="row g-4 align-items-center">
          <div class="col-lg-8">
            <span class="rur-kicker mb-3">Project Viewer</span>
            <h1 class="rur-section-title mb-2">LUMOBOX</h1>
            <p class="rur-subtitle mb-0">A RUR project space prepared for model previews, documentation, and team details.</p>
          </div>
          <div class="col-lg-4">
            <div class="rur-methods justify-content-lg-end">
              <span class="rur-chip">Prototype</span>
              <span class="rur-chip">Hardware</span>
              <span class="rur-chip">Research</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="rur-page-section">
      <div class="rur-panel">
        <span class="rur-kicker mb-3">Introducing</span>
        <h2 class="rur-section-title mb-2">LUMOBOX</h2>
        <p class="rur-subtitle mb-0">The project page now follows the same visual system as the rest of the site.</p>
      </div>
    </section>

    <?php include __DIR__ . '/components/footer.php'; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.2.0/model-viewer.min.js"></script>
</body>
</html>
