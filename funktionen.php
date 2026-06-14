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

function formatDate(string $datum): string
{
    try {
        $date = new DateTimeImmutable($datum);
        return $date->format('d.m.Y H:i');
    } catch (Exception $e) {
        // Falls das Datum nicht geparst werden kann, geben wir den ursprünglichen String sicher aus
        return htmlspecialchars($datum, ENT_QUOTES, 'UTF-8');
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
        "SELECT kommentare.inhalt, kommentare.datum, kommentare.id, kommentare.benutzer_id, benutzer.benutzername
         FROM kommentare
         LEFT JOIN benutzer ON kommentare.benutzer_id = benutzer.id
         WHERE kommentare.beitrag_id = ?
         ORDER BY kommentare.datum DESC"
    );
    $anweisung->bind_param('i', $beitrag_id);
    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    return $ergebnis ? $ergebnis->fetch_all(MYSQLI_ASSOC) : [];
}

function sendeToast(string $nachricht): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['toast_nachricht'] = $nachricht;
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

