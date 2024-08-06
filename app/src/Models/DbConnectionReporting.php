<?php

namespace app\src\Models;

use Dotenv\Dotenv;
use PDO;
use PDOException;
Use config\Logging;

require_once __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

class DbConnectionReporting
{
    private $connection;
    private static $_instance = null;

    private function __construct()
    
    {   
        $this->connect();
    }

    public function connect()
    {
        $serverName = $_ENV['DB_HOST_REPORTING'];
        $dbName = $_ENV['DB_DATABASE_REPORTING'];
        $userName = $_ENV['DB_USERNAME_REPORTING'];
        $password = $_ENV['DB_PASSWORD_REPORTING'];
        $port = $_ENV['DB_PORT_REPORTING'];

        try {
            $this->connection = new PDO("mysql:host={$serverName};port={$port};dbname={$dbName}", $userName, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::MYSQL_ATTR_LOCAL_INFILE, true);
            $this->connection->setAttribute(PDO::MYSQL_ATTR_LOCAL_INFILE_DIRECTORY, true);
            echo "Connection Success....";
            Logging::logInfo('DB connection success for smsfw_php8_reporting');
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
        return $this->connection;
    }
}

