<?php
session_start();
require_once 'db.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
$aktueller_benutzername = isset($_SESSION['benutzername']) ? $_SESSION['benutzername'] : 'Gast';


$abfrage = $datenbankverbindung->query("
    SELECT beitraege.*, benutzer.benutzername AS benutzer_benutzername 
    FROM beitraege 
    LEFT JOIN benutzer ON beitraege.benutzer_id = benutzer.id;
");?>

<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pflanzenblog</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="container">

            <?php include 'header.php'; ?>

            <main>
                <p>Hallo <?php echo($aktueller_benutzername);?>!</p>
                <div class="blog-container">
                    <?php
                    $beitraege = $abfrage->fetch_all(MYSQLI_ASSOC);
                    foreach ($beitraege as $beitrag):
                        ?>
                        <div class="post-card">
                            <h2><?php echo e($beitrag['titel']); ?></h2>
                            <!-- löschen/bearbeiten wird nur angezeigt, wenn user amdin ist oder der autor des beitrags -->
                            <?php if (istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
                                <a href="beitrag_bearbeiten.php?id=<?php echo $beitrag['id']; ?>">Bearbeiten</a>
                                <a href="beitrag_loeschen.php?id=<?php echo $beitrag['id']; ?>">Löschen</a>
                            <?php endif; ?>
                            <?php if (!empty($beitrag['bild'])): ?>
                                <div class="post-bild">
                                    <img src="bilder/<?php echo e($beitrag['bild']); ?>" alt="Bild zum Beitrag: <?php echo htmlspecialchars($beitrag['titel']); ?>">
                                </div>
                            <?php endif; ?>
                            <p><?php echo nl2br(e($beitrag['inhalt'])); ?></p>
                            <div class="metadaten">
                                <small>Veröffentlicht am: <?php echo $beitrag['datum']; ?></small><br>
                                <small>Autor: <strong><?php echo e(isset($beitrag['benutzer_benutzername']) ? $beitrag['benutzer_benutzername'] : 'Gast'); ?></strong></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
            <footer>
                <p><a href="impressum.php">Impressum</a></p>
                <p>© 2026 Pflanzenblog </p>
            </footer>
        </div>
    </body>
</html>