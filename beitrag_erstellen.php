<?php
require_once 'db.php';
/** @var PDO $pdo */

if (isset($_POST['submit_post'])) {
    $titel = $_POST['titel'];
    $inhalt = $_POST['inhalt'];
    $benutzer_id = $_POST['benutzer_id'];

    $stmt = $pdo->prepare("INSERT INTO beitraege(titel, inhalt, benutzer_id) VALUES (?,?,?)");

    if ($stmt->execute([$titel, $inhalt, $benutzer_id])) {
        echo "Beitrag erstellt";
        header("Location: index.php");
        exit;
    }
}
?>

<a href="index.php">⬅ Zurück zur Übersicht</a>

<form method="post" action="erstellen.php">
    <label for="titel">Titel:</label>
    <input type="text" name="titel" id="titel" placeholder="Titel">
    <label for ="inhalt">Inhalt:</label>
    <input type="text" name="inhalt" id="inhalt" placeholder="inhalt">
    <label for ="benutzer_id">Autorennummer:</label>
    <input type="number" name="benutzer_id" id="benutzer_id" placeholder="benutzer_id">
    <input type="submit" name="submit_post" id="submit_post">
</form>