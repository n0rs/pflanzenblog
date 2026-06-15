<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;

pruefeEingeloggt($sicherheitsstufe);

$kommentar_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$kommentar_id) {
    sendeToast("Ungültige Kommentar-ID.");
    header("Location: index.php");
    exit;
}

$meldung = '';

try {
    $anweisung = $datenbankverbindung->prepare(
        "SELECT kommentare.*, beitraege.titel AS beitrag_titel
         FROM kommentare
         LEFT JOIN beitraege ON kommentare.beitrag_id = beitraege.id
         WHERE kommentare.id = ?"
    );
    $anweisung->bind_param('i', $kommentar_id);
    $anweisung->execute();

    $kommentar = $anweisung->get_result()->fetch_assoc();

    if (!$kommentar) {
        sendeToast("Kommentar nicht gefunden.");
        header("Location: index.php");
        exit;
    }

    if (!istKommentator($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)) {
        sendeToast("Nicht berechtigt Kommentar zu bearbeiten.");
        header("Location: index.php");
        exit;
    }

} catch (Throwable $e) {
    sendeToast("Fehler beim Laden des Kommentars.");
    header("Location: index.php");
    exit;
}

$inhalt = $kommentar['inhalt'];
$beitrag_id = (int)$kommentar['beitrag_id'];

if (isset($_POST['submit_update'])) {
    $inhalt = trim($_POST['inhalt'] ?? '');

    if ($inhalt === '') {
        $meldung = "Der Kommentar darf nicht leer sein.";
    } else {
        try {
            $update = $datenbankverbindung->prepare(
                "UPDATE kommentare
                 SET inhalt = ?
                 WHERE id = ?"
            );
            $update->bind_param('si', $inhalt, $kommentar_id);
            $update->execute();

            sendeToast("Kommentar aktualisiert.");
            header("Location: index.php#post-" . $beitrag_id);
            exit;

        } catch (Throwable $e) {
            $meldung = "Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kommentar bearbeiten - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="container">

    <?php include 'header.php'; ?>

    <main>
        <div class="zurueck-container">
            <a href="index.php#post-<?php echo $beitrag_id; ?>" class="zurueck-link">
                ⬅ Zurück zum Beitrag
            </a>
        </div>

        <h2>Kommentar bearbeiten</h2>

        <?php if ($meldung !== ''): ?>
            <p class="message error"><?php echo e($meldung); ?></p>
        <?php endif; ?>

        <form method="post" action="kommentar_bearbeiten.php?id=<?php echo $kommentar_id; ?>" class="comment-form">
            <div class="input-group">
                <label for="inhalt">Kommentar</label>
                <textarea name="inhalt" id="inhalt" required><?php echo e($inhalt); ?></textarea>
            </div>

            <div class="submit-container">
                <input type="submit" name="submit_update" value="Kommentar speichern">
            </div>
        </form>
    </main>

    <?php include 'footer.php'; ?>

</div>
</body>
</html>