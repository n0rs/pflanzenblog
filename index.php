<?php
session_start();
require_once __DIR__ . '/funktionen/datenbank.php';
require_once __DIR__ . '/funktionen/laden.php';
require_once __DIR__ . '/funktionen/ausgabe.php';

/** @var mysqli $datenbankverbindung */

// Sicherheitsstufe und Benutzerinfos aus der Session laden
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;
$aktueller_benutzername = isset($_SESSION['benutzername']) ? $_SESSION['benutzername'] : 'Gast';

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'success';
if (isset($_SESSION['message'])) {
    unset($_SESSION['message']);
}
if (isset($_SESSION['message_type'])) {
    unset($_SESSION['message_type']);
}

$kommentareTabelleVorhanden = kommentareTabelleExistiert($datenbankverbindung);

$beitragsFilter = bereinigeBeitragsFilter($_GET);

$beitraege_pro_seite = 5;

$aktuelle_seite = isset($_GET['seite']) ? (int)$_GET['seite'] : 1;
if ($aktuelle_seite < 1) {
    $aktuelle_seite = 1;
}

$offset = ($aktuelle_seite - 1) * $beitraege_pro_seite;
$gesamt_beitraege = zaehleAlleBeitraege($datenbankverbindung, $beitragsFilter);
$gesamt_seiten = max(1, (int)ceil($gesamt_beitraege / $beitraege_pro_seite));

if ($aktuelle_seite > $gesamt_seiten) {
    $aktuelle_seite = $gesamt_seiten;
    $offset = ($aktuelle_seite - 1) * $beitraege_pro_seite;
}

$beitraege = holeBeitraegeProSeite($datenbankverbindung, $beitraege_pro_seite, $offset, $beitragsFilter);
?>

