<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;
pruefeEingeloggt($sicherheitsstufe);

$beitrag_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$beitrag_id) {
    sendeToast("Ungültige Beitrags-ID.");
    header("Location: index.php");
    exit;
}

$meldung = '';

try {
    $laden_anweisung = $datenbankverbindung->prepare("SELECT * FROM beitraege WHERE id = ?");
    $laden_anweisung->bind_param('i', $beitrag_id);
    $laden_anweisung->execute();
    $ergebnis = $laden_anweisung->get_result();
    $beitrag = $ergebnis->fetch_assoc();

    if (!$beitrag) {
        sendeToast("Beitrag nicht gefunden.");
        header("Location: index.php");
        exit;
    }

    if ($beitrag['benutzer_id'] !== $_SESSION['benutzer_id'] && $sicherheitsstufe < 2) {
        sendeToast("Nicht berechtigt Beitrag zu bearbeiten.");
        header("Location: index.php");
        exit;
    }
} catch (Throwable $e) {
    sendeToast("Fehler beim Laden des Beitrags.");
    header("Location: index.php");
    exit;
}

$titel = $beitrag['titel'];
$inhalt = $beitrag['inhalt'];
$botanischer_name = $beitrag['botanischer_name'] ?? '';
$standort = $beitrag['standort'] ?? '';
$bewasserung = $beitrag['bewasserung'] ?? '';
$lichtmenge = $beitrag['lichtmenge'] ?? '';
$winterhart = $beitrag['winterhart'] ?? '';
$schwierigkeitsgrad = $beitrag['schwierigkeitsgrad'] ?? '';
$altes_bild = $beitrag['bild'];

if (isset($_POST['submit_update'])) {
    $titel = trim($_POST['titel'] ?? '');
    $inhalt = trim($_POST['inhalt'] ?? '');
    $botanischer_name = trim($_POST['botanischer_name'] ?? '');
    $standort = trim($_POST['standort'] ?? '');
    $bewasserung = $_POST['bewasserung'] !== '' ? trim($_POST['bewasserung']) : null;
    $lichtmenge = $_POST['lichtmenge'] !== '' ? trim($_POST['lichtmenge']) : null;
    $winterhart = trim($_POST['winterhart'] ?? '');
    $schwierigkeitsgrad = $_POST['schwierigkeitsgrad'] !== '' ? trim($_POST['schwierigkeitsgrad']) : null;


    $bild_dateiname = $altes_bild;

    if (isset($_POST['bild_status']) && $_POST['bild_status'] === 'loeschen') {
        $bild_dateiname = null;

        if ($altes_bild && file_exists("uploads/" . $altes_bild)) {
            @unlink("uploads/" . $altes_bild);
        }
    }
    elseif (isset($_FILES['beitrag_bild']) && $_FILES['beitrag_bild']['error'] !== UPLOAD_ERR_NO_FILE) {
        $neues_bild = uploadBild($_FILES['beitrag_bild']);
        if ($neues_bild === null) {
            $meldung = "Ungültiges Dateiformat. JPG, PNG und GIF erlaubt.";
        } else {
            $bild_dateiname = $neues_bild;
            // Altes Bild löschen
            if ($altes_bild && file_exists("uploads/" . $altes_bild)) {
                @unlink("uploads/" . $altes_bild);
            }
        }
    }

        if ($meldung === '') {
            try {
                // UPDATE statt INSERT
                $anweisung = $datenbankverbindung->prepare(
                        "UPDATE beitraege SET 
                        titel = ?, inhalt = ?, bild = ?, botanischer_name = ?, 
                        standort = ?, bewasserung = ?, lichtmenge = ?, 
                        winterhart = ?, schwierigkeitsgrad = ? 
                     WHERE id = ?"
                );

                $anweisung->bind_param(
                        'sssssssssi',
                        $titel,
                        $inhalt,
                        $bild_dateiname,
                        $botanischer_name,
                        $standort,
                        $bewasserung,
                        $lichtmenge,
                        $winterhart,
                        $schwierigkeitsgrad,
                        $beitrag_id
                );

                if ($anweisung->execute()) {
                    sendeToast("Beitrag aktualisiert.");
                    header("Location: index.php#<?php echo $beitrag_id; ?>");
                    exit;
                } else {
                    $meldung = 'Datenbank-Fehler beim Speichern: ' . $anweisung->error;
                }
            } catch (Throwable $e) {
                $meldung = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
            }
        }
}
?>

