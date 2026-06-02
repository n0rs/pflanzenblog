<?php
session_start();
require_once 'db.php';
/** @var PDO $pdo */

//variablen vorbereiten
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
$beitrag_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

eingeloggtCheck($sicherheitsstufe);

//daten laden
$beitrag = holeBeitrag($pdo, $beitrag_id);

if (!istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)) {
    header("Location: index.php");
    exit;
}
//formular
if (isset($_POST['edit_speichern'])) {
    $neuer_titel = $_POST['titel'];
    $neuer_inhalt = $_POST['inhalt'];
    $bild_dateiname = $beitrag['bild'];

    $hochgeladenes_bild = uploadBild($_FILES['neues_bild']);

    if ($hochgeladenes_bild !== null) {
        if (!empty($beitrag['bild']) && file_exists("bilder/" . $beitrag['bild'])) {
            unlink("bilder/" . $beitrag['bild']);
        }
        $bild_dateiname = $hochgeladenes_bild;
    }

$update_stmt = $pdo->prepare("UPDATE beitraege SET titel = ?, inhalt = ?, bild = ? WHERE id = ?");
if ($update_stmt->execute([$neuer_titel, $neuer_inhalt, $bild_dateiname, $beitrag_id])) {
    header("Location: index.php");
    exit;
}
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pflanzenblog - Bearbeiten</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="beitrag-erstellen-container">

    <?php include 'header.php'; ?>

    <main>
        <h2>Beitrag bearbeiten</h2>

        <form method="post" action="beitrag_bearbeiten.php?id=<?php echo $beitrag_id; ?>" enctype="multipart/form-data">
            <label for="titel">Titel:</label>
            <input type="text" name="titel" id="titel" value="<?php echo e($beitrag['titel']); ?>">

            <label for="inhalt">Inhalt:</label>
            <textarea name="inhalt" id="inhalt"><?php echo e($beitrag['inhalt']); ?></textarea>

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

    <footer>
        <p><a href="impressum.php">Impressum</a></p>
        <p>© 2026 Pflanzenblog</p>
    </footer>
</div>
</body>
</html>
