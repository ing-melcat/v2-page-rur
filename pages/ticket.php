<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login(false);
$orderId = (int) ($_GET['order_id'] ?? 0);
$order = get_order_by_id($orderId, (int) current_user()['id']);
if (!$order) {
    http_response_code(404);
    echo 'Orden no encontrada';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ticket <?= e($order['order_number']) ?></title>
  <link rel="icon" type="image/png" href="<?= e(base_url('resources/RUR_logo_white.png')) ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link href="<?= e(base_url('styles/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= e(base_url('styles/style.css')) ?>" rel="stylesheet">
  <style>
    @media print {
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>
<body class="store-page">
  <?php include __DIR__ . '/components/nav-bar.php'; ?>

  <main class="store-main container py-4 py-lg-5">
    <section class="rur-page-section no-print">
      <div class="rur-hero">
        <div class="row g-4 align-items-center">
          <div class="col-lg-8">
            <span class="rur-kicker mb-3">Comprobante</span>
            <h1 class="rur-section-title mb-2">Ticket de compra <?= e($order['order_number']) ?></h1>
            <p class="rur-subtitle mb-0">Revisa el resumen final de la orden y genera la version impresa desde una vista coherente con carrito, compras e invoices.</p>
          </div>
          <div class="col-lg-4">
            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
              <a class="btn rur-btn-outline" href="<?= e(base_url('pages/purchases.php')) ?>">Volver</a>
              <button class="btn rur-btn-dark" onclick="window.print()">Imprimir ticket</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="rur-page-section">
      <div class="rur-panel">
        <div class="text-center mb-4">
          <img src="<?= e(base_url('resources/RUR_logo_white.png')) ?>" alt="RUR" width="70" class="bg-dark rounded p-2 mb-2">
          <h2 class="mb-1">Ticket de compra</h2>
          <div class="text-muted">Research Unit of Robotics</div>
        </div>

        <div class="row mb-4">
          <div class="col-md-6">
            <div><strong>Orden:</strong> <?= e($order['order_number']) ?></div>
            <div><strong>Cliente:</strong> <?= e($order['user_name']) ?></div>
            <div><strong>Correo:</strong> <?= e($order['user_email']) ?></div>
          </div>
          <div class="col-md-6 text-md-end">
            <div><strong>Estado:</strong> <?= e($order['status']) ?></div>
            <div><strong>Pago:</strong> <?= e($order['payment_status']) ?></div>
            <div><strong>Fecha:</strong> <?= e($order['created_at']) ?></div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table rur-table align-middle">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($order['items'] as $item): ?>
                <tr>
                  <td><?= e($item['product_name']) ?></td>
                  <td><?= e(money_format_mx((float) $item['unit_price'])) ?></td>
                  <td><?= (int) $item['quantity'] ?></td>
                  <td class="fw-semibold"><?= e(money_format_mx((float) $item['line_total'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="text-end fs-5 fw-bold">Total: <?= e(money_format_mx((float) $order['total_amount'])) ?></div>
      </div>
    </section>
  </main>
</body>
</html>
