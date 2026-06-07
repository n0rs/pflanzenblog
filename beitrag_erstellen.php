<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

pruefeEingeloggt($sicherheitsstufe);


$meldung = '';
$titel = '';
$inhalt = '';

if (isset($_POST['submit_post'])) {
    $titel = trim($_POST['titel'] ?? '');
    $inhalt = trim($_POST['inhalt'] ?? '');
    $benutzer_id = $_SESSION['benutzer_id'];

    if ($titel === '' || $inhalt === '') {
        $meldung = 'Bitte geben Sie einen Titel und Inhalt ein.';
    } else {
        $bild_dateiname = uploadBild($_FILES['beitrag_bild']);

        if (isset($_FILES['beitrag_bild']) && $_FILES['beitrag_bild']['error'] !== UPLOAD_ERR_NO_FILE && $bild_dateiname === null) {
            $meldung = 'Ungültiges Bildformat. Bitte JPG, PNG oder GIF verwenden.';
        } else {
            $anweisung = $datenbankverbindung->prepare("INSERT INTO beitraege(titel, inhalt, benutzer_id,bild) VALUES (?,?,?,?)");
            $anweisung->bind_param('ssis', $titel, $inhalt, $benutzer_id, $bild_dateiname);

            if ($anweisung->execute()) {
                header("Location: index.php");
                exit;
            }
        }
    }
}
?>

<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Erstelle einen neuen Pflanzenblog-Beitrag mit Titel, Inhalt und optionalem Bild.">
    <title>Neuen Beitrag erstellen - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="beitrag-erstellen-container">

    <?php include 'header.php'; ?>

    <main>
        <h2>Beitrag erstellen</h2>
        <?php if ($meldung !== ''): ?>
            <p class="message error"><?php echo htmlspecialchars($meldung, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="post" action="beitrag_erstellen.php" enctype="multipart/form-data">
            <div class="zeile">
                <div>
                    <label for="titel">Titel:</label>
                    <input type="text" name="titel" id="titel" placeholder="Titel" value="<?php echo htmlspecialchars($titel, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label for ="inhalt">Inhalt:</label>
                    <textarea name="inhalt" id="inhalt" placeholder="Inhalt"><?php echo htmlspecialchars($inhalt, ENT_QUOTES, 'UTF-8'); ?></textarea>
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
    <?php include 'footer.php'; ?>
</div>
</body>
