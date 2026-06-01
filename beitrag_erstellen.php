<?php
session_start();
require_once 'db.php';
/** @var mysqli $mysqli */

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
    <header>
        <h1>🌿 Mein Pflanzenblog</h1>
        <nav>
            <a href="index.php">⬅ Zurück zur Übersicht</a>
            <?php if ($sicherheitsstufe >= 1): ?>
                <a href="beitrag_erstellen.php">Neuer Beitrag</a>
            <?php endif; ?>
            <?php if ($sicherheitsstufe == 0): ?>
                <a href="registrieren.php">Registrieren</a>
            <?php endif; ?>
            <?php if ($sicherheitsstufe >= 1): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
            <a href="ueber_uns.php">Über uns</a>
        </nav>
    </header>
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
                    <input type="text" name="inhalt" id="inhalt" placeholder="inhalt">
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
