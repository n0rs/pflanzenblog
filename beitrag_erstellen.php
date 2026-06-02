<?php
session_start();
require_once 'db.php';
/** @var mysqli $mysqli */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

eingeloggtCheck($sicherheitsstufe);


if (isset($_POST['submit_post'])) {
   // print_r($_FILES);
   // print_r($_POST);
    $titel = $_POST['titel'];
    $inhalt = $_POST['inhalt'];
    $benutzer_id = $_SESSION['benutzer_id'];

    $bild_dateiname = uploadBild($_FILES['beitrag_bild']);

    $anweisung = $mysqli->prepare("INSERT INTO beitraege(titel, inhalt, benutzer_id,bild) VALUES (?,?,?,?)");
    $anweisung->bind_param('ssis', $titel, $inhalt, $benutzer_id, $dateiname);

    if ($anweisung->execute()) {
        echo "Beitrag erstellt";
        header("Location: index.php");
        exit;
    }
}
?>

<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="beitrag-erstellen-container">

    <?php include 'header.php'; ?>

    <main>
        <h2>Beitrag erstellen</h2>
        <form method="post" action="beitrag_erstellen.php" enctype="multipart/form-data">
            <div class="zeile">
                <div>
                    <label for="titel">Titel:</label>
                    <input type="text" name="titel" id="titel" placeholder="Titel">
                </div>
                <div>
                    <label for ="inhalt">Inhalt:</label>
                    <textarea name="inhalt" id="inhalt" placeholder="Inhalt"></textarea>
                </div>
                <div class="bild-hochladen">
                    <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
                    <input type="file" name="beitrag_bild"  id="beitrag_bild">
                </div>
                <div>
                    <input type="submit" name="submit_post" id="submit_post">
                </div>
            <div>
        </form>
    </main>
    <footer>
        <p><a href="impressum.php">Impressum</a></p>
        <p>© 2026 Pflanzenblog </p>
    </footer>
</div>
</body>
