<?php

function holeBeitrag(PDO $pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM beitraege WHERE id = ?");
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
}

function eingeloggtCheck($sicherheitsstufe) {
    if ($sicherheitsstufe <= 0) {
        header("Location: index.php");
        exit;
    }
}

function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe) {
    // wenn der beitrag  nicht existiert
    if (!$beitrag) {
        return false;
    }
    // Admin
    if ($sicherheitsstufe == 2) {
        return true;
    }
    return $beitrag['benutzer_id'] == $aktueller_benutzer_id;
}

function uploadBild($file_input) {
    if (isset($file_input) && $file_input['error'] === UPLOAD_ERR_OK) {
        $datei_name = $file_input['name'];
        $datei_tmp = $file_input['tmp_name'];

        $fragmente = explode(".", $datei_name);
        $erweiterung = end($fragmente); //dateiendung
        $neuer_bildname = time() . "_" . rand(1000000, 9999999) . "." . $erweiterung;

        if (move_uploaded_file($datei_tmp, "bilder/" . $neuer_bildname)) {
            return $neuer_bildname; // Erfolg: Name zurückgeben
        }
    }
    return null;
}

?>
