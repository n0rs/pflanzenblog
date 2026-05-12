<?php
require_once 'db.php';
/** @var PDO $pdo */
$query = $pdo->query("
    SELECT beitraege.*, autoren.name AS autor_name 
    FROM beitraege 
    LEFT JOIN autoren ON beitraege.autor_id = autoren.id
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
                <a href="erstellen.php" class="btn-create">➕ Neuer Beitrag</a>
                <a href="#">Über</a>
            </nav>
        </header>
        <main>
            <div class="blog-container">
                <?php
                $beitraege = $query->fetchAll();
                foreach ($beitraege as $beitrag):
                    ?>
                    <div class="post-card">
                        <h2><?php echo htmlspecialchars($beitrag['titel']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($beitrag['inhalt'])); ?></p>

                        <div class="meta">
                            <small>Veröffentlicht am: <?php echo $beitrag['datum']; ?></small><br>
                            <small>Autor: <strong><?php echo htmlspecialchars(isset($beitrag['autor_name']) ? $beitrag['autor_name'] : 'Unbekannt'); ?></strong></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
            <footer>
                <p>© 2026 Pflanzenblog</p>
            </footer>
        </div>
    </body>
</html>