<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beitrag bearbeiten - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="container">

    <?php include 'kopfzeile.php'; ?>

    <main>
        <div style="margin-bottom: 10px;">
            <a href="index.php#post-<?php echo($beitrag_id) ?>" style="text-decoration: none; color: #2e7d32; font-weight: bold;">
                ⬅ Zurück zur Übersicht
            </a>
        </div>
        <h2>Beitrag bearbeiten</h2>
        <?php if ($meldung !== ''): ?>
            <p class="message error"><?php echo e($meldung); ?></p>
        <?php endif; ?>

        <form method="post" action="beitrag_bearbeiten.php?id=<?php echo $beitrag_id; ?>" enctype="multipart/form-data">
            <div class="zeile">

                <div>
                    <label for="titel">Titel *</label>
                    <input type="text" name="titel" id="titel" value="<?php echo e($titel); ?>" required>
                </div>

                <div>
                    <label for="inhalt">Inhalt *</label>
                    <textarea name="inhalt" id="inhalt" required><?php echo e($inhalt); ?></textarea>
                </div>

                <div class="bild-hochladen">
                    <input type="hidden" name="MAX_FILE_SIZE" value="5000000">

                    <input type="hidden" name="bild_status" id="bild_status" value="behalten">

                    <input type="file" name="beitrag_bild" id="beitrag_bild" style="display: none;">
                    <label for="beitrag_bild" class="icon-box clickable">
                        <img src="<?php echo e(assetPath('icons/camera.svg')); ?>" alt="Kamera" class="gross-icon">
                        <span id="upload-text">
                            <?php echo $altes_bild ? "Bild ändern (Aktuell: $altes_bild)" : "Foto auswählen"; ?>
                        </span>
                    </label>

                    <button type="button" id="reset-bild" style="display: <?php echo $altes_bild ? 'inline-block' : 'none'; ?>;">
                        ❌ Bild entfernen
                    </button>
                </div>

                <details class="ausklappen-box plus">
                    <summary> Details</summary>

                    <div class="ausklappen-inhalt">
                        <div>
                            <label for="botanischer_name">Botanischer Name:</label>
                            <input type="text" name="botanischer_name" id="botanischer_name" value="<?php echo e($botanischer_name); ?>">
                        </div>

                        <div>
                            <label for="standort">Standort:</label>
                            <input type="text" name="standort" id="standort" value="<?php echo e($standort); ?>">
                        </div>

                        <div>
                            <label for="bewasserung">Bewässerung:</label>
                            <select name="bewasserung" id="bewasserung">
                                <option value="" <?php echo $bewasserung === null || $bewasserung === '' ? 'selected' : ''; ?>>-</option>
                                <option value="wenig" <?php echo $bewasserung === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                                <option value="mittel" <?php echo $bewasserung === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="viel" <?php echo $bewasserung === 'viel' ? 'selected' : ''; ?>>Viel</option>
                            </select>
                        </div>

                        <div>
                            <label for="lichtmenge">Lichtmenge:</label>
                            <select name="lichtmenge" id="lichtmenge">
                                <option value="" <?php echo $lichtmenge === null || $lichtmenge === '' ? 'selected' : ''; ?>>-</option>
                                <option value="wenig" <?php echo $lichtmenge === 'wenig' ? 'selected' : ''; ?>>Wenig</option>
                                <option value="mittel" <?php echo $lichtmenge === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="viel" <?php echo $lichtmenge === 'viel' ? 'selected' : ''; ?>>Viel</option>
                            </select>
                        </div>

                        <div>
                            <label for="winterhart">Winterhart:</label>
                            <input type="text" name="winterhart" id="winterhart" value="<?php echo e($winterhart); ?>">
                        </div>

                        <div>
                            <label for="schwierigkeitsgrad">Schwierigkeitsgrad:</label>
                            <select name="schwierigkeitsgrad" id="schwierigkeitsgrad">
                                <option value="" <?php echo $schwierigkeitsgrad === null || $schwierigkeitsgrad === '' ? 'selected' : ''; ?>>-</option>
                                <option value="einfach" <?php echo $schwierigkeitsgrad === 'einfach' ? 'selected' : ''; ?>>Einfach</option>
                                <option value="mittel" <?php echo $schwierigkeitsgrad === 'mittel' ? 'selected' : ''; ?>>Mittel</option>
                                <option value="anspruchsvoll" <?php echo $schwierigkeitsgrad === 'anspruchsvoll' ? 'selected' : ''; ?>>Anspruchsvoll</option>
                            </select>
                        </div>
                    </div>
                </details>

                <div class="absenden-container">
                    <input type="submit" name="submit_update" id="submit_post" value="Änderungen speichern">
                </div>
            </div>
        </form>
    </main>
    <?php include 'fusszeile.php'; ?>
</div>
</body>

<script>
    const bildInput = document.getElementById('beitrag_bild');
    const textElement = document.getElementById('upload-text');
    const resetButton = document.getElementById('reset-bild');
    const bildStatus = document.getElementById('bild_status');

    bildInput.addEventListener('change', function(event) {
        const datei = event.target.files[0];
        if (datei) {
            textElement.textContent = "Ausgewählt: " + datei.name;
            resetButton.style.display = "inline-block";
            bildStatus.value = "behalten";
        }
    });

    resetButton.addEventListener('click', function() {
        bildInput.value = "";
        textElement.textContent = "Foto auswählen";
        resetButton.style.display = "none";

        bildStatus.value = "loeschen";
    });
</script>

</html>
