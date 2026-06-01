<?php
session_start();
require_once 'db.php';
/** @var mysqli $mysqli */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

if(isset($_POST['anmelden'])) {
    $benutzername = $_POST['benutzername'];
    $passwort = $_POST['passwort'];

    $anweisung = $mysqli->prepare("SELECT * FROM `benutzer` WHERE `benutzername`=?");
    $anweisung->bind_param('s', $benutzername);
    $anweisung->execute();

    $benutzer = $anweisung->get_result()->fetch_assoc();

    if ($benutzer && password_verify($passwort, $benutzer['passwort'])) {
        $_SESSION['benutzer_id'] = $benutzer['id'];
        $_SESSION['sicherheitsstufe'] = $benutzer['sicherheitsstufe'];
        $_SESSION['benutzername'] = $benutzer['benutzername'];
        header("Location: index.php");
        exit;
    } else {
        var_dump($benutzer);
        echo "Benutzername oder Passwort sind nicht korrekt";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
    <body>
        <div class="login-container">
            <header>
                <h1>🌿 Mein Pflanzenblog</h1>
                <nav>
                    <a href="index.php">Start</a>
                    <?php if ($sicherheitsstufe >= 1): ?>
                        <a href="beitrag_erstellen.php">Neuer Beitrag</a>
                    <?php endif; ?>
                    <?php if ($sicherheitsstufe == 0): ?>
                        <a href="registrieren.php">Registrieren</a>
                    <?php endif; ?>
                    <?php if ($sicherheitsstufe >= 1): ?>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                    <a href="ueber_uns.php">Über uns</a>
                </nav>
            </header>
            <main>
                <h2>Login</h2>
                <form action="login.php" method="POST">
                    <div class="input-group">
                        <label>Benutzername:</label>
                        <input type="text" name="benutzername" required>
                    </div>

                    <div class="input-group">
                        <label>Passwort:</label>
                        <input type="password" name="passwort" required>
                    </div>
                    <button type="submit" name="anmelden">Anmelden</button>
                    <p>Noch kein Konto? <a href="registrieren.php">Zur Registrierung</a></p>
                </form>
            </main>
            <footer>
                <p><a href="impressum.php">Impressum</a></p>
                <p>© 2026 Pflanzenblog </p>
            </footer>
        </div>
    </body>
</html>
