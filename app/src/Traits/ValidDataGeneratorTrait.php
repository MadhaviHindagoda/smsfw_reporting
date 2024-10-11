<?php

namespace app\src\Traits;

require_once __DIR__ . '/../../../vendor/autoload.php';

use DateTime;
use Dotenv\Dotenv;
use InvalidArgumentException;
use PDOException;
use Exception;
use PDO;

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
    public function generateAutoIncrementId(): int
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

        // Check if this virtual IMSI has been used before
        if (!isset($this->lastUsedTimestamps[$virtualIMSI])) {
            // If not, initialize the timestamp with the current time
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
    public function generateReference($createdAt): string
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
    public function generateDateRange(string $startDate, string $endDate): array
    {
        return [
            'start' => $startDate . ' 00:00:00',
            'end' => $endDate . ' 23:59:59'
        ];
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

    /**
     * Generates an array of unique ports.
     *
     * @param int $numPorts The number of ports to generate.
     * @return array An array of unique ports.
     */
    private function generatePorts(int $numPorts, int $start, int $end)
    {
        for ($i = 0; $i < $numPorts; $i++) {
            // Generate a unique port within the specified range
            $port = $this->faker->numberBetween($start, $end);
            $ports[] = $port;
        }
        return $ports;
    }

    /**
     * Generates SMS content split into multiple parts based on the number of parts and whether Unicode is used.
     *
     * @param int $numParts The number of parts to split the SMS content into. This determines how many separate records will be generated.
     * @param bool $isUnicode Indicates whether the SMS content should use Unicode encoding. If true, the content length and handling will be adjusted for Unicode.
     * 
     * @return array An array of associative arrays, each representing a part of the SMS content. Each part includes:
     *     - 'msg_part' (int): The part number of the message.
     *     - 'msg_parts' (int): Total number of parts in the message.
     *     - 'content' (string): The content of the SMS part.
     *     - 'tpdu_length' (int): The length of the TPDU (Transport Protocol Data Unit) in characters.
     */

    private function generateSMSContent(int $numParts, bool $isUnicode): array
    {
        $contentParts = [];

        $partLength = $isUnicode ? 67 : 153;

        // Generate a long content text
        $longConLength = $partLength * $numParts * 2;
        $longContent = $this->faker->text($longConLength);

        for ($part = 1; $part <= $numParts; $part++) {
            if ($numParts === 1) {
                $charLength = $isUnicode ? rand(1, 70) : rand(1, 160);
            } elseif ($part === $numParts) {
                $charLength = $isUnicode ? rand(1, 67) : rand(1, 153);
            } else {
                $charLength = $partLength;
            }

            $content = mb_substr($longContent, 0, $charLength);
            $longContent = mb_substr($longContent, $charLength);

            $tpduLength = mb_strlen($content);

            $isUnicode && $content = $this->addUnicodeCharacter($content);

            $contentParts[] = [
                'msg_part' => $part,
                'msg_parts' => $numParts,
                'content' => $content,
                'tpdu_length' => $tpduLength,
            ];
        }

        return $contentParts;
    }



    /**
     * Adds a random Unicode character to the content string, optionally removing a character first.
     *
     * @param string $content The original content string to which a Unicode character will be added.
     * 
     * @return string The content string with a random Unicode character added.
     */
    private function addUnicodeCharacter(string $content): string
    {
        $length = mb_strlen($content);

        if ($length > 0) {
            $positionToRemove = random_int(0, $length - 1);
            $content = mb_substr($content, 0, $positionToRemove) . mb_substr($content, $positionToRemove + 1);
        }

        // Add a random Unicode character
        $codepoint = random_int(0x0020, 0x04FF);
        $unicodeCharacter = mb_chr($codepoint, 'UTF-8');

        // Insert the Unicode character at a random position
        $positionToInsert = random_int(0, mb_strlen($content));

        return mb_substr($content, 0, $positionToInsert) . $unicodeCharacter . mb_substr($content, $positionToInsert);
    }

    /**
     * Generates an array of sequential timestamps between the specified start and end dates.
     *
     * @param string $startDate The start date in 'Y-m-d H:i:s' format.
     * @param string $endDate The end date in 'Y-m-d H:i:s' format.
     * @param int $numSriSmsmtPairs The number of timestamp pairs (SRI and SMSMT) to generate.
     *
     * @return array An array of DateTime objects representing sequential timestamps.
     */

    public function generateRandomTimestamps(string $startDate, string $endDate, int $numTimestamps): array
    {

        $dateRange = $this->generateDateRange($startDate, $endDate);

        $timestamps = [];

        for ($i = 0; $i < $numTimestamps; $i++) {
            $createdAt = $this->faker->dateTimeBetween($dateRange['start'], $dateRange['end']);
            $timestamps[] = $createdAt->format('Y-m-d H:i:s');
        }

        sort($timestamps);

        return $timestamps;
    }


    /**
     * Retrieves node IDs by populating the 'nodes' table with test data and storing the IDs.
     * 
     * @return void
     * 
     * @throws Exception If there is an error during table truncation or data insertion.
     */
    public function getNodeIds(): void
    {
        $tableName = 'nodes';

        $this->truncateTable($tableName);

        $data = [];
        for ($i = 1; $i <= 3; $i++) {
            $data[] = [
                'id' => $i,
                'name' => $this->faker->company
            ];
        }

        $this->insertDataBatch($tableName, $data);

        $this->nodeIds = array_column($data, 'id');
    }

    private function distributeRowCountRandomly(int $totalCount, int $days): array
    {
        $distribution = [];
        $remaining = $totalCount;

        for ($i = 0; $i < $days - 1; $i++) {
            $randomCount = rand(1, (int)($remaining / ($days - $i)) * 2); 
            $distribution[] = $randomCount;
            $remaining -= $randomCount;
        }

        $distribution[] = $remaining;

        return $distribution;
    }
}
