<?php
require_once 'db.php';
/** @var mysqli $mysqli */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Über uns - Pflanzenblog</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
    <div class="ueber-uns-container">

        <?php include 'header.php'; ?>

        <main>
        <h2>Über uns</h2>
        </main>
        <footer>
            <p><a href="impressum.php">Impressum</a></p>
            <p>© 2026 Pflanzenblog </p>
        </footer>
    </div>
    </body>
</html>