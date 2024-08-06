<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\src\Controllers\CsvGeneratorController;
use config\Logging;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$options = getopt("", ["table:", "rows:", "dailytable::"]);

// Validate options
if (!isset($options['table']) || !isset($options['rows'])) {
    echo "Usage: php generate_csv.php --table=<table_name> --rows=<number_of_rows> [--dailytable]\n";
    exit(1);
}

$tablename = $options['table'];
$rowCount = (int)$options['rows'];
$dailyTable = isset($options['dailytable']);


try {
    $csvGenerator = new CsvGeneratorController($tablename);
    $csvGenerator->generateData($rowCount, $dailyTable);
    echo "CSV file generated and uploaded successfully: " . $csvGenerator->getFilePath() . "\n";
    Logging::logInfo("CSV generated and uploaded successfully: " . $csvGenerator->getFilePath() . "\n");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Logging::logError("Error in CSV generation and uploading: " . $e->getMessage());
    exit(1);
}

echo 'Memory Usage: ' . memory_get_usage() . ' bytes';

