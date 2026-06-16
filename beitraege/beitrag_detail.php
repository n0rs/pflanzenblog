<?php
session_start();
require_once dirname(__DIR__) . '/funktionen/datenbank.php';
require_once dirname(__DIR__) . '/funktionen/laden.php';

/** @var mysqli $datenbankverbindung */


$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
$aktueller_benutzername = isset($_SESSION['benutzername']) ? $_SESSION['benutzername'] : 'Gast';
$beitrag_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$beitrag = holeBeitrag($datenbankverbindung, $beitrag_id);

// Wenn die ID nicht in der Datenbank existiert (Beitrag wurde evtl. gelöscht)
if (!$beitrag) {
    header("Location: ../index.php");
    exit;
}

$kommentareTabelleVorhanden = kommentareTabelleExistiert($datenbankverbindung);

$detailansicht = true;
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detailansicht für: <?php echo e($beitrag['titel']); ?>">
    <title><?php echo e($beitrag['titel']); ?> - Pflanzenblog</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo projektPfad('icons/favicon.svg'); ?>">
    <link rel="stylesheet" href="../stylesheet.css">
</head>

<body style="background-image: url('<?php echo projektPfad('icons/natalie-kovach-ph7QQq63lCs-unsplash.jpg'); ?>');">
<div class="container">

    <?php include dirname(__DIR__) . '/kopfzeile.php'; ?>

    <main>
        <div class="blog-container">
            <div class="zurueck-container">
                <a href="../index.php#post-<?php echo $beitrag_id ?>" class="zurueck-link">
                    ⬅ Zurück zur Übersicht
                </a>
            </div>

            <?php
            include __DIR__ . '/beitragskarte.php';
            ?>
        </div>

    </main>
    <?php include dirname(__DIR__) . '/fusszeile.php'; ?>
</body>
</html>


