<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login(false);
$orders = get_user_orders((int) current_user()['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Compras recientes | RUR Store</title>
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
            <span class="rur-kicker mb-3">Historial de compra</span>
            <h1 class="rur-section-title mb-2">Compras recientes y seguimiento de pago</h1>
            <p class="rur-subtitle mb-0">Consulta cada orden, valida su estado y salta directo a ticket o facturacion desde el mismo flujo visual de la tienda.</p>
          </div>
          
          </div>
        </div>
      </div>                
    </section>

    <section class="rur-page-section">
      <div class="rur-panel">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
          <div>
            <span class="rur-kicker">Resumen</span>
            <h2 class="rur-section-title mt-3 mb-2">Tus ultimas operaciones</h2>
            <p class="rur-help-text mb-0">Abre cada comprobante o prepara la solicitud de factura cuando la orden ya este pagada.</p>
          </div>
          <a class="btn rur-btn-outline" href="<?= e(base_url('pages/product.php')) ?>">Volver a productos</a>
        </div>

        <?php if (empty($orders)): ?>
          <div class="rur-empty-state">
            <h3 class="h5 fw-bold mb-2">Todavia no hay compras registradas</h3>
            <p class="mb-3">Cuando completes una orden, aparecera aqui junto con su ticket y accesos de facturacion.</p>
            <a class="btn rur-btn-primary" href="<?= e(base_url('pages/product.php')) ?>">Explorar productos</a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table rur-table align-middle mb-0">
              <thead>
                <tr>
                  <th>Orden</th>
                  <th>Fecha</th>
                  <th>Total</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                  <?php
                  $statusClass = 'rur-status-cancelled';
                  if ($order['status'] === 'paid') {
                      $statusClass = 'rur-status-paid';
                  } elseif ($order['status'] === 'pending_payment') {
                      $statusClass = 'rur-status-pending';
                  } elseif ($order['status'] === 'declined') {
                      $statusClass = 'rur-status-declined';
                  }
                  ?>
                  <tr>
                    <td class="fw-semibold"><?= e($order['order_number']) ?></td>
                    <td><?= e($order['created_at']) ?></td>
                    <td class="fw-semibold"><?= e(money_format_mx((float) $order['total_amount'])) ?></td>
                    <td><span class="rur-status-badge <?= e($statusClass) ?>"><?= e($order['status']) ?></span></td>
                    <td class="d-flex gap-2 flex-wrap">
                      <a class="btn rur-btn-dark py-2 px-3" href="<?= e(base_url('pages/ticket.php?order_id=' . (int) $order['id'])) ?>">Ticket</a>
                      <a class="btn rur-btn-outline py-2 px-3" href="<?= e(base_url('pages/invoices.php?order_id=' . (int) $order['id'])) ?>">Factura</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
