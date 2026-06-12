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
$beitrag_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

pruefeEingeloggt($sicherheitsstufe);

//beitrag aus der db ziehen anhand von id
$anweisung = $datenbankverbindung->prepare("SELECT * FROM beitraege WHERE id=?");
$anweisung->bind_param('i', $beitrag_id);
$anweisung->execute();
$beitrag = $anweisung->get_result()->fetch_assoc();


if (!istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)) {
    header("Location: index.php");
    exit;
}

//prüfen, ob ein bild am beitrag existiert und ob die datei im bilder-ordner abgelegt ist
if(!empty($beitrag['bild']) && file_exists("bilder/".$beitrag['bild'])) {
    //wenn ja, dann das bild aus dem ordner löschen
    unlink("bilder/" . $beitrag['bild']);
}
//beitrag aus db löschen
$loeschAnweisung = $datenbankverbindung->prepare("DELETE FROM beitraege WHERE id=?");
$loeschAnweisung->bind_param('i', $beitrag_id);
$loeschAnweisung->execute();

//zurück auf die startseite
sendeToast("Beitrag gelöscht");
header("Location: index.php");
exit;

?>
