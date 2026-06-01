<?php
session_start();
require_once 'db.php';
/** @var mysqli $mysqli */

//sicherheitsstufe des eingeloggten users speichern
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
//benutzer id des aktuellen users speichern
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
//beitrag_id aus der URL übernehmen
$beitrag_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

//prüfen, ob user berechtigt ist, den beitrag zu löschen (man könnte ja theoretisch über url aufrufen)
if ($beitrag_id <= 0 || $sicherheitsstufe <= 0) {
    header("Location: index.php");
    exit;
}
//beitrag aus der db ziehen anhand von id
$anweisung = $mysqli->prepare("SELECT * FROM beitraege WHERE id=?");
$anweisung->bind_param('i', $beitrag_id);
$anweisung->execute();
$beitrag = $anweisung->get_result()->fetch_assoc();

//prüfen, ob der beitrag existiert und ob der eingeloggte benutzer wirklich derselbe ist wie der autor
if (!$beitrag || ($sicherheitsstufe != 2 && $beitrag['benutzer_id'] != $aktueller_benutzer_id)) {
    header("Location: index.php");
    exit;
}

echo("coming soon");
exit;

?>