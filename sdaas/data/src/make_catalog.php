#
# Knowledge base configuration description
#
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix kees: <http://linkeddata.center/kees/v1#> .
@prefix sd: <http://www.w3.org/ns/sparql-service-description#> .
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



#
# This must be referenced by the bubblegraph as source
#
resource:ultimo_bilancio_disponibile a dcat:dataSet ;
    dct:identifier "esercizio_2017" ;
	dct:title "Bilancio gestionale 2017 INPS"@it ;
    dct:description "Dati dell'ultimo bilancio gestionale entrate/uscite pubblicato da INPS"@it ;
	dct:publisher  resource:Copernicani ;
	dct:modified "2018-11-27"^^xsd:date ;
	dcat:theme <https://dbpedia.org/resource/Financial_statement> ;
	dct:source resource:bilancio_entrate_2017, resource:bilancio_uscite_2017
.


resource:ID_2888 a fr:FinancialReport;
    dct:identifier "ID-2888" ;
	dct:title       "Bilancio gestionale entrate 2014"@it ;
	dct:publisher   resource:INPS ;
	dct:modified      "2017-03-04"^^xsd:date ;
	fr:refPeriod <http://reference.data.gov.uk/id/gregorian-interval/2014-01-01T00:00:00/P1Y> ;
	sdmx-attribute:unitMeasure <http://publications.europa.eu/resource/authority/currency/EUR> ;
	dcat:theme <https://dbpedia.org/resource/Financial_statement> ;
	dcat:accessURL <https://www.inps.it/NuovoportaleINPS/default.aspx?itemdir=46772&ifaccettaargomento=51&ifaccettaperiodo=72&iiddataset=924> ;
	dcat:distribution resource:ID_2888_csv
.
resource:ID_2888_csv a dcat:Distribution ;
	dcat:downloadURL <https://www.inps.it/NuovoportaleINPS/default.aspx?itemdir=46772&ifaccettaargomento=51&ifaccettaperiodo=72&iiddataset=924&scaricadataset=2> ;
	dcat:license <https://www.dati.gov.it/content/italian-open-data-license-v20>
.
	
	
resource:ID_2889 a fr:FinancialReport;
    dct:identifier "ID-2889" ;
	dct:title       "Bilancio gestionale uscite 2014"@it ;
	dct:publisher   resource:INPS ;
	dct:modified      "2017-03-04"^^xsd:date ;
	fr:refPeriod <http://reference.data.gov.uk/id/gregorian-interval/2014-01-01T00:00:00/P1Y> ;
	sdmx-attribute:unitMeasure <http://publications.europa.eu/resource/authority/currency/EUR> ;
	dcat:theme <https://dbpedia.org/resource/Financial_statement> ;
	dcat:distribution resource:ID_2889_csv ;
	dcat:accessURL <https://www.inps.it/NuovoportaleINPS/default.aspx?itemdir=46772&ifaccettaargomento=51&ifaccettaperiodo=72&iiddataset=925> 
.
resource:ID_2889_csv a dcat:Distribution ;	
	dct:publisher   resource:INPS ;
	dcat:downloadURL <https://www.inps.it/NuovoportaleINPS/default.aspx?itemdir=46772&ifaccettaargomento=51&ifaccettaperiodo=72&iiddataset=925&scaricadataset=2> ;
	dcat:license <https://www.dati.gov.it/content/italian-open-data-license-v20>
.

resource:ds_tassonomia_gestionale a dcat:Dataset;
        dct:description  "Tassonomia delle voci del bilancio INPS"@it ;
        dct:identifier   "tassonomia" ;
        dct:modified	 "2018-11-15T10:55:01Z"^^xsd:dateTime;
        dct:publisher    resource:MEF ;
        dct:title        "Tassonomia voci di bilancio"@it ;
        dcat:distribution    resource:tassonomia_gestionale_ttl 
.
resource:tassonomia_gestionale_ttl a  dcat:Distribution ;
        dct:format    "text/csv" ;
        dcat:downloadURL <https://github.com/g0v-it/data-inps/tree/master/sdaas/data/tassonomia.ttl> ;
        dcat:license <http://creativecommons.org/licenses/by/3.0> 
.

<?php

foreach( ["2015", "2016", "2017"] as $year ) {
    foreach( ['entrate', 'uscite'] as $label ) {
        $title = "Bilancio gestionale $label $year" ;
        $fileName = strtoupper("GESTIONALE%20$label%20$year")) ;
        $downloadURL = "https://github.com/g0v-it/data-inps/tree/master/sdaas/data/{$fileName}.csv";
        $id = "bilancio_{$label}_{$year}";
        $cUri = "resource:$id";
        echo "
$cUri a fr:FinancialReport;
    dct:identifier \"$id\" ;
	dct:title \"$title\"@it ;
	dct:publisher resource:INPS ;
	fr:refPeriod <http://reference.data.gov.uk/id/gregorian-interval/{$year}-01-01T00:00:00/P1Y> ;
	sdmx-attribute:unitMeasure <http://publications.europa.eu/resource/authority/currency/EUR> ;
	dcat:theme <https://dbpedia.org/resource/Financial_statement> ;
	dcat:distribution resource:{$id}_csv 
.
resource:{$id}_csv a dcat:Distribution ;	
	dcat:downloadURL <$downloadURL> ;
	dcat:license <https://www.dati.gov.it/content/italian-open-data-license-v20>;
    
.
<$downloadURL> dct:source <https://mail.google.com/mail/u/0?ik=cb29cb291d&view=om&permmsgid=msg-f%3A1618217983817212046>.
";
    }
}


