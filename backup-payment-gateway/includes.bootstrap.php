<?php
declare(strict_types=1);

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

if (session_status() === PHP_SESSION_NONE) {
    $configuredSessionPath = (string) ini_get('session.save_path');
    $configuredSessionPath = preg_replace('/^[0-9]+;/', '', $configuredSessionPath) ?: '';
    $configuredSessionPath = trim($configuredSessionPath);

    if ($configuredSessionPath === '' || !is_dir($configuredSessionPath) || !is_writable($configuredSessionPath)) {
        $fallbackSessionPath = PROJECT_ROOT . '/storage/sessions';
        if (!is_dir($fallbackSessionPath)) {
            mkdir($fallbackSessionPath, 0777, true);
        }
        if (is_dir($fallbackSessionPath) && is_writable($fallbackSessionPath)) {
            session_save_path($fallbackSessionPath);
        }
    }

    session_start();
}

require_once PROJECT_ROOT . '/includes/env.php';
load_env(PROJECT_ROOT . '/.env');

date_default_timezone_set((string) env('APP_TIMEZONE', 'America/Mexico_City'));

require_once PROJECT_ROOT . '/includes/helpers.php';
require_once PROJECT_ROOT . '/includes/db.php';
require_once PROJECT_ROOT . '/includes/auth.php';
require_once PROJECT_ROOT . '/includes/conekta.php';
require_once PROJECT_ROOT . '/includes/facturama.php';
require_once PROJECT_ROOT . '/includes/purchases.php';
