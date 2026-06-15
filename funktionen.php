<?php

// Funktion zum Abrufen eines einzelnen Beitrags inklusive Autor- und Pflanzeninformationen
function holeBeitrag(mysqli $datenbankverbindung, int $id)
{
    $anweisung = $datenbankverbindung->prepare(
        "SELECT beitraege.*, benutzer.benutzername AS benutzer_benutzername
         FROM beitraege
         LEFT JOIN benutzer ON beitraege.benutzer_id = benutzer.id
         WHERE beitraege.id = ?"
    );
    $anweisung->bind_param('i', $id);
    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    return $ergebnis->fetch_assoc();
}

// Funktion zum Laden aller Beiträge mit zusätzlichen Informationen zum Autor und zur Pflanze
function holeAlleBeitraege(mysqli $datenbankverbindung): array
{
    $anweisung = $datenbankverbindung->prepare(
        "SELECT beitraege.*, benutzer.benutzername AS benutzer_benutzername
         FROM beitraege
         LEFT JOIN benutzer ON beitraege.benutzer_id = benutzer.id
         ORDER BY beitraege.datum DESC"
    );
    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    return $ergebnis ? $ergebnis->fetch_all(MYSQLI_ASSOC) : [];
}

// Diese Funktion leitet nicht eingeloggte Benutzer zurück zur Startseite
// Dadurch sind bestimmte Seiten nur für angemeldete Nutzer zugänglich
function pruefeEingeloggt($sicherheitsstufe)
{
    if ($sicherheitsstufe <= 0) {
        header("Location: index.php");
        exit;
    }
}

// Sicheres HTML-Encoding von Ausgabetexten
function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function assetPath(string $relativePath): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

    if ($scriptName === '') {
        return ltrim($relativePath, '/');
    }

    $baseDir = str_replace('\\', '/', dirname($scriptName));
    if ($baseDir === '/' || $baseDir === '.') {
        $baseDir = '';
    }

    return ($baseDir !== '' ? $baseDir : '') . '/' . ltrim($relativePath, '/');
}

function formatDate(string $datum): string
{
    try {
        $date = new DateTimeImmutable($datum);
        return $date->format('d.m.Y H:i');
    } catch (Exception $e) {
        // Falls das Datum nicht geparst werden kann, geben wir den ursprünglichen String sicher aus
        return e($datum);
    }
}

// Prüft, ob der aktuelle Benutzer den Beitrag bearbeiten darf
function istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)
{
    if (!$beitrag) {
        return false;
    }
    if ($sicherheitsstufe == 2) {
        return true;
    }
    return $beitrag['benutzer_id'] == $aktueller_benutzer_id;
}

function istKommentator($kommentar, $aktueller_benutzer_id, $sicherheitsstufe) {
    if (!$kommentar) {
        return false;
    }
    if($sicherheitsstufe == 2) {
        return true;
    }
    return $kommentar['benutzer_id'] == $aktueller_benutzer_id;
}

