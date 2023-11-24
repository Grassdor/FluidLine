<?php

namespace App\DbExportImport;

require_once("../Config/Config.php");

use PDOException;
use PDO;
use Config\Config;

class CsvToDb
{
    private PDO $pdo;

    private array $updatedValues = [];

    public function __construct()
    {
        $db = new Config();
        $dbConn = $db->dbConn();

        $this->pdo = new PDO($dbConn["dsn"], $dbConn["username"], $dbConn["password"]);
    }

    public function exportCsv($file): void
    {
        $rowPosition = 0;
        
        while ($lineArr = fgetcsv($file, separator: ";")) {
            if ($rowPosition) {
                try {
                    $this->updateDb($this->pdo, $lineArr);
                } catch (PDOException $e) {
                    echo "Ошибка записи в БД: " . $e->getMessage() . PHP_EOL;
                }
            }
            
            $rowPosition++;
        }


    }

    private function updateDb(PDO $pdo, array $data): void //updates data in you DB
    {
        $preparedData = $this->dataEncoder($data);
        $contentid = $preparedData[0];
        $pagetitle = $preparedData[1];
        $tmplvarid = $preparedData[2];
        $value = $preparedData[3];

        $getId = $pdo->prepare("
            SELECT `value` FROM `product_tmplvar_contentvalues`
            WHERE `tmplvarid` = :tmplvarid AND `contentid` = :contentid
        ");
        
        $getId->bindParam('contentid', $contentid);
        $getId->bindParam('tmplvarid', $tmplvarid);
        $getId->execute();

        $response = $getId->fetch(PDO::FETCH_ASSOC);

        if (isset($response['value'])) {
            if (!in_array((int) $response['value'], $this->updatedValues)) {
                $sql = $pdo->prepare("
                    UPDATE `product_tmplvar_data` AS ptd SET ptd.`value` = :valuen
                    WHERE `id` = :idn;
                ");
    
                $sql->bindParam('valuen', $value);
                $sql->bindParam('idn', $response['value']);
            
                $sql->execute();

                $this->updatedValues[] = $response['value'];
            }
        }
    }

    private function dataEncoder(array $data): array //encodes data from Excel encode to UTF-8
    {
        $result = [];

        foreach ($data as $item) {
            $result[] = mb_convert_encoding($item, "UTF-8", "Windows-1251");
        }

        return $result;
    }
}
