<?php
require_once 'db.php';
require_once 'session.php';

/** @var PDO $pdo */

$fehler = '';
if (isset($_GET['aktion']) && $_GET['aktion'] === 'abmelden') {
    loescheBenutzerSession();
    header('Location: index.php');
    exit;
}

if (istAngemeldet()) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['anmelden'])) {
    $benutzername = trim($_POST['benutzername']);
    $passwort_raw = $_POST['passwort'];

    if ($benutzername === '' || $passwort_raw === '') {
        $fehler = 'Bitte fülle alle Felder aus.';
    } else {
        $stmt = $pdo->prepare('SELECT id, passwort FROM autoren WHERE benutzername = ?');
        $stmt->execute([$benutzername]);
        $autor = $stmt->fetch();

        if ($autor && password_verify($passwort_raw, $autor['passwort'])) {
            setzeBenutzerInSession($benutzername, (int) $autor['id']);
            header('Location: index.php');
            exit;
        }

        $fehler = 'Benutzername oder Passwort ist falsch.';
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Anmelden - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if ($fehler !== ''): ?>
        <p class="meldung"><?php echo sichereAusgabe($fehler); ?></p>
    <?php endif; ?>
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
    </form>
    <p>Noch keinen Account? <a href="registrieren.php">Registrieren</a></p>
</div>
</body>
</html>