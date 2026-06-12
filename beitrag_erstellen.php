<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

// Nur eingeloggte Benutzer dürfen neue Beiträge anlegen
pruefeEingeloggt($sicherheitsstufe);


$meldung = '';
$titel = '';
$inhalt = '';
$botanischer_name = '';
$standort = '';
$bewasserung = 'mittel';
$lichtmenge = 'mittel';
$winterhart = '';
$schwierigkeitsgrad = 'mittel';

if (isset($_POST['submit_post'])) {
    $titel = trim($_POST['titel'] ?? '');
    $inhalt = trim($_POST['inhalt'] ?? '');
    $botanischer_name = trim($_POST['botanischer_name'] ?? '');
    $standort = trim($_POST['standort'] ?? '');
    $bewasserung = trim($_POST['bewasserung'] ?? '');
    $lichtmenge = trim($_POST['lichtmenge'] ?? '');
    $winterhart = trim($_POST['winterhart'] ?? '');
    $schwierigkeitsgrad = trim($_POST['schwierigkeitsgrad'] ?? 'mittel');
    $benutzer_id = $_SESSION['benutzer_id'];

    if ($titel === '' || $inhalt === '' || $bewasserung === '' || $lichtmenge === '') {
        $meldung = 'Bitte geben Sie Titel, Inhalt, Bewässerung und Lichtmenge ein.';
    } else {
        // Bild hochladen, wenn eines ausgewählt wurde
        $bild_dateiname = uploadBild($_FILES['beitrag_bild']);

        if (isset($_FILES['beitrag_bild']) && $_FILES['beitrag_bild']['error'] !== UPLOAD_ERR_NO_FILE && $bild_dateiname === null) {
            $meldung = 'Ungültiges Bildformat. Bitte JPG, PNG oder GIF verwenden.';
        } else {
            // Transaktion verwenden, damit Beitrag und zugehörige Pflanzeninfos konsistent gespeichert werden
            $datenbankverbindung->begin_transaction();
            try {
                // Beitragstext in die Tabelle beitraege einfügen
                $anweisung = $datenbankverbindung->prepare("INSERT INTO beitraege(titel, inhalt, benutzer_id, bild) VALUES (?,?,?,?)");
                $anweisung->bind_param('ssis', $titel, $inhalt, $benutzer_id, $bild_dateiname);
                $anweisung->execute();

                // ID des neu erstellten Beitrags für die zugehörige Pflanzeninformation verwenden
                $beitrag_id = $datenbankverbindung->insert_id;
                $pflanzenAnweisung = $datenbankverbindung->prepare(
                    "INSERT INTO pflanzen (beitrag_id, botanischer_name, standort, bewasserung, lichtmenge, winterhart, schwierigkeitsgrad)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $pflanzenAnweisung->bind_param(
                    'issssss',
                    $beitrag_id,
                    $botanischer_name,
                    $standort,
                    $bewasserung,
                    $lichtmenge,
                    $winterhart,
                    $schwierigkeitsgrad
                );
                $pflanzenAnweisung->execute();
                $datenbankverbindung->commit();

                header("Location: index.php");
                exit;
            } catch (Throwable $e) {
                $datenbankverbindung->rollback();
                $meldung = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
            }
        }
    }
}
?>

<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Erstelle einen neuen Pflanzenblog-Beitrag mit Titel, Inhalt und optionalem Bild.">
    <title>Neuen Beitrag erstellen - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="container">

    <?php include 'header.php'; ?>

    <main>
        <h2>Beitrag erstellen</h2>
        <?php if ($meldung !== ''): ?>
            <p class="message error"><?php echo e($meldung, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="post" action="beitrag_erstellen.php" enctype="multipart/form-data">
            <div class="zeile">
                <div>
                    <label for="titel">Titel:</label>
                    <input type="text" name="titel" id="titel" placeholder="Titel" value="<?php echo e($titel, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label for="botanischer_name">Botanischer Name (optional):</label>
                    <input type="text" name="botanischer_name" id="botanischer_name" placeholder="z. B. Ficus lyrata" value="<?php echo htmlspecialchars($botanischer_name, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label for="standort">Standort (optional):</label>
                    <input type="text" name="standort" id="standort" placeholder="z. B. Wohnzimmer, Balkon" value="<?php echo htmlspecialchars($standort, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label for="bewasserung">Bewässerung:</label>
                    <select name="bewasserung" id="bewasserung">
                        <option value="wenig" <?php echo $bewasserung === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                        <option value="mittel" <?php echo $bewasserung === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                        <option value="viel" <?php echo $bewasserung === 'viel' ? 'selected' : ''; ?>>Viel</option>
                    </select>
                </div>
                <div>
                    <label for="lichtmenge">Lichtmenge:</label>
                    <select name="lichtmenge" id="lichtmenge">
                        <option value="wenig" <?php echo $lichtmenge === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                        <option value="mittel" <?php echo $lichtmenge === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                        <option value="viel" <?php echo $lichtmenge === 'viel' ? 'selected' : ''; ?>>Viel</option>
                    </select>
                </div>
                <div>
                    <label for="winterhart">Winterhart (optional):</label>
                    <input type="text" name="winterhart" id="winterhart" placeholder="z. B. frosthart, nicht frosthart" value="<?php echo htmlspecialchars($winterhart, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label for="schwierigkeitsgrad">Schwierigkeitsgrad (optional):</label>
                    <select name="schwierigkeitsgrad" id="schwierigkeitsgrad">
                        <option value="einfach" <?php echo $schwierigkeitsgrad === 'einfach' ? 'selected' : ''; ?>>Einfach</option>
                        <option value="mittel" <?php echo $schwierigkeitsgrad === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                        <option value="anspruchsvoll" <?php echo $schwierigkeitsgrad === 'anspruchsvoll' ? 'selected' : ''; ?>>Anspruchsvoll</option>
                    </select>
                </div>
                <div>
                    <label for ="inhalt">Inhalt:</label>
                    <textarea name="inhalt" id="inhalt" placeholder="Inhalt"><?php echo e($inhalt, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <div class="bild-hochladen">
                    <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
                    <input type="file" name="beitrag_bild"  id="beitrag_bild">
                </div>
                <div>
                    <input type="submit" name="submit_post" id="submit_post">
                </div>
            </div>
        </form>
    </main>
    <?php include 'footer.php'; ?>
</div>
</body>
