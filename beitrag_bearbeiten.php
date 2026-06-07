<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

//variablen vorbereiten
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
$beitrag_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$meldung = '';
$neuer_titel = '';
$neuer_inhalt = '';

pruefeEingeloggt($sicherheitsstufe);

//daten laden
$beitrag = holeBeitrag($datenbankverbindung, $beitrag_id);

if (!istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)) {
    header("Location: index.php");
    exit;
}
//beitrag aus der db ziehen anhand von id (Sicherheits-Backup)
$anweisung = $datenbankverbindung->prepare("SELECT * FROM beitraege WHERE id=?");
$anweisung->bind_param('i', $beitrag_id);
$anweisung->execute();
$beitrag = $anweisung->get_result()->fetch_assoc();

//formular
if (isset($_POST['edit_speichern'])) {
    $neuer_titel = trim($_POST['titel'] ?? '');
    $neuer_inhalt = trim($_POST['inhalt'] ?? '');
    $bild_dateiname = $beitrag['bild'];

    if ($neuer_titel === '' || $neuer_inhalt === '') {
        $meldung = 'Bitte geben Sie einen Titel und Inhalt ein.';
    } else {
        $hochgeladenes_bild = uploadBild($_FILES['neues_bild']);

        if (isset($_FILES['neues_bild']) && $_FILES['neues_bild']['error'] !== UPLOAD_ERR_NO_FILE && $hochgeladenes_bild === null) {
            $meldung = 'Ungültiges Bildformat. Bitte JPG, PNG oder GIF verwenden.';
        } else {
            if ($hochgeladenes_bild !== null) {
                if (!empty($beitrag['bild']) && file_exists(__DIR__ . "/bilder/" . $beitrag['bild'])) {
                    unlink(__DIR__ . "/bilder/" . $beitrag['bild']);
                }
                $bild_dateiname = $hochgeladenes_bild;
            }

            $updateAnweisung = $datenbankverbindung->prepare("UPDATE beitraege SET titel = ?, inhalt = ?, bild = ? WHERE id = ?");
            $updateAnweisung->bind_param('sssi', $neuer_titel, $neuer_inhalt, $bild_dateiname, $beitrag_id);
            if ($updateAnweisung->execute()) {
                header("Location: index.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bearbeite deinen Pflanzenblog-Beitrag: aktualisiere Titel, Text oder Bild.">
    <title>Beitrag bearbeiten - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="beitrag-erstellen-container">

    <?php include 'header.php'; ?>

    <main>
        <h2>Beitrag bearbeiten</h2>
        <?php if ($meldung !== ''): ?>
            <p class="message error"><?php echo htmlspecialchars($meldung, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <?php
        if ($neuer_titel === '') {
            $neuer_titel = $beitrag['titel'];
        }
        if ($neuer_inhalt === '') {
            $neuer_inhalt = $beitrag['inhalt'];
        }
        ?>

        <form method="post" action="beitrag_bearbeiten.php?id=<?php echo $beitrag_id; ?>" enctype="multipart/form-data">
            <label for="titel">Titel:</label>
            <input type="text" name="titel" id="titel" value="<?php echo e($neuer_titel); ?>">

            <label for="inhalt">Inhalt:</label>
            <textarea name="inhalt" id="inhalt"><?php echo e($neuer_inhalt); ?></textarea>

            <?php if (!empty($beitrag['bild'])): ?>
                <div class="aktuelles-bild">
                    <p>Aktuelles Bild:</p>
                    <img src="bilder/<?php echo e($beitrag['bild']); ?>" width="150" alt="Vorschau">
                </div>
            <?php endif; ?>

            <label for="neues_bild">Bild ändern (optional):</label>
            <input type="file" name="neues_bild" id="neues_bild">

            <input type="submit" name="edit_speichern" value="Änderungen speichern">
        </form>
    </main>

    <?php include 'footer.php'; ?>
</div>
</body>
</html>
