<?php
session_start();
require_once dirname(__DIR__) . '/funktionen/datenbank.php';
require_once dirname(__DIR__) . '/funktionen/laden.php';

/** @var mysqli $datenbankverbindung */
$kommentareTabelleVorhanden = kommentareTabelleExistiert($datenbankverbindung);
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;

$ergebnis = null;
$suchbegriff = '';

if (isset($_GET['suchbegriff'])) {

    if($_GET['suchbegriff'] == "" || $_GET['suchbegriff'] == null) {
        sendeToast("Bitte Suchbegriff eingeben");
        header('Location: ../index.php');
        exit;
    } else {
        $suchbegriff = trim($_GET['suchbegriff']);

        $suchbegriff_erweitert = '%'. $suchbegriff . '%';

        $anweisung = $datenbankverbindung->prepare(
            "SELECT * FROM beitraege b WHERE b.titel LIKE ? OR b.inhalt LIKE ?"
        );

        $anweisung->bind_param(
            'ss',
            $suchbegriff_erweitert,
             $suchbegriff_erweitert
        );
        $anweisung->execute();

        $ergebnis = $anweisung->get_result();
    }
}
?>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suchergebnisse für "<?php echo e($suchbegriff); ?>" - Pflanzenblog</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo projektPfad('icons/favicon.svg'); ?>">
    <link rel="stylesheet" href="../stylesheet.css">
</head>
<body style="background-image: url('<?php echo projektPfad('icons/natalie-kovach-ph7QQq63lCs-unsplash.jpg'); ?>');">

<div class="container">

    <?php include dirname(__DIR__) . '/kopfzeile.php'; ?>

    <main>
        <div class="zurueck-container">
            <a href="../index.php" class="zurueck-link">⬅ Zurück zur Übersicht</a>
        </div>

        <div class="suchleiste">


        </div>

        <h2>Suchergebnisse für "<?php echo e($suchbegriff); ?>"</h2>

        <div class="blog-container">
            <?php
            if ($ergebnis && $ergebnis->num_rows > 0) {
                while ($beitrag = $ergebnis->fetch_assoc()) {
                    include __DIR__ . '/beitragskarte.php';
                }
            } else {
                leereSuche(e($suchbegriff));
            }
            ?>
        </div>
    </main>

    <?php include dirname(__DIR__) . '/fusszeile.php'; ?>
</div>
</body>
</html>
