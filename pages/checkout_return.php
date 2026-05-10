<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login(false);

$userId = (int) current_user()['id'];
$localOrderId = (int) ($_GET['local_order_id'] ?? 0);
$order = null;
$message = 'La pasarela de pagos está deshabilitada. No se procesará ningún checkout.';
$messageType = 'warning';

if ($localOrderId > 0) {
    $order = get_order_by_id($localOrderId, $userId);
    if ($order) {
        $message = 'Orden encontrada. Este sitio no utiliza actualmente una pasarela de pagos.';
        $messageType = 'info';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resultado del pago</title>
  <link rel="icon" type="image/png" href="<?= e(base_url('resources/RUR_logo_white.png')) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
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
            <span class="rur-kicker mb-3">Retorno de Conekta</span>
            <h1 class="rur-section-title mb-2">Resultado del checkout</h1>
            <p class="rur-subtitle mb-0">Aqui confirmas si la orden ya quedo pagada, si sigue pendiente o si fue rechazada, manteniendo el mismo estilo visual del flujo completo de compra.</p>
          </div>
          <div class="col-lg-4">
            <div class="rur-methods justify-content-lg-end">
              <span class="rur-chip">Estado local</span>
              <span class="rur-chip">Estado de pago</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="rur-page-section">
      <?php
        $navItems = [
          ['label' => 'Productos', 'href' => base_url('pages/product.php')],
          ['label' => 'Carrito', 'href' => base_url('pages/cart.php')],
          ['label' => 'Compras recientes', 'href' => base_url('pages/purchases.php')],
          ['label' => 'Facturas', 'href' => base_url('pages/invoices.php')],
          ['label' => 'Resultado', 'href' => '#', 'active' => true],
        ];
      ?>
      <?php include __DIR__ . '/components/page-nav.php'; ?>
    </section>

    <section class="rur-page-section">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="rur-status-card">
            <span class="rur-kicker">Confirmación</span>
            <h2 class="h3 fw-bold mt-3 mb-3">Pasarela deshabilitada</h2>
            <div class="alert alert-<?= e($messageType) ?> rounded-4"><?= e($message ?? 'Sin novedades.') ?></div>
            <?php if ($order): ?>
              <div class="rur-login-note mb-4">
                <div><strong>Orden:</strong> <?= e($order['order_number']) ?></div>
                <div><strong>Estado local:</strong> <?= e($order['status']) ?></div>
                <div><strong>Total:</strong> <?= e(money_format_mx((float) $order['total_amount'])) ?></div>
              </div>
              <div class="d-flex gap-2 flex-wrap">
                <a class="btn rur-btn-dark" href="<?= e(base_url('pages/purchases.php')) ?>">Ver compras</a>
                <a class="btn rur-btn-outline" href="<?= e(base_url('pages/product.php')) ?>">Volver a productos</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/components/last_modified.php'; ?>

  <?php include __DIR__ . '/components/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
