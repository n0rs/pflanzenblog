<?php
/** @var array $beitrag */
/** @var int|null $aktueller_benutzer_id */
/** @var int $sicherheitsstufe */
/** @var mysqli $datenbankverbindung */
/** @var bool $kommentareTabelleVorhanden */
?>
<div class="post-card" id="post-<?php echo $beitrag['id']; ?>">

    <?php // 1. BEITRAGSTITEL & AUTOR-AKTIONEN ?>
    <h2><?php echo e($beitrag['titel']); ?></h2>
    <?php if (istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
        <a href="beitrag_bearbeiten.php?id=<?php echo $beitrag['id']; ?>">
            <img src="icons/pencil.svg" alt="Bearbeiten" class="icon">
        </a>
        <a href="beitrag_loeschen.php?id=<?php echo $beitrag['id']; ?>"
           onclick="return confirm('Beitrag \'<?php echo e($beitrag['titel']); ?>\' unwiderruflich löschen?');">
            <img src="icons/trash.svg" alt="Löschen" class="icon">
        </a>
    <?php endif; ?>

    <?php // 2. BEITRAGSBILD ?>
    <?php if (!empty($beitrag['bild'])): ?>
        <div class="post-bild">
            <img src="bilder/<?php echo e($beitrag['bild']); ?>" alt="Bild zum Beitrag: <?php echo htmlspecialchars($beitrag['titel'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
    <?php endif; ?>

    <?php // 3. DYNAMISCHE TEXTKÜRZUNG ?>
    <?php if ((isset($detailansicht) && $detailansicht === true) || mb_strlen($beitrag['inhalt']) <= 150): ?>
        <p><?php echo nl2br(e($beitrag['inhalt'])); ?></p>
    <?php else: ?>
        <p>
            <?php echo nl2br(e(mb_substr($beitrag['inhalt'], 0, 150))) . "..."; ?>
            <br>
            <a href="beitrag_detail.php?id=<?php echo $beitrag['id']; ?>" class="weiterlesen-link" style="color: #2e7d32; font-weight: bold; text-decoration: none;">Weiterlesen ➔</a>
        </p>
    <?php endif; ?>

    <?php // 4. PFLANZEN-DETAILS ?>
    <?php zeigePflanzenDetails($beitrag); ?>
    <?php // 5. METADATEN ?>
    <div class="metadaten">
        <small>Veröffentlicht am: <?php echo formatDate($beitrag['datum']); ?></small><br>
        <small>Autor: <strong><?php echo e(isset($beitrag['benutzer_benutzername']) ? $beitrag['benutzer_benutzername'] : 'Gast'); ?></strong></small>
    </div>

    <?php // 6. KOMMENTARBEREICH ?>
    <?php if ($kommentareTabelleVorhanden): ?>
        <?php $kommentare = holeKommentare($datenbankverbindung, (int)$beitrag['id']); ?>
        <details class="ausklappen-box">
            <summary>Kommentare</summary>

            <?php if (!empty($kommentare)): ?>
                <?php foreach ($kommentare as $kommentar): ?>
                    <div class="ausklappen-inhalt comment">
                        <p><?php echo nl2br(e($kommentar['inhalt'])); ?></p>
                        <small>Von <strong><?php echo e(isset($kommentar['benutzername']) ? $kommentar['benutzername'] : 'Gast'); ?></strong> am <?php echo formatDate($kommentar['datum']); ?></small>
                        <?php if (istKommentator($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
                            <a href="kommentar_loeschen.php?id=<?php echo $kommentar['id']; ?>"
                               onclick="return confirm('Kommentar wirklich löschen?');">
                                <img src="icons/trash.svg" alt="Löschen" class="icon">
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
    <?php endif; ?>
</div>