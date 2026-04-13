<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_login(true);

if (!is_post()) {
    json_response(['ok' => false, 'message' => 'Método no permitido.'], 405);
}

$data = request_data();
$orderId = (int) post_or_get($data, 'order_id', 0);
$order = get_order_by_id($orderId, (int) current_user()['id'], false);
if (!$order) {
    json_response(['ok' => false, 'message' => 'Orden no encontrada.'], 404);
}
if (($order['status'] ?? '') !== 'paid') {
    json_response(['ok' => false, 'message' => 'Solo puedes solicitar factura para compras pagadas.'], 422);
}

try {
    $invoiceRequestId = create_or_update_invoice_request((int) current_user()['id'], $orderId, $data);
    $invoice = get_invoice_request_by_id($invoiceRequestId, (int) current_user()['id']);

    $message = 'Solicitud de factura registrada.';
    if (facturama_enabled()) {
        $invoice = issue_invoice_with_facturama($invoiceRequestId, (int) current_user()['id']);
        $message = 'Factura emitida correctamente en Facturama.';
    } else {
        $message .= ' Facturama no esta configurado todavia; la solicitud quedo guardada para pruebas.';
    }

    json_response([
        'ok' => true,
        'message' => $message,
        'invoice' => $invoice,
    ]);
} catch (Throwable $e) {
    json_response(['ok' => false, 'message' => $e->getMessage()], 422);
}