// Prüft das hochgeladene Bild und speichert es unter einem eindeutigen Dateinamen
function uploadBild($datei_input)
{
    if (!isset($datei_input) || $datei_input['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $erlaubteErweiterungen = ['jpg', 'jpeg', 'png', 'gif'];
    $datei_name = $datei_input['name'];
    $datei_tmp = $datei_input['tmp_name'];
    $fragmente = explode('.', $datei_name);
    $erweiterung = strtolower(end($fragmente));

    if (!in_array($erweiterung, $erlaubteErweiterungen, true)) {
        // Ungültige Dateiendung: Datei wird nicht akzeptiert
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($datei_tmp);
    $erlaubteMimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
    ];

    if (!isset($erlaubteMimeTypes[$erweiterung]) || $mimeType !== $erlaubteMimeTypes[$erweiterung]) {
        // Mime-Type stimmt nicht mit der Dateiendung überein
        return null;
    }

    // Eindeutigen Dateinamen erzeugen, um Kollisionen zu vermeiden
    $neuer_bildname = time() . '_' . rand(1000000, 9999999) . '.' . $erweiterung;

    if (move_uploaded_file($datei_tmp, __DIR__ . '/bilder/' . $neuer_bildname)) {
        return $neuer_bildname;
    }

    return null;
}

// Prüft, ob die Kommentar-Tabelle in der Datenbank existiert
function kommentareTabelleExistiert(mysqli $datenbankverbindung): bool
{
    $ergebnis = $datenbankverbindung->query("SHOW TABLES LIKE 'kommentare'");
    return $ergebnis && $ergebnis->num_rows > 0;
}

// Lädt alle Kommentare für einen bestimmten Beitrag, sortiert nach Datum absteigend
function holeKommentare(mysqli $datenbankverbindung, int $beitrag_id): array
{
    $anweisung = $datenbankverbindung->prepare(
        "SELECT kommentare.inhalt,
                kommentare.datum,
                kommentare.id,
                kommentare.benutzer_id,
                kommentare.beitrag_id,
                kommentare.kom_id,
                benutzer.benutzername
         FROM kommentare
         LEFT JOIN benutzer ON kommentare.benutzer_id = benutzer.id
         WHERE kommentare.beitrag_id = ?
         ORDER BY kommentare.datum ASC"
    );

    $anweisung->bind_param('i', $beitrag_id);
    $anweisung->execute();

    $ergebnis = $anweisung->get_result();

    return $ergebnis ? $ergebnis->fetch_all(MYSQLI_ASSOC) : [];
}

function baueKommentarBaum(array $kommentare): array
{
    $nachId = [];
    $baum = [];

    foreach ($kommentare as $kommentar) {
        $kommentar['antworten'] = [];
        $nachId[$kommentar['id']] = $kommentar;
    }

    foreach ($nachId as $id => &$kommentar) {
        if (!empty($kommentar['kom_id']) && isset($nachId[$kommentar['kom_id']])) {
            $nachId[$kommentar['kom_id']]['antworten'][] = &$kommentar;
        } else {
            $baum[] = &$kommentar;
        }
    }

    unset($kommentar);

    return $baum;
}

function zeigeKommentarMitAntworten(
    array $kommentar,
    array $beitrag,
    int|null $aktueller_benutzer_id,
    int $sicherheitsstufe,
    bool $istDetailseite,
    int $tiefe = 0
): void {
    $maxTiefe = 5;
    $cssKlasse = $tiefe > 0 ? 'comment antwort' : 'ausklappen-inhalt comment';

    ?>
    <div class="<?php echo $cssKlasse; ?>" id="kommentar-<?php echo (int)$kommentar['id']; ?>">
        <p><?php echo nl2br(e($kommentar['inhalt'])); ?></p>

        <small>
            <?php echo $tiefe > 0 ? 'Antwort von' : 'Von'; ?>
            <strong><?php echo e($kommentar['benutzername'] ?? 'Gast'); ?></strong>
            am <?php echo formatDate($kommentar['datum']); ?>
        </small>

        <?php if (istKommentator($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
            <div class="comment-aktionen">
                <details class="kommentar-edit-details-inline">
                    <summary title="Bearbeiten">
                        <img src="<?php echo e(assetPath('icons/pencil.svg')); ?>" alt="Bearbeiten" class="icon" title="Bearbeiten">
                    </summary>

                    <form action="kommentar_aktualisieren.php"
                        method="POST"
                        class="comment-form kommentar-edit-form-inline">
                        <input type="hidden" name="kommentar_id" value="<?php echo (int)$kommentar['id']; ?>">

                        <textarea name="inhalt" required><?php echo e($kommentar['inhalt']); ?></textarea>

                        <button type="submit">Speichern</button>
                    </form>
                </details>

                <a href="kommentar_loeschen.php?id=<?php echo (int)$kommentar['id']; ?>"
                onclick="return confirm('Kommentar wirklich löschen?');">
                    <img src="<?php echo e(assetPath('icons/trash.svg')); ?>" alt="Löschen" class="icon" title="Löschen">
                </a>
            </div>
        <?php endif; ?>

        <?php if ($istDetailseite && $sicherheitsstufe >= 1): ?>
            <details class="antwort-details">
                <summary class="antwort-button">Antworten</summary>

                <form action="kommentar_erstellen.php?beitrag_id=<?php echo (int)$beitrag['id']; ?>"
                    method="POST"
                    class="comment-form antwort-form">
                    <input type="hidden" name="kom_id" value="<?php echo (int)$kommentar['id']; ?>">

                    <label for="antwort_<?php echo (int)$kommentar['id']; ?>">
                        Antwort schreiben
                    </label>

                    <textarea id="antwort_<?php echo (int)$kommentar['id']; ?>" name="inhalt" required></textarea>

                    <button type="submit" name="kommentar_submit">
                        <img src="<?php echo e(assetPath('icons/send.svg')); ?>" alt="Senden" class="icon-button">
                        <span class="text-button">Antwort absenden</span>
                    </button>
                </form>
            </details>
        <?php endif; ?>

        <?php if (!empty($kommentar['antworten'])): ?>
            <div class="antworten tiefe-<?php echo min($tiefe + 1, $maxTiefe); ?>">
                <?php foreach ($kommentar['antworten'] as $antwort): ?>
                    <?php zeigeKommentarMitAntworten(
                        $antwort,
                        $beitrag,
                        $aktueller_benutzer_id,
                        $sicherheitsstufe,
                        $istDetailseite,
                        $tiefe + 1
                    ); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function gruppiereKommentareNachAntworten(array $kommentare): array
{
    $gruppen = [];

    foreach ($kommentare as $kommentar) {
        if (empty($kommentar['kom_id'])) {
            $kommentar['antworten'] = [];
            $gruppen[$kommentar['id']] = $kommentar;
        }
    }

    foreach ($kommentare as $kommentar) {
        if (!empty($kommentar['kom_id']) && isset($gruppen[$kommentar['kom_id']])) {
            $gruppen[$kommentar['kom_id']]['antworten'][] = $kommentar;
        }
    }

    return array_values($gruppen);
}


function sendeToast(string $nachricht): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['toast_nachricht'] = $nachricht;
}

function leereSuche($suchbegriff) {
    ?>
        <div class="container">
            <label class="icon-box">
                <img src="<?php echo e(assetPath('icons/search.svg')); ?>" alt="Lupe" class="gross-icon">
                <span >Keine Ergebnisse zu "<?php echo $suchbegriff ?>" gefunden.</span>
            </label>
        </div>

    <?php
}


function zeigePflanzenDetails(array $beitrag): void
{
    if (!empty($beitrag['botanischer_name']) || !empty($beitrag['standort']) ||
        !empty($beitrag['bewasserung']) || !empty($beitrag['lichtmenge']) ||
        !empty($beitrag['winterhart']) || !empty($beitrag['schwierigkeitsgrad'])):

        ?>
        <div class="pflanzen-details">
            <h3>Pflanzen-Details</h3>

            <?php if (!empty($beitrag['botanischer_name'])): ?>
                <p><strong>Botanischer Name:</strong> <?php echo e($beitrag['botanischer_name']); ?></p>
            <?php endif; ?>

            <?php if (!empty($beitrag['standort'])): ?>
                <p><strong>Standort:</strong> <?php echo e($beitrag['standort']); ?></p>
            <?php endif; ?>

            <?php if (!empty($beitrag['bewasserung'])): ?>
                <p><strong>Bewässerung:</strong> <?php echo e(ucfirst($beitrag['bewasserung'])); ?></p>
            <?php endif; ?>

            <?php if (!empty($beitrag['lichtmenge'])): ?>
                <p><strong>Lichtmenge:</strong> <?php echo e(ucfirst($beitrag['lichtmenge'])); ?></p>
            <?php endif; ?>

            <?php if (!empty($beitrag['winterhart'])): ?>
                <p><strong>Winterhart:</strong> <?php echo e($beitrag['winterhart']); ?></p>
            <?php endif; ?>

            <?php if (!empty($beitrag['schwierigkeitsgrad'])): ?>
                <p><strong>Schwierigkeitsgrad:</strong> <?php echo e(ucfirst($beitrag['schwierigkeitsgrad'])); ?></p>
            <?php endif; ?>
        </div>
    <?php
    endif;
}

// Zählt, wie viele Beiträge insgesamt in der Datenbank existieren
function zaehleAlleBeitraege(mysqli $datenbankverbindung): int
{
    $ergebnis = $datenbankverbindung->query("SELECT COUNT(*) AS anzahl FROM beitraege");
    if ($ergebnis) {
        $reihe = $ergebnis->fetch_assoc();
        return (int)$reihe['anzahl'];
    }
    return 0;
}

// Lädt nur eine bestimmte Anzahl an Beiträgen (LIMIT) ab einem Startpunkt (OFFSET)
function holeBeitraegeProSeite(mysqli $datenbankverbindung, int $limit, int $offset): array
{
    $anweisung = $datenbankverbindung->prepare(
        "SELECT beitraege.*, benutzer.benutzername AS benutzer_benutzername
         FROM beitraege
         LEFT JOIN benutzer ON beitraege.benutzer_id = benutzer.id
         ORDER BY beitraege.datum DESC
         LIMIT ? OFFSET ?"
    );
    $anweisung->bind_param('ii', $limit, $offset);
    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    return $ergebnis ? $ergebnis->fetch_all(MYSQLI_ASSOC) : [];
}
?>

