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
    $dotPos = strrpos($value, '.');
    $commaPos = strrpos($value, ',');
    $separatorIsComma = (($commaPos > $dotPos) && $commaPos);
    
    // format like 1.121.345,200 moved to 1121345.200 
    if($separatorIsComma) {
        $value = preg_replace('/\./', '', $value);    // remove dots
        $value = preg_replace('/,/', '.', $value);    // substitute ',' with '.'
    }
    
    $value = preg_replace('/[^\d\.]/', '', $value);    // remove anything except digits and dot
    
    assert( preg_match('/^\d*\.?\d*$/', $value), "expected a float or empty value, found: <$value>" );
    
    return floatval($value);
}

/**
 * code must be like 1U1209017 or 1E1209017
 */
function FILTER_SANITIZE_CODE($value)
{
    $value = preg_replace('/[^\dEU]/', '', $value);    //remove invalid characters
    
    assert( preg_match('/^\d[EU]\d{7}$/', $value), "unexpected code format for CodiceCapitolo" );
    
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
$csv->fields = [null,'capitolo', 'descrizione', null, null,null,null,'amount'];
$csv->parse(stream_get_contents(STDIN));
//print_r($csv->data);exit;
foreach( $csv->data as $row => $rawdata) {
    
    $codiceCapitolo = FILTER_SANITIZE_CODE($rawdata['capitolo']);
    $denominazioneCapitolo = FILTER_SANITIZE_TURTLE_STRING($rawdata['descrizione']);
    
    // le uscite sono memorizzate come valori negativi
    $sign = $codiceCapitolo[1]=='E'?'':'-';
    $amount = $sign . FILTER_SANITIZE_IT_CURRENCY($rawdata['amount']);
    
    echo
        "resource:{$datasetId}_row_$row a fr:Fact ;" .
            "qb:dataSet resource:$datasetId ;" .
            "fr:amount $amount ;" .
            "fr:concept resource:concept_$codiceCapitolo .\n"; 
    $cUriCategoria = 'resource:concept_' . substr($codiceCapitolo, 0, 6);
    $cUriCapitolo = 'resource:concept_' . $codiceCapitolo;
    echo
        "$cUriCapitolo a skos:Concept ;" .
            "skos:notation \"$codiceCapitolo\" ;" .
            "skos:prefLabel \"$denominazioneCapitolo\"@it ;" .
            "skos:broader $cUriCategoria ;".
            "skos:inScheme resource:tassonomia_gestionale .\n" .
        "$cUriCategoria skos:narrower $cUriCapitolo .\n" ;         
}