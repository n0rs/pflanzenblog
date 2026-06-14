<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */


$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
$aktueller_benutzername = isset($_SESSION['benutzername']) ? $_SESSION['benutzername'] : 'Gast';
$beitrag_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$beitrag = holeBeitrag($datenbankverbindung, $beitrag_id);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detailansicht eines Beitrags.">
    <title>Beitrag - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="container">

    <?php include 'header.php'; ?>

    <main>
        <div class="post-card">
            <h2> <span> <a href="index.php#post-<?php echo $beitrag_id ?>"><img src="icons/back.svg" alt="Zurück" class="icon" title="Zurück"></a></span> <?php echo e($beitrag['titel']) ?></h2>
            <?php if (istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
              <div class="beitrag-aktionen">
                 <a href="beitrag_bearbeiten.php?id=<?php echo $beitrag['id']; ?>">
                       <img src="icons/pencil.svg" alt="Bearbeiten" class="icon" title="Bearbeiten">
                 </a>
                   <a href="beitrag_loeschen.php?id=<?php echo $beitrag['id']; ?>"
                    onclick="return confirm('Beitrag \'<?php echo e($beitrag['titel']); ?>\' unwiderruflich löschen?');">
                      <img src="icons/trash.svg" alt="Löschen" class="icon" title="Löschen">
                  </a>
             </div>
            <?php endif; ?>
            <?php if (!empty($beitrag['bild'])): ?>
                <div class="post-bild">
                    <img src="bilder/<?php echo e($beitrag['bild']); ?>" alt="Bild zum Beitrag: <?php echo htmlspecialchars($beitrag['titel'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            <?php endif; ?>

            <p><?php echo nl2br(e($beitrag['inhalt'])); ?></p>

            <?php zeigePflanzenDetails($beitrag); ?>

            <div class="metadaten">
                    <small>Veröffentlicht am: <?php echo formatDate($beitrag['datum']); ?></small><br>
                    <small>Autor: <strong><?php echo e(isset($beitrag['benutzer_benutzername']) ? $beitrag['benutzer_benutzername'] : 'Gast'); ?></strong></small>
            </div>

            <?php include 'kommentarbereich.php'; ?>

        </div>

    </main>

    <?php include 'footer.php'; ?>
</div>
</body>
</html>

