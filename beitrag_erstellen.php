<?php
require_once 'db.php';
require_once 'session.php';

/** @var PDO $pdo */

$meldung = '';
if (!istAngemeldet()) {
    $meldung = 'Nur angemeldete Benutzer können Beiträge erstellen. Bitte melde dich an oder registriere dich.';
}

if (istAngemeldet() && isset($_POST['submit_post'])) {
    $titel = trim($_POST['titel']);
    $inhalt = trim($_POST['inhalt']);
    $benuter_id = holeAutorId();

    if ($titel === '' || $inhalt === '') {
        $meldung = 'Bitte trage einen Titel und einen Inhalt ein.';
    } elseif ($benuter_id === null) {
        $meldung = 'Es ist ein Fehler aufgetreten. Bitte melde dich erneut an.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO beitraege(titel, inhalt, benuter_id) VALUES (?,?,?)");
        if ($stmt->execute([$titel, $inhalt, $benuter_id])) {
            header('Location: index.php');
            exit;
        }
        $meldung = 'Der Beitrag konnte nicht gespeichert werden.';
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Neuer Beitrag - Pflanzenblog</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<div class="container">
    <a href="index.php">⬅ Zurück zur Übersicht</a>
    <h2>Neuen Beitrag erstellen</h2>

    <?php if ($meldung !== ''): ?>
        <p class="meldung"><?php echo sichereAusgabe($meldung); ?></p>
    <?php endif; ?>

    <?php if (istAngemeldet()): ?>
        <form method="post" action="beitrag_erstellen.php">
            <div class="input-group">
                <label for="titel">Titel:</label>
                <input type="text" name="titel" id="titel" placeholder="Titel" required>
            </div>
            <div class="input-group">
                <label for="inhalt">Inhalt:</label>
                <textarea name="inhalt" id="inhalt" placeholder="Inhalt" required></textarea>
            </div>
            <button type="submit" name="submit_post">Beitrag veröffentlichen</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Zum Login</a> oder <a href="registrieren.php">Registrieren</a></p>
    <?php endif; ?>
</div>
</body>
</html>
