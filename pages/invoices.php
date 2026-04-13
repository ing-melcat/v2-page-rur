<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login(false);
$user = current_user();
$orderId = (int) ($_GET['order_id'] ?? 0);
$order = $orderId ? get_order_by_id($orderId, (int) $user['id']) : null;
$invoiceRequests = get_invoice_requests((int) $user['id']);
$facturamaConfigured = facturama_enabled();
$facturamaDemoMode = facturama_demo_mode();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Facturas | RUR Store</title>
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
            <span class="rur-kicker mb-3">Facturacion</span>
            <h1 class="rur-section-title mb-2">Solicita y consulta tus facturas</h1>
            <p class="rur-subtitle mb-0">Esta vista conserva el mismo lenguaje visual de carrito y productos para registrar datos fiscales, validar ordenes pagadas y revisar solicitudes ya enviadas.</p>
          </div>
          <div class="col-lg-4">
            <div class="rur-methods justify-content-lg-end">
              <span class="rur-chip"><?= count($invoiceRequests) ?> solicitud(es)</span>
              <span class="rur-chip">Orden vinculada</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="rur-page-section">
      <div class="row g-4">
        <div class="col-lg-6">
          <div class="rur-status-card h-100">
            <span class="rur-kicker">Captura fiscal</span>
            <h2 class="h3 fw-bold mt-3 mb-3">Solicitar factura</h2>
            <p class="rur-help-text mb-4">Registra los datos fiscales de una compra pagada y deja lista la solicitud para procesamiento.</p>
            <?php if ($facturamaDemoMode): ?>
              <div class="alert alert-info rounded-4">Modo demo activo: el sistema generara una factura de demostracion descargable sin timbrado real en Facturama.</div>
            <?php endif; ?>
            <?php if (!$facturamaConfigured): ?>
              <div class="alert alert-warning rounded-4">Facturama no esta configurado en el <code>.env</code>. La pantalla guardara solicitudes, pero no emitira CFDI hasta agregar credenciales.</div>
            <?php endif; ?>
            <?php if (!$order): ?>
              <div class="rur-login-note">Abre esta pantalla desde una compra pagada para precargar la orden.</div>
            <?php elseif ($order['status'] !== 'paid'): ?>
              <div class="alert alert-warning rounded-4">La orden seleccionada todavia no esta pagada.</div>
            <?php else: ?>
              <div class="rur-login-note mb-4">
                <strong>Orden:</strong> <?= e($order['order_number']) ?><br>
                <strong>Total:</strong> <?= e(money_format_mx((float) $order['total_amount'])) ?>
              </div>
              <form id="invoiceForm" class="d-grid gap-3">
                <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                <div><label class="form-label fw-semibold">RFC</label><input class="form-control rur-auth-input" name="rfc" required></div>
                <div><label class="form-label fw-semibold">Razon social</label><input class="form-control rur-auth-input" name="razon_social" required></div>
                <div><label class="form-label fw-semibold">Correo de facturacion</label><input type="email" class="form-control rur-auth-input" name="billing_email" value="<?= e($user['email']) ?>" required></div>
                <div><label class="form-label fw-semibold">Uso CFDI</label><input class="form-control rur-auth-input" name="uso_cfdi" placeholder="G03" required></div>
                <div><label class="form-label fw-semibold">Regimen fiscal</label><input class="form-control rur-auth-input" name="regimen_fiscal" placeholder="601" required></div>
                <div><label class="form-label fw-semibold">Codigo postal</label><input class="form-control rur-auth-input" name="postal_code" required></div>
                <div><label class="form-label fw-semibold">Forma de pago SAT</label><input class="form-control rur-auth-input" name="payment_form" placeholder="99" value="99" required></div>
                <div>
                  <label class="form-label fw-semibold">Metodo de pago</label>
                  <select class="form-select rur-select" name="payment_method" required>
                    <option value="PUE" selected>PUE</option>
                    <option value="PPD">PPD</option>
                  </select>
                </div>
                <button class="btn rur-btn-primary" type="submit"><?= $facturamaDemoMode ? 'Generar factura demo' : ($facturamaConfigured ? 'Emitir factura con Facturama' : 'Guardar solicitud') ?></button>
              </form>
              <div id="invoiceMessage" class="mt-3"></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="rur-panel h-100">
            <span class="rur-kicker">Historial</span>
            <h2 class="h3 fw-bold mt-3 mb-3">Solicitudes registradas</h2>
            <?php if (empty($invoiceRequests)): ?>
              <div class="rur-empty-state">
                <h3 class="h5 fw-bold mb-2">No hay solicitudes de factura todavia</h3>
                <p class="mb-0">Cuando guardes una solicitud aparecera aqui con su estado actual.</p>
              </div>
            <?php else: ?>
              <?php foreach ($invoiceRequests as $invoice): ?>
                <div class="border rounded-4 p-3 mb-3 bg-white">
                  <div class="d-flex justify-content-between align-items-center gap-3">
                    <div class="fw-semibold"><?= e($invoice['razon_social']) ?></div>
                    <span class="rur-status-badge rur-status-cancelled"><?= e($invoice['status']) ?></span>
                  </div>
                  <div class="small text-muted mt-2">Orden: <?= e($invoice['order_number']) ?> · <?= e(money_format_mx((float) $invoice['total_amount'])) ?></div>
                  <div class="small mt-1">RFC: <?= e($invoice['rfc']) ?> · Uso CFDI: <?= e($invoice['uso_cfdi']) ?></div>
                  <?php if (!empty($invoice['facturama_uuid'])): ?>
                    <div class="small mt-1">UUID: <?= e($invoice['facturama_uuid']) ?></div>
                  <?php endif; ?>
                  <?php if (($invoice['facturama_status'] ?? '') === 'demo'): ?>
                    <div class="small mt-1 text-primary fw-semibold">Factura demo para entrega academica.</div>
                  <?php endif; ?>
                  <?php if (!empty($invoice['facturama_cfdi_id'])): ?>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                      <a class="btn rur-btn-dark py-2 px-3" href="<?= e(base_url('api/invoices/download.php?invoice_request_id=' . (int) $invoice['id'] . '&format=pdf')) ?>">Descargar PDF</a>
                      <a class="btn rur-btn-outline py-2 px-3" href="<?= e(base_url('api/invoices/download.php?invoice_request_id=' . (int) $invoice['id'] . '&format=xml')) ?>">Descargar XML</a>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="<?= e(base_url('assets/js/store-api.js')) ?>"></script>
  <script>
    const invoiceForm = document.getElementById('invoiceForm');
    if (invoiceForm) {
      invoiceForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const payload = Object.fromEntries(new FormData(invoiceForm).entries());
        const box = document.getElementById('invoiceMessage');
        box.innerHTML = '';
        try {
          const data = await StoreApi.post('<?= e(base_url('api/invoices/request.php')) ?>', payload);
          box.innerHTML = `<div class="alert alert-success rounded-4">${data.message}</div>`;
          setTimeout(() => window.location.reload(), 500);
        } catch (error) {
          box.innerHTML = `<div class="alert alert-danger rounded-4">${error.message}</div>`;
        }
      });
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
