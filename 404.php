<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Research Unit of Robotics</title>
  <link rel="icon" type="image/png" href="resources/RUR_logo_white.png">
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="styles/bootstrap.min.css" rel="stylesheet">
  <link href="styles/style.css" rel="stylesheet">

  <style>
.fixed-img {
  width: 60%;
  aspect-ratio: 16 / 9;  /* keeps consistent shape */
  object-fit: cover;
}
.fixed-img2 {
  width: 40%;
  aspect-ratio: 16 / 9;  /* keeps consistent shape */
  object-fit: cover;
}
.bg-darker {
  background-color: #0a0a0a !important; 
}
.bg-dark-medium{
    background-color: #191616 !important; 
}
.bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
</style>

</head>

<body class="store-page">
<?php include 'pages/components/nav-bar.php'; ?>
<main class="store-main container py-4 py-lg-5">
  <section class="rur-page-section">
    <div class="rur-hero">
      <div class="row g-4 align-items-center">
        <div class="col-lg-8">
          <span class="rur-kicker mb-3">404</span>
          <h1 class="rur-section-title mb-2">Pagina no encontrada</h1>
          <p class="rur-subtitle mb-0">La vista de error tambien comparte ahora el mismo estilo de header que el resto del sitio principal.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
          <a class="btn rur-btn-outline" href="/index.php">Volver al inicio</a>
        </div>
      </div>
    </div>
  </section>

  <section class="rur-page-section">
    <div class="rur-panel">
      <?php include 'pages/components/404_component.php'; ?>
    </div>
  </section>
</main>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
