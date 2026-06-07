<?php
/** @var array $beitrag */
/** @var int|null $aktueller_benutzer_id */
/** @var int $sicherheitsstufe */
/** @var mysqli $datenbankverbindung */
/** @var bool $kommentareTabelleVorhanden */
?>
<div class="post-card" id="post-<?php echo $beitrag['id']; ?>">
    <h2><?php echo e($beitrag['titel']); ?></h2>
    <?php if (istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
        <a href="beitrag_bearbeiten.php?id=<?php echo $beitrag['id']; ?>">Bearbeiten</a>
        <a href="beitrag_loeschen.php?id=<?php echo $beitrag['id']; ?>">Löschen</a>
    <?php endif; ?>
    <?php if (!empty($beitrag['bild'])): ?>
        <div class="post-bild">
            <img src="bilder/<?php echo e($beitrag['bild']); ?>" alt="Bild zum Beitrag: <?php echo htmlspecialchars($beitrag['titel']); ?>">
        </div>
    <?php endif; ?>
    <p><?php echo nl2br(e($beitrag['inhalt'])); ?></p>
    <div class="metadaten">
        <small>Veröffentlicht am: <?php echo formatDate($beitrag['datum']); ?></small><br>
        <small>Autor: <strong><?php echo e(isset($beitrag['benutzer_benutzername']) ? $beitrag['benutzer_benutzername'] : 'Gast'); ?></strong></small>
    </div>

    <?php if ($kommentareTabelleVorhanden): ?>
        <?php $kommentare = holeKommentare($datenbankverbindung, (int)$beitrag['id']); ?>
        <div class="comments">
            <h3>Kommentare</h3>
            <?php if (!empty($kommentare)): ?>
                <?php foreach ($kommentare as $kommentar): ?>
                    <div class="comment">
                        <p><?php echo nl2br(e($kommentar['inhalt'])); ?></p>
                        <small>Von <strong><?php echo e(isset($kommentar['benutzername']) ? $kommentar['benutzername'] : 'Gast'); ?></strong> am <?php echo formatDate($kommentar['datum']); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Es gibt noch keine Kommentare.</p>
            <?php endif; ?>

            <?php if ($sicherheitsstufe >= 1): ?>
                <form action="kommentar_erstellen.php?beitrag_id=<?php echo $beitrag['id']; ?>" method="POST" class="comment-form">
                    <label for="kommentar_<?php echo $beitrag['id']; ?>">Kommentar hinzufügen</label>
                    <textarea id="kommentar_<?php echo $beitrag['id']; ?>" name="inhalt" required></textarea>
                    <button type="submit" name="kommentar_submit">Kommentar senden</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Einloggen</a>, um zu kommentieren.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
