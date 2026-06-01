<?php
require_once 'db.php';
/** @var PDO $pdo */

$sicherheitsstufe = isset($_SESSION['sicherheitsstufe']) ? $_SESSION['sicherheitsstufe'] : 0;

if (isset($_POST['registrieren'])) {
    $benutzername = $_POST['benutzername'];
    $passwort_raw = $_POST['passwort'];

    $passwort_hash = password_hash($passwort_raw, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO benutzer (benutzername, passwort) VALUES (?, ?)");

    try {
        $stmt->execute([$benutzername, $passwort_hash]);
        echo "Registrierung erfolgreich <a href='login.php'>Hier einloggen</a>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
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
        <div class="registrieren-container">

            <?php include 'header.php'; ?>

            <main>
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
            </main>
            <footer>
                <p><a href="#">Impressum</a></p>
                <p>© 2026 Pflanzenblog </p>
            </footer>
        </div>
    </body>
</html>
