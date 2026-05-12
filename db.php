<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


function loadDotEnv(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        return [];
    }

    $result = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (strlen($value) >= 2 && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        }

        $result[$key] = $value;
    }

    return $result;
}

function getDbConfig(): array
{
    $env = loadDotEnv(__DIR__ . '/.env');

    return [
        'DB_HOST'     => getenv('DB_HOST') ?: ($env['DB_HOST'] ?? 'localhost'),
        'DB_NAME'     => getenv('DB_NAME') ?: ($env['DB_NAME'] ?? 'webdev2'),
        'DB_USER'     => getenv('DB_USER') ?: ($env['DB_USER'] ?? 'webdev2'),
        'DB_PASSWORD' => getenv('DB_PASSWORD') ?: ($env['DB_PASSWORD'] ?? ''),
    ];
}

function validateDbConfig(array $config): void
{
    foreach ($config as $key => $value) {
        if ($value === '') {
            throw new RuntimeException("Datenbankkonfiguration fehlt: $key");
        }
    }
}

$config = getDbConfig();
validateDbConfig($config);

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['DB_HOST'], $config['DB_NAME']);
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD'], $options);
} catch (PDOException $e) {
    die('Verbindung zur Datenbank fehlgeschlagen: ' . $e->getMessage());
}
