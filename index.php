<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
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
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="container">

            <?php include 'kopfzeile.php'; ?>

            <main>
                <?php if (!empty($message)): ?>
                    <p class="message <?php echo e($messageType); ?>"><?php echo e($message); ?></p>
                <?php endif; ?>

                <form action="index.php" method="get" class="beitrags-filter">
                    <div class="filter-gruppe filter-suche">
                        <label for="filter-suchbegriff">Beiträge durchsuchen</label>
                        <input
                            type="text"
                            id="filter-suchbegriff"
                            name="suchbegriff"
                            value="<?php echo e($beitragsFilter['suchbegriff']); ?>"
                            placeholder="Titel, Inhalt oder botanischer Name"
                        >
                    </div>

                    <div class="filter-gruppe">
                        <label for="filter-sortierung">Sortierung</label>
                        <select id="filter-sortierung" name="sortierung">
                            <option value="datum_desc" <?php echo $beitragsFilter['sortierung'] === 'datum_desc' ? 'selected' : ''; ?>>Neueste zuerst</option>
                            <option value="datum_asc" <?php echo $beitragsFilter['sortierung'] === 'datum_asc' ? 'selected' : ''; ?>>Älteste zuerst</option>
                            <option value="titel_asc" <?php echo $beitragsFilter['sortierung'] === 'titel_asc' ? 'selected' : ''; ?>>Alphabetisch A-Z</option>
                            <option value="titel_desc" <?php echo $beitragsFilter['sortierung'] === 'titel_desc' ? 'selected' : ''; ?>>Alphabetisch Z-A</option>
                        </select>
                    </div>

                    <div class="filter-gruppe">
                        <label for="filter-schwierigkeitsgrad">Schwierigkeit</label>
                        <select id="filter-schwierigkeitsgrad" name="schwierigkeitsgrad">
                            <option value="">Alle</option>
                            <option value="einfach" <?php echo $beitragsFilter['schwierigkeitsgrad'] === 'einfach' ? 'selected' : ''; ?>>Einfach</option>
                            <option value="mittel" <?php echo $beitragsFilter['schwierigkeitsgrad'] === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                            <option value="anspruchsvoll" <?php echo $beitragsFilter['schwierigkeitsgrad'] === 'anspruchsvoll' ? 'selected' : ''; ?>>Anspruchsvoll</option>
                        </select>
                    </div>

                    <div class="filter-gruppe">
                        <label for="filter-lichtmenge">Licht</label>
                        <select id="filter-lichtmenge" name="lichtmenge">
                            <option value="">Alle</option>
                            <option value="wenig" <?php echo $beitragsFilter['lichtmenge'] === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                            <option value="mittel" <?php echo $beitragsFilter['lichtmenge'] === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                            <option value="viel" <?php echo $beitragsFilter['lichtmenge'] === 'viel' ? 'selected' : ''; ?>>Viel</option>
                        </select>
                    </div>

                    <div class="filter-gruppe">
                        <label for="filter-bewasserung">Bewässerung</label>
                        <select id="filter-bewasserung" name="bewasserung">
                            <option value="">Alle</option>
                            <option value="wenig" <?php echo $beitragsFilter['bewasserung'] === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                            <option value="mittel" <?php echo $beitragsFilter['bewasserung'] === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                            <option value="viel" <?php echo $beitragsFilter['bewasserung'] === 'viel' ? 'selected' : ''; ?>>Viel</option>
                        </select>
                    </div>

                    <div class="filter-gruppe">
                        <label for="filter-winterhart">Winterhart</label>
                        <select id="filter-winterhart" name="winterhart">
                            <option value="">Alle</option>
                            <option value="Winterhart" <?php echo $beitragsFilter['winterhart'] === 'Winterhart' ? 'selected' : ''; ?>>Winterhart</option>
                            <option value="Bedingt winterhart" <?php echo $beitragsFilter['winterhart'] === 'Bedingt winterhart' ? 'selected' : ''; ?>>Bedingt winterhart</option>
                            <option value="Nicht winterhart" <?php echo $beitragsFilter['winterhart'] === 'Nicht winterhart' ? 'selected' : ''; ?>>Nicht winterhart</option>
                        </select>
                    </div>

                    <div class="filter-aktionen">
                        <button type="submit">Anwenden</button>
                        <?php if (beitragsFilterAktiv($beitragsFilter) || $beitragsFilter['sortierung'] !== 'datum_desc'): ?>
                            <a href="index.php" class="filter-zuruecksetzen">Zurücksetzen</a>
                        <?php endif; ?>
                    </div>
                </form>

                <p class="filter-ergebnis">
                    <?php echo (int)$gesamt_beitraege; ?>
                    <?php echo $gesamt_beitraege === 1 ? 'Beitrag gefunden' : 'Beiträge gefunden'; ?>
                </p>

                <div class="blog-container index-ansicht">
                    <?php if (!empty($beitraege)): ?>
                        <?php foreach ($beitraege as $beitrag): ?>
                            <?php include 'beitragskarte.php'; ?>
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
