<?php
/**
 * Gateway per trasformare file nel "bilancio gestionale uscite" 
 * csv a rdf g0v fr-ap
 *  
 **/
isset($argv[1]) || die("Usage: gestionale <dataset_id>");
$datasetId = $argv[1];

/**
 * escape double quotes, backslash and new line
 * empty allowed
 */
function FILTER_SANITIZE_TURTLE_STRING($value)
{
    $value = preg_replace('/\\\\/', '\\\\\\\\', $value);    // escape backslash
    $value = preg_replace('/\r?\n|\r/', '\\n', $value);  // newline
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


//PREFIXES
echo "@prefix fr: <http://linkeddata.center/botk-fr/v1#> .\n" ;
echo "@prefix skos: <http://www.w3.org/2004/02/skos/core#> .\n"; 
echo "@prefix qb: <http://purl.org/linked-data/cube#> .\n";
echo "@prefix resource: <http://inps.linkeddata.cloud/resource/> .\n";

//skips header
fgets(STDIN);
while ($rawdata = fgetcsv(STDIN, 2048, ';')) {
    $esercizio = intval($rawdata[0]);
    $codiceCapitolo = $rawdata[1];
    $denominazioneCapitolo = FILTER_SANITIZE_TURTLE_STRING($rawdata[2]);
    $valore = FILTER_SANITIZE_IT_CURRENCY($rawdata[7]);

    echo
        "resource:fact_$codiceCapitolo a fr:Fact ;" .
            "qb:dataSet resource:$datasetId ;" .
            "fr:amount $valore ;" .
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