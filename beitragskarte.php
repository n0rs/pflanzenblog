<?php
/** @var array $beitrag */
/** @var int|null $aktueller_benutzer_id */
/** @var int $sicherheitsstufe */
/** @var mysqli $datenbankverbindung */
/** @var bool $kommentareTabelleVorhanden */
?>
<div class="beitrags-karte" id="post-<?php echo $beitrag['id']; ?>">

    <?php // 1. BEITRAGSTITEL & AUTOR-AKTIONEN ?>
    <h2><?php echo e($beitrag['titel']); ?></h2>
    <?php if (istAutor($beitrag, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
    <div class="beitrag-aktionen">
        <a href="beitrag_bearbeiten.php?id=<?php echo $beitrag['id']; ?>">
            <img src="<?php echo e(assetPath('icons/pencil.svg')); ?>" alt="Bearbeiten" class="icon" title="Bearbeiten">
        </a>
        <a href="beitrag_loeschen.php?id=<?php echo $beitrag['id']; ?>"
           onclick="return confirm('Beitrag \'<?php echo e($beitrag['titel']); ?>\' unwiderruflich löschen?');">
            <img src="<?php echo e(assetPath('icons/trash.svg')); ?>" alt="Löschen" class="icon" title="Löschen">
        </a>
    </div>
    <?php endif; ?>

    <?php // 2. BEITRAGSBILD ?>
    <?php if (!empty($beitrag['bild'])): ?>
        <div class="beitrags-bild">
            <img src="bilder/<?php echo e($beitrag['bild']); ?>" alt="Bild zum Beitrag: <?php echo e($beitrag['titel']); ?>">
        </div>
    <?php endif; ?>

    <?php // 3. DYNAMISCHE TEXTKÜRZUNG ?>
    <?php if ((isset($detailansicht) && $detailansicht === true) || mb_strlen($beitrag['inhalt']) <= 300): ?>
        <p><?php echo nl2br(e($beitrag['inhalt'])); ?></p>
    <?php else: ?>
        <p>
            <?php echo nl2br(e(mb_substr($beitrag['inhalt'], 0, 300))) . "..."; ?>
            <br>
            <a href="beitrag_detail.php?id=<?php echo $beitrag['id']; ?>" class="weiterlesen-link">Weiterlesen ➔</a>
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
   <?php include 'kommentarbereich.php'; ?>

</div>
