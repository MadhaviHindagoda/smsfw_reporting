<?php 


function generateRandomTimestamps(string $startDate, string $endDate, int $totalRecords) {

    $startTimestamp = strtotime($startDate);
    $endTimestamp = strtotime($endDate);

    // Calculate the total number of days in the range
    $totalDays = (int)floor(($endTimestamp - $startTimestamp) / (60 * 60 * 24));

    $timestamps = [];
    
    // Generate random counts of records for each day
    $recordsPerDay = [];
    $remainingRecords = $totalRecords;

    // Loop through each day to distribute the total records
    for ($day = 0; $day <= $totalDays; $day++) {
        // Randomly assign a count of records for this day
        // Ensure we don't assign more records than the remaining total
        $dailyRecords = rand(0, max(1, min($remainingRecords, $remainingRecords / ($totalDays - $day + 1))));
        $recordsPerDay[$day] = $dailyRecords;
        $remainingRecords -= $dailyRecords;

        // If there are no records left, break early
        if ($remainingRecords <= 0) {
            break;
        }
    }

    // Generate timestamps based on the records assigned per day
    for ($day = 0; $day <= $totalDays; $day++) {
        if (!isset($recordsPerDay[$day])) {
            continue; // Skip days with no records
        }

        // Get the start timestamp for the current day
        $currentDayStart = $startTimestamp + $day * 86400;

        // Generate timestamps for the current day
        for ($i = 0; $i < $recordsPerDay[$day]; $i++) {
            // Create a random time for the current day
            $randomTimeInDay = rand(0, 86400 - 1); 
            $currentTimestamp = $currentDayStart + $randomTimeInDay;

            // Ensure the timestamp does not exceed the end timestamp
            if ($currentTimestamp > $endTimestamp) {
                break;
            }

            // Append the formatted timestamp to the array
            $timestamps[] = date('Y-m-d H:i:s', $currentTimestamp);
        }
    }

    // Sort timestamps to maintain order
    sort($timestamps);

    return $timestamps;
}

// Usage example
$startDate = '2024-10-01 00:00:00';
$endDate = '2024-10-07 23:59:59';
$totalRecords = 20; // Total number of timestamps to generate

$timestamps = generateRandomTimestamps($startDate, $endDate, $totalRecords);
print_r($timestamps);
