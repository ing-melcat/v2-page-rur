<?php
require_once __DIR__ . '/../includes/bootstrap.php';
if (is_logged_in()) {
    redirect_to(base_url('pages/product.php'));
}
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | RUR Store</title>
  <link rel="icon" type="image/png" href="<?= e(base_url('resources/RUR_logo_white.png')) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="<?= e(base_url('styles/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(base_url('styles/style.css')) ?>" rel="stylesheet">
</head>
<body class="store-page store-login-page">
  <?php include __DIR__ . '/components/nav-bar.php'; ?>

  <main class="store-main container py-4 py-lg-5">
    <section class="rur-page-section">
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-6 mx-auto">
          <div class="rur-auth-card h-100 d-flex flex-column justify-content-center">
            <div class="mb-4">
              
              <h2 class="rur-section-title mt-3 mb-2">Bienvenido de vuelta</h2>
              <p class="rur-help-text mb-0">Usa tu correo y contraseña para desbloquear el módulo de compra.</p>
            </div>

            <?php if ($flash): ?>
              <div class="alert alert-<?= e($flash['type']) ?> rounded-4"><?= e($flash['message']) ?></div>
            <?php endif; ?>

           

            <form id="loginForm" class="d-grid gap-3">
              <div>
                <label class="form-label fw-semibold">Correo</label>
                <input type="email" class="form-control rur-auth-input" name="email" placeholder="ingresa tu correo" required>
              </div>
              <div>
                <label class="form-label fw-semibold">Contraseña</label>
                <input type="password" class="form-control rur-auth-input" name="password" placeholder="Tu contraseña" required>
              </div>
              <div class="d-grid d-sm-flex gap-2 pt-2">
                <button class="btn rur-btn-primary flex-fill" type="submit">Entrar</button>
                <a class="btn rur-btn-outline flex-fill" href="<?= e(base_url('pages/register.php')) ?>">Crear cuenta</a>
              </div>
            </form>

            <div id="loginMessage" class="mt-4"></div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/components/footer.php'; ?>

  <script src="<?= e(base_url('assets/js/store-api.js')) ?>"></script>
  <script>
    document.getElementById('loginForm').addEventListener('submit', async (event) => {
      event.preventDefault();
      const form = event.currentTarget;
      const formData = Object.fromEntries(new FormData(form).entries());
      const box = document.getElementById('loginMessage');
      box.innerHTML = '';
      try {
        const data = await StoreApi.post('<?= e(base_url('api/auth/login.php')) ?>', formData);
        box.innerHTML = `<div class="alert alert-success rounded-4">${data.message}</div>`;
        window.location.href = data.redirect;
      } catch (error) {
        box.innerHTML = `<div class="alert alert-danger rounded-4">${error.message}</div>`;
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
