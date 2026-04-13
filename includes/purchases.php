<?php
declare(strict_types=1);

function get_products(bool $onlyActive = true): array
{
    $sql = 'SELECT * FROM products';
    if ($onlyActive) {
        $sql .= ' WHERE is_active = 1';
    }
    $sql .= ' ORDER BY id ASC';
    return db()->query($sql)->fetchAll();
}

function get_product(int $productId): ?array
{
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    return $product ?: null;
}

function get_cart_items(int $userId): array
{
    $stmt = db()->prepare('SELECT c.product_id, c.quantity, p.name, p.description, p.price, p.stock, p.image_url FROM cart_items c INNER JOIN products p ON p.id = c.product_id WHERE c.user_id = ? ORDER BY c.id DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function get_cart_summary(int $userId): array
{
    $items = get_cart_items($userId);
    $count = 0;
    $subtotal = 0.0;
    foreach ($items as &$item) {
        $item['price'] = (float) $item['price'];
        $item['quantity'] = (int) $item['quantity'];
        $item['line_total'] = $item['price'] * $item['quantity'];
        $count += $item['quantity'];
        $subtotal += $item['line_total'];
    }
    unset($item);
    return [
        'items' => $items,
        'count' => $count,
        'subtotal' => $subtotal,
    ];
}

function add_product_to_cart(int $userId, int $productId, int $quantity = 1): array
{
    if ($quantity < 1) {
        throw new InvalidArgumentException('La cantidad debe ser mayor a cero.');
    }

    $product = get_product($productId);
    if (!$product || !(int) $product['is_active']) {
        throw new RuntimeException('Producto no disponible.');
    }

    if ((int) $product['stock'] < 1) {
        throw new RuntimeException('Producto sin stock.');
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ? LIMIT 1');
    $stmt->execute([$userId, $productId]);
    $existing = $stmt->fetch();

    $newQty = $quantity;
    if ($existing) {
        $newQty = (int) $existing['quantity'] + $quantity;
    }

    if ($newQty > (int) $product['stock']) {
        throw new RuntimeException('No hay suficiente stock para agregar esa cantidad.');
    }

    if ($existing) {
        $update = $pdo->prepare('UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?');
        $update->execute([$newQty, $userId, $productId]);
    } else {
        $insert = $pdo->prepare('INSERT INTO cart_items (user_id, product_id, quantity, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
        $insert->execute([$userId, $productId, $quantity]);
    }

    return get_cart_summary($userId);
}

function update_cart_quantity(int $userId, int $productId, int $quantity): array
{
    $pdo = db();
    if ($quantity <= 0) {
        $stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
        return get_cart_summary($userId);
    }

    $product = get_product($productId);
    if (!$product) {
        throw new RuntimeException('Producto no encontrado.');
    }
    if ($quantity > (int) $product['stock']) {
        throw new RuntimeException('La cantidad excede el stock disponible.');
    }

    $stmt = $pdo->prepare('UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$quantity, $userId, $productId]);
    return get_cart_summary($userId);
}

function remove_cart_item(int $userId, int $productId): array
{
    $stmt = db()->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$userId, $productId]);
    return get_cart_summary($userId);
}

function clear_cart(int $userId): void
{
    $stmt = db()->prepare('DELETE FROM cart_items WHERE user_id = ?');
    $stmt->execute([$userId]);
}

function create_pending_order_from_cart(int $userId): array
{
    $summary = get_cart_summary($userId);
    if (empty($summary['items'])) {
        throw new RuntimeException('Tu carrito está vacío.');
    }

    foreach ($summary['items'] as $item) {
        if ((int) $item['quantity'] > (int) $item['stock']) {
            throw new RuntimeException('Uno de los productos ya no tiene stock suficiente.');
        }
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $orderNumber = 'RUR-' . date('YmdHis') . '-' . random_int(1000, 9999);
        $orderStmt = $pdo->prepare('INSERT INTO orders (user_id, order_number, total_amount, status, payment_status, payment_provider, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $orderStmt->execute([$userId, $orderNumber, $summary['subtotal'], 'pending_payment', 'pending_payment', 'conekta']);
        $orderId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        foreach ($summary['items'] as $item) {
            $itemStmt->execute([
                $orderId,
                (int) $item['product_id'],
                $item['name'],
                (float) $item['price'],
                (int) $item['quantity'],
                (float) $item['line_total'],
            ]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    return get_order_by_id($orderId, $userId, true);
}

function get_order_by_id(int $orderId, ?int $userId = null, bool $withItems = true): ?array
{
    $sql = 'SELECT o.*, u.name AS user_name, u.email AS user_email FROM orders o INNER JOIN users u ON u.id = o.user_id WHERE o.id = ?';
    $params = [$orderId];
    if ($userId !== null) {
        $sql .= ' AND o.user_id = ?';
        $params[] = $userId;
    }
    $sql .= ' LIMIT 1';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $order = $stmt->fetch();
    if (!$order) {
        return null;
    }

    $order['total_amount'] = (float) $order['total_amount'];
    if ($withItems) {
        $itemStmt = db()->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC');
        $itemStmt->execute([$orderId]);
        $order['items'] = $itemStmt->fetchAll();
    }

    return $order;
}

function get_user_orders(int $userId): array
{
    $stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function finalize_paid_order(int $localOrderId, array $providerOrder = []): void
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? FOR UPDATE');
        $stmt->execute([$localOrderId]);
        $order = $stmt->fetch();
        if (!$order) {
            throw new RuntimeException('Orden local no encontrada.');
        }

        if ((int) $order['stock_discounted'] === 0) {
            $itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
            $itemsStmt->execute([$localOrderId]);
            $items = $itemsStmt->fetchAll();

            foreach ($items as $item) {
                $productStmt = $pdo->prepare('SELECT stock FROM products WHERE id = ? FOR UPDATE');
                $productStmt->execute([(int) $item['product_id']]);
                $product = $productStmt->fetch();
                if (!$product) {
                    throw new RuntimeException('Producto de la orden no encontrado.');
                }
                if ((int) $product['stock'] < (int) $item['quantity']) {
                    throw new RuntimeException('No hay stock suficiente para confirmar la orden #' . $order['order_number']);
                }

                $updateProduct = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
                $updateProduct->execute([(int) $item['quantity'], (int) $item['product_id']]);
            }
        }

        $providerId = $providerOrder['id'] ?? $order['payment_provider_order_id'] ?? null;
        $checkoutId = $providerOrder['checkout']['id'] ?? $order['payment_provider_checkout_id'] ?? null;
        $checkoutUrl = $providerOrder['checkout']['url'] ?? $order['payment_checkout_url'] ?? null;
        $paymentStatus = $providerOrder['payment_status'] ?? 'paid';

        $updateOrder = $pdo->prepare('UPDATE orders SET status = ?, payment_status = ?, payment_provider_order_id = ?, payment_provider_checkout_id = ?, payment_checkout_url = ?, stock_discounted = 1, paid_at = COALESCE(paid_at, NOW()), updated_at = NOW() WHERE id = ?');
        $updateOrder->execute(['paid', $paymentStatus, $providerId, $checkoutId, $checkoutUrl, $localOrderId]);

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function mark_order_status(int $localOrderId, string $status, string $paymentStatus, array $providerOrder = []): void
{
    $stmt = db()->prepare('UPDATE orders SET status = ?, payment_status = ?, payment_provider_order_id = COALESCE(?, payment_provider_order_id), payment_provider_checkout_id = COALESCE(?, payment_provider_checkout_id), payment_checkout_url = COALESCE(?, payment_checkout_url), updated_at = NOW() WHERE id = ?');
    $stmt->execute([
        $status,
        $paymentStatus,
        $providerOrder['id'] ?? null,
        $providerOrder['checkout']['id'] ?? null,
        $providerOrder['checkout']['url'] ?? null,
        $localOrderId,
    ]);
}

function find_local_order_by_provider_order_id(string $providerOrderId): ?array
{
    $stmt = db()->prepare('SELECT * FROM orders WHERE payment_provider_order_id = ? LIMIT 1');
    $stmt->execute([$providerOrderId]);
    $order = $stmt->fetch();
    return $order ?: null;
}

function save_payment_event(?int $localOrderId, string $provider, string $eventType, ?string $eventId, array $payload): void
{
    $stmt = db()->prepare('INSERT INTO payment_events (provider, event_type, event_id, order_id, payload_json, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $provider,
        $eventType,
        $eventId,
        $localOrderId,
        json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);
}

function get_invoice_requests(int $userId): array
{
    $stmt = db()->prepare('SELECT ir.*, o.order_number, o.total_amount FROM invoice_requests ir INNER JOIN orders o ON o.id = ir.order_id WHERE ir.user_id = ? ORDER BY ir.id DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function ensure_invoice_request_schema(): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $pdo = db();
    $databaseName = (string) env('DB_NAME', 'rur_store');
    $table = 'invoice_requests';
    $required = [
        'payment_form' => "ALTER TABLE invoice_requests ADD COLUMN payment_form VARCHAR(10) NOT NULL DEFAULT '99' AFTER postal_code",
        'payment_method' => "ALTER TABLE invoice_requests ADD COLUMN payment_method VARCHAR(10) NOT NULL DEFAULT 'PUE' AFTER payment_form",
        'facturama_cfdi_id' => "ALTER TABLE invoice_requests ADD COLUMN facturama_cfdi_id VARCHAR(120) DEFAULT NULL AFTER status",
        'facturama_uuid' => "ALTER TABLE invoice_requests ADD COLUMN facturama_uuid VARCHAR(80) DEFAULT NULL AFTER facturama_cfdi_id",
        'facturama_status' => "ALTER TABLE invoice_requests ADD COLUMN facturama_status VARCHAR(40) DEFAULT NULL AFTER facturama_uuid",
        'facturama_issued_at' => "ALTER TABLE invoice_requests ADD COLUMN facturama_issued_at DATETIME DEFAULT NULL AFTER facturama_status",
        'facturama_response_json' => "ALTER TABLE invoice_requests ADD COLUMN facturama_response_json LONGTEXT DEFAULT NULL AFTER facturama_issued_at",
    ];

    $columnStmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    foreach ($required as $column => $sql) {
        $columnStmt->execute([$databaseName, $table, $column]);
        if ((int) $columnStmt->fetchColumn() === 0) {
            $pdo->exec($sql);
        }
    }
}

function normalize_invoice_request_data(array $data): array
{
    $payload = [
        'rfc' => strtoupper(trim((string) ($data['rfc'] ?? ''))),
        'razon_social' => strtoupper(trim((string) ($data['razon_social'] ?? ''))),
        'billing_email' => trim((string) ($data['billing_email'] ?? '')),
        'uso_cfdi' => strtoupper(trim((string) ($data['uso_cfdi'] ?? ''))),
        'regimen_fiscal' => trim((string) ($data['regimen_fiscal'] ?? '')),
        'postal_code' => trim((string) ($data['postal_code'] ?? '')),
        'payment_form' => trim((string) ($data['payment_form'] ?? env('FACTURAMA_PAYMENT_FORM', '99'))),
        'payment_method' => trim((string) ($data['payment_method'] ?? env('FACTURAMA_PAYMENT_METHOD', 'PUE'))),
    ];

    if ($payload['rfc'] === '' || !preg_match('/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $payload['rfc'])) {
        throw new InvalidArgumentException('RFC invalido. Verifica el formato.');
    }
    if ($payload['razon_social'] === '') {
        throw new InvalidArgumentException('La razon social es obligatoria.');
    }
    if (!filter_var($payload['billing_email'], FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Correo de facturacion invalido.');
    }
    if (!preg_match('/^[0-9]{5}$/', $payload['postal_code'])) {
        throw new InvalidArgumentException('Codigo postal invalido.');
    }
    if (!preg_match('/^[0-9]{3}$/', $payload['regimen_fiscal'])) {
        throw new InvalidArgumentException('Regimen fiscal invalido.');
    }
    if (!preg_match('/^[A-Z0-9]{3}$/', $payload['uso_cfdi'])) {
        throw new InvalidArgumentException('Uso CFDI invalido.');
    }
    if (!preg_match('/^[0-9]{2}$/', $payload['payment_form'])) {
        throw new InvalidArgumentException('Forma de pago SAT invalida.');
    }
    if (!in_array($payload['payment_method'], ['PUE', 'PPD'], true)) {
        throw new InvalidArgumentException('Metodo de pago invalido.');
    }

    return $payload;
}

function create_or_update_invoice_request(int $userId, int $orderId, array $data): int
{
    ensure_invoice_request_schema();
    $payload = normalize_invoice_request_data($data);

    $existingStmt = db()->prepare('SELECT id FROM invoice_requests WHERE user_id = ? AND order_id = ? LIMIT 1');
    $existingStmt->execute([$userId, $orderId]);
    $existing = $existingStmt->fetch();

    if ($existing) {
        $stmt = db()->prepare('UPDATE invoice_requests SET rfc = ?, razon_social = ?, billing_email = ?, uso_cfdi = ?, regimen_fiscal = ?, postal_code = ?, payment_form = ?, payment_method = ?, status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([
            $payload['rfc'],
            $payload['razon_social'],
            $payload['billing_email'],
            $payload['uso_cfdi'],
            $payload['regimen_fiscal'],
            $payload['postal_code'],
            $payload['payment_form'],
            $payload['payment_method'],
            'requested',
            (int) $existing['id'],
        ]);
        return (int) $existing['id'];
    }

    $stmt = db()->prepare('INSERT INTO invoice_requests (order_id, user_id, rfc, razon_social, billing_email, uso_cfdi, regimen_fiscal, postal_code, payment_form, payment_method, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([
        $orderId,
        $userId,
        $payload['rfc'],
        $payload['razon_social'],
        $payload['billing_email'],
        $payload['uso_cfdi'],
        $payload['regimen_fiscal'],
        $payload['postal_code'],
        $payload['payment_form'],
        $payload['payment_method'],
        'requested',
    ]);

    return (int) db()->lastInsertId();
}

function get_invoice_request_by_id(int $invoiceRequestId, int $userId): ?array
{
    ensure_invoice_request_schema();
    $stmt = db()->prepare('SELECT ir.*, o.order_number, o.total_amount FROM invoice_requests ir INNER JOIN orders o ON o.id = ir.order_id WHERE ir.id = ? AND ir.user_id = ? LIMIT 1');
    $stmt->execute([$invoiceRequestId, $userId]);
    $invoice = $stmt->fetch();
    return $invoice ?: null;
}

function get_invoice_request_by_order(int $orderId, int $userId): ?array
{
    ensure_invoice_request_schema();
    $stmt = db()->prepare('SELECT ir.*, o.order_number, o.total_amount FROM invoice_requests ir INNER JOIN orders o ON o.id = ir.order_id WHERE ir.order_id = ? AND ir.user_id = ? LIMIT 1');
    $stmt->execute([$orderId, $userId]);
    $invoice = $stmt->fetch();
    return $invoice ?: null;
}

function mark_invoice_request_processing(int $invoiceRequestId): void
{
    db()->prepare('UPDATE invoice_requests SET status = ?, updated_at = NOW() WHERE id = ?')->execute(['processing', $invoiceRequestId]);
}

function mark_invoice_request_completed(int $invoiceRequestId, array $cfdiResponse): void
{
    $uuid = (string) ($cfdiResponse['Complement']['TaxStamp']['Uuid'] ?? $cfdiResponse['Complement']['TaxStamp']['UUID'] ?? '');
    db()->prepare('UPDATE invoice_requests SET status = ?, facturama_status = ?, facturama_cfdi_id = ?, facturama_uuid = ?, facturama_issued_at = NOW(), facturama_response_json = ?, updated_at = NOW() WHERE id = ?')->execute([
        'completed',
        'issued',
        (string) ($cfdiResponse['Id'] ?? ''),
        $uuid !== '' ? $uuid : null,
        json_encode($cfdiResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        $invoiceRequestId,
    ]);
}

function mark_invoice_request_demo_completed(int $invoiceRequestId, array $cfdiResponse): void
{
    $uuid = (string) ($cfdiResponse['Complement']['TaxStamp']['Uuid'] ?? '');
    db()->prepare('UPDATE invoice_requests SET status = ?, facturama_status = ?, facturama_cfdi_id = ?, facturama_uuid = ?, facturama_issued_at = NOW(), facturama_response_json = ?, updated_at = NOW() WHERE id = ?')->execute([
        'completed',
        'demo',
        (string) ($cfdiResponse['Id'] ?? 'DEMO'),
        $uuid !== '' ? $uuid : null,
        json_encode($cfdiResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        $invoiceRequestId,
    ]);
}

function mark_invoice_request_rejected(int $invoiceRequestId, string $message): void
{
    db()->prepare('UPDATE invoice_requests SET status = ?, facturama_status = ?, facturama_response_json = ?, updated_at = NOW() WHERE id = ?')->execute([
        'rejected',
        'error',
        json_encode([
            'message' => $message,
            'at' => date('c'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        $invoiceRequestId,
    ]);
}

function issue_invoice_with_facturama(int $invoiceRequestId, int $userId): array
{
    $invoiceRequest = get_invoice_request_by_id($invoiceRequestId, $userId);
    if (!$invoiceRequest) {
        throw new RuntimeException('Solicitud de factura no encontrada.');
    }
    if (!empty($invoiceRequest['facturama_cfdi_id'])) {
        return $invoiceRequest;
    }

    $order = get_order_by_id((int) $invoiceRequest['order_id'], $userId, true);
    if (!$order) {
        throw new RuntimeException('Orden no encontrada para facturacion.');
    }
    if (($order['status'] ?? '') !== 'paid') {
        throw new RuntimeException('Solo se puede facturar una orden pagada.');
    }

    mark_invoice_request_processing($invoiceRequestId);
    try {
        if (facturama_demo_mode()) {
            $response = facturama_build_demo_cfdi($order, $invoiceRequest);
            mark_invoice_request_demo_completed($invoiceRequestId, $response);
        } else {
            $payload = facturama_build_cfdi_payload($order, $invoiceRequest);
            $response = facturama_create_cfdi($payload);
            mark_invoice_request_completed($invoiceRequestId, $response);
        }
    } catch (Throwable $e) {
        mark_invoice_request_rejected($invoiceRequestId, $e->getMessage());
        throw $e;
    }

    $updated = get_invoice_request_by_id($invoiceRequestId, $userId);
    if (!$updated) {
        throw new RuntimeException('No se pudo recargar la factura emitida.');
    }

    return $updated;
}
