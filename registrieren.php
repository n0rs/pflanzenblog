<?php
session_start();
require_once __DIR__ . '/funktionen/datenbank.php';
require_once __DIR__ . '/funktionen/laden.php';

/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

if (isset($_SESSION['benutzer_id'])) {
    header('Location: ' . projektPfad('index.php'));
    exit;
}

$meldung = '';
$meldung_type = 'error';
$benutzername = '';

if (isset($_POST['registrieren'])) {
    $benutzername = trim($_POST['benutzername'] ?? '');
    $passwort_raw = $_POST['passwort'] ?? '';

    if ($benutzername === '' || $passwort_raw === '') {
        $meldung = 'Bitte einen Benutzernamen und ein Passwort eingeben.';
    } elseif (strlen($passwort_raw) < 6) {
        $meldung = 'Das Passwort muss mindestens 6 Zeichen lang sein.';
    } else {
        $passwort_hash = password_hash($passwort_raw, PASSWORD_DEFAULT);

        $anweisung = $datenbankverbindung->prepare("INSERT INTO benutzer (benutzername, passwort) VALUES (?, ?)");
        $anweisung->bind_param('ss', $benutzername, $passwort_hash);

        try {
            $anweisung->execute();
            sendeToast('Registrierung erfolgreich. Du kannst dich jetzt einloggen.');
            header('Location: ' . projektPfad('login.php'));
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $meldung = 'Fehler: Dieser Benutzername ist bereits vergeben.';
            } else {
                $meldung = 'Ein Fehler ist aufgetreten: ' . e($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="Erstelle einen Account im Pflanzenblog und teile deine Gartenbeiträge und Kommentare mit anderen Pflanzenfreunden.">
        <title>Registrieren - Pflanzenblog</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo projektPfad('icons/favicon.svg'); ?>">
        <link rel="stylesheet" href="<?php echo projektPfad('stylesheet.css'); ?>">
    </head>
    <body style="background-image: url('<?php echo e(projektPfad('bilder/hb.jpg')); ?>');">
    <div class="container">

            <?php include __DIR__ . '/kopfzeile.php'; ?>

            <main>
                <h2>Neuen Account erstellen</h2>
                <?php if ($meldung !== ''): ?>
                    <p class="message <?php echo $meldung_type; ?>">
                        <?php echo $meldung_type === 'success' ? $meldung : e($meldung); ?>
                    </p>
                <?php endif; ?>
                <form action="<?php echo projektPfad('registrieren.php'); ?>" method="POST">
                    <div class="eingabe-gruppe">
                        <label>Benutzername:</label>
                        <input type="text" name="benutzername" required value="<?php echo e($benutzername); ?>">
                    </div>

                    <div class="eingabe-gruppe">
                        <label>Passwort:</label>
                        <input type="password" name="passwort" required>
                    </div>
                    <button type="submit" name="registrieren">Konto erstellen</button>
                </form>
                <p>Schon ein Konto? <a href="<?php echo projektPfad('login.php'); ?>">Zum Login</a></p>
            </main>
            <?php include __DIR__ . '/fusszeile.php'; ?>
        </div>
    </body>
</html>
