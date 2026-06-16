<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

// Sicherheitsstufe des eingeloggten Users speichern
$sicherheitsstufe = $_SESSION['sicherheitsstufe'] ?? 0;
// Benutzer-ID des aktuellen Users aus der Session holen
$aktueller_benutzer_id = $_SESSION['benutzer_id'] ?? null;

$benutzer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

pruefeEingeloggt($sicherheitsstufe);

if ($aktueller_benutzer_id == $benutzer_id) {

    // Benutzer , mit cascade werden alle beiträge und kommentare automatisch mitgelöscht
    $loeschAnweisung = $datenbankverbindung->prepare("DELETE FROM benutzer WHERE id = ?");
    $loeschAnweisung->bind_param('i', $benutzer_id);
    $loeschAnweisung->execute();

     $_SESSION = array();
        session_destroy();

        session_start();
        sendeToast("Konto gelöscht.");
        header("location: index.php");
    } else {
        sendeToast("Keine Berechtigung.");
        header('Location: index.php');
        exit;
    }
?>