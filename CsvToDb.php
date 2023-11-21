<?php
namespace App;

require_once("/Develop/FluidLine/Config/Config.php");

use PDOException;
use PDO;
use Config\Config;

class CsvToDb
{
    public function exportCsv($file): string
    {
        while ($lineArr = fgetcsv($file, null, ";")) {
            try {
                $this->updateDb($lineArr);
            } catch (PDOException $e) {
                echo "Ошибка записи в БД: " . $e->getMessage() . PHP_EOL;
            }

        }
        return "rew";
        echo $lineArr;
    }

    private function updateDb(array $data): void //updates data in you DB
    {

        $db = new Config();
        $dbConn = $db->dbConn();

        $pdo = new PDO($dbConn["dsn"], $dbConn["username"], $dbConn["password"]);
        $preparedData = $this->dataEncoder($data);
        $contentid = $preparedData[0];
        $pagetitle = $preparedData[1];
        $tmplvarid = $preparedData[2];
        $value = $preparedData[3];
        
        $sql = $pdo->prepare("UPDATE product_tmplvar_data SET `value` = :value
                              WHERE id = (
                                SELECT `value` FROM product_tmplvar_contentvalues
                                WHERE tmplvarid = :tmplvarid
                                AND contentid = :contentid
                              )");

        
        $sql->bindParam(':contentid', $contentid);
        $sql->bindParam(':tmplvarid', $tmplvarid);
        $sql->bindParam(':value', $value);
        $sql->execute();
        echo $preparedData[3] . PHP_EOL;
    }

    private function dataEncoder(array $data): array //encodes data from Excel encode to UTF-8
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = iconv("Windows-1251", "UTF-8", $item);
        }
        return $result;
    }
}