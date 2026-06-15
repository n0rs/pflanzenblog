<?php
session_start();
require_once 'db.php';
require_once 'funktionen.php';
/** @var mysqli $datenbankverbindung */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

if (isset($_SESSION['benutzer_id'])) {
    header('Location: index.php');
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
            $meldung_type = 'success';
            $meldung = 'Registrierung erfolgreich. <a href="login.php">Hier einloggen</a>';
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
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="container">

            <?php include 'kopfzeile.php'; ?>

            <main>
                <h2>Neuen Account erstellen</h2>
                <?php if ($meldung !== ''): ?>
                    <p class="message <?php echo $meldung_type; ?>">
                        <?php echo $meldung_type === 'success' ? $meldung : e($meldung); ?>
                    </p>
                <?php endif; ?>
                <form action="registrieren.php" method="POST">
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
                <p>Schon ein Konto? <a href="login.php">Zum Login</a></p>
            </main>
            <?php include 'fusszeile.php'; ?>
        </div>
    </body>
</html>
