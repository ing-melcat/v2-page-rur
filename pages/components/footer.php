<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  .rur-site-footer {
    position: relative;
    left: 50%;
    right: 50%;
    width: 100vw;
    max-width: 100vw;
    margin-left: -50vw;
    margin-right: -50vw;
    margin-top: 2.5rem;
    background: #0f1424;
    color: #fff;
    padding: 2rem 0 1rem;
    flex-shrink: 0;
  }

  .store-main .rur-site-footer {
    margin-bottom: -1.5rem;
  }

  .rur-site-footer .footer-inner {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 1rem;
  }

  .rur-site-footer h5,
  .rur-site-footer h6 {
    color: #fff;
    font-weight: 700;
  }

  .rur-site-footer p,
  .rur-site-footer a,
  .rur-site-footer small {
    color: rgba(255, 255, 255, 0.62);
  }

  .rur-site-footer a {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .08rem 0;
    text-decoration: none;
    transition: color 160ms ease;
  }

  .rur-site-footer a:hover {
    color: #ecbf03;
  }

  .rur-site-footer .footer-rule {
    border-color: rgba(255, 255, 255, 0.18);
    margin: 2.25rem 0 1rem;
  }

  @media (min-width: 992px) {
    .store-main .rur-site-footer {
      margin-bottom: -3rem;
    }
  }
</style>

<footer class="rur-site-footer">
  <div class="footer-inner">
    <div class="row g-4">
      <div class="col-lg-6 col-md-7">
        <h5>Research Unit of Robotics</h5>
        <p class="mb-0">
          A team dedicated to developing robotics projects and promoting innovation, research,
          and technological skills among students.
        </p>
      </div>

      <div class="col-lg-3 col-md-5 ms-lg-auto">
        <h6>Follow us!</h6>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a href="https://www.instagram.com/_ru.robotics/" target="_blank" rel="noopener">
              <i class="bi bi-instagram"></i> Instagram
            </a>
          </li>
          <li class="nav-item">
            <a href="https://www.facebook.com/profile.php?id=61578506520193" target="_blank" rel="noopener">
              <i class="bi bi-facebook"></i> Facebook
            </a>
          </li>
        </ul>
      </div>
    </div>

    <hr class="footer-rule">
    <p class="text-center mb-0">
      <small>(c) 2025 Research Unit of Robotics - All rights reserved</small>
    </p>
  </div>
</footer>
