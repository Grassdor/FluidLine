<?php
require_once("CsvToDb.php");

use App\DbExportImport\CsvToDb;

$fp = fopen('files/import/dbExport.csv', 'r'); //insert path to you csv file instead of "db_to_csv.csv"
$updateDb = new CsvToDb();
$updateDb->exportCsv($fp);
fclose($fp);