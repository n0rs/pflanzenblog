<?php
require_once 'db.php';
/** @var PDO $pdo */

if (isset($_POST['registrieren'])) {
    $benutzername = $_POST['benutzername'];
    $passwort_raw = $_POST['passwort'];

    // 1. Passwort sicher verschlüsseln
    $passwort_hash = password_hash($passwort_raw, PASSWORD_DEFAULT);

    // 2. In die Datenbank schreiben
    $stmt = $pdo->prepare("INSERT INTO autoren (benutzername, passwort) VALUES (?, ?)");

    try {
        $stmt->execute([$benutzername, $passwort_hash]);
        echo "Registrierung erfolgreich <a href='login.php'>Hier einloggen</a>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Fehlercode für "Duplicate Entry"
            echo "Fehler: Dieser Benutzername ist bereits vergeben.";
        } else {
            echo "Ein Fehler ist aufgetreten: " . $e->getMessage();
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
</div>
</body>
</html>
