<?php
session_start();
require_once __DIR__ . '/funktionen/datenbank.php';
require_once __DIR__ . '/funktionen/laden.php';
/** @var mysqli $datenbankverbindung */

// Wenn bereits eingeloggt, zurück zur Startseite
$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

if (isset($_SESSION['benutzer_id'])) {
    header('Location: index.php');
    exit;
}

$meldung = '';
$benutzername = '';

if(isset($_POST['anmelden'])) {
    $benutzername = trim($_POST['benutzername'] ?? '');
    $passwort = $_POST['passwort'] ?? '';

    // Überprüfung, ob Benutzername und Passwort eingegeben wurden
    if ($benutzername === '' || $passwort === '') {
        $meldung = 'Bitte Benutzername und Passwort eingeben.';
    } else {
        // Benutzername in der Datenbank suchen
        $anweisung = $datenbankverbindung->prepare("SELECT * FROM `benutzer` WHERE `benutzername`=?");
        $anweisung->bind_param('s', $benutzername);
        $anweisung->execute();

        $benutzer = $anweisung->get_result()->fetch_assoc();

        // Passwort-Hash prüfen und Sitzung setzen
        if ($benutzer && password_verify($passwort, $benutzer['passwort'])) {
            $_SESSION['benutzer_id'] = $benutzer['id'];
            $_SESSION['sicherheitsstufe'] = $benutzer['sicherheitsstufe'];
            $_SESSION['benutzername'] = $benutzer['benutzername'];
            header("Location: index.php");
            exit;
        } else {
            $meldung = 'Benutzername oder Passwort sind nicht korrekt.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Melde dich im Pflanzenblog an, um Beiträge zu erstellen und Kommentare zu schreiben.">
    <title>Login - Pflanzenblog</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo projektPfad('icons/favicon.svg'); ?>">
    <link rel="stylesheet" href="stylesheet.css">
</head>
    <body>
        <div class="container">

            <?php include 'kopfzeile.php'; ?>

            <main>
                <h2>Login</h2>
                <?php if ($meldung !== ''): ?>
                    <p class="message error"><?php echo e($meldung); ?></p>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="eingabe-gruppe">
                        <label>Benutzername:</label>
                        <input type="text" name="benutzername" required value="<?php echo e($benutzername); ?>">
                    </div>

                    <div class="eingabe-gruppe">
                        <label>Passwort:</label>
                        <input type="password" name="passwort" required>
                    </div>
                    <button type="submit" name="anmelden">Anmelden</button>
                    <p>Noch kein Konto? <a href="registrieren.php">Zur Registrierung</a></p>
                </form>
            </main>
            <?php include 'fusszeile.php'; ?>
        </div>
    </body>
</html>
