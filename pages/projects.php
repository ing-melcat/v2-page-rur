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

.project-preview {
  width: 100%;
  aspect-ratio: 16 / 9;
  object-fit: cover;
  border-radius: 14px;
}

.proj-btn {
  background-color: #ecbf03;
  border-color: #ecbf03;
  color: #0f1424;
  font-weight: 700;
}

.proj-btn:hover {
  background-color: #0f1424;
  border-color: #0f1424;
  color: #fff;
}

.mem-btn {
  background-color: #0f1424;
  border-color: #0f1424;
  color: #fff;
  font-weight: 700;
}

.mem-btn:hover {
  background-color: #ecbf03;
  border-color: #ecbf03;
  color: #0f1424;
}
</style>

</head>

<body class="store-page">
        <!--NavBar section -->
    <?php include __DIR__ . '/components/nav-bar.php'; ?>
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
      <img src="<?= e(base_url('resources/rur-1.png')) ?>" class="project-preview shadow-sm" alt="UNIPOLITO project preview">
    </div>
    <div class="col-md-6">
      <h3>UNIPOLITO</h3>
      <p>Mobile robotics platform developed as part of the RUR prototyping and research ecosystem.</p>
      <a href="<?= e(base_url('pages/3dVisualizer_unipolitov4.php')) ?>" class="btn d-block mb-2 proj-btn">
        Discover more about the project
      </a>
      <a href="<?= e(base_url('pages/members.php#unipolito')) ?>" class="btn d-block mem-btn">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 2 (swap order) -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6 order-md-2">
      <img src="<?= e(base_url('resources/delivery.jpg')) ?>" class="project-preview shadow-sm" alt="DELIVER-E project preview">
    </div>
    <div class="col-md-6 order-md-1">
      <h3>DELIVER-E</h3>
      <p>Autonomous delivery robot created to move snacks and small items around the university.</p>
      <a href="<?= e(base_url('pages/3dVisualizer_delivery.php')) ?>" class="btn d-block mb-2 proj-btn">
        Discover more about the project
      </a>
      <a href="<?= e(base_url('pages/members.php#deliver-e')) ?>" class="btn d-block mem-btn">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 3 -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <img src="<?= e(base_url('resources/gallery/21.jpeg')) ?>" class="project-preview shadow-sm" alt="UMO AI project preview">
    </div>
    <div class="col-md-6">
      <h3>UMO AI</h3>
      <p>AI-focused robotics platform for experiments in navigation, sensing, and interaction.</p>
      <a href="<?= e(base_url('pages/3dVisualizer_UMO.php')) ?>" class="btn d-block mb-2 proj-btn">
        Discover more about the project
      </a>
      <a href="<?= e(base_url('pages/members.php#umo')) ?>" class="btn d-block mem-btn">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 4 (swap order) -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6 order-md-2">
      <img src="<?= e(base_url('resources/lumobox.jpg')) ?>" class="project-preview shadow-sm" alt="LUMOBOX project preview">
    </div>
    <div class="col-md-6 order-md-1">
      <h3>LUMOBOX</h3>
      <p>Hardware prototype project prepared for embedded systems and robotics demonstrations.</p>
      <a href="<?= e(base_url('pages/3dVisualizer_lumobox.php')) ?>" class="btn d-block mb-2 proj-btn">
        Discover more about the project
      </a>
      <a href="<?= e(base_url('pages/members.php#lumobox')) ?>" class="btn d-block mem-btn">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 5 -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <img src="<?= e(base_url('resources/rur-members1.jpeg')) ?>" class="project-preview shadow-sm" alt="ROVER RASSOR project preview">
    </div>
    <div class="col-md-6">
      <h3>ROVER RASSOR</h3>
      <p>Rover documentation and research collaboration around RASSOR-style robotics development.</p>
      <a href="<?= e(base_url('pages/projects/rover_rassor.php')) ?>" class="btn d-block mb-2 proj-btn">
        Discover more about the project
      </a>
      <a href="<?= e(base_url('pages/members.php#rassor')) ?>" class="btn d-block mem-btn">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 6 (swap order) -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6 order-md-2">
      <img src="<?= e(base_url('resources/line.jpeg')) ?>" class="project-preview shadow-sm" alt="LINE FOLLOWER project preview">
    </div>
    <div class="col-md-6 order-md-1">
      <h3>LINE FOLLOWER</h3>
      <p>Line-following robot project focused on control, sensing, and fast iteration.</p>
      <a href="<?= e(base_url('pages/3dVisualizer_unipolitov4.php')) ?>" class="btn d-block mb-2 proj-btn disabled">
        Discover more about the project
      </a>
      <a href="<?= e(base_url('pages/members.php#line-fo')) ?>" class="btn d-block mem-btn">
        Discover more about the creators
      </a>
    </div>
  </div>

  <!-- Project 7 -->
  <div class="row align-items-center mb-5">
    <div class="col-md-6">
      <img src="<?= e(base_url('resources/page.png')) ?>" class="project-preview shadow-sm" alt="RUR web page project preview">
    </div>
    <div class="col-md-6">
      <h3>RUR WEB PAGE</h3>
      <p>Web platform for presenting RUR projects, members, gallery content, and documentation.</p>
      <a href="<?= e(base_url('pages/3dVisualizer_unipolitov4.php')) ?>" class="btn d-block mb-2 proj-btn disabled">
        Discover more about the project
      </a>
      <a href="<?= e(base_url('pages/members.php#page')) ?>" class="btn d-block mem-btn">
        Discover more about the creators
      </a>
    </div>
  </div>

</div>
    </div>
    </section>

<!--Main card section -->

  <!-- Last modified-->
  <?php include __DIR__ . '/components/last_modified.php'; ?>
  <!-- FOOTER -->
  <?php include __DIR__ . '/components/footer.php'; ?>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
