<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="Erfahre mehr über das Team und die Mission hinter dem Pflanzenblog.">
        <title>Über uns - Pflanzenblog</title>
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
    <div class="container">

        <?php include 'header.php'; ?>

        <main>
        <h2>Über uns</h2>
        </main>
        <?php include 'footer.php'; ?>
    </div>
    </body>
</html>