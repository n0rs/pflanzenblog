<?php

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

    if (move_uploaded_file($datei_tmp, dirname(__DIR__) . '/bilder/' . $neuer_bildname)) {
        return $neuer_bildname;
    }

    return null;
}
