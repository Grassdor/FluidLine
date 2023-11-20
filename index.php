<?php
require_once("/Develop/FluidLine/CsvToDb.php");

use App\CsvToDb;

$fp = fopen('db_to_csv.csv', 'r'); //insert path to you csv file instead of "db_to_csv.csv"
$updateDb = new CsvToDb();
$updateDb->exportCsv($fp);
fclose($fp);