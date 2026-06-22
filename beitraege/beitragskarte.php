<?php
/** @var array $beitrag */
/** @var int|null $aktueller_benutzer_id */
/** @var int $sicherheitsstufe */
/** @var mysqli $datenbankverbindung */
?>
<div class="beitrags-karte <?php echo (isset($detailansicht) && $detailansicht === true) ? 'detailansicht' : ''; ?>" id="post-<?php echo $beitrag['id']; ?>">

    <?php // UNSICHTBARER HAUPTLINK FÜR DIE GANZE KARTE (nur in der Übersicht) ?>
    <?php if (!isset($detailansicht) || $detailansicht !== true): ?>
        <a href="<?php echo projektPfad('beitraege/beitrag_detail.php?id=' . (int)$beitrag['id']); ?>"
           style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"
           aria-label="Weiterlesen: <?php echo e($beitrag['titel']); ?>"></a>
    <?php endif; ?>

    <?php // 1. BEITRAGSTITEL & AUTOR-AKTIONEN ?>
    <h2><?php echo e($beitrag['titel']); ?></h2>
    <?php if (istEigentuemer($beitrag, $aktueller_benutzer_id, $sicherheitsstufe) && (isset($detailansicht) && $detailansicht === true)): ?>
    <div class="beitrag-aktionen">
        <a href="<?php echo projektPfad('beitraege/beitrag_bearbeiten.php?id=' . (int)$beitrag['id']); ?>">
            <?php echo inlineIcon('pencil.svg', ['class' => 'icon', 'role' => 'img', 'aria-label' => 'Bearbeiten', 'title' => 'Bearbeiten']); ?>
        </a>
        <a href="<?php echo projektPfad('beitraege/beitrag_loeschen.php?id=' . (int)$beitrag['id']); ?>"
           onclick="return confirm('Beitrag \'<?php echo e($beitrag['titel']); ?>\' unwiderruflich löschen?');">
            <?php echo inlineIcon('trash.svg', ['class' => 'icon', 'role' => 'img', 'aria-label' => 'Löschen', 'title' => 'Löschen']); ?>
        </a>
    </div>
    <?php endif; ?>

    <?php // 2. BEITRAGSBILD ?>
    <?php if (!empty($beitrag['bild'])): ?>
        <div class="beitrags-bild">
            <img src="<?php echo projektPfad('bilder/' . e($beitrag['bild'])); ?>" alt="Bild zum Beitrag: <?php echo e($beitrag['titel']); ?>">
        </div>
    <?php endif; ?>

    <?php // 3. DYNAMISCHE TEXTKÜRZUNG ?>
    <?php
    $desktop_limit = 300;
    $mobile_limit = 120; // Auf dem Handy kürzen wir früher (z. B. nach 120 Zeichen)
    $inhalt = $beitrag['inhalt'];
    $ist_detail = (isset($detailansicht) && $detailansicht === true);
    ?>

    <?php if ($ist_detail): ?>
        <p><?php echo nl2br(e($inhalt)); ?></p>
    <?php else: ?>

        <div class="text-desktop">
            <?php if (mb_strlen($inhalt) <= $desktop_limit): ?>
                <p><?php echo nl2br(e($inhalt)); ?></p>
            <?php else: ?>
                <p>
                    <?php echo nl2br(e(mb_substr($inhalt, 0, $desktop_limit))) . "..."; ?>
                    <br>
                    <a href="<?php echo projektPfad('beitraege/beitrag_detail.php?id=' . (int)$beitrag['id']); ?>" class="weiterlesen-link">Weiterlesen ➔</a>
                </p>
            <?php endif; ?>
        </div>

        <div class="text-mobile">
            <?php if (mb_strlen($inhalt) <= $mobile_limit): ?>
                <p><?php echo nl2br(e($inhalt)); ?></p>
            <?php else: ?>
                <p>
                    <?php echo nl2br(e(mb_substr($inhalt, 0, $mobile_limit))) . "..."; ?>
                    <br>
                    <a href="<?php echo projektPfad('beitraege/beitrag_detail.php?id=' . (int)$beitrag['id']); ?>" class="weiterlesen-link">Weiterlesen ➔</a>
                </p>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    <?php // 4. PFLANZEN-DETAILS ?>
    <?php zeigePflanzenDetails($beitrag); ?>

    <?php // 5. METADATEN ?>
    <div class="metadaten">
        <small>Veröffentlicht am: <?php echo formatDate($beitrag['datum']); ?></small><br>
        <small>Autor: <strong><?php echo e(isset($beitrag['benutzer_benutzername']) ? $beitrag['benutzer_benutzername'] : 'Gast'); ?></strong></small>
    </div>

    <?php //KOMMENTARBEREICH ?>
    <?php if ($ist_detail): ?>
        <?php include dirname(__DIR__) . '/kommentare/kommentarbereich.php'; ?>
    <?php endif; ?>

</div>
