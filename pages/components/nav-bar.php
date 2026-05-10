<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
?>
<header class="site-header sticky-top">
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #0f1424;">
    <div class="container-fluid">
      <a class="navbar-brand site-navbar-brand d-flex align-items-center" href="<?= e(base_url('index.php')) ?>" style="font-family: 'Roboto', sans-serif;">
        <img src="<?= e(base_url('resources/RUR_logo_white.png')) ?>" alt="Logo" width="40" height="40" class="me-2">
        Research Unit of Robotics
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavMain" aria-controls="navbarNavMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse site-header-collapse" id="navbarNavMain">
        <ul class="navbar-nav site-header-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link <?= e(active_link('/index.php')) ?>" href="<?= e(base_url('index.php')) ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link <?= e(active_link('/pages/members.php')) ?>" href="<?= e(base_url('pages/members.php')) ?>">Team Members</a></li>
          <li class="nav-item"><a class="nav-link <?= e(active_link('/pages/projects.php')) ?>" href="<?= e(base_url('pages/projects.php')) ?>">Projects</a></li>
          <li class="nav-item"><a class="nav-link <?= e(active_link('/pages/gallery.php')) ?>" href="<?= e(base_url('pages/gallery.php')) ?>">Gallery</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>
