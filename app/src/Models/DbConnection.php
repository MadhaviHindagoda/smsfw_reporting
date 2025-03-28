<?php

namespace app\src\Models;

use Dotenv\Dotenv;
use PDO;
use PDOException;
Use config\Logging;

require_once __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

class DbConnection
{
    private $conn;
    private static $_instance = null;

    private function __construct()
    
    {   
        $this->connect();
    }

    public function connect()
    {
        $serverName = $_ENV['DB_HOST'];
        $dbName = $_ENV['DB_DATABASE'];
        $userName = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $port = $_ENV['DB_PORT'];

        try {
            $this->conn = new PDO("mysql:host={$serverName};port={$port};dbname={$dbName}", $userName, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::MYSQL_ATTR_LOCAL_INFILE, true);
            $this->conn->setAttribute(PDO::MYSQL_ATTR_LOCAL_INFILE_DIRECTORY, true);
            echo "Connection Success for smsfw_php8";
            Logging::logInfo('DB connection success for smsfw_php8');
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            Logging::logError("Connection failed: " . $e->getMessage());
            die();
        }
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    
}

