<?php
require_once __DIR__.'/RandomServerData.php';

function generateAndGetRandomServerData(): string
{
    $randomServerData = new RandomServerData();
    $serverData = $randomServerData->generateAndGetRandomServerData();

    return json_encode($serverData);
}

function sendResponse(string $response) : void
{
    header('Content-Type: application/json');
    echo $response;
}

try {
    if (!isset($_POST['method'])) {
        $jsonResponse = json_encode("invalid request");
        sendResponse($jsonResponse);
        exit;
    }

    $method = $_POST['method'];
    switch ($method) {
        case 'ss7':
            $jsonResponse = generateAndGetRandomServerData();
            sendResponse($jsonResponse);
            break;
        default:
            sendResponse("invalid request");
            break;
    }

    exit;
} catch (Exception $e) {
    echo "operation failed!";
    exit;
}