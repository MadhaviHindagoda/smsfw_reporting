<?php

namespace app\src\Traits;

require_once __DIR__ . '/../../../vendor/autoload.php';

use DateTime;
use Dotenv\Dotenv;
use InvalidArgumentException;
use PDOException;
use Exception;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

trait ValidDataGeneratorTrait
{

    private $recentLocalOnNetMSISDNs = [];
    private $recentLocalOloMSISDNs = [];
    private $recentInternationalMSISDNs = [];
    private $recentVirtualIMSIs = [];
    private $lastUsedTimestamps = [];
    private $maxStored = 500;
    private $currentId = 1;

    // Method to generate auto-incrementing IDs
    private function generateAutoIncrementId(): int
    {
        return $this->currentId++;
    }

    /**
     * Generates an MSISDN based on the specified type (local_onnet, local_olo, international).
     *
     * @param string $type The type of MSISDN to generate('local_onnet', 'local_olo', 'international').
     * @return string The generated MSISDN.
     */
    public function generateMSISDN(string $type): string
    {
        // Environment variables
        $localCC = $_ENV['LOCAL_CC'];
        $onNetNCArray = explode(',', $_ENV['ONNET_NC']);
        $oloNCArray = explode(',', $_ENV['OLO_NC']);
        $intlCC = $_ENV['INTL_CC'];
        $intlNCArray = explode(',', $_ENV['INTL_NC']);
        $this->duplicateChance = $_ENV['DUPLICATE_CHANCE'];

        // Helper function to generate MSISDN
        $generateMSISDN = function (string $cc, array $ncArray) {
            $nc = $this->faker->randomElement($ncArray);
            $msisdnPrefix = "{$cc}{$nc}";
            $msisdnSuffix = $this->faker->numerify('#######');

            return "{$msisdnPrefix}{$msisdnSuffix}";
        };

        $msisdn = '';

        // Generate MSISDN based on the selected type
        switch ($type) {
            case 'local_onnet':
                $msisdn = $generateMSISDN($localCC, $onNetNCArray);
                $msisdn = $this->handleDuplicates($this->recentLocalOnNetMSISDNs, $msisdn, $this->duplicateChance);
                break;
            case 'local_olo':
                $msisdn = $generateMSISDN($localCC, $oloNCArray);
                $msisdn = $this->handleDuplicates($this->recentLocalOloMSISDNs, $msisdn, $this->duplicateChance);
                break;
            case 'international':
                $msisdn = $generateMSISDN($intlCC, $intlNCArray);
                $msisdn = $this->handleDuplicates($this->recentInternationalMSISDNs, $msisdn, $this->duplicateChance);
                break;
            default:
                throw new InvalidArgumentException("Invalid MSISDN type: {$type}");
        }

        return $msisdn;
    }

    /**
     * Handles duplicates for the generated MSISDNs or IMSIs.
     * It either returns a random existing item (with a chance) or adds the new item to the list.
     *
     * @param array $recentItems Array of recent items to check and store duplicates.
     * @param string $newItem The new item to potentially add or duplicate.
     * @param float $duplicateChance The chance of returning a duplicate item.
     * @return string The item, either new or duplicated.
     */
    private function handleDuplicates(array &$recentItems, string $newItem, float $duplicateChance): string
    {
        if (mt_rand() / mt_getrandmax() < $duplicateChance && !empty($recentItems)) {
            $randomKey = array_rand($recentItems);
            return $recentItems[$randomKey];
        } else {
            if (count($recentItems) >= $this->maxStored) {
                array_shift($recentItems);
            }
            // Add the new item to the list of recent items
            $recentItems[] = $newItem;

            return $newItem;
        }
    }

    /**
     * Generates an IMSI based on the given MSISDN.
     * It determines the IMSI prefix based on the MSISDN's country code, then hashes and pads the suffix.
     *
     * @param string $msisdn The MSISDN to generate the IMSI.
     * @return string The generated IMSI.
     */
    public function generateIMSI(string $msisdn): string
    {
        // Determine if the MSISDN is local or international by checking the prefix
        if (substr($msisdn, 0, 2) === '94') {
            $mcc = $_ENV['LOCAL_MCC'];
            $ncc = $_ENV['LOCAL_MNC'];
        } else {
            $mcc = $_ENV['INTL_MCC'];
            $ncc = $_ENV['INTL_MNC'];
        }

        // Create a hash of the MSISDN
        $hash = md5($msisdn);

        // Extract it to a number
        $numericHash = substr(preg_replace("/[^0-9]/", '', $hash), 0, 15);
        $number = "1";

        $imsiPrefix = "{$mcc}{$ncc}{$number}";
        $imsiSuffix = str_pad($numericHash, 10, '0', STR_PAD_LEFT);
        $imsi = "{$imsiPrefix}{$imsiSuffix}";

        // Ensure the IMSI has 15 digits
        $imsi = substr($imsi, 0, 15);

        return $imsi;
    }

