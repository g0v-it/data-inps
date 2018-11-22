#
# Tassonomia bilancio inps: generato manualmente dal file "CLASSIFICAZIONE DEI CAPITOLI.xlsx" e
# dal programma tassonomia_gestionale.php nella directory data/src
#
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix dct: <http://purl.org/dc/terms/> .
@prefix void: <http://rdfs.org/ns/void#> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .
@prefix skos: <http://www.w3.org/2004/02/skos/core#> . 
@prefix prov: <http://www.w3.org/ns/prov#> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix dcat: <http://www.w3.org/ns/dcat#> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .
@prefix fr: <http://linkeddata.center/botk-fr/v1#> .
@prefix sdmx-attribute:	<http://purl.org/linked-data/sdmx/2009/attribute#> .
@prefix resource: <http://inps.linkeddata.cloud/resource/> .

<> a prov:Entity;
	dct:created "<?php echo date('c'); ?>"^^xsd:dateTime;
	foaf:primaryTopicOf resource:tassonomia_gestionale;
	prov:wasGeneratedBy [ a prov:Activity;
		prov:startedAtTime    "<?php echo date('c'); ?>"^^xsd:dateTime;
		rdfs:comment "Attività manuale di creazione del file SKOS partendo dal file excel ricevuto da INPS"@it; 
	    prov:qualifiedAssociation [
	        a prov:Association;
	        prov:agent <http://linkeddata.center/>;
	    ];
		prov:used [ a prov:Entity;
			rdfs:comment "File via mail da INPS il 12.11.2018"@it;
			prov:wasDerivedFrom <https://mail.google.com/mail/u/0?ik=cb29cb291d&view=om&permmsgid=msg-f%3A1616940456919619278>
		]
	]
.

resource:tassonomia_gestionale a skos:ConceptScheme;
	dct:title "Tassonomia per il bilancio gestionale INPS"@it ;
	dct:description "Gerarchia dei concetti che descrivono i fatti nei bilanci gestionali di INPS"@it ;
	dct:source <https://github.com/g0v-it/data-inps/tree/master/sdaas/data/src/CLASSIFICAZIONE_DEI_CAPITOLI.xlsx>;
	dct:creator [ foaf:mbox <mailto:enrico@linkeddata.center> ] ;
	dct:created "2018-11-21T10:55:15-08:00"^^xsd:dateTime ;
	dct:modified "2018-11-21T10:55:15-08:00"^^xsd:dateTime ;
.


<?php

function concept ($notation, $prefLabel)
{
    return (object) ['notation'=>$notation, 'prefLabel'=>$prefLabel ];
}


$upbs = [
    concept( '1', 'UPB Entrate'), 
    concept( '2', 'Pensioni'),
    concept( '3', 'Prestazioni a sostegno del reddito'),
    concept( '4','Risorse Umane'),
    concept( '5','Risorse Strumentali'),
    concept( '8','Altre strutture di Direzione Generale'),
];

$capitoli = [
    'E11' => concept('E11', "ENTRATE CONTRIBUTIVE"),
    'E12' => concept('E12', "ENTRATE DERIVANTI DA TRASFERIMENTI CORRENTI"),
    'E13' => concept('E13', "ALTRE ENTRATE"),
    'E21' => concept('E21', "ENTRATE PER ALIENAZIONE DI BENI PATRIMONIALI E RISCOSSIONI DI CREDITI"),
    'E22' => concept('E22', "TRASFERIMENTI IN CONTO CAPITALE"),
    'E23' => concept('E23', "ACCENSIONE DI PRESTITI"),
    'E41' => concept('E41', "PARTITE DI GIRO"),
    'U11' => concept('U11', "FUNZIONAMENTO"),
    'U12' => concept('U12', "INTERVENTI DIVERSI"),
    'U14' => concept('U14', "TRATTAMENTI DI QUIESCENZA, INTEGRATIVI E SOSTITUTIVI"),
    'U21' => concept('U21', "INVESTIMENTI "),
    'U22' => concept('U22', "ONERI COMUNI"),
    'U41' => concept('U41', "PARTITE DI GIRO"),
];

