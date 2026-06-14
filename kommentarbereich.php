<?php // 6. KOMMENTARBEREICH

/** @var array $beitrag */
/** @var int|null $aktueller_benutzer_id */
/** @var int $sicherheitsstufe */
/** @var mysqli $datenbankverbindung */
/** @var bool $kommentareTabelleVorhanden */
?>
    <?php $kommentare = holeKommentare($datenbankverbindung, (int)$beitrag['id']); ?>
    <details class="ausklappen-box" title="Kommentare">
        <summary>Kommentare</summary>

        <?php if (!empty($kommentare)): ?>
            <?php foreach ($kommentare as $kommentar): ?>
                <div class="ausklappen-inhalt comment">
                    <p><?php echo nl2br(e($kommentar['inhalt'])); ?></p>
                    <small>Von <strong><?php echo e(isset($kommentar['benutzername']) ? $kommentar['benutzername'] : 'Gast'); ?></strong> am <?php echo formatDate($kommentar['datum']); ?></small>
                    <?php if (istKommentator($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
                        <a href="kommentar_loeschen.php?id=<?php echo $kommentar['id']; ?>"
                           onclick="return confirm('Kommentar wirklich löschen?');">
                            <img src="icons/trash.svg" alt="Löschen" class="icon" title="Löschen">
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Es gibt noch keine Kommentare.</p>
        <?php endif; ?>

        <?php // NEUER KOMMENTAR FORMULAR ?>
        <?php if ($sicherheitsstufe >= 1): ?>
            <form action="kommentar_erstellen.php?beitrag_id=<?php echo $beitrag['id']; ?>" method="POST" class="comment-form">
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
