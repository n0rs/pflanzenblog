<?php
require_once 'db.php';
require_once 'session.php';

/** @var PDO $pdo */

$meldung = '';
if (istAngemeldet()) {
    $meldung = 'Du bist bereits angemeldet als: ' . sichereAusgabe(holeBenutzername());
} elseif (isset($_POST['registrieren'])) {
    $benutzername = trim($_POST['benutzername']);
    $passwort_raw = $_POST['passwort'];

    if ($benutzername === '' || $passwort_raw === '') {
        $meldung = 'Bitte fülle alle Felder aus.';
    } else {
        $passwort_hash = password_hash($passwort_raw, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO autoren (benutzername, passwort) VALUES (?, ?)");

        try {
            $stmt->execute([$benutzername, $passwort_hash]);
            $meldung = 'Registrierung erfolgreich. <a href="login.php">Hier einloggen</a>';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $meldung = 'Fehler: Dieser Benutzername ist bereits vergeben.';
            } else {
                $meldung = 'Ein Fehler ist aufgetreten: ' . sichereAusgabe($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrieren - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="container">
    <h2>Neuen Account erstellen</h2>
    <?php if ($meldung !== ''): ?>
        <p class="meldung"><?php echo $meldung; ?></p>
    <?php endif; ?>

    <?php if (!istAngemeldet()): ?>
        <form action="registrieren.php" method="POST">
            <div class="input-group">
                <label>Benutzername:</label>
                <input type="text" name="benutzername" required>
            </div>

            <div class="input-group">
                <label>Passwort:</label>
                <input type="password" name="passwort" required>
            </div>

            <button type="submit" name="registrieren">Konto erstellen</button>
        </form>
        <p>Schon ein Konto? <a href="login.php">Zum Login</a></p>
    <?php else: ?>
        <p><a href="index.php">Zurück zur Startseite</a></p>
    <?php endif; ?>
</div>
</body>
</html>
