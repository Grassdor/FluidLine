<?php
namespace App;

use PDOException;
use PDO;

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
        $dsn = "mysql:dbname=modxlocal;host=localhost:3360"; //you should put you DB dsn, username and password 
        $username = "modxlocal";
        $password = "modxlocal";
        $pdo = new PDO($dsn, $username, $password);
        $preparedData = $this->dataEncoder($data);
        $contentid = $preparedData[0];
        $pagetitle = $preparedData[1];
        $tmplvarid = $preparedData[2];
        $value = $preparedData[3];
        
        $sql = $pdo->prepare("UPDATE product_tmplvar_contentvalues SET tmplvarid = :tmplvarid WHERE `value` = :contentid;
                UPDATE product_tmplvar_data SET `value` = `:value` WHERE `id` = :contentid;
                UPDATE modx_site_content JOIN product_tmplvar_contentvalues
                ON modx_site_content.id = product_tmplvar_contentvalues.contentid SET modx_site_content.pagetitle = :pagetitle;");

        $sql->bindParam(':contentid', $contentid);
        $sql->bindParam(':pagetitle', $pagetitle);
        $sql->bindParam(':tmplvarid', $tmplvarid);
        $sql->bindParam(':value', $value);
        $sql->execute();
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