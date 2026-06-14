<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
$aktueller_benutzer_id = isset($_SESSION['benutzer_id']) ? $_SESSION['benutzer_id'] : null;

pruefeEingeloggt($sicherheitsstufe);

$beitrag_id = isset($_GET['beitrag_id']) ? (int)$_GET['beitrag_id'] : 0;
if ($beitrag_id <= 0) {
    header('Location: index.php');
    exit;
}

if (!kommentareTabelleExistiert($datenbankverbindung)) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inhalt = trim($_POST['inhalt'] ?? '');
    if ($inhalt === '') {
        $_SESSION['message'] = 'Bitte geben Sie einen Kommentar ein.';
        $_SESSION['message_type'] = 'error';
        header("Location: index.php#post-$beitrag_id");
        exit;
    }

    $anweisung = $datenbankverbindung->prepare(
        "INSERT INTO kommentare (beitrag_id, benutzer_id, inhalt, datum) VALUES (?, ?, ?, NOW())"
    );
    $anweisung->bind_param('iis', $beitrag_id, $aktueller_benutzer_id, $inhalt);
    $anweisung->execute();

    sendeToast("Kommentar erstellt");
    header("Location: index.php#post-$beitrag_id");
    exit;
}
?>