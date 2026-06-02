<?php
session_start();
require_once 'db.php';
/** @var mysqli $datenbank */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Impressum</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="impressum-container">

            <?php include 'header.php'; ?>

            <main>
                <div class = "impressum-container1">
                    <h2>Impressum</h2>
                    <h3>Angaben gemäß §5 DDG</h3>
                    <p>Rheinische Hochschule Köln gGmbH </p>
                    <p>
                        Schaevenstr. 1 a - b
                        <br>
                        50676 Köln
                    </p>
                </div>
                <div class="impressum-container2">
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
            <footer>
                <p><a href="#">Impressum</a></p>
                <p>© 2026 Pflanzenblog </p>
            </footer>
        </div>
    </body>
</html>