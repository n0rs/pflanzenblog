<?php

function holeBeitrag(mysqli $datenbank, int $id)
{
    $anweisung = $datenbank->prepare("SELECT * FROM beitraege WHERE id = ?");
    $anweisung->bind_param('i', $id);
    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    return $ergebnis->fetch_assoc();
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
    if (isset($datei_input) && $datei_input['error'] === UPLOAD_ERR_OK) {
        $datei_name = $datei_input['name'];
        $datei_tmp = $datei_input['tmp_name'];

        $fragmente = explode(".", $datei_name);
        $erweiterung = end($fragmente);
        $neuer_bildname = time() . "_" . rand(1000000, 9999999) . "." . $erweiterung;

        if (move_uploaded_file($datei_tmp, "bilder/" . $neuer_bildname)) {
            return $neuer_bildname;
        }
    }
    return null;
}

?>
