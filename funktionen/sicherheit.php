<?php

function pruefeEingeloggt($sicherheitsstufe)
{
    if ($sicherheitsstufe <= 0) {
        header('Location: ' . projektPfad('index.php'));
        exit;
    }
}

function istEigentuemer($datensatz, $aktueller_benutzer_id, $sicherheitsstufe): bool
{
    if (!$datensatz) {
        return false;
    }
    if ($sicherheitsstufe == 2) {
        return true;
    }
    if ($aktueller_benutzer_id === null || !isset($datensatz['benutzer_id'])) {
        return false;
    }

    return (int)$datensatz['benutzer_id'] === (int)$aktueller_benutzer_id;
}
