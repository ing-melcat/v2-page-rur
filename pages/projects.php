<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Research Unit of Robotics</title>
  <link rel="icon" type="image/png" href="../resources/RUR_logo_white.png">
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="../styles/bootstrap.min.css" rel="stylesheet">
  <link href="../styles/style.css" rel="stylesheet">

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
  background-color: #0a0a0a !important; /* darker than Bootstrap's bg-dark */
}
.bg-dark-medium{
    background-color: #191616 !important; /* darker than Bootstrap's bg-dark */
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
        <!--NavBar section -->
    <?php include 'components/nav-bar.php'; ?>
    <!--NavBar section -->
    <main class="store-main container py-4 py-lg-5">
    <section class="rur-page-section">
      <div class="rur-hero">
        <div class="row g-4 align-items-center">
          <div class="col-lg-8">
            <span class="rur-kicker mb-3">Projects</span>
            <h1 class="rur-section-title mb-2">Research Unit of Robotics Projects</h1>
            <p class="rur-subtitle mb-0">La pagina de proyectos ahora abre con el mismo header visual del flujo de tienda para unificar la experiencia del sitio.</p>
          </div>
          <div class="col-lg-4">
            <div class="rur-methods justify-content-lg-end">
              <span class="rur-chip">Investigacion</span>
              <span class="rur-chip">Hardware</span>
              <span class="rur-chip">IA</span>
            </div>
          </div>
        </div>
      </div>
    </section>
    

    <section class="rur-page-section">
    <div class="rur-panel">

  <!-- Project 1 -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <img src="/resources/rur-1.png" class="img-fluid rounded shadow-sm" alt="Project 1">
    </div>
    <div class="col-md-6">
      <h3>UNIPOLITO</h3>
      <p>Short description of project 1...</p>
      <a href="../pages/3dVisualizer_unipolitov4.php" class="btn btn-primary d-block mb-2">
        Discover more about the project
      </a>
      <a href="../pages/members.php#unipolito" class="btn btn-secondary d-block">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 2 (swap order) -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6 order-md-2">
      <img src="/resources/rassor1.jpeg" class="img-fluid rounded shadow-sm" alt="Project 2">
    </div>
    <div class="col-md-6 order-md-1">
      <h3>DELIVER-E</h3>
      <p>Short description of project 2...</p>
      <a href="../pages/3dVisualizer_delivery.php" class="btn btn-primary d-block mb-2">
        Discover more about the project
      </a>
      <a href="../pages/members.php#unipolito" class="btn btn-secondary d-block">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 3 -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <img src="/resources/rassor1.jpeg" class="img-fluid rounded shadow-sm" alt="Project 3">
    </div>
    <div class="col-md-6">
      <h3>UMO AI</h3>
      <p>Short description of project 3...</p>
      <a href="../pages/3dVisualizer_UMO.php" class="btn btn-primary d-block mb-2">
        Discover more about the project
      </a>
      <a href="../pages/members.php#unipolito" class="btn btn-secondary d-block">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 4 (swap order) -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6 order-md-2">
      <img src="/resources/rassor1.jpeg" class="img-fluid rounded shadow-sm" alt="Project 4">
    </div>
    <div class="col-md-6 order-md-1">
      <h3>LUMOBOX</h3>
      <p>Short description of project 4...</p>
      <a href="../pages/3dVisualizer_lumobox.php" class="btn btn-primary d-block mb-2">
        Discover more about the project
      </a>
      <a href="../pages/members.php#unipolito" class="btn btn-secondary d-block">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 5 -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <img src="/resources/rur-members1.jpeg" class="img-fluid rounded shadow-sm" alt="Project 5">
    </div>
    <div class="col-md-6">
      <h3>ROVER RASSOR</h3>
      <p>Short description of project 5...</p>
      <a href="../pages/projects/rover_rassor.php" class="btn btn-primary d-block mb-2">
        Discover more about the project
      </a>
      <a href="../pages/members.php#unipolito" class="btn btn-secondary d-block">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 6 (swap order) -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6 order-md-2">
      <img src="/resources/rassor1.jpeg" class="img-fluid rounded shadow-sm" alt="Project 6">
    </div>
    <div class="col-md-6 order-md-1">
      <h3>LINE FOLLOWER</h3>
      <p>Short description of project 6...</p>
      <a href="../pages/3dVisualizer_unipolitov4.php" class="btn btn-primary d-block mb-2 disabled">
        Discover more about the project
      </a>
      <a href="../pages/members.php#unipolito" class="btn btn-secondary d-block">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 7 -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <img src="/resources/rur-members1.jpeg" class="img-fluid rounded shadow-sm" alt="Project 5">
    </div>
    <div class="col-md-6">
      <h3>RUR WEB PAGE</h3>
      <p>Short description of project 7...</p>
      <a href="../pages/3dVisualizer_unipolitov4.php" class="btn btn-primary d-block mb-2">
        Discover more about the project
      </a>
      <a href="../pages/members.php#unipolito" class="btn btn-secondary d-block">
        Discover more about the creators
      </a>
    </div>
  </div>

</div>
    </div>
    </section>

<!--Main card section -->

<?php include 'components/page_on_build.php'; ?>

  <!-- Last modified-->
  <?php include '../pages/components/last_modified.php'; ?>
  <!-- FOOTER -->
  <?php include 'components/footer.php'; ?>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
