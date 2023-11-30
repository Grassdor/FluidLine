<?php
require_once("../Config/Config.php");

use Config\Config;

$db = new Config();
$dbConn = $db->dbConn();

$pdo = new PDO($dbConn["dsn"], $dbConn["username"], $dbConn["password"]);

$sql = "SELECT ptc.contentid, msc.pagetitle, ptc.tmplvarid, ptd.value  FROM product_tmplvar_contentvalues AS ptc
LEFT JOIN product_tmplvar_data AS ptd
ON ptc.value = ptd.id
INNER JOIN modx_site_content AS msc
ON ptc.contentid = msc.id WHERE
`contentid` IN (SELECT `id` FROM `modx_site_content`
WHERE `parent` = 25845)";

$stmt = $pdo->query($sql);
$fp = fopen('files/export/dbExport.csv', 'w');
fputcsv($fp, ["contentid", "pagetitle", "tmplvarid", "value"], ";");
while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $encodedResult = [];
    foreach($result as $item) {
        $encodedResult[] = iconv("UTF-8", "Windows-1251", $item);
    }
    fputcsv($fp, $encodedResult, ";");
}
fclose($fp);