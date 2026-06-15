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

        <?php include 'kopfzeile.php'; ?>

        <main>
            <h2>Über uns</h2>
            <h3>Das Team, das hinter diesem Pflanzenblog steckt :)</h3>

            <div class="team-container">
                <div class="beitrags-karte team-karte">
                    <div class="team-bild">
                        <img src="<?php echo e(assetPath('icons/account.svg')); ?>" alt="Christian">
                    </div>
                    <div class="team-info">
                        <h3>Liv Grimsehl-Schmitz</h3>
                        <p><strong>Rolle:</strong> Co-Gründerin & Community Manager</p>
                        <p>Liv sorgt für den Austausch unter den Gartenfreunden. Sie moderiert den Kommentarbereich und organisiert tolle Pflanzen-Tauschbörsen für die Community.</p>
                    </div>
                </div>

                <div class="beitrags-karte team-karte rechts-gerichtet">
                    <div class="team-bild">
                        <img src="<?php echo e(assetPath('icons/account.svg')); ?>" alt="Liv">
                    </div>
                    <div class="team-info"><h3>Christian Müsse</h3>
                        <p><strong>Rolle:</strong> Co-Gründer & Pflanzen-Experte</p>
                        <p>Christian teilt leidenschaftlich gerne sein Wissen über tropische Zimmerpflanzen.</p>
                    </div>
                </div>

                <div class="beitrags-karte team-karte">
                    <div class="team-bild">
                        <img src="<?php echo e(assetPath('icons/account.svg')); ?>" alt="Lisa">
                    </div>
                    <div class="team-info">
                        <h3>Lisa Pham</h3>
                        <p><strong>Rolle:</strong> Co-Gründerin & Pflanzendoktor</p>
                        <p>Lisa schreibt die hilfreichen Ratgeber. Wenn eine Pflanze kränkelt, hat sie garantiert den passenden biologischen Tipp parat, um sie wieder aufzupeppeln.</p>
                    </div>
                </div>
            </div>
        </main>
        <?php include 'fusszeile.php'; ?>
    </div>
    </body>
</html>
