<?php require_once __DIR__ . '/../includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Research Unit of Robotics</title>
  <link rel="icon" type="image/png" href="<?= e(base_url('resources/RUR_logo_white.png')) ?>">
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="<?= e(base_url('styles/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(base_url('styles/style.css')) ?>" rel="stylesheet">
  <!-- GLightbox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<style>
  .gallery-thumb {
    width: 100%;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(15, 20, 36, 0.08);
    transition: transform 180ms ease, box-shadow 180ms ease;
  }

  .gallery-thumb:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 40px rgba(15, 20, 36, 0.14);
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
        <span class="rur-kicker mb-3">Galeria</span>
        <h1 class="rur-section-title mb-2">Research Unit of Robotics Gallery</h1>
        <p class="rur-subtitle mb-0">La galeria ya abre con la misma cabecera visual que las paginas de tienda para que la navegacion se sienta uniforme.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a class="btn rur-btn-outline" href="<?= e(base_url('index.php')) ?>">Go home</a>
      </div>
    </div>
  </div>
</section>


<section class="rur-page-section">
<div class="rur-panel">
<div class="row g-3">
  <?php for ($i = 1; $i <= 28; $i++): ?>
    <?php
      $relativePath = 'resources/gallery/' . $i . '.jpeg';
      $filePath = project_root() . '/' . $relativePath;
    ?>
    <?php if (is_file($filePath)): ?>
      <div class="col-6 col-md-4 col-xl-3">
        <a href="<?= e(base_url($relativePath)) ?>" class="glightbox" data-gallery="gallery1">
          <img src="<?= e(base_url($relativePath)) ?>" class="gallery-thumb" alt="Gallery Image <?= e((string) $i) ?>">
        </a>
      </div>
    <?php endif; ?>
  <?php endfor; ?>
</div>
</div>
</section>



  <!-- Last modified-->
  <?php include __DIR__ . '/components/last_modified.php'; ?>
  <!-- FOOTER -->
  <?php include __DIR__ . '/components/footer.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- GLightbox JS -->
  <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const lightbox = GLightbox({
      selector: '.glightbox'
    });
  });
</script>
  </main>
</body>
</html>
