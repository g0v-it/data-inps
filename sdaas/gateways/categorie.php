<?php
/**
 * Gateway per estrarre la descrizione delle categorie
 *  
 **/
require_once __DIR__ . '/vendor/autoload.php';

/**
 * code must be like 1U1209017 or 1E1209017
 */
function FILTER_SANITIZE_CODE($value)
{
    $value = preg_replace('/\./', '', $value);    //remove .
    $value = preg_replace('/\s/', '', $value);    //remove spaces
    return preg_match('/^[123458][EU]\d{4}$/', $value)?$value:false;
}

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


//PREFIXES
echo "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .\n";
echo "@prefix resource: <http://inps.linkeddata.cloud/resource/> .\n";


$csv = new ParseCsv\Csv();
$csv->fields = ['entrata','descrizione_entrata', null, 'uscita','descrizione_uscita'];
$csv->parse(stream_get_contents(STDIN));
//print_r($csv->data);exit;
foreach( $csv->data as $row => $rawdata) {
    $codiceCategoriaEntrate = FILTER_SANITIZE_CODE($rawdata['entrata']);
    $codiceCategoriaUscite = FILTER_SANITIZE_CODE($rawdata['uscita']);
    if($codiceCategoriaEntrate){
        $descrizione = FILTER_SANITIZE_TURTLE_STRING($rawdata['descrizione_entrata']);
        echo "resource:concept_{$codiceCategoriaEntrate} rdfs:comment \"$descrizione\"@it . \n" ;
    }
    
    if($codiceCategoriaUscite){
        $descrizione = FILTER_SANITIZE_TURTLE_STRING($rawdata['descrizione_uscita']);
        echo "resource:concept_{$codiceCategoriaUscite} rdfs:comment \"$descrizione\"@it . \n" ;
    }
}