$categories = [
    concept('E1101', "Aliquote contributive a carico dei datori di lavoro e/o degli iscritti"),
    concept('E1102', "Quote di partecipazione degli iscritti all'onere di specifiche gestioni"),
    concept('E1203', "Trasferimenti da parte dello Stato"),
    concept('E1204', "Trasferimenti da parte delle Regioni"),
    concept('E1205', "Trasferimenti da parte dei Comuni e delle Province"),
    concept('E1206', "Trasferimenti da parte di altri Enti del settore pubblico"),
    concept('E1307', "Entrate derivanti dalla vendita di beni e dalla prestazione di servizi"),
    concept('E1308', "Redditi e proventi patrimoniali"),
    concept('E1309', "Poste correttive e compensative di spese correnti"),
    concept('E1310', "Entrate non classificabili in altre voci"),
    concept('E2111', "Alienazione di immobili e diritti reali"),
    concept('E2112', "Alienazione di immobilizzazioni tecniche"),
    concept('E2113', "Realizzo di valori mobiliari"),
    concept('E2114', "Riscossione dei crediti"),
    concept('E2215', "Trasferimenti dallo Stato"),
    concept('E2216', "Trasferimenti dalle Regioni"),
    concept('E2217', "Trasferimenti da Comuni e Province"),
    concept('E2218', "Trasferimenti da altri Enti del settore pubblico"),
    concept('E2319', "Assunzione di mutui"),
    concept('E2320', "Assunzione di altri debiti finanziari"),
    concept('E2321', "Emissioni di obbligazioni"),
    concept('E4122', "Entrate aventi natura di partita di giro"),
    concept('U1101', "Uscite per gli organi dell'Ente"),
    concept('U1102', "Oneri per il personale in attività di servizio"),
    concept('U1104', "Uscite per l'acquisto di beni di consumo e di servizio"),
    concept('U1205', "Uscite per prestazioni istituzionali"),
    concept('U1206', "Trasferimenti passivi"),
    concept('U1207', "Oneri finanziari"),
    concept('U1208', "Oneri tributari"),
    concept('U1209', "Poste correttive e compensative di entrate correnti"),
    concept('U1210', "Uscite non classificabili in altre voci"),
    concept('U1403', "Oneri per il personale in quiescenza"),
    concept('U2111', "Acquisizione beni di uso durevole e opere immobiliari"),
    concept('U2112', "Acquisizione di immobilizzazioni tecniche"),
    concept('U2113', "Partecipazioni e acquisto di valori mobiliari"),
    concept('U2114', "Concessioni di crediti e anticipazioni"),
    concept('U2115', "Indennità di anzianità e similari al personale cessato dal servizio"),
    concept('U2216', "Rimborsi di mutui"),
    concept('U2217', "Rimborsi di anticipazioni passive"),
    concept('U2218', "Rimborsi di obbligazioni"),
    concept('U2219', "Restituzioni alle gestioni autonome di anticipazioni"),
    concept('U2220', "Estinzione debiti diversi"),
    concept('U4121', "Uscite aventi natura di partite di giro"), 
];

foreach( $upbs as $upb ) {
    $upbCuri = 'resource:concept_' . $upb->notation;
    echo 
        "{$upbCuri} a skos:Concept ;" .
            "skos:notation \"{$upb->notation}\" ;" .
            "skos:prefLabel \"{$upb->prefLabel}\"@it ;" .
            "skos:inScheme resource:tassonomia_gestionale .\n" .
        "resource:tassonomia_gestionale skos:hasTopConcept {$upbCuri} .\n" ;
    
    foreach( $capitoli as $capitolo ) {
        $notation = $upb->notation . $capitolo->notation;
        $curi = 'resource:concept_' . $notation;
        echo
            "$curi a skos:Concept ;" .
                "skos:notation \"$notation\" ;" .
                "skos:prefLabel \"{$capitolo->prefLabel}\"@it ;" .
                "skos:broader $upbCuri ;" .
                "skos:inScheme resource:tassonomia_gestionale .\n" .
            "$upbCuri skos:narrower $curi .\n" ;
    }
     
    foreach( $categories as $cat ) {
        $capitolo = $capitoli[substr($cat->notation,0,3)] ?? false;
        if($capitolo) {
            $capitoloCuri = 'resource:concept_' . $upb->notation . $capitolo->notation;
            
            $notation = $upb->notation . $cat->notation;
            $curi = 'resource:concept_' . $notation;
            echo
                "$curi a skos:Concept ;" .
                    "skos:notation \"$notation\" ;" .
                    "skos:prefLabel \"{$cat->prefLabel}\"@it ;" .
                    "skos:broader $capitoloCuri ;" .
                    "skos:inScheme resource:tassonomia_gestionale .\n" .
                "$capitoloCuri skos:narrower $curi .\n" ;
        } else {
            echo "# WARNING: {$cat->notation} does not refer to a capitolo\n";
        }
    }
}
