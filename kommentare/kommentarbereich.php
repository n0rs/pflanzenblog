<?php
require_once dirname(__DIR__) . '/funktionen/laden.php';
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
        <form action="<?php echo projektPfad('kommentare/kommentar_erstellen.php?beitrag_id=' . (int)$beitrag['id']); ?>"
            method="POST"
            class="kommentar-formular">
            <input type="hidden" name="kom_id" value="">
            <label for="kommentar_<?php echo $beitrag['id']; ?>">Kommentar hinzufügen</label>
            <textarea id="kommentar_<?php echo $beitrag['id']; ?>" name="inhalt" required></textarea>
            <div class="kommentieren">
                <button type="submit" name="kommentar_absenden">
                    <?php echo inlineIcon('send.svg', ['class' => 'icon-button', 'role' => 'img', 'aria-label' => 'Senden', 'title' => 'Senden']); ?>
                    <span class="text-button">Kommentieren</span>
                </button>
            </div>
        </form>
    <?php else: ?>
        <p><a href="<?php echo projektPfad('login.php'); ?>">Einloggen</a>, um zu kommentieren.</p>
    <?php endif; ?>
</details>
