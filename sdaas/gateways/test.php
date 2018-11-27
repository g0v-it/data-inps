<?php
require_once __DIR__ . '/vendor/autoload.php';

$csv = new ParseCsv\Csv();
$csv->encoding('ISO-8859-1', 'UTF-8');
$csv->delimiter = ";";
$csv->parse(stream_get_contents(STDIN));
print_r($csv->data);
