<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = $_SESSION['sicherheitsstufe'] ?? 0;
$aktueller_benutzer_id = $_SESSION['benutzer_id'] ?? null;

pruefeEingeloggt($sicherheitsstufe);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$kommentar_id = isset($_POST['kommentar_id']) ? (int)$_POST['kommentar_id'] : 0;
$inhalt = trim($_POST['inhalt'] ?? '');

if ($kommentar_id <= 0 || $inhalt === '') {
    sendeToast("Kommentar konnte nicht aktualisiert werden.");
    header("Location: index.php");
    exit;
}

$anweisung = $datenbankverbindung->prepare(
    "SELECT * FROM kommentare WHERE id = ?"
);
$anweisung->bind_param('i', $kommentar_id);
$anweisung->execute();
$kommentar = $anweisung->get_result()->fetch_assoc();

if (!$kommentar) {
    sendeToast("Kommentar nicht gefunden.");
    header("Location: index.php");
    exit;
}

$beitrag_id = (int)$kommentar['beitrag_id'];

if (!istKommentator($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)) {
    sendeToast("Nicht berechtigt Kommentar zu bearbeiten.");
    header("Location: beitrag_detail.php?id=$beitrag_id#kommentar-$kommentar_id");
    exit;
}

$update = $datenbankverbindung->prepare(
    "UPDATE kommentare
     SET inhalt = ?
     WHERE id = ?"
);
$update->bind_param('si', $inhalt, $kommentar_id);
$update->execute();

sendeToast("Kommentar aktualisiert.");
header("Location: beitrag_detail.php?id=$beitrag_id#kommentar-$kommentar_id");
exit;