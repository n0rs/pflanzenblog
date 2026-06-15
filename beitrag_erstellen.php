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
$bewasserung = '';
$lichtmenge = '';
$winterhart = '';
$schwierigkeitsgrad = '';

if (isset($_POST['submit_post'])) {
    $titel = trim($_POST['titel'] ?? '');
    $inhalt = trim($_POST['inhalt'] ?? '');
    $botanischer_name = trim($_POST['botanischer_name'] ?? '');
    $standort = trim($_POST['standort'] ?? '');
    $bewasserung = trim($_POST['bewasserung'] ?? 'mittel');
    $lichtmenge = trim($_POST['lichtmenge'] ?? 'mittel');
    $winterhart = trim($_POST['winterhart'] ?? '');
    $schwierigkeitsgrad = trim($_POST['schwierigkeitsgrad'] ?? 'mittel');
    $benutzer_id = $_SESSION['benutzer_id'];

    if ($titel === '' || $inhalt === '') {
        $meldung = 'Bitte geben Sie einen Titel und Inhalt ein.';
    } else {
        // Bild hochladen, wenn ausgewählt wurde
        $bild_dateiname = uploadBild($_FILES['beitrag_bild']);

        if (isset($_FILES['beitrag_bild']) && $_FILES['beitrag_bild']['error'] !== UPLOAD_ERR_NO_FILE && $bild_dateiname === null) {
            $meldung = "Ungültiges Dateiformat. JPG, PNG und GIF erlaubt.";
        } else {
            try {
                $anweisung = $datenbankverbindung->prepare(
                        "INSERT INTO beitraege (titel, inhalt, benutzer_id, bild, botanischer_name, standort, bewasserung, lichtmenge, winterhart, schwierigkeitsgrad) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $anweisung->bind_param(
                        'ssisssssss',
                        $titel,
                        $inhalt,
                        $benutzer_id,
                        $bild_dateiname,
                        $botanischer_name,
                        $standort,
                        $bewasserung,
                        $lichtmenge,
                        $winterhart,
                        $schwierigkeitsgrad
                );

                if ($anweisung->execute()) {
                    sendeToast("Beitrag erstellt");
                    header("Location: index.php");
                    exit;
                } else {
                    $meldung = 'Datenbank-Fehler beim Speichern: ' . $anweisung->error;
                }
            } catch (Throwable $e) {
                // $meldung = 'Fehler: ' . $e->getMessage();
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
            <p class="message error"><?php echo e($meldung); ?></p>
        <?php endif; ?>
        <form method="post" action="beitrag_erstellen.php" enctype="multipart/form-data">
            <div class="zeile">

                <div>
                    <label for="titel">Titel *</label>
                    <input type="text" name="titel" id="titel" placeholder="Titel des Beitrags" value="<?php echo e($titel); ?>" required>
                </div>

                <div>
                    <label for="inhalt">Inhalt *</label>
                    <textarea name="inhalt" id="inhalt" placeholder="Schreibe hier deinen Blogbeitrag..." required><?php echo e($inhalt); ?></textarea>
                </div>

                <div class="bild-hochladen">
                    <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
                    <input type="file" name="beitrag_bild" id="beitrag_bild">
                    <label for="beitrag_bild" class="icon-box clickable">
                        <img src="icons/camera.svg" alt="Kamera" class="gross-icon">
                        <span id="upload-text">Foto auswählen</span>
                    </label>
                    <button type="button" id="reset-bild" style="display: none;">
                        ❌ Auswahl aufheben
                    </button>
                </div>

                <details class="ausklappen-box plus">
                    <summary> Details</summary>

                    <div class="ausklappen-inhalt">
                        <div>
                            <label for="botanischer_name">Botanischer Name:</label>
                            <input type="text" name="botanischer_name" id="botanischer_name" placeholder="z. B. Ficus lyrata" value="<?php echo e($botanischer_name); ?>">
                        </div>

                        <div>
                            <label for="standort">Standort:</label>
                            <input type="text" name="standort" id="standort" placeholder="z. B. Wohnzimmer, halbschattig" value="<?php echo e($standort); ?>">
                        </div>

                        <div>
                            <label for="bewasserung">Bewässerung:</label>
                            <select name="bewasserung" id="bewasserung">
                                <option value="" <?php echo $bewasserung === ''; ?>>-</option>
                                <option value="wenig" <?php echo $bewasserung === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                                <option value="mittel" <?php echo $bewasserung === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="viel" <?php echo $bewasserung === 'viel' ? 'selected' : ''; ?>>Viel</option>
                            </select>
                        </div>

                        <div>
                            <label for="lichtmenge">Lichtmenge:</label>
                            <select name="lichtmenge" id="lichtmenge">
                                <option value="" <?php echo $lichtmenge === ''; ?>>-</option>
                                <option value="wenig" <?php echo $lichtmenge === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                                <option value="mittel" <?php echo $lichtmenge === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="viel" <?php echo $lichtmenge === 'viel' ? 'selected' : ''; ?>>Viel</option>
                            </select>
                        </div>

                        <div>
                            <label for="winterhart">Winterhart:</label>
                            <input type="text" name="winterhart" id="winterhart" placeholder="z. B. winterhart, frostempfindlich" value="<?php echo e($winterhart); ?>">
                        </div>

                        <div>
                            <label for="schwierigkeitsgrad">Schwierigkeitsgrad:</label>
                            <select name="schwierigkeitsgrad" id="schwierigkeitsgrad">
                                <option value="" <?php echo $schwierigkeitsgrad === ''; ?>>-</option>
                                <option value="einfach" <?php echo $schwierigkeitsgrad === 'einfach' ? 'selected' : ''; ?>>Einfach</option>
                                <option value="mittel" <?php echo $schwierigkeitsgrad === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="anspruchsvoll" <?php echo $schwierigkeitsgrad === 'anspruchsvoll' ? 'selected' : ''; ?>>Anspruchsvoll</option>
                            </select>
                        </div>
                    </div>
                </details>

                <div class="submit-container">
                    <input type="submit" name="submit_post" id="submit_post" value="Beitrag veröffentlichen">
                </div>
            </div>
        </form>
    </main>
    <?php include 'footer.php'; ?>
</div>
</body>

<script>
    const bildInput = document.getElementById('beitrag_bild');
    const textElement = document.getElementById('upload-text');
    const resetButton = document.getElementById('reset-bild');

    bildInput.addEventListener('change', function(event) {
        const datei = event.target.files[0];
        if (datei) {
            textElement.textContent = "Ausgewählt: " + datei.name;
            resetButton.style.display = "inline-block";
        }
    });

    resetButton.addEventListener('click', function() {
        bildInput.value = "";
        textElement.textContent = "Foto auswählen";
        resetButton.style.display = "none";
    });
</script>