<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Entdecke den Pflanzenblog mit aktuellen Beiträgen, Tipps und Kommentaren rund um Garten und Pflanzen.">
        <title>Pflanzenblog</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo pleskAssetUrl('icons/favicon.svg'); ?>">
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body style="background-image: url('<?php echo pleskAssetUrl('icons/hb.jpg'); ?>');">
    <div class="container">

            <?php include 'kopfzeile.php'; ?>

            <main>
                <?php if (!empty($message)): ?>
                    <p class="message <?php echo e($messageType); ?>"><?php echo e($message); ?></p>
                <?php endif; ?>

                <?php
                    $datumSortierung = $beitragsFilter['sortierung'] === 'datum_desc' ? 'datum_asc' : 'datum_desc';
                    $titelSortierung = $beitragsFilter['sortierung'] === 'titel_asc' ? 'titel_desc' : 'titel_asc';
                    $datumAktiv = in_array($beitragsFilter['sortierung'], ['datum_desc', 'datum_asc'], true);
                    $titelAktiv = in_array($beitragsFilter['sortierung'], ['titel_asc', 'titel_desc'], true);
                ?>

                <div class="beitrags-filter">
                    <div class="sortier-buttons">
                        <span class="filter-label">Sortieren:</span>
                        <a
                            href="?<?php echo e(beitragsQueryString($beitragsFilter, ['sortierung' => $datumSortierung, 'seite' => 1])); ?>"
                            class="filter-button <?php echo $datumAktiv ? 'aktiv' : ''; ?>"
                        >
                            Datum <?php echo $beitragsFilter['sortierung'] === 'datum_asc' ? '↑' : '↓'; ?>
                        </a>
                        <a
                            href="?<?php echo e(beitragsQueryString($beitragsFilter, ['sortierung' => $titelSortierung, 'seite' => 1])); ?>"
                            class="filter-button <?php echo $titelAktiv ? 'aktiv' : ''; ?>"
                        >
                            A-Z <?php echo $beitragsFilter['sortierung'] === 'titel_desc' ? '↓' : '↑'; ?>
                        </a>
                    </div>

                    <details class="pflege-filter" <?php echo beitragsFilterAktiv($beitragsFilter) ? 'open' : ''; ?>>
                        <summary class="filter-button">Filter</summary>

                        <form action="index.php" method="get" class="pflege-filter-formular">
                            <input type="hidden" name="sortierung" value="<?php echo e($beitragsFilter['sortierung']); ?>">

                            <select aria-label="Schwierigkeit" name="schwierigkeitsgrad">
                                <option value="">Schwierigkeit</option>
                                <option value="einfach" <?php echo $beitragsFilter['schwierigkeitsgrad'] === 'einfach' ? 'selected' : ''; ?>>Einfach</option>
                                <option value="mittel" <?php echo $beitragsFilter['schwierigkeitsgrad'] === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="anspruchsvoll" <?php echo $beitragsFilter['schwierigkeitsgrad'] === 'anspruchsvoll' ? 'selected' : ''; ?>>Anspruchsvoll</option>
                            </select>

                            <select aria-label="Licht" name="lichtmenge">
                                <option value="">Licht</option>
                                <option value="wenig" <?php echo $beitragsFilter['lichtmenge'] === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                                <option value="mittel" <?php echo $beitragsFilter['lichtmenge'] === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="viel" <?php echo $beitragsFilter['lichtmenge'] === 'viel' ? 'selected' : ''; ?>>Viel</option>
                            </select>

                            <select aria-label="Bewässerung" name="bewasserung">
                                <option value="">Bewässerung</option>
                                <option value="wenig" <?php echo $beitragsFilter['bewasserung'] === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                                <option value="mittel" <?php echo $beitragsFilter['bewasserung'] === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="viel" <?php echo $beitragsFilter['bewasserung'] === 'viel' ? 'selected' : ''; ?>>Viel</option>
                            </select>

                            <select aria-label="Winterhart" name="winterhart">
                                <option value="">Winterhart</option>
                                <option value="Winterhart" <?php echo $beitragsFilter['winterhart'] === 'Winterhart' ? 'selected' : ''; ?>>Winterhart</option>
                                <option value="Bedingt winterhart" <?php echo $beitragsFilter['winterhart'] === 'Bedingt winterhart' ? 'selected' : ''; ?>>Bedingt winterhart</option>
                                <option value="Nicht winterhart" <?php echo $beitragsFilter['winterhart'] === 'Nicht winterhart' ? 'selected' : ''; ?>>Nicht winterhart</option>
                            </select>

                            <button type="submit" class="filter-button">OK</button>

                            <?php if (beitragsFilterAktiv($beitragsFilter) || $beitragsFilter['sortierung'] !== 'datum_desc'): ?>
                                <a href="index.php" class="filter-button filter-zuruecksetzen">Zurücksetzen</a>
                            <?php endif; ?>
                        </form>
                    </details>
                </div>

                <p class="filter-ergebnis">
                    <?php echo (int)$gesamt_beitraege; ?>
                    <?php echo $gesamt_beitraege === 1 ? 'Beitrag gefunden' : 'Beiträge gefunden'; ?>
                </p>

                <div class="blog-container index-ansicht">
                    <?php if (!empty($beitraege)): ?>
                        <?php foreach ($beitraege as $beitrag): ?>
                            <?php include __DIR__ . '/beitraege/beitragskarte.php'; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="leerer-zustand">Keine Beiträge passen zu dieser Auswahl.</p>
                    <?php endif; ?>
                </div>

                <?php if ($gesamt_seiten > 1 && $gesamt_beitraege > 0): ?>
                    <div class="umblaettern">
                        <?php if ($aktuelle_seite > 1): ?>
                            <a href="?<?php echo e(beitragsQueryString($beitragsFilter, ['seite' => $aktuelle_seite - 1])); ?>" class="umblaettern-button davor">
                                ⬅ <span class="text-button">Neuere Beiträge</span>
                            </a>
                        <?php endif; ?>

                        <span class="umblaettern-text"> Seite <?php echo $aktuelle_seite; ?> von <?php echo $gesamt_seiten; ?> </span>

                        <?php if ($aktuelle_seite < $gesamt_seiten): ?>
                            <a href="?<?php echo e(beitragsQueryString($beitragsFilter, ['seite' => $aktuelle_seite + 1])); ?>" class="umblaettern-button danach">
                                <span class="text-button">Ältere Beiträge</span> ➔
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </main>
        <?php include 'fusszeile.php'; ?>
        </div>
    </body>
</html>
