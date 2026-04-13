<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_login(false);

$invoiceRequestId = (int) ($_GET['invoice_request_id'] ?? 0);
$format = strtolower(trim((string) ($_GET['format'] ?? 'pdf')));

if ($invoiceRequestId <= 0) {
    http_response_code(400);
    exit('Solicitud de factura invalida.');
}

try {
    $invoice = get_invoice_request_by_id($invoiceRequestId, (int) current_user()['id']);
    if (!$invoice) {
        throw new RuntimeException('Factura no encontrada.');
    }
    if (empty($invoice['facturama_cfdi_id'])) {
        throw new RuntimeException('Esta solicitud aun no tiene CFDI emitido.');
    }

    $order = get_order_by_id((int) $invoice['order_id'], (int) current_user()['id'], true);
    if (!$order) {
        throw new RuntimeException('No se encontro la orden asociada a la factura.');
    }

    if (($invoice['facturama_status'] ?? '') === 'demo') {
        if ($format === 'pdf') {
            $content = facturama_demo_pdf($invoice, $order);
            $extension = 'pdf';
            header('Content-Type: application/pdf');
        } else {
            $content = facturama_demo_xml($invoice, $order);
            $extension = 'xml';
            header('Content-Type: application/xml; charset=utf-8');
        }
    } else {
        $file = facturama_download_cfdi((string) $invoice['facturama_cfdi_id'], $format, 'issued');
        $content = base64_decode((string) ($file['Content'] ?? ''), true);
        if ($content === false) {
            throw new RuntimeException('No se pudo decodificar el archivo devuelto por Facturama.');
        }

        $contentType = (string) ($file['ContentType'] ?? $format);
        $extension = strtolower($contentType);
        header('Content-Type: application/' . ($extension === 'pdf' ? 'pdf' : ($extension === 'xml' ? 'xml' : 'octet-stream')));
    }

    $fileName = 'factura-' . preg_replace('/[^A-Za-z0-9_-]/', '-', (string) ($invoice['order_number'] ?? 'rur')) . '.' . $extension;

    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . strlen($content));
    echo $content;
    exit;
} catch (Throwable $e) {
    http_response_code(422);
    header('Content-Type: text/plain; charset=utf-8');
    echo $e->getMessage();
    exit;
}
