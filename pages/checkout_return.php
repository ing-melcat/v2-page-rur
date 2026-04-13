<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login(false);

$userId = (int) current_user()['id'];
$localOrderId = (int) ($_GET['local_order_id'] ?? 0);
$status = (string) ($_GET['status'] ?? 'unknown');
$providerOrderId = (string) ($_GET['order_id'] ?? '');
$order = null;
$message = null;
$messageType = 'secondary';

if ($localOrderId > 0) {
    $order = get_order_by_id($localOrderId, $userId);
}

if (!$order && $providerOrderId !== '') {
    $providerOrder = null;

    try {
        $providerOrder = conekta_get_order($providerOrderId);
    } catch (Throwable $ignored) {
    }

    if (is_array($providerOrder)) {
        if (!empty($providerOrder['metadata']['local_order_id'])) {
            $localOrderId = (int) $providerOrder['metadata']['local_order_id'];
            if ($localOrderId > 0) {
                $order = get_order_by_id($localOrderId, $userId);
            }
        }

        if (!$order) {
            $order = find_local_order_by_provider_order_id($providerOrderId);
            if ($order && (int) $order['user_id'] !== $userId) {
                $order = null;
            }
        }
    }
}

if ($order && !empty($order['payment_provider_order_id'])) {
    try {
        $providerOrder = conekta_get_order((string) $order['payment_provider_order_id']);
        $paymentStatus = (string) ($providerOrder['payment_status'] ?? 'pending_payment');

        if ($paymentStatus === 'paid') {
            finalize_paid_order((int) $order['id'], $providerOrder);
            clear_cart($userId);
            $order = get_order_by_id((int) $order['id'], $userId);
            $message = 'Pago confirmado correctamente. Tu ticket ya esta disponible.';
            $messageType = 'success';
        } elseif (in_array($paymentStatus, ['declined', 'failed'], true)) {
            mark_order_status((int) $order['id'], 'declined', $paymentStatus, $providerOrder);
            $order = get_order_by_id((int) $order['id'], $userId);
            $message = 'El pago fue rechazado por Conekta.';
            $messageType = 'danger';
        } else {
            mark_order_status((int) $order['id'], 'pending_payment', $paymentStatus, $providerOrder);
            $order = get_order_by_id((int) $order['id'], $userId);
            $message = 'La orden sigue pendiente. Si configuraste webhook, el estado se actualizara en cuanto Conekta confirme el pago.';
            $messageType = 'warning';
        }
    } catch (Throwable $e) {
        $message = 'No se pudo consultar el estado real en Conekta: ' . $e->getMessage();
        $messageType = 'warning';
    }
} else {
    $message = 'No se encontro la orden local asociada al retorno del checkout.';
    $messageType = 'danger';
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
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="rur-status-card">
            <span class="rur-kicker">Confirmacion</span>
            <h2 class="h3 fw-bold mt-3 mb-3">Estado actualizado</h2>
            <div class="alert alert-<?= e($messageType) ?> rounded-4"><?= e($message ?? 'Sin novedades.') ?></div>
            <?php if ($order): ?>
              <div class="rur-login-note mb-4">
                <div><strong>Orden:</strong> <?= e($order['order_number']) ?></div>
                <div><strong>Estado local:</strong> <?= e($order['status']) ?></div>
                <div><strong>Estado de pago:</strong> <?= e($order['payment_status']) ?></div>
                <div><strong>Total:</strong> <?= e(money_format_mx((float) $order['total_amount'])) ?></div>
              </div>
              <div class="d-flex gap-2 flex-wrap">
                <a class="btn rur-btn-dark" href="<?= e(base_url('pages/purchases.php')) ?>">Ver compras</a>
                <a class="btn rur-btn-outline" href="<?= e(base_url('pages/ticket.php?order_id=' . (int) $order['id'])) ?>">Abrir ticket</a>
                <a class="btn rur-btn-outline" href="<?= e(base_url('pages/product.php')) ?>">Volver a productos</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
