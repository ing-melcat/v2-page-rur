<?php
declare(strict_types=1);

function facturama_enabled(): bool
{
    return trim((string) env('FACTURAMA_USERNAME', '')) !== ''
        && trim((string) env('FACTURAMA_PASSWORD', '')) !== '';
}

function facturama_demo_mode(): bool
{
    return filter_var((string) env('FACTURAMA_DEMO_MODE', 'false'), FILTER_VALIDATE_BOOLEAN);
}

function facturama_api_base(): string
{
    return rtrim((string) env('FACTURAMA_API_BASE', 'https://apisandbox.facturama.mx'), '/');
}

function facturama_headers(): array
{
    $username = (string) env('FACTURAMA_USERNAME', '');
    $password = (string) env('FACTURAMA_PASSWORD', '');
    $token = base64_encode($username . ':' . $password);

    return [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . $token,
    ];
}

function facturama_request(string $method, string $endpoint, ?array $payload = null): array
{
    if (!facturama_enabled()) {
        throw new RuntimeException('Facturama no esta configurado. Agrega FACTURAMA_USERNAME y FACTURAMA_PASSWORD en tu .env');
    }

    $url = facturama_api_base() . '/' . ltrim($endpoint, '/');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => facturama_headers(),
        CURLOPT_TIMEOUT => 45,
    ]);

    if ($payload !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    $body = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        throw new RuntimeException('No se pudo conectar con Facturama: ' . $curlError);
    }

    $decoded = json_decode($body, true);
    if ($httpCode < 200 || $httpCode >= 300) {
        $message = 'Facturama devolvio un error.';
        if (is_array($decoded)) {
            if (!empty($decoded['Message'])) {
                $message .= ' ' . $decoded['Message'];
            } elseif (!empty($decoded['message'])) {
                $message .= ' ' . $decoded['message'];
            }

            $modelStateMessage = facturama_extract_model_state_message($decoded['ModelState'] ?? null);
            if ($modelStateMessage !== null && $modelStateMessage !== '') {
                $message .= ' ' . $modelStateMessage;
            }
        }

        if (is_string($body) && trim($body) !== '' && (!is_array($decoded) || str_contains($message, 'La solicitud no es valida'))) {
            $message .= ' Respuesta cruda: ' . trim($body);
        }
        $message .= ' (HTTP ' . $httpCode . ')';
        throw new RuntimeException($message);
    }

    if (!is_array($decoded)) {
        throw new RuntimeException('La respuesta de Facturama no pudo interpretarse.');
    }

    return $decoded;
}

function facturama_extract_model_state_message(mixed $modelState): ?string
{
    if (!is_array($modelState)) {
        return null;
    }

    $messages = [];
    array_walk_recursive($modelState, static function ($value) use (&$messages): void {
        if (is_scalar($value) && trim((string) $value) !== '') {
            $messages[] = trim((string) $value);
        }
    });

    if (empty($messages)) {
        return null;
    }

    return implode(' | ', array_unique($messages));
}

function facturama_create_cfdi(array $payload): array
{
    return facturama_request('POST', '/3/cfdis', $payload);
}

function facturama_get_cfdi(string $cfdiId, string $type = 'issued'): array
{
    return facturama_request('GET', '/cfdi/' . rawurlencode($cfdiId) . '?type=' . rawurlencode($type));
}

function facturama_download_cfdi(string $cfdiId, string $format, string $type = 'issued'): array
{
    $format = strtolower(trim($format));
    if (!in_array($format, ['pdf', 'xml', 'html'], true)) {
        throw new InvalidArgumentException('Formato de descarga no soportado.');
    }

    return facturama_request('GET', '/cfdi/' . rawurlencode($format) . '/' . rawurlencode($type) . '/' . rawurlencode($cfdiId));
}

function facturama_receiver_rfc_is_generic(string $rfc): bool
{
    $rfc = strtoupper(trim($rfc));
    return in_array($rfc, ['XAXX010101000', 'XEXX010101000'], true);
}

function facturama_build_cfdi_payload(array $order, array $invoiceRequest): array
{
    $paymentForm = trim((string) ($invoiceRequest['payment_form'] ?? env('FACTURAMA_PAYMENT_FORM', '99')));
    $paymentMethod = trim((string) ($invoiceRequest['payment_method'] ?? env('FACTURAMA_PAYMENT_METHOD', 'PUE')));
    $expeditionPlace = trim((string) env('FACTURAMA_EXPEDITION_PLACE', ''));
    $defaultProductCode = trim((string) env('FACTURAMA_DEFAULT_PRODUCT_CODE', '01010101'));
    $defaultUnitCode = trim((string) env('FACTURAMA_DEFAULT_UNIT_CODE', 'H87'));
    $defaultUnit = trim((string) env('FACTURAMA_DEFAULT_UNIT', 'Pieza'));
    $taxRate = (float) env('FACTURAMA_TAX_RATE', '0.16');

    if ($expeditionPlace === '') {
        throw new RuntimeException('Falta FACTURAMA_EXPEDITION_PLACE en tu .env');
    }

    $receiverRfc = strtoupper(trim((string) ($invoiceRequest['rfc'] ?? '')));
    $receiverName = strtoupper(trim((string) ($invoiceRequest['razon_social'] ?? '')));
    $receiverFiscalRegime = trim((string) ($invoiceRequest['regimen_fiscal'] ?? ''));
    $receiverZip = trim((string) ($invoiceRequest['postal_code'] ?? ''));
    $receiverCfdiUse = trim((string) ($invoiceRequest['uso_cfdi'] ?? ''));

    $items = [];
    foreach (($order['items'] ?? []) as $item) {
        $quantity = (float) ($item['quantity'] ?? 0);
        $unitPrice = round((float) ($item['unit_price'] ?? 0), 2);
        $subtotal = round($quantity * $unitPrice, 2);
        $taxTotal = round($subtotal * $taxRate, 2);
        $total = round($subtotal + $taxTotal, 2);

        $items[] = [
            'Quantity' => $quantity,
            'ProductCode' => $defaultProductCode,
            'UnitCode' => $defaultUnitCode,
            'Unit' => $defaultUnit,
            'Description' => (string) ($item['product_name'] ?? 'Producto RUR'),
            'IdentificationNumber' => 'ORDER-' . (string) ($order['order_number'] ?? $order['id'] ?? 'RUR'),
            'UnitPrice' => $unitPrice,
            'Subtotal' => $subtotal,
            'TaxObject' => '02',
            'Taxes' => [
                [
                    'Name' => 'IVA',
                    'Rate' => $taxRate,
                    'Total' => $taxTotal,
                    'Base' => $subtotal,
                    'IsRetention' => false,
                    'IsFederalTax' => true,
                ],
            ],
            'Total' => $total,
        ];
    }

    $payload = [
        'CfdiType' => 'I',
        'NameId' => '1',
        'ExpeditionPlace' => $expeditionPlace,
        'PaymentForm' => $paymentForm,
        'PaymentMethod' => $paymentMethod,
        'Currency' => 'MXN',
        'Folio' => (string) ($order['order_number'] ?? ''),
        'Exportation' => '01',
        'Receiver' => [
            'Rfc' => $receiverRfc,
            'Name' => $receiverName,
            'CfdiUse' => $receiverCfdiUse,
            'FiscalRegime' => $receiverFiscalRegime,
            'TaxZipCode' => $receiverZip,
        ],
        'Items' => $items,
    ];

    if (facturama_receiver_rfc_is_generic($receiverRfc)) {
        $payload['GlobalInformation'] = [
            'Periodicity' => '01',
            'Months' => date('m'),
            'Year' => date('Y'),
        ];
    }

    return $payload;
}

function facturama_build_demo_cfdi(array $order, array $invoiceRequest): array
{
    $uuid = strtoupper(bin2hex(random_bytes(16)));
    $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);

    return [
        'Id' => 'DEMO-' . (int) ($invoiceRequest['id'] ?? 0),
        'IsDemo' => true,
        'Folio' => (string) ($order['order_number'] ?? ''),
        'Date' => date('c'),
        'Receiver' => [
            'Rfc' => (string) ($invoiceRequest['rfc'] ?? ''),
            'Name' => (string) ($invoiceRequest['razon_social'] ?? ''),
            'CfdiUse' => (string) ($invoiceRequest['uso_cfdi'] ?? ''),
            'FiscalRegime' => (string) ($invoiceRequest['regimen_fiscal'] ?? ''),
            'TaxZipCode' => (string) ($invoiceRequest['postal_code'] ?? ''),
        ],
        'Complement' => [
            'TaxStamp' => [
                'Uuid' => $uuid,
            ],
        ],
    ];
}

function facturama_demo_xml(array $invoice, array $order): string
{
    $itemsXml = '';
    foreach (($order['items'] ?? []) as $item) {
        $itemsXml .= '  <Concepto descripcion="' . e((string) ($item['product_name'] ?? 'Producto')) . '" cantidad="' . (int) ($item['quantity'] ?? 0) . '" valorUnitario="' . number_format((float) ($item['unit_price'] ?? 0), 2, '.', '') . '" importe="' . number_format((float) ($item['line_total'] ?? 0), 2, '.', '') . "\" />\n";
    }

    return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
        . "<FacturaDemo>\n"
        . '  <Folio>' . e((string) ($order['order_number'] ?? '')) . "</Folio>\n"
        . '  <UUID>' . e((string) ($invoice['facturama_uuid'] ?? '')) . "</UUID>\n"
        . '  <Emisor>DEMO_FACTURAMA</Emisor>' . "\n"
        . '  <Receptor rfc="' . e((string) ($invoice['rfc'] ?? '')) . '" nombre="' . e((string) ($invoice['razon_social'] ?? '')) . '" usoCfdi="' . e((string) ($invoice['uso_cfdi'] ?? '')) . "\" />\n"
        . '  <Total>' . number_format((float) ($order['total_amount'] ?? 0), 2, '.', '') . "</Total>\n"
        . $itemsXml
        . "  <Nota>Documento DEMO solo para entrega academica. No es un CFDI timbrado.</Nota>\n"
        . "</FacturaDemo>\n";
}

function facturama_demo_pdf(array $invoice, array $order): string
{
    $lines = [
        'FACTURA DEMO',
        'Documento no fiscal. Solo para entrega.',
        'Orden: ' . (string) ($order['order_number'] ?? ''),
        'UUID demo: ' . (string) ($invoice['facturama_uuid'] ?? ''),
        'Receptor: ' . (string) ($invoice['razon_social'] ?? ''),
        'RFC: ' . (string) ($invoice['rfc'] ?? ''),
        'Total: $' . number_format((float) ($order['total_amount'] ?? 0), 2),
    ];

    $content = "BT\n/F1 18 Tf\n50 780 Td (" . facturama_pdf_escape(array_shift($lines)) . ") Tj\n";
    $content .= "/F1 11 Tf\n";
    $y = 758;
    foreach ($lines as $line) {
        $content .= "1 0 0 1 50 {$y} Tm (" . facturama_pdf_escape($line) . ") Tj\n";
        $y -= 18;
    }
    $content .= "ET";

    $pdfObjects = [];
    $pdfObjects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
    $pdfObjects[] = '2 0 obj << /Type /Pages /Count 1 /Kids [3 0 R] >> endobj';
    $pdfObjects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj';
    $pdfObjects[] = '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';
    $pdfObjects[] = '5 0 obj << /Length ' . strlen($content) . " >> stream\n" . $content . "\nendstream endobj";

    $pdf = "%PDF-1.4\n";
    $offsets = [];
    foreach ($pdfObjects as $object) {
        $offsets[] = strlen($pdf);
        $pdf .= $object . "\n";
    }

    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . (count($pdfObjects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    foreach ($offsets as $offset) {
        $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
    }
    $pdf .= "trailer << /Size " . (count($pdfObjects) + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

    return $pdf;
}

function facturama_pdf_escape(string $value): string
{
    $value = str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], $value);
    return preg_replace('/[^\x20-\x7E]/', '?', $value) ?? '';
}
