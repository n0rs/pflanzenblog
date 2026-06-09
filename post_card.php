<?php
/** @var array $beitrag */
/** @var int|null $aktueller_benutzer_id */
/** @var int $sicherheitsstufe */
/** @var mysqli $datenbankverbindung */
/** @var bool $kommentareTabelleVorhanden */
?>
<div class="post-card" id="post-<?php echo $beitrag['id']; ?>">
    <?php // Beitragstitel und Bearbeiten/Löschen nur für berechtigte Benutzer anzeigen ?>
    <h2><?php echo e($beitrag['titel']); ?></h2>
    <?php if (istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
        <a href="beitrag_bearbeiten.php?id=<?php echo $beitrag['id']; ?>">Bearbeiten</a>
        <a href="beitrag_loeschen.php?id=<?php echo $beitrag['id']; ?>">Löschen</a>
    <?php endif; ?>
    <?php if (!empty($beitrag['bild'])): ?>
        <?php // Zeigt ein Beitragsbild an, falls eines hochgeladen wurde ?>
        <div class="post-bild">
            <img src="bilder/<?php echo e($beitrag['bild']); ?>" alt="Bild zum Beitrag: <?php echo htmlspecialchars($beitrag['titel']); ?>">
        </div>
    <?php endif; ?>
    <?php // Der eigentliche Beitragstext, Zeilenumbrüche mit nl2br erhalten ?>
    <p><?php echo nl2br(e($beitrag['inhalt'])); ?></p>
    <?php // Zusätzliche Pflanzeninformationen nur anzeigen, wenn mindestens eines der Attribute vorhanden ist ?>
    <?php if (!empty($beitrag['botanischer_name']) || !empty($beitrag['standort']) || !empty($beitrag['bewasserung']) || !empty($beitrag['lichtmenge']) || !empty($beitrag['winterhart']) || !empty($beitrag['schwierigkeitsgrad'])): ?>
        <div class="pflanzen-details">
            <h3>Pflanzen-Details</h3>
            <?php if (!empty($beitrag['botanischer_name'])): ?>
                <p><strong>Botanischer Name:</strong> <?php echo e($beitrag['botanischer_name']); ?></p>
            <?php endif; ?>
            <?php if (!empty($beitrag['standort'])): ?>
                <p><strong>Standort:</strong> <?php echo e($beitrag['standort']); ?></p>
            <?php endif; ?>
            <?php if (!empty($beitrag['bewasserung'])): ?>
                <p><strong>Bewässerung:</strong> <?php echo e(ucfirst($beitrag['bewasserung'])); ?></p>
            <?php endif; ?>
            <?php if (!empty($beitrag['lichtmenge'])): ?>
                <p><strong>Lichtmenge:</strong> <?php echo e(ucfirst($beitrag['lichtmenge'])); ?></p>
            <?php endif; ?>
            <?php if (!empty($beitrag['winterhart'])): ?>
                <p><strong>Winterhart:</strong> <?php echo e($beitrag['winterhart']); ?></p>
            <?php endif; ?>
            <?php if (!empty($beitrag['schwierigkeitsgrad'])): ?>
                <p><strong>Schwierigkeitsgrad:</strong> <?php echo e(ucfirst($beitrag['schwierigkeitsgrad'])); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php // Veröffentlichungsdatum und Autorenname anzeigen ?>
    <div class="metadaten">
        <small>Veröffentlicht am: <?php echo formatDate($beitrag['datum']); ?></small><br>
        <small>Autor: <strong><?php echo e(isset($beitrag['benutzer_benutzername']) ? $beitrag['benutzer_benutzername'] : 'Gast'); ?></strong></small>
    </div>

    <?php // Kommentarbereich nur anzeigen, wenn die Tabelle für Kommentare vorhanden ist ?>
    <?php if ($kommentareTabelleVorhanden): ?>
        <?php $kommentare = holeKommentare($datenbankverbindung, (int)$beitrag['id']); ?>
        <div class="comments">
            <h3>Kommentare</h3>
            <?php // Existierende Kommentare rendern oder Hinweis anzeigen, wenn noch keine Kommentare vorhanden sind ?>
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

            <?php // Formular zum Hinzufügen eines neuen Kommentars nur für angemeldete Benutzer anzeigen ?>
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
