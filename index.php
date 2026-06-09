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
                <p>Hallo <?php echo($aktueller_benutzername);?>!</p>
                <?php if (!empty($message)): ?>
                    <p class="message <?php echo e($messageType); ?>"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <div class="blog-container">
                    <?php foreach ($beitraege as $beitrag): ?>
                        <?php include 'post_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </main>
            <?php include 'footer.php'; ?>
        </div>
    </body>
</html>