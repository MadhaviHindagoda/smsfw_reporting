<?php

namespace app\src\Models;

require_once __DIR__ . '/../../../vendor/autoload.php';


use app\src\Models\DbConnection;
use app\src\Models\DbConnectionReporting;
use config\Logging;
use PDO;
use PDOException;
use Exception;

class Model
{
    public $pdo;

    public function __construct($dbConnection = 'report_db_connection')
    {
        switch ($dbConnection) {
            case 'main_connection':
                $connection = DbConnection::getInstance();  
                break;

            default:
                $connection = DbConnectionReporting::getInstance();
                break;
        }

        $this->pdo = $connection->getConnection();
        
    }

    public function insertData($tableName, $data) {
        $fields = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));
    
        $query = "INSERT INTO {$tableName} ({$fields}) VALUES ({$placeholders})";
    
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error inserting data: " . $e->getMessage());
        }
    }

    public function dataExists($tableName, $field, $value) {
        $query = "SELECT COUNT(*) FROM {$tableName} WHERE {$field} = :value";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':value', $value);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;

        
    }



}

