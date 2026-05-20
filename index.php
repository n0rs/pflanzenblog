<?php
session_start();
require_once 'db.php';
/** @var PDO $pdo */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
$aktueller_benutzername = isset($_SESSION['benutzername']) ? $_SESSION['benutzername'] : 'Gast';


$query = $pdo->query("
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
                <a href="#">Über</a>

            </nav>
        </header>
        <main>
            <p>Hallo <?php echo($aktueller_benutzername);?>!</p>
            <div class="blog-container">
                <?php
                $beitraege = $query->fetchAll();
                foreach ($beitraege as $beitrag):
                    ?>
                    <div class="post-card">
                        <h2><?php echo htmlspecialchars($beitrag['titel']); ?></h2>
                        <?php if (!empty($beitrag['bild'])): ?>
                            <div class="post-bild">
                                <img src="bilder/<?php echo htmlspecialchars($beitrag['bild']); ?>" alt="Bild zum Beitrag: <?php echo htmlspecialchars($beitrag['titel']); ?>">
                            </div>
                        <?php endif; ?>
                        <p><?php echo nl2br(htmlspecialchars($beitrag['inhalt'])); ?></p>
                        <div class="metadaten">
                            <small>Veröffentlicht am: <?php echo $beitrag['datum']; ?></small><br>
                            <small>Autor: <strong><?php echo htmlspecialchars(isset($beitrag['benutzer_benutzername']) ? $beitrag['benutzer_benutzername'] : 'Gast'); ?></strong></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
            <footer>
                <p>© 2026 Pflanzenblog </p>
            </footer>
        </div>
    </body>
</html>