<?php
/**
 * Gateway per trasformare file nel "bilancio gestionale uscite" 
 * csv a rdf g0v fr-ap
 *  
 **/
require_once __DIR__ . '/vendor/autoload.php';

isset($argv[1]) || die("Usage: gestionale <dataset_id>");
$datasetId = $argv[1];

/**
 * escape double quotes, backslash and new line
 * empty allowed
 */
function FILTER_SANITIZE_TURTLE_STRING($value)
{
    $value = preg_replace('/\r?\n|\r/', ' ', $value);  // newline as space
    $value = preg_replace('/\\\\/', '\\\\\\\\', $value);    // escape backslash
    $value = preg_replace('/"/', '\\"', $value);        // escape double quote
    
    return $value?:null;
}

/**
 * e.g.from "  1 . 900 . 000 ,33 "  to "1900000.33"
 */
function FILTER_SANITIZE_IT_CURRENCY($value)
{
    $value = preg_replace('/\s/', '', $value);    //remove spaces
    $value = preg_replace('/\./', '', $value);    // remove dots
    $value = preg_replace('/,/', '.', $value);    // substitute ',' with '.'
    
    return floatval($value);
}


function FILTER_SANITIZE_CODE($value)
{
    $value = preg_replace('/[^0-9EU]/', '', $value);    //remove invalid characters
    
    return $value;
}


//PREFIXES
echo "@prefix fr: <http://linkeddata.center/botk-fr/v1#> .\n" ;
echo "@prefix skos: <http://www.w3.org/2004/02/skos/core#> .\n"; 
echo "@prefix qb: <http://purl.org/linked-data/cube#> .\n";
echo "@prefix resource: <http://inps.linkeddata.cloud/resource/> .\n";


$csv = new ParseCsv\Csv();
$csv->encoding('ISO-8859-1', 'UTF-8');
$csv->delimiter = ";";
$csv->parse(stream_get_contents(STDIN));
//print_r($csv->data);exit;
foreach( $csv->data as $row => $rawdata) {
    $codiceCapitolo = FILTER_SANITIZE_CODE($rawdata['Codice Capitolo']);
    if(!$codiceCapitolo) {
        echo "# WARNING: invalid code detected at row $row ({$rawdata['Codice Capitolo']}).\n";
        continue;
    }
    $denominazioneCapitolo = FILTER_SANITIZE_TURTLE_STRING($rawdata[
        isset($rawdata['Denominazione Capitolo'])?'Denominazione Capitolo':'Descrizione Capitolo'
    ]);
    $sign = $codiceCapitolo[1]=='E'?'':'-';
    $amount = $sign.FILTER_SANITIZE_IT_CURRENCY($rawdata[
        isset($rawdata['Totale impegni'])?'Totale impegni':'Totale accertamenti'
    ]);
    
    assert($codiceCapitolo && $denominazioneCapitolo && $amount);
    
    echo
        "resource:{$datasetId}_fact_$codiceCapitolo a fr:Fact ;" .
            "qb:dataSet resource:$datasetId ;" .
            "fr:amount $amount ;" .
            "fr:concept resource:concept_$codiceCapitolo .\n"; 
    $codiceCategoria = substr($codiceCapitolo, 0, 6);
    echo
        "resource:concept_$codiceCapitolo a skos:Concept ;" .
            "skos:notation \"$codiceCapitolo\" ;" .
            "skos:prefLabel \"$denominazioneCapitolo\"@it ;" .
            "skos:broader resource:concept_$codiceCategoria ;".
            "skos:inScheme resource:tassonomia_gestionale .\n" .
        "resource:concept_$codiceCategoria skos:narrower resource:concept_$codiceCapitolo .\n" ;         
}