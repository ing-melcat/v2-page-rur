<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$products = get_products(true);
$methods = array_values(array_filter(array_map('trim', explode(',', (string) env('CONEKTA_ALLOWED_PAYMENT_METHODS', 'card,cash,bank_transfer')))));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Productos | RUR Store</title>
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
      
    </section>

    <section class="rur-page-section">
      <?php
        $navItems = [
          ['label' => 'Productos', 'href' => base_url('pages/product.php'), 'active' => true],
        ];
      ?>
      <?php include __DIR__ . '/components/page-nav.php'; ?>
    </section>

    <section class="rur-page-section">
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-8">
          <div class="rur-panel h-100">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
              <div>
                <span class="rur-kicker">Tienda</span>
                <h2 class="rur-section-title mt-3 mb-2">Explora el catálogo</h2>
                <p class="rur-help- text mb-0">Pruductos / Servicios que proporcionamos</p>
              </div>
            </div>

            <div id="storeMessage"></div>

            <div class="row rur-grid-gap">
              <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-xl-6">
                  <div class="card rur-product-card border-0">
                    <img src="<?= e(base_url($product['image_url'] ?: 'resources/rur-1.png')) ?>" alt="<?= e($product['name']) ?>">
                    <div class="card-body d-flex flex-column">
                      <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <h3 class="h5 fw-bold mb-0"><?= e($product['name']) ?></h3>
                        <span class="rur-price-pill"><?= e(money_format_mx((float) $product['price'])) ?></span>
                      </div>
                      <p class="text-muted mb-3 flex-grow-1"><?= e($product['description']) ?></p>
                      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <span class="rur-stock-pill">Stock: <?= (int) $product['stock'] ?></span>
                        <button class="btn rur-btn-dark" type="button" disabled>Carrito deshabilitado</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
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
