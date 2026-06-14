<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

// Sicherheitsstufe und Benutzerinfos aus der Session laden
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
$aktueller_benutzername = isset($_SESSION['benutzername']) ? $_SESSION['benutzername'] : 'Gast';

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'success';
if (isset($_SESSION['message'])) {
    unset($_SESSION['message']);
}
if (isset($_SESSION['message_type'])) {
    unset($_SESSION['message_type']);
}

$kommentareTabelleVorhanden = kommentareTabelleExistiert($datenbankverbindung);

$beitraege = holeAlleBeitraege($datenbankverbindung);

$beitraege_pro_seite = 5;

$aktuelle_seite = isset($_GET['seite']) ? (int)$_GET['seite'] : 1;
if ($aktuelle_seite < 1) {
    $aktuelle_seite = 1;
}

$offset = ($aktuelle_seite - 1) * $beitraege_pro_seite;
$gesamt_beitraege = zaehleAlleBeitraege($datenbankverbindung);
$gesamt_seiten = ceil($gesamt_beitraege / $beitraege_pro_seite);

$beitraege = holeBeitraegeProSeite($datenbankverbindung, $beitraege_pro_seite, $offset);
?>

<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Entdecke den Pflanzenblog mit aktuellen Beiträgen, Tipps und Kommentaren rund um Garten und Pflanzen.">
        <title>Pflanzenblog</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="container">

            <?php include 'header.php'; ?>

            <main>
                <?php if (!empty($message)): ?>
                    <p class="message <?php echo e($messageType); ?>"><?php echo e($message); ?></p>
                <?php endif; ?>
                <div class="blog-container index-ansicht">
                    <?php foreach ($beitraege as $beitrag): ?>
                        <?php include 'post_card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <?php if ($gesamt_seiten > 1): ?>
                    <div class="umblaettern">
                        <?php if ($aktuelle_seite > 1): ?>
                            <a href="?seite=<?php echo $aktuelle_seite - 1; ?>" class="btn-umblaettern davor">
                                ⬅ <span class="btn-text">Neuere Beiträge</span>
                            </a>
                        <?php endif; ?>

                        <span class="umblaettern-text"> Seite <?php echo $aktuelle_seite; ?> von <?php echo $gesamt_seiten; ?> </span>

                        <?php if ($aktuelle_seite < $gesamt_seiten): ?>
                            <a href="?seite=<?php echo $aktuelle_seite + 1; ?>" class="btn-umblaettern danach">
                                <span class="btn-text">Ältere Beiträge</span> ➔
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </main>
            <?php include 'footer.php'; ?>
        </div>
    </body>
</html>