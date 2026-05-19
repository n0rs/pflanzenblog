<?php
require_once 'db.php';
require_once 'session.php';

/** @var PDO $pdo */

$query = $pdo->query("
    SELECT beitraege.*, benutzer.benutzername AS benutzer_benutzername 
    FROM beitraege 
    LEFT JOIN benutzer ON beitraege.benutzer_id = benutzer_id
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
                <?php if (istAngemeldet()): ?>
                    <a href="beitrag_erstellen.php" class="btn-create">➕ Neuer Beitrag</a>
                    <a href="login.php?aktion=abmelden">Abmelden</a>
                <?php else: ?>
                    <a href="login.php">Anmelden</a>
                    <a href="registrieren.php">Registrieren</a>
                <?php endif; ?>
                <a href="#">Über</a>
            </nav>
            <p class="status">Angemeldet als: <strong><?php echo sichereAusgabe(holeBenutzername()); ?></strong></p>
        </header>
        <main>
            <div class="blog-container">
                <?php
                $beitraege = $query->fetchAll();
                foreach ($beitraege as $beitrag):
                    ?>
                    <div class="post-card">
                        <h2><?php echo sichereAusgabe($beitrag['titel']); ?></h2>
                        <p><?php echo nl2br(sichereAusgabe($beitrag['inhalt'])); ?></p>

                        <div class="meta">
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