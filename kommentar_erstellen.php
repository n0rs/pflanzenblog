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
    $kom_id = isset($_POST['kom_id']) && $_POST['kom_id'] !== ''
        ? (int)$_POST['kom_id']
        : null;

    if ($inhalt === '') {
        $_SESSION['message'] = 'Bitte geben Sie einen Kommentar ein.';
        $_SESSION['message_type'] = 'error';
        header("Location: beitrag_detail.php?id=$beitrag_id#post-$beitrag_id");
        exit;
    }

    // Wenn es eine Antwort ist, prüfen, ob der Eltern-Kommentar zum selben Beitrag gehört
    if ($kom_id !== null) {
        $pruefung = $datenbankverbindung->prepare(
            "SELECT id FROM kommentare WHERE id = ? AND beitrag_id = ?"
        );
        $pruefung->bind_param('ii', $kom_id, $beitrag_id);
        $pruefung->execute();
        $elternKommentar = $pruefung->get_result()->fetch_assoc();

        if (!$elternKommentar) {
            sendeToast("Ungültiger Antwort-Kommentar.");
            header("Location: beitrag_detail.php?id=$beitrag_id#post-$beitrag_id");
            exit;
        }
    }

    $anweisung = $datenbankverbindung->prepare(
        "INSERT INTO kommentare (beitrag_id, benutzer_id, inhalt, datum, kom_id)
         VALUES (?, ?, ?, NOW(), ?)"
    );

    $anweisung->bind_param('iisi', $beitrag_id, $aktueller_benutzer_id, $inhalt, $kom_id);
    $anweisung->execute();

    sendeToast($kom_id === null ? "Kommentar erstellt" : "Antwort erstellt");
    header("Location: beitrag_detail.php?id=$beitrag_id#post-$beitrag_id");
    exit;
}
?>