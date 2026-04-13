<?php
declare(strict_types=1);

function db_connection_config(): array
{
    $databaseUrl = env_first(['DB_URL', 'DATABASE_URL', 'MYSQL_URL']);
    if ($databaseUrl !== null) {
        $parts = parse_url($databaseUrl);
        if (is_array($parts) && isset($parts['host'])) {
            return [
                'host' => (string) $parts['host'],
                'port' => isset($parts['port']) ? (string) $parts['port'] : '3306',
                'name' => isset($parts['path']) ? ltrim((string) $parts['path'], '/') : 'railway_v2',
                'user' => isset($parts['user']) ? urldecode((string) $parts['user']) : 'root',
                'pass' => isset($parts['pass']) ? urldecode((string) $parts['pass']) : '',
            ];
        }
    }

    return [
        'host' => (string) env_first(['DB_HOST', 'MYSQLHOST', 'MYSQLHOSTPRIVATE'], '127.0.0.1'),
        'port' => (string) env_first(['DB_PORT', 'MYSQLPORT'], '3306'),
        'name' => (string) env_first(['DB_NAME', 'MYSQLDATABASE'], 'railway_v2'),
        'user' => (string) env_first(['DB_USER', 'MYSQLUSER'], 'root'),
        'pass' => (string) env_first(['DB_PASS', 'MYSQLPASSWORD'], ''),
    ];
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = db_connection_config();
    $host = $config['host'];
    $port = $config['port'];
    $name = $config['name'];
    $user = $config['user'];
    $pass = $config['pass'];

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    return $pdo;
}
