<?php
session_start();
require_once 'db.php';
/** @var PDO $pdo */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

if($sicherheitsstufe <= 0) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['submit_post'])) {
   // print_r($_FILES);
   // print_r($_POST);
    $titel = $_POST['titel'];
    $inhalt = $_POST['inhalt'];
    $benutzer_id = $_SESSION['benutzer_id'];

    $fragmente=explode(".",$_FILES["beitrag_bild"]["name"]);
    $dateiname=time()."_".rand(1000000,9999999).".".$fragmente[count($fragmente)-1];
    move_uploaded_file($_FILES["beitrag_bild"]["tmp_name"],"bilder/".$dateiname);

    $stmt = $pdo->prepare("INSERT INTO beitraege(titel, inhalt, benutzer_id,bild) VALUES (?,?,?,?)");

    if ($stmt->execute([$titel, $inhalt, $benutzer_id, $dateiname])) {
        echo "Beitrag erstellt";
        header("Location: index.php");
        exit;
    }
}
?>

<a href="index.php">⬅ Zurück zur Übersicht</a>

<form method="post" action="beitrag_erstellen.php" enctype="multipart/form-data">
    <label for="titel">Titel:</label>
    <input type="text" name="titel" id="titel" placeholder="Titel">
    <label for ="inhalt">Inhalt:</label>
    <input type="text" name="inhalt" id="inhalt" placeholder="inhalt">
    <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
    <input type="file" name="beitrag_bild"  id="beitrag_bild">
    <input type="submit" name="submit_post" id="submit_post">
</form>