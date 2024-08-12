<?php
require_once __DIR__.'/RandomServerData.php';

function generateGetAndSendRandomServerData(): void
{
    $randomServerData = new RandomServerData();
    $serverData = $randomServerData->generateAndGetRandomServerData();

    header('Content-Type: application/json');
    echo json_encode($serverData);
}

try {
    generateGetAndSendRandomServerData();
  
} catch (Exception $e) {
    echo "operation failed!";
    exit;
}