<?php
session_start();
require_once dirname(__DIR__) . '/funktionen/datenbank.php';
require_once dirname(__DIR__) . '/funktionen/laden.php';
/** @var mysqli $datenbankverbindung */

// Sicherheitsstufe des eingeloggten Users speichern
$sicherheitsstufe = $_SESSION['sicherheitsstufe'] ?? 0;
// Benutzer-ID des aktuellen Users speichern
$aktueller_benutzer_id = $_SESSION['benutzer_id'] ?? null;
// Kommentar-ID aus der URL übernehmen
$kommentar_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

pruefeEingeloggt($sicherheitsstufe);

// Kommentar aus der DB ziehen anhand der ID
$anweisung = $datenbankverbindung->prepare("SELECT id, beitrag_id, benutzer_id FROM kommentare WHERE id=?");
$anweisung->bind_param('i', $kommentar_id);
$anweisung->execute();
$kommentar = $anweisung->get_result()->fetch_assoc();

if (!$kommentar) {
    sendeToast("Kommentar nicht gefunden");
    header("Location: ../index.php");
    exit;
}

$beitrag_kommentar = (int)$kommentar['beitrag_id'];

if (!istEigentuemer($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)) {
    header("Location: ../index.php#post-$beitrag_kommentar");
    exit;
}

// Der Elternkommentar wird geloescht; Antworten entfernt die Datenbank per ON DELETE CASCADE automatisch.
$loeschAnweisung = $datenbankverbindung->prepare("DELETE FROM kommentare WHERE id=?");
$loeschAnweisung->bind_param('i', $kommentar_id);
$loeschAnweisung->execute();

// Zurueck zur Beitragsseite
sendeToast("Kommentar geloescht");
header("Location: ../beitraege/beitrag_detail.php?id=$beitrag_kommentar#post-$beitrag_kommentar");
exit;
