<?php
require_once 'funktionen.php';
/** @var array $beitrag */
/** @var int|null $aktueller_benutzer_id */
/** @var int $sicherheitsstufe */
/** @var mysqli $datenbankverbindung */
/** @var bool $kommentareTabelleVorhanden */

$kommentare = holeKommentare($datenbankverbindung, (int)$beitrag['id']);
$kommentarBaum = baueKommentarBaum($kommentare);
$istDetailseite = isset($detailansicht) && $detailansicht === true;
?>

<details class="ausklappen-box" title="Kommentare" <?php echo $istDetailseite ? 'open' : ''; ?>>
    <summary>Kommentare</summary>

    <?php if (!empty($kommentarBaum)): ?>
        <?php foreach ($kommentarBaum as $kommentar): ?>
            <?php zeigeKommentarMitAntworten(
                $kommentar,
                $beitrag,
                $aktueller_benutzer_id,
                $sicherheitsstufe,
                $istDetailseite
            ); ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Es gibt noch keine Kommentare.</p>
    <?php endif; ?>

    <?php if ($sicherheitsstufe >= 1): ?>
        <form action="kommentar_erstellen.php?beitrag_id=<?php echo $beitrag['id']; ?>"
            method="POST"
            class="comment-form">
            <input type="hidden" name="kom_id" value="">
            <label for="kommentar_<?php echo $beitrag['id']; ?>">Kommentar hinzufügen</label>
            <textarea id="kommentar_<?php echo $beitrag['id']; ?>" name="inhalt" required></textarea>
            <button type="submit" name="kommentar_submit">
                <img src="icons/send.svg" alt="Senden" class="icon-button">
                <span class="text-button">Kommentieren</span>
            </button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Einloggen</a>, um zu kommentieren.</p>
    <?php endif; ?>
</details>