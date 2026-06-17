<?php
/** @var array $beitrag */
/** @var int|null $aktueller_benutzer_id */
/** @var int $sicherheitsstufe */
/** @var mysqli $datenbankverbindung */
/** @var bool $kommentareTabelleVorhanden */
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
    <?php if (istEigentuemer($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
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
    <?php if ((isset($detailansicht) && $detailansicht === true) || mb_strlen($beitrag['inhalt']) <= 300): ?>
        <p><?php echo nl2br(e($beitrag['inhalt'])); ?></p>
    <?php else: ?>
        <p>
            <?php echo nl2br(e(mb_substr($beitrag['inhalt'], 0, 300))) . "..."; ?>
            <br>
            <a href="<?php echo projektPfad('beitraege/beitrag_detail.php?id=' . (int)$beitrag['id']); ?>" class="weiterlesen-link">Weiterlesen ➔</a>
        </p>
    <?php endif; ?>

    <?php // 4. PFLANZEN-DETAILS ?>
    <?php zeigePflanzenDetails($beitrag); ?>

    <?php // 5. METADATEN ?>
    <div class="metadaten">
        <small>Veröffentlicht am: <?php echo formatDate($beitrag['datum']); ?></small><br>
        <small>Autor: <strong><?php echo e(isset($beitrag['benutzer_benutzername']) ? $beitrag['benutzer_benutzername'] : 'Gast'); ?></strong></small>
    </div>

    <?php //KOMMENTARBEREICH ?>
   <?php include dirname(__DIR__) . '/kommentare/kommentarbereich.php'; ?>

</div>
