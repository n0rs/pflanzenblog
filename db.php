<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function credentialsLaden(string $pfad): array
{
    if (!is_file($pfad) || !is_readable($pfad)) {
        return [];
    }

    $ergebnis = [];
    $zeilen = file($pfad, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($zeilen as $zeile) {
        $zeile = trim($zeile);
        if ($zeile === '' || $zeile[0] === '#') {
            continue;
        }
        if (strpos($zeile, '=') === false) {
            continue;
        }

        [$schluessel, $wert] = explode('=', $zeile, 2);
        $schluessel = trim($schluessel);
        $wert = trim($wert);

        if (strlen($wert) >= 2 && (($wert[0] === '"' && substr($wert, -1) === '"') || ($wert[0] === "'" && substr($wert, -1) === "'"))) {
            $wert = substr($wert, 1, -1);
        }

        $ergebnis[$schluessel] = $wert;
    }

    return $ergebnis;
}

function erhalteDbKonfig(): array
{
    $umgebung = credentialsLaden(__DIR__ . '/.env');

    return [
        'DB_HOST'     => getenv('DB_HOST') ?: ($umgebung['DB_HOST'] ?? 'localhost'),
        'DB_NAME'     => getenv('DB_NAME') ?: ($umgebung['DB_NAME'] ?? ''),
        'DB_USER'     => getenv('DB_USER') ?: ($umgebung['DB_USER'] ?? ''),
        'DB_PASSWORD' => getenv('DB_PASSWORD') ?: ($umgebung['DB_PASSWORD'] ?? ''),
    ];
}

function pruefeDbKonfig(array $konfiguration): void
{
    $erforderlicheSchlussel = ['DB_HOST', 'DB_NAME', 'DB_USER'];

    foreach ($erforderlicheSchlussel as $schluessel) {
        if (!isset($konfiguration[$schluessel]) || $konfiguration[$schluessel] === '') {
            throw new RuntimeException("Datenbankkonfiguration fehlt: $schluessel");
        }
    }
}

$konfiguration = erhalteDbKonfig();
pruefeDbKonfig($konfiguration);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $datenbankverbindung = new mysqli(
        $konfiguration['DB_HOST'],
        $konfiguration['DB_USER'],
        $konfiguration['DB_PASSWORD'],
        $konfiguration['DB_NAME']
    );
    $datenbankverbindung->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Verbindung zur Datenbank fehlgeschlagen: ' . $e->getMessage());
}

// $datenbank ist die mysqli-Verbindung, verwende diese in anderen Dateien