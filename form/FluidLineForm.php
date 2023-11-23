<?php

namespace App\Form;

require_once("/Develop/FluidLine/Config/Config.php");

use PDOException;
use PDO;
use Config\Config;
use Exception;

class FluidLineForm
{
    private PDO $pdo;

    private array $updatedValues = [];

    public function __construct()
    {
        $db = new Config();
        $dbConn = $db->dbConn();

        $this->pdo = new PDO($dbConn["dsn"], $dbConn["username"], $dbConn["password"]);
    }

    public function processInput(array $data): void
    {
        foreach ($data as $key => $rows) {
            foreach ($rows as $k => $v) {
                try {
                    $this->updateDb($this->pdo, $k, $v, $key);
                } catch (PDOException $e) {
                    echo "Ошибка записи в БД: " . $e->getMessage() . PHP_EOL;
                } catch (Exception $e) {
                    echo "Не существует страницы с pagetitle: " . $e->getMessage() . " и значением tplvarid " . $k . PHP_EOL;
                }
            }
        }
    }

    private function updateDb(PDO $pdo, string $tmplvarid, string $valuen, string $pagetitle): void //updates data in you DB
    {
        $getId = $pdo->prepare("
            SELECT id FROM `product_tmplvar_data`
            WHERE id = (
                SELECT `value` FROM `product_tmplvar_contentvalues`
                WHERE `tmplvarid` = :tmplvarid AND `contentid` = (
                    SELECT `id` FROM `modx_site_content` WHERE `pagetitle` = :pagetitle))
        ");

        $getId->bindParam('pagetitle', $pagetitle);
        $getId->bindParam('tmplvarid', $tmplvarid);
        $getId->execute();

        $response = $getId->fetch(PDO::FETCH_ASSOC);

        if (isset($response["id"])) {
            if (!in_array((int) $response["id"], $this->updatedValues)) {
                $sql = $pdo->prepare("
                    UPDATE `product_tmplvar_data` AS ptd SET ptd.`value` = :valuen
                    WHERE `id` = :idn;
                ");

                $sql->bindParam('valuen', $valuen);
                $sql->bindParam('idn', $response["id"]);

                $sql->execute();
                $this->updatedValues[] = $response["id"];
            }
        } else {
            $getParent = $pdo->prepare("
                SELECT `parent` FROM `modx_site_content`
                WHERE `pagetitle` = :pagetitle
            ");

            $getParent->bindParam('pagetitle', $pagetitle);
            $getParent->execute();

            $parentResponse = $getParent->fetch(PDO::FETCH_ASSOC);
            if (!$parentResponse) {
                throw new Exception($pagetitle, $tmplvarid);
            }
            $insert = $pdo->prepare("
                INSERT INTO `product_tmplvar_data` (`id`, `parent`, `value`) VALUES (NULL, :parent, :valuen)
            ");

            $insert->bindParam('valuen', $valuen);
            $insert->bindParam('parent', $parentResponse["parent"]);
            $insert->execute();

            $newId = $pdo->lastInsertId();

            $update = $pdo->prepare("
                UPDATE `product_tmplvar_contentvalues` AS ptc SET ptc.`value` = :valuen
                WHERE id = (
                    SELECT `id` FROM `product_tmplvar_contentvalues`
                    WHERE `tmplvarid` = :tmplvarid AND `contentid` = (
                        SELECT `id` FROM `modx_site_content` WHERE `pagetitle` = :pagetitle))
            ");

            $update->bindParam('valuen', $newId);
            $update->bindParam('pagetitle', $pagetitle);
            $update->bindParam('tmplvarid', $tmplvarid);

            $update->execute();
        }
    }
}
