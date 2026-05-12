<?php

$host = 'localhost';
$db   = 'webdev2';
$user = 'webdev2';
$pass = 'TwwqC6a?Ossv!x55';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage());
}
?>