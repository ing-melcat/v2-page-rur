<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Research Unit of Robotics</title>
  <link rel="icon" type="image/png" href="<?= e(base_url('resources/RUR_logo_white.png')) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="<?= e(base_url('styles/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(base_url('styles/style.css')) ?>" rel="stylesheet">
  <style>
    .fixed-img { width: 60%; aspect-ratio: 16 / 9; object-fit: cover; }
    #myCarousel{ width: 80%; height: 40%; margin: auto; overflow: hidden; }
    #myCarousel .carousel-item img { width: 100%; height: 100%; object-fit: cover; }
    #myCarousel .carousel-caption h5,
    #myCarousel .carousel-caption p {
      background-color: rgba(255,255,255,0.6);
      padding: 6px 10px; border-radius: 4px; display: block; color: #0a0a0a;
      font-family: 'Roboto';
    }
    #myCarousel .carousel-caption button{ font-size: clamp(0.8rem, 2vw, 1.2rem); padding: clamp(0.4rem, 1vw, 0.8rem) clamp(0.8rem, 2vw, 1.5rem); background-color: #ecbf03; margin-bottom: 10px; border: none; }
    #myCarousel .carousel-caption a{ text-decoration: none; color: #fff; }
    #myCarousel .carousel-caption button:hover{ background-color: #0a0a0a; }
  </style>
</head>
<body class="store-page">
  <?php include __DIR__ . '/pages/components/nav-bar.php'; ?>

  <main class="store-main container py-4 py-lg-5">
    <section class="rur-page-section">
      <div class="rur-hero rur-hero-dark">
        <div class="row g-4 align-items-center">
          <div class="col-lg-8">
            <span class="rur-kicker mb-3">Research Unit of Robotics</span>
            <h1 class="rur-display-title mb-3">Welcome to the Research Unit of Robotics</h1>
            <p class="rur-subtitle mb-0">The research Unit of robotics is an organization that works in robotics projects 
            for the students to develop skills.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="rur-page-section">
      <div id="myCarousel">
        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3" aria-label="Slide 4"></button>
          </div>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="<?= e(base_url('resources/rur-members1.jpeg')) ?>" class="d-block fixed-img" alt="...">
              <div class="carousel-caption d-none d-md-block">
                <h5>OUR TEAMS</h5>
                <p>Discover more about the talented people that makes real our projects</p>
                <button><a href="<?= e(base_url('pages/members.php')) ?>">Discover</a></button>
              </div>
            </div>
            <div class="carousel-item">
              <img src="<?= e(base_url('resources/projects1.jpeg')) ?>" class="d-block fixed-img" alt="...">
              <div class="carousel-caption d-none d-md-block">
                <h5>OUR PROJECTS</h5>
                <p>Discover more about the projects the RUR is developing</p>
                <button><a href="<?= e(base_url('pages/projects.php')) ?>">Discover</a></button>
              </div>
            </div>
            <div class="carousel-item">
              <img src="<?= e(base_url('resources/rassor1.jpeg')) ?>" class="d-block fixed-img" alt="...">
              <div class="carousel-caption d-none d-md-block ">
                <h5>ROVER RASSOR DOCUMENTATION</h5>
                <p>Visit the ROVER RASSOR documentation and discover more about the project!!</p>
                <button><a href="<?= e(base_url('pages/projects/rover_rassor.php')) ?>">Discover</a></button>
              </div>
            </div>
            <div class="carousel-item">
              <img src="<?= e(base_url('resources/rur-1.png')) ?>" class="d-block fixed-img" alt="...">
              <div class="carousel-caption d-none d-md-block">
                <h5>DISCOVER MORE ABOUT THE RUR</h5>
                <p>Discover more about how it was created, its porpuse and objectives!</p>
                <button><a href="<?= e(base_url('pages/members.php')) ?>">Discover</a></button>
              </div>
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </section>

    <section class="rur-page-section">
      <div class="rur-panel">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
          <div>
            <span class="rur-kicker">Preview</span>
            <h2 class="rur-section-title mt-3 mb-2">RUR's Photo Gallery</h2>
          </div>
          <a class="btn rur-btn-outline" href="<?= e(base_url('pages/gallery.php')) ?>">Abrir galeria completa</a>
        </div>
        <div class="row g-3">
          <div class="col-6 col-md-4"><img src="<?= e(base_url('resources/rassor1.jpeg')) ?>" class="img-fluid rounded shadow-sm" alt="Gallery Image 1"></div>
          <div class="col-6 col-md-4"><img src="<?= e(base_url('resources/projects1.jpeg')) ?>" class="img-fluid rounded shadow-sm" alt="Gallery Image 2"></div>
          <div class="col-6 col-md-4"><img src="<?= e(base_url('resources/rur-members1.jpeg')) ?>" class="img-fluid rounded shadow-sm" alt="Gallery Image 3"></div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/pages/components/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
