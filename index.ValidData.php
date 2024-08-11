<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\src\Controllers\CsvGeneratorController;
use app\src\Controllers\Cd;
use app\src\Controllers\CdrSmsTableController;
use config\Logging;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// $options = getopt("", ["table:", "rows:", "dailytable::"]);

// // Validate options
// if (!isset($options['table']) || !isset($options['rows'])) {
//     echo "Usage: php generate_csv.php --table=<table_name> --rows=<number_of_rows> [--dailytable]\n";
//     exit(1);
// }

// $tablename = $options['table'];
// $rowCount = (int)$options['rows'];
// $dailyTable = isset($options['dailytable']);

$rowCount = $argv[1];

try {
    $csvGenerator = new CdrSmsTableController();
    $csvGenerator->generateCSV($rowCount);
    echo "CSV file generated and uploaded successfully: " . "\n";
    Logging::logInfo("CSV generated and uploaded successfully: " . "\n");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Logging::logError("Error in CSV generation : " . $e->getMessage());
    exit(1);
}



