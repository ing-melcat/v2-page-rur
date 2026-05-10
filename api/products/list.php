<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
json_response(['ok' => true, 'products' => get_products(true)]);
