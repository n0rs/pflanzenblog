<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

//sicherheitsstufe des eingeloggten users speichern
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
//benutzer id des aktuellen users speichern
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
//beitrag_id aus der URL übernehmen
$kommentar_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

pruefeEingeloggt($sicherheitsstufe);

//kommentar aus der db ziehen anhand von id
$anweisung = $datenbankverbindung->prepare("SELECT * FROM kommentare WHERE id=?");
$anweisung->bind_param('i', $kommentar_id);
$anweisung->execute();
$kommentar = $anweisung->get_result()->fetch_assoc();

$beitrag_kommentar=$kommentar['beitrag_id'];

if (!istKommentator($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)) {
    header("Location: index.php");
    exit;
}

//beitrag aus db löschen
$loeschAnweisung = $datenbankverbindung->prepare("DELETE FROM kommentare WHERE id=?");
$loeschAnweisung->bind_param('i', $kommentar_id);
$loeschAnweisung->execute();

//zurück auf die startseite
header("Location: index.php#post-$beitrag_kommentar");
exit;

?>
