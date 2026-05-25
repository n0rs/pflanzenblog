<?php
require_once 'db.php';
/** @var PDO $pdo */

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
        <header>
            <h1>🌿 Mein Pflanzenblog</h1>
            <nav>
                <a href="index.php">Start</a>
                <?php if ($sicherheitsstufe >= 1): ?>
                    <a href="beitrag_erstellen.php">Neuer Beitrag</a>
                <?php endif; ?>
                <?php if ($sicherheitsstufe == 0): ?>
                    <a href="registrieren.php">Registrieren</a>
                <?php endif; ?>
                <?php if ($sicherheitsstufe >= 1): ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <a href="ueber_uns.php">Über uns</a>
            </nav>
        </header>
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