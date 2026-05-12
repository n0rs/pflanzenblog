<?php
require_once 'db.php';
/** @var PDO $pdo */

if (isset($_POST['submit_post'])) {
    $titel = $_POST['titel'];
    $inhalt = $_POST['inhalt'];
    $autor_id = $_POST['autor_id'];

    $stmt = $pdo->prepare("INSERT INTO beitraege(titel, inhalt, autor_id) VALUES (?,?,?)");

    if ($stmt->execute([$titel, $inhalt, $autor_id])) {
        echo "Beitrag erstellt";
        header("Location: index.php");
        exit;
    }

}
?>

<a href="index.php">⬅ Zurück zur Übersicht</a>

<form method="post" action="erstellen.php">
    <input type="text" name="titel" id="titel" placeholder="Titel">
    <label for="titel">Titel:</label>
    <input type="text" name="inhalt" id="inhalt" placeholder="inhalt">
    <label for ="inhalt">Inhalt:</label>
    <input type="number" name="autor_id" id="autor_id" placeholder="autor_id">
    <label for ="autor_id">Autorennummer:</label>
    <input type="submit" name="submit_post" id="submit_post">
</form>