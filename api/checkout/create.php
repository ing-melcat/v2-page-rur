<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_login(true);

if (!is_post()) {
    json_response(['ok' => false, 'message' => 'Método no permitido.'], 405);
}

json_response([
    'ok' => false,
    'message' => 'La pasarela de pagos está deshabilitada. No se puede crear checkout.',
], 503);
