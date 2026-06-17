<?php
session_start();
require_once __DIR__ . '/funktionen/datenbank.php';
require_once __DIR__ . '/funktionen/laden.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="Impressum des Pflanzenblogs mit Kontaktdaten und rechtlichen Informationen.">
        <title>Impressum</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo projektPfad('icons/favicon.svg'); ?>">
        <link rel="stylesheet" href="<?php echo projektPfad('stylesheet.css'); ?>">
    </head>
    <body style="background-image: url('<?php echo e(projektPfad('bilder/hb.jpg')); ?>');">
    <div class="container">

            <?php include __DIR__ . '/kopfzeile.php'; ?>

            <main>
                <div class = "container impressum">
                    <h2>Impressum</h2>
                    <h3>Angaben gemäß §5 DDG</h3>
                    <p>Rheinische Hochschule Köln gGmbH </p>
                    <p>
                        Schaevenstr. 1 a - b
                        <br>
                        50676 Köln
                    </p>
                    <h3>Kontakt:</h3>
                    <p>
                        Telefon: +49 221 20302-0
                        <br>
                        Telefax: +49 221 20302-6100
                        <br>
                        E-Mail: info@rh-koeln.de
                    </p>
                </div>
            </main>
            <?php include __DIR__ . '/fusszeile.php'; ?>
        </div>
    </body>
</html>
