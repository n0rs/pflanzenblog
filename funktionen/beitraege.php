<?php

function beitragsAuswahlSql(): string
{
    return "beitraege.id,
            beitraege.titel,
            beitraege.inhalt,
            beitraege.benutzer_id,
            beitraege.bild,
            beitraege.botanischer_name,
            beitraege.standort,
            beitraege.bewasserung,
            beitraege.lichtmenge,
            beitraege.winterhart,
            beitraege.schwierigkeitsgrad,
            beitraege.datum,
            benutzer.benutzername AS benutzer_benutzername";
}

function holeBeitrag(mysqli $datenbankverbindung, int $id)
{
    $anweisung = $datenbankverbindung->prepare(
        "SELECT " . beitragsAuswahlSql() . "
         FROM beitraege
         LEFT JOIN benutzer ON beitraege.benutzer_id = benutzer.id
         WHERE beitraege.id = ?"
    );
    $anweisung->bind_param('i', $id);
    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    return $ergebnis->fetch_assoc();
}

function zeigePflanzenDetails(array $beitrag): void
{
    if (!empty($beitrag['botanischer_name']) || !empty($beitrag['standort']) ||
        !empty($beitrag['bewasserung']) || !empty($beitrag['lichtmenge']) ||
        !empty($beitrag['winterhart']) || !empty($beitrag['schwierigkeitsgrad'])):

        ?>
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
    <?php
    endif;
}

function bereinigeBeitragsFilter(array $quelle): array
{
    $erlaubteSortierungen = ['datum_desc', 'datum_asc', 'titel_asc', 'titel_desc'];
    $erlaubteBewasserung = ['wenig', 'mittel', 'viel'];
    $erlaubteLichtmenge = ['wenig', 'mittel', 'viel'];
    $erlaubteSchwierigkeit = ['einfach', 'mittel', 'anspruchsvoll'];
    $erlaubteWinterhart = ['Winterhart', 'Bedingt winterhart', 'Nicht winterhart'];

    $filter = [
        'sortierung' => isset($quelle['sortierung']) ? trim((string)$quelle['sortierung']) : 'datum_desc',
        'bewasserung' => isset($quelle['bewasserung']) ? trim((string)$quelle['bewasserung']) : '',
        'lichtmenge' => isset($quelle['lichtmenge']) ? trim((string)$quelle['lichtmenge']) : '',
        'schwierigkeitsgrad' => isset($quelle['schwierigkeitsgrad']) ? trim((string)$quelle['schwierigkeitsgrad']) : '',
        'winterhart' => isset($quelle['winterhart']) ? trim((string)$quelle['winterhart']) : '',
    ];

    if (!in_array($filter['sortierung'], $erlaubteSortierungen, true)) {
        $filter['sortierung'] = 'datum_desc';
    }
    if (!in_array($filter['bewasserung'], $erlaubteBewasserung, true)) {
        $filter['bewasserung'] = '';
    }
    if (!in_array($filter['lichtmenge'], $erlaubteLichtmenge, true)) {
        $filter['lichtmenge'] = '';
    }
    if (!in_array($filter['schwierigkeitsgrad'], $erlaubteSchwierigkeit, true)) {
        $filter['schwierigkeitsgrad'] = '';
    }
    if (!in_array($filter['winterhart'], $erlaubteWinterhart, true)) {
        $filter['winterhart'] = '';
    }

    return $filter;
}

function beitragsFilterAktiv(array $filter): bool
{
    return $filter['bewasserung'] !== ''
        || $filter['lichtmenge'] !== ''
        || $filter['schwierigkeitsgrad'] !== ''
        || $filter['winterhart'] !== '';
}

function baueBeitragsFilterSql(array $filter, array &$werte, string &$typen): string
{
    $bedingungen = [];

    foreach (['bewasserung', 'lichtmenge', 'schwierigkeitsgrad', 'winterhart'] as $feld) {
        if ($filter[$feld] !== '') {
            $bedingungen[] = "beitraege.$feld = ?";
            $werte[] = $filter[$feld];
            $typen .= 's';
        }
    }

    return empty($bedingungen) ? '' : ' WHERE ' . implode(' AND ', $bedingungen);
}

function beitragsSortierungSql(string $sortierung): string
{
    switch ($sortierung) {
        case 'datum_asc':
            return 'beitraege.datum ASC, beitraege.id ASC';
        case 'titel_asc':
            return 'beitraege.titel ASC, beitraege.datum DESC';
        case 'titel_desc':
            return 'beitraege.titel DESC, beitraege.datum DESC';
        case 'datum_desc':
        default:
            return 'beitraege.datum DESC, beitraege.id DESC';
    }
}

function beitragsQueryString(array $filter, array $zusatz = []): string
{
    $parameter = array_merge($filter, $zusatz);

    foreach ($parameter as $name => $wert) {
        if (
            $wert === ''
            || $wert === null
            || ($name === 'sortierung' && $wert === 'datum_desc')
            || ($name === 'seite' && (string)$wert === '1')
        ) {
            unset($parameter[$name]);
        }
    }

    return http_build_query($parameter);
}

function zaehleAlleBeitraege(mysqli $datenbankverbindung, array $filter = []): int
{
    $filter = bereinigeBeitragsFilter($filter);
    $werte = [];
    $typen = '';
    $where = baueBeitragsFilterSql($filter, $werte, $typen);
    $anweisung = $datenbankverbindung->prepare("SELECT COUNT(*) AS anzahl FROM beitraege" . $where);

    if (!$anweisung) {
        return 0;
    }

    if (!empty($werte)) {
        $anweisung->bind_param($typen, ...$werte);
    }

    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    if ($ergebnis) {
        $reihe = $ergebnis->fetch_assoc();
        return (int)$reihe['anzahl'];
    }
    return 0;
}

function holeBeitraegeProSeite(mysqli $datenbankverbindung, int $limit, int $offset, array $filter = []): array
{
    $filter = bereinigeBeitragsFilter($filter);
    $werte = [];
    $typen = '';
    $where = baueBeitragsFilterSql($filter, $werte, $typen);
    $orderBy = beitragsSortierungSql($filter['sortierung']);

    $anweisung = $datenbankverbindung->prepare(
        "SELECT " . beitragsAuswahlSql() . "
         FROM beitraege
         LEFT JOIN benutzer ON beitraege.benutzer_id = benutzer.id
         " . $where . "
         ORDER BY " . $orderBy . "
         LIMIT ? OFFSET ?"
    );

    if (!$anweisung) {
        return [];
    }

    $werte[] = $limit;
    $werte[] = $offset;
    $typen .= 'ii';
    $anweisung->bind_param($typen, ...$werte);
    $anweisung->execute();
    $ergebnis = $anweisung->get_result();
    return $ergebnis ? $ergebnis->fetch_all(MYSQLI_ASSOC) : [];
}