    /**
     * Generates a virtual IMSI with a fixed prefix and random suffix.
     * The generated IMSI is checked for duplicates and updated accordingly.
     *
     * @return string The generated virtual IMSI.
     */
    public function generateVirtualIMSI(): string
    {
        $mcc = $_ENV['LOCAL_MCC'];
        $ncc = $_ENV['LOCAL_MNC'];
        $number = '00';
        $prefix = "{$mcc}{$ncc}{$number}";

        $suffix = $this->faker->numerify('########');
        $virtualIMSI = "{$prefix}{$suffix}";

        $virtualIMSI = $this->handleDuplicates($this->recentVirtualIMSIs, $virtualIMSI, $this->duplicateChance);

        // Check and update the timestamp for the generated IMSI
        if (!isset($this->lastUsedTimestamps[$virtualIMSI])) {
            // If this IMSI has not been used before, record the current time
            $this->lastUsedTimestamps[$virtualIMSI] = new DateTime();
        }

        return $virtualIMSI;
    }


    /**
     * Generates a unique reference number based on the given creation time.
     * It combines a timestamp with a unique number to form the reference.
     *
     * @param mixed $createdAt The creation time as a string or DateTime object.
     * @return string The generated reference number.
     */
    private function generateReference($createdAt): string
    {
        $timestamp = is_string($createdAt) ? strtotime($createdAt) : $createdAt->getTimestamp();
        $uniqueNumber = $this->faker->numerify('####');
        $reference = "{$timestamp}{$uniqueNumber}";

        return $reference;
    }

    /**
     * Generates a date range with start and end timestamps for the given dates.
     *
     * @param string $startDate The start date in 'YYYY-MM-DD' format.
     * @param string $endDate The end date in 'YYYY-MM-DD' format.
     * 
     * @return array An associative array with 'start' and 'end' keys, containing the start and end timestamps.
     */
    private function generateDateRange(string $startDate, string $endDate): array
    {
        return [
            'start' => $startDate . ' 00:00:00',
            'end' => $endDate . ' 23:59:59'
        ];
    }

    /**
     * Generates a specified number of SMSMO records and writes them to the CSV file.
     *
     * @param int $numRecords The number of SMSMO records to generate.
     * @param string $trafficType The traffic type (local_onnet, local_olo, international).
     * @param resource $csvFile The open CSV file resource.
     */
    private function generateSMSMORecords(int $numRecords, string $trafficType, string $startDate, string $endDate, $csvFile): void
    {
        for ($i = 0; $i < $numRecords; $i++) {
            $smsmoRecords = $this->generateSMSMOFields($trafficType, $startDate, $endDate);
            foreach ($smsmoRecords as $smsmoRecord) {
                fputcsv($csvFile, $smsmoRecord);
            }
        }
    }

    /**
     * Generates a specified number of SRI-SMSMT record pairs and writes them to the CSV file.
     *
     * @param int $index 
     * @param int $numRecords The number of SRI-SMSMT record pairs to generate.
     * @param string $trafficType The traffic type (local_olo, international).
     * @param resource $csvFile The open CSV file resource.
     */
    public function generateSRISMSMTRecords($numRecords, $trafficType, string $startDate, string $endDate, $csvFile)
    {
        for ($i = 0; $i < $numRecords; $i++) {
            $commonValues = $this->generateCommonSMSMTValues($trafficType, $startDate, $endDate);
            // Generate SRI record
            $sriRecord = $this->generateSRIFields($commonValues);
            fputcsv($csvFile, $sriRecord);

            // Generate SMSMT records
            $smsmtRecords = $this->generateSMSMTFields($commonValues);
            foreach ($smsmtRecords as $smsmtRecord) {
                fputcsv($csvFile, $smsmtRecord);
            }
        }
    }

    private function generateSMPPRecords(int $numRecords, string $trafficType, string $startDate, string $endDate, $csvFile): void
    {
        for ($i = 0; $i < $numRecords; $i++) {
            $smsmoRecords = $this->generateSMPPFields($trafficType, $startDate, $endDate);
            foreach ($smsmoRecords as $smsmoRecord) {
                fputcsv($csvFile, $smsmoRecord);
            }
        }
    }


    /**
     * Truncates the specified table in the database.
     *
     * @param string $tableName The name of the table to truncate.
     * @return void
     */
    public function truncateTable(string $tableName): void
    {
        $this->pdo->query("TRUNCATE TABLE $tableName");
    }

    /**
     * Inserts a batch of data into the specified table.
     *
     * @param string $tableName The name of the table where data will be inserted.
     * @param array $dataBatch An array of associative arrays, where each associative array represents a row of data.
     * @return void
     */
    public function insertDataBatch($tableName, $dataBatch)
    {
        $fields = implode(',', array_keys($dataBatch[0]));

        $placeholders = ':' . implode(',:', array_keys($dataBatch[0]));

        $query = "INSERT INTO {$tableName} ({$fields}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($query);

        foreach ($dataBatch as $data) {
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
        }
    }
}
