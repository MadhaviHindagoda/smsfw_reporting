<?php

require_once __DIR__ . '/vendor/autoload.php';


use app\src\Controllers\CdrSmsTableController;
use config\Logging;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$options = getopt("", ["start-date:", "end-date:", "sms-count:", "daily-table"]);

if (!isset($options['start-date']) || !isset($options['end-date']) || !isset($options['sms-count'])) {
    echo "Usage: php cdr_sms_generator.php --start-date=YYYY-MM-DD --end-date=YYYY-MM-DD --sms-count=N [--daily-tables]\n";
    exit(1);
}

$startDate = $options['start-date'];
$endDate = $options['end-date'];
$rowCount = (int)$options['sms-count'];
$dailyTables = isset($options['daily-table']);

if (!$rowCount || !$startDate || !$endDate) {
    echo "Invalid arguments. Please provide valid start-date, end-date, and row-count.\n";
    exit(1);
}

// Ensure date format is valid
if (!DateTime::createFromFormat('Y-m-d', $startDate) || !DateTime::createFromFormat('Y-m-d', $endDate)) {
    echo "Invalid date format. Use YYYY-MM-DD.\n";
    exit(1);
}

try {
    // Initialize the controller
    $csvGenerator = new CdrSmsTableController();

    $csvGenerator->generateRecords($rowCount, $startDate, $endDate, $dailyTables);

    // Log and output success messages
    if ($dailyTables) {
        echo "Daily tables created and uploaded successfully.\n";
        Logging::logInfo("Daily tables created and uploaded successfully.\n");
    } else {
        echo "cdr_sms CSV file generated and uploaded successfully.\n";
        Logging::logInfo("cdr_sms CSV file generated and uploaded successfully.\n");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Logging::logError("Error in CSV generation : " . $e->getMessage());
    exit(1);
}
