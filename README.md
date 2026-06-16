CREATE TABLE IF NOT EXISTS `beitraege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(266) NOT NULL,
  `inhalt` text NOT NULL,
  `bild` varchar(255) DEFAULT NULL,
  `botanischer_name` varchar(255) DEFAULT NULL,
  `standort` varchar(255) DEFAULT NULL,
  `bewasserung` varchar(50) DEFAULT NULL,
  `lichtmenge` varchar(50) DEFAULT NULL,
  `winterhart` varchar(255) DEFAULT NULL,
  `schwierigkeitsgrad` varchar(50) DEFAULT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `benutzer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `benutzer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benutzername` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `sicherheitsstufe` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `benutzername` (`benutzername`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `kommentare` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benutzer_id` int(11) NOT NULL,
  `inhalt` text NOT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `beitrag_id` int(11) NOT NULL,
  `kom_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kom_id` (`kom_id`),
  CONSTRAINT `kommentare_ibfk_1` FOREIGN KEY (`beitrag_id`) REFERENCES `beitraege` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kommentare_ibfk_2` FOREIGN KEY (`benutzer_id`) REFERENCES `benutzer` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kommentare_ibfk_3` FOREIGN KEY (`kom_id`) REFERENCES `kommentare` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


### Beispielblgoposts
I-- 20 Beispielbeiträge für den Pflanzenblog.
-- Voraussetzung: In der Tabelle `benutzer` existiert ein Benutzer mit `id = 1`.
-- Die referenzierten Bilder liegen im Ordner `bilder/`.

INSERT INTO beitraege
(titel, inhalt, bild, botanischer_name, standort, bewasserung, lichtmenge, winterhart, schwierigkeitsgrad, benutzer_id)
VALUES
(
    'Monstera pflegen: So bleibt das Fensterblatt gesund',
    'Die Monstera ist eine unkomplizierte Zimmerpflanze, solange sie hell steht und nicht dauerhaft nasse Erde hat. Ideal ist ein Platz mit viel indirektem Licht, zum Beispiel nahe an einem Ost- oder Westfenster. Direkte Mittagssonne kann die Blätter verbrennen.

Gegossen wird erst, wenn die oberen Zentimeter der Erde trocken sind. Eine Rankhilfe hilft der Pflanze, grössere Blätter zu bilden. Staub sollte regelmässig von den Blättern gewischt werden, damit sie gut atmen und Licht aufnehmen können.',
    'beispiel_monstera.jpg',
    'Monstera deliciosa',
    'Hell, ohne direkte Mittagssonne',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'einfach',
    2
),
(
    'Lavendel im Garten: Duftender Klassiker für sonnige Beete',
    'Lavendel liebt Sonne, Wärme und einen durchlässigen Boden. Besonders gut passt er in Steingärten, an Beetränder oder in Kübel auf Balkon und Terrasse. Wichtig ist, dass Wasser gut ablaufen kann, denn Staunässe schadet den Wurzeln.

Nach der Blüte lohnt sich ein Rückschnitt, damit die Pflanze kompakt bleibt. Wer Lavendel nicht zu stark düngt und eher sparsam giesst, wird mit vielen duftenden Blüten und zahlreichen Insektenbesuchen belohnt.',
    'beispiel_lavendel.jpg',
    'Lavandula angustifolia',
    'Sonniges Beet oder Kübel',
    'wenig',
    'viel',
    'Winterhart',
    'einfach',
    2
),
(
    'Calathea orbifolia: Schöne Blätter, klare Ansprüche',
    'Die Calathea orbifolia ist vor allem wegen ihrer grossen, gestreiften Blätter beliebt. Sie mag es warm, hell bis halbschattig und vor allem luftfeucht. Trockene Heizungsluft führt schnell zu braunen Blatträndern.

Beim Giessen eignet sich kalkarmes Wasser besonders gut. Die Erde sollte gleichmässig leicht feucht bleiben, aber nie nass sein. Wer einen ruhigen Standort ohne direkte Sonne findet, hat mit dieser Pflanze einen besonderen Blickfang.',
    'beispiel_calathea.jpg',
    'Calathea orbifolia',
    'Warm, hell bis halbschattig',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'anspruchsvoll',
    2
),
(
    'Tomaten auf dem Balkon anbauen',
    'Tomaten lassen sich auch ohne Garten sehr gut anbauen. Wichtig sind ein grosser Topf, nährstoffreiche Erde und ein sonniger, möglichst geschützter Standort. Je mehr Licht die Pflanze bekommt, desto aromatischer werden die Früchte.

Gegossen wird regelmässig und am besten direkt an die Erde. Nasse Blätter erhöhen das Risiko für Pilzkrankheiten. Eine Rankhilfe stabilisiert die Pflanze, und reife Früchte sollten laufend geerntet werden.',
    'beispiel_tomate.jpg',
    'Solanum lycopersicum',
    'Sonniger, windgeschützter Balkon',
    'viel',
    'viel',
    'Nicht winterhart',
    'mittel',
    2
),
(
    'Bogenhanf: Die robuste Pflanze für Anfänger',
    'Der Bogenhanf verzeiht Pflegefehler besser als viele andere Zimmerpflanzen. Er kommt mit wenig Wasser aus und verträgt helle bis halbschattige Standorte. Besonders wichtig ist ein Topf mit gutem Wasserabzug.

Zwischen den Wassergaben darf die Erde komplett abtrocknen. Im Winter wird nur sehr sparsam gegossen. Durch seinen aufrechten Wuchs passt Bogenhanf gut in schmale Ecken, Flure oder Arbeitszimmer.',
    'beispiel_bogenhanf.jpg',
    'Dracäna trifasciata',
    'Hell bis halbschattig',
    'wenig',
    'mittel',
    'Nicht winterhart',
    'einfach',
    2
),
(
    'Pilea vermehren: Kleine Ableger richtig einsetzen',
    'Die Pilea peperomioides bildet oft kleine Ableger am Topfrand. Sobald diese eigene Blätter und ein paar Wurzeln haben, können sie vorsichtig abgetrennt und in lockere Erde gesetzt werden.

Nach dem Einpflanzen sollte die Erde leicht feucht bleiben. Ein heller Standort ohne direkte Mittagssonne hilft den jungen Pflanzen beim Anwachsen. Nach einigen Wochen zeigen frische Blätter, dass der Ableger gut angekommen ist.',
    'beispiel_pilea.jpg',
    'Pilea peperomioides',
    'Hell, ohne pralle Sonne',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'einfach',
    2
),
(
    'Efeutute: Schnell wachsender Grünakzent für die Wohnung',
    'Die Efeutute ist pflegeleicht, wächst schnell und kann hängend oder kletternd kultiviert werden. Sie eignet sich gut für Regale, Ampeln oder Rankhilfen. Je heller der Standort, desto kräftiger bleibt meist die Blattzeichnung.

Die Erde darf vor dem Giessen leicht antrocknen. Zu viel Wasser ist häufiger ein Problem als zu wenig. Stecklinge lassen sich einfach im Wasserglas bewurzeln und später eintopfen.',
    'beispiel_efeutute.jpg',
    'Epipremnum aureum',
    'Hell bis halbschattig',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'einfach',
    3
),
(
    'Geigenfeige: Was sie wirklich braucht',
    'Die Geigenfeige wirkt mit ihren grossen Blättern sehr prägnant, reagiert aber empfindlich auf Standortwechsel. Ein heller Platz mit gleichmässigen Bedingungen ist für sie besonders wichtig.

Gegossen wird, wenn die obere Erdschicht trocken ist. Zugluft, kalte Füsse und Staunässe sollten vermieden werden. Werden die Blätter regelmässig abgestaubt, sieht die Pflanze nicht nur besser aus, sondern kann auch mehr Licht aufnehmen.',
    'beispiel_geigenfeige.jpg',
    'Ficus lyrata',
    'Sehr hell, ohne kalte Zugluft',
    'mittel',
    'viel',
    'Nicht winterhart',
    'mittel',
    3
),
(
    'Grünlilie: Pflegeleicht und ideal zum Vermehren',
    'Die Grünlilie gehört zu den unkompliziertesten Zimmerpflanzen. Sie bildet lange Triebe mit kleinen Kindeln, die sich leicht abnehmen und neu einpflanzen lassen. Dadurch eignet sie sich perfekt zum Tauschen oder Verschenken.

Sie kommt mit vielen Standorten zurecht, mag aber helles indirektes Licht besonders gern. Gegossen wird moderat. Wenn die Blattspitzen braun werden, kann zu trockene Luft oder sehr kalkhaltiges Wasser eine Ursache sein.',
    'beispiel_gruenlilie.jpg',
    'Chlorophytum comosum',
    'Hell bis halbschattig',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'einfach',
    3
),
(
    'Aloe vera: Sukkulente Pflege ohne viel Aufwand',
    'Aloe vera speichert Wasser in ihren fleischigen Blättern und braucht deshalb nur selten Wasser. Ein heller, warmer Platz ist ideal. Im Sommer kann sie geschützt draussen stehen, sollte aber langsam an direkte Sonne gewöhnt werden.

Die Erde muss zwischen den Wassergaben gut abtrocknen. Kakteenerde oder eine mineralisch gemischte Erde verhindert Staunässe. Im Winter reicht sehr sparsames Giessen aus.',
    'beispiel_aloe_vera.jpg',
    'Aloe vera',
    'Sehr hell und warm',
    'wenig',
    'viel',
    'Nicht winterhart',
    'einfach',
    3
),
(
    'Glücksfeder: Dunklere Ecken besser begrünen',
    'Die Glücksfeder ist robust, langsam wachsend und kommt auch mit weniger Licht zurecht. Dadurch eignet sie sich gut für Büros, Flure oder Zimmer, in denen andere Pflanzen schnell schwach werden.

Ihre dicken Speicherorgane machen sie empfindlich gegen zu viel Wasser. Vor dem nächsten Giessen sollte die Erde deutlich abgetrocknet sein. Gelbe Blätter sind oft ein Hinweis auf zu nasse Erde.',
    'beispiel_gluecksfeder.jpg',
    'Zamioculcas zamiifolia',
    'Hell bis schattiger Innenraum',
    'wenig',
    'wenig',
    'Nicht winterhart',
    'einfach',
    3
),
(
    'Einblatt: Blüten und frisches Grün für halbschattige Plätze',
    'Das Einblatt ist eine beliebte Zimmerpflanze für helle bis halbschattige Standorte. Es mag gleichmässige Feuchtigkeit, sollte aber nicht dauerhaft im Wasser stehen. Hängende Blätter zeigen oft an, dass gegossen werden muss.

Bei guter Pflege bildet die Pflanze weisse Hochblätter, die lange dekorativ bleiben. Eine etwas höhere Luftfeuchtigkeit und regelmässiges Entfernen alter Blüten halten sie vital.',
    'beispiel_einblatt.jpg',
    'Spathiphyllum',
    'Hell bis halbschattig',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'mittel',
    3
),
(
    'Basilikum auf der Fensterbank kräftig halten',
    'Basilikum braucht viel Licht, Wärme und gleichmässig feuchte Erde. Im Supermarkttopf stehen die Pflanzen oft zu dicht. Wer sie teilt und in grössere Töpfe setzt, verlängert die Lebensdauer deutlich.

Beim Ernten sollten ganze Triebspitzen abgeschnitten werden, nicht nur einzelne Blätter. So verzweigt sich die Pflanze besser. Kalte Zugluft und trockene Erde mag Basilikum gar nicht.',
    'beispiel_basilikum.jpg',
    'Ocimum basilicum',
    'Sehr hell und warm',
    'viel',
    'viel',
    'Nicht winterhart',
    'mittel',
    1
),
(
    'Rosmarin im Topf: Mediterran und genügsam',
    'Rosmarin liebt Sonne, mageren Boden und gute Drainage. Im Topf sollte die Erde nicht zu humos sein, sondern eher mineralisch und locker. Ein sonniger Balkon oder eine Terrasse ist ideal.

Gegossen wird sparsam, aber nicht so selten, dass der Wurzelballen komplett austrocknet. Im Winter braucht Rosmarin einen kühlen, hellen Platz und Schutz vor zu viel Nässe.',
    'beispiel_rosmarin.jpg',
    'Salvia rosmarinus',
    'Sonnig, draussen oder sehr hell',
    'wenig',
    'viel',
    'Bedingt winterhart',
    'mittel',
    1
),
(
    'Hortensien: Warum Wasser und Boden so wichtig sind',
    'Hortensien brauchen gleichmässig feuchte Erde und einen Standort, der nicht zu heiss ist. Besonders im Kübel trocknen sie an warmen Tagen schnell aus. Morgens zu giessen ist meist besser als in der Mittagshitze.

Viele Hortensien bevorzugen leicht saure Erde. Bei Bauernhortensien kann der pH-Wert sogar die Blütenfarbe beeinflussen. Verblühtes wird je nach Sorte vorsichtig entfernt, ohne neue Knospen zu verletzen.',
    'beispiel_hortensie.jpg',
    'Hydrangea macrophylla',
    'Halbschattig, humoser Boden',
    'viel',
    'mittel',
    'Winterhart',
    'mittel',
    1
),
(
    'Funkien: Blattschmuck für schattige Gartenbereiche',
    'Funkien sind ideal für halbschattige bis schattige Plätze im Garten. Ihre dekorativen Blätter bringen Struktur in Bereiche, in denen viele Blütenpflanzen weniger gut wachsen.

Der Boden sollte humos und gleichmässig feucht sein. In Kübeln brauchen Funkien regelmässig Wasser. Im Herbst ziehen sie ein und treiben im Frühjahr wieder frisch aus.',
    'beispiel_funkie.jpg',
    'Hosta',
    'Halbschatten bis Schatten',
    'mittel',
    'wenig',
    'Winterhart',
    'einfach',
    1
),
(
    'Geranien für Balkonkästen richtig pflegen',
    'Geranien blühen ausdauernd, wenn sie sonnig stehen und regelmässig versorgt werden. Balkonkästen sollten Abzugslöcher haben, damit Regenwasser nicht stehen bleibt.

Verblühte Blütenstände werden entfernt, damit neue Knospen nachkommen. In der Hauptsaison brauchen Geranien ausreichend Wasser und Nährstoffe. Frost vertragen sie nicht, können aber hell und kühl überwintert werden.',
    'beispiel_geranie.jpg',
    'Pelargonium',
    'Sonniger Balkon oder Terrasse',
    'mittel',
    'viel',
    'Nicht winterhart',
    'einfach',
    1
),
(
    'Minze im Topf: Frisch, wuchsfreudig und besser begrenzt',
    'Minze wächst stark und sollte im Garten besser in einem Topf oder mit Wurzelsperre kultiviert werden. So bleibt sie kontrollierbar und kann trotzdem kräftig austreiben.

Sie mag feuchte, nährstoffreiche Erde und einen hellen bis halbschattigen Standort. Regelmässiges Ernten fördert buschigen Wuchs. Blütentriebe können entfernt werden, wenn viele aromatische Blätter gewünscht sind.',
    'beispiel_minze.jpg',
    'Mentha',
    'Hell bis halbschattig',
    'viel',
    'mittel',
    'Winterhart',
    'einfach',
    1
),
(
    'Chili anbauen: Von der Jungpflanze bis zur Ernte',
    'Chilipflanzen brauchen viel Licht, Wärme und Geduld. Ein sonniger Standort auf Balkon, Terrasse oder Fensterbank ist entscheidend. Der Topf sollte gross genug sein, damit die Pflanze gleichmässig versorgt wird.

Gegossen wird regelmässig, aber ohne Staunässe. Während der Fruchtbildung helfen ausreichend Nährstoffe. Reife Chilis können frisch verwendet, getrocknet oder eingefroren werden.',
    'beispiel_chili.jpg',
    'Capsicum annuum',
    'Sehr sonnig und warm',
    'mittel',
    'viel',
    'Nicht winterhart',
    'mittel',
    1
),
(
    'Phalaenopsis: Orchideenpflege ohne Mythos',
    'Phalaenopsis-Orchideen sind robuster, als ihr Ruf vermuten lässt. Sie wachsen am besten in speziellem Orchideensubstrat, das Luft an die Wurzeln lässt. Ein heller Platz ohne direkte Mittagssonne ist ideal.

Statt häufig zu giessen, wird der Topf besser kurz getaucht und danach gut abtropfen gelassen. Silberne Wurzeln zeigen Trockenheit an, grüne Wurzeln sind noch gut versorgt. Nach der Blüte kann ein gesunder Trieb manchmal erneut austreiben.',
    'beispiel_orchidee.jpg',
    'Phalaenopsis',
    'Hell, ohne direkte Mittagssonne',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'mittel',
    1
);
