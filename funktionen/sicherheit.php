<?php

function pruefeEingeloggt($sicherheitsstufe)
{
    if ($sicherheitsstufe <= 0) {
        header('Location: ' . projektPfad('index.php'));
        exit;
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

function istKommentator($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)
{
    if (!$kommentar) {
        return false;
    }
    if ($sicherheitsstufe == 2) {
        return true;
    }
    return $kommentar['benutzer_id'] == $aktueller_benutzer_id;
}
