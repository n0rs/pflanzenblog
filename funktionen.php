<?php

function holeBeitrag(mysqli $datenbankverbindung, int $id)
{
    $anweisung = $datenbankverbindung->prepare("SELECT * FROM beitraege WHERE id = ?");
    $anweisung->bind_param('i', $id);
    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    return $ergebnis->fetch_assoc();
}

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

function pruefeEingeloggt($sicherheitsstufe)
{
    if ($sicherheitsstufe <= 0) {
        header("Location: index.php");
        exit;
    }
}

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
        return htmlspecialchars($datum, ENT_QUOTES, 'UTF-8');
    }
}

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
        return null;
    }

    $neuer_bildname = time() . '_' . rand(1000000, 9999999) . '.' . $erweiterung;

    if (move_uploaded_file($datei_tmp, __DIR__ . '/bilder/' . $neuer_bildname)) {
        return $neuer_bildname;
    }

    return null;
}

function kommentareTabelleExistiert(mysqli $datenbankverbindung): bool
{
    $ergebnis = $datenbankverbindung->query("SHOW TABLES LIKE 'kommentare'");
    return $ergebnis && $ergebnis->num_rows > 0;
}

function holeKommentare(mysqli $datenbankverbindung, int $beitrag_id): array
{
    $anweisung = $datenbankverbindung->prepare(
        "SELECT kommentare.inhalt, kommentare.datum, benutzer.benutzername
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

?>
