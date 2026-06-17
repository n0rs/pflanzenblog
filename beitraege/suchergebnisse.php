<?php
session_start();
require_once dirname(__DIR__) . '/funktionen/datenbank.php';
require_once dirname(__DIR__) . '/funktionen/laden.php';

/** @var mysqli $datenbankverbindung */
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;

$ergebnis = null;
$suchbegriff = '';

if (isset($_GET['suchbegriff'])) {

    if($_GET['suchbegriff'] == "" || $_GET['suchbegriff'] == null) {
        sendeToast("Bitte Suchbegriff eingeben");
    } else {
        $suchbegriff = trim($_GET['suchbegriff']);

        $suchbegriff_erweitert = '%'. $suchbegriff . '%';

        $anweisung = $datenbankverbindung->prepare(
            "SELECT " . beitragsAuswahlSql() . "
             FROM beitraege
             LEFT JOIN benutzer ON beitraege.benutzer_id = benutzer.id
             WHERE beitraege.titel LIKE ? OR beitraege.inhalt LIKE ?
             ORDER BY beitraege.datum DESC, beitraege.id DESC"
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
<body style="background-image: url('<?= e(projektUrl('bilder/hb.jpg')) ?>');">
<div class="container">

    <?php include dirname(__DIR__) . '/kopfzeile.php'; ?>

    <main>
        <div class="zurueck-container">
            <a href="../index.php" class="zurueck-link">⬅ Zurück zur Übersicht</a>
        </div>

        <div class="suchleiste-box">
            <form action="<?php echo projektPfad('beitraege/suchergebnisse.php'); ?>" method="get" class="suchleiste">
                <input type="text" name="suchbegriff" placeholder="Suchen..." id="suchleiste" value="<?php echo e($suchbegriff); ?>">
                <button type="submit" class="suche-icon-button">
                    <?php echo inlineIcon('search.svg', ['class' => 'icon stay', 'role' => 'img', 'aria-label' => 'Suchen', 'title' => 'Suchen']); ?>
                </button>
            </form>
        </div>

        <?php if ($suchbegriff != ''): ?>
            <h2>Suchergebnisse für "<?php echo e($suchbegriff); ?>"</h2>
        <?php endif; ?>

        <div class="blog-container">
            <?php
            if ($ergebnis && $ergebnis->num_rows > 0) {
                while ($beitrag = $ergebnis->fetch_assoc()) {
                    include __DIR__ . '/beitragskarte.php';
                }
            } else {
                ?>
                <div class="container">
                    <div class="leer-box">
                        <?php echo inlineIcon('search.svg', ['class' => 'gross-icon', 'role' => 'img', 'aria-label' => 'Lupe', 'title' => 'Suche']); ?>
                        <?php if ($suchbegriff != ''): ?>
                            <span>Keine Ergebnisse gefunden.</span>
                        <?php else: ?> <span>Kein Suchbegriff eingegeben.</span>
                        <?php endif; ?> </div>
                </div>
                <?php
            }
            ?>
        </div>
    </main>

    <?php include dirname(__DIR__) . '/fusszeile.php'; ?>
</div>
</body>
</html>
