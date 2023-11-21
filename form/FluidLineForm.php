<?php 
namespace App\Form;

require_once("/Develop/FluidLine/Config/Config.php");

use PDOException;
use PDO;
use Config\Config;

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

    public function processForm()
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
}
?>
