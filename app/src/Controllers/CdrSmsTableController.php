<?php

namespace app\src\Controllers;

require_once __DIR__ . '/../../../vendor/autoload.php';

use app\src\Models\DbConnectionReporting;
use Faker\Factory as Faker;
use app\src\Traits\ValidDataGeneratorTrait;
use PDOException;
use Exception;
use Dotenv\Dotenv;
use config\Logging;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

class CdrSmsTableController
{
    private $faker;
    public $nodeIds = [];
    private $pdo;


    use ValidDataGeneratorTrait;

    public function __construct()
    {
        $dbConnection = DbConnectionReporting::getInstance();
        $this->pdo = $dbConnection->getConnection();
        $this->faker = Faker::create();
    }

    /**
     * Generates SMSMO fields based on the traffic type and date range.
     *
     * @param string $trafficType The type of traffic, which can be 'local_onnet' or other types.
     * @param string $startDate The start date for the date range to generate timestamps.
     * @param string $endDate The end date for the date range to generate timestamps.
     *
     * @return array An array of SMSMO records.
     */
    private function generateSMSMOFields(string $trafficType, $startDate, $endDate): array
    {
        try {
            $oa = $this->generateMSISDN('local_onnet');
            $da = $this->generateMSISDN($trafficType);
            $trafficType = (substr($da, 0, 2) === '94') ? 'local' : 'international';

            $localMscGtArray = explode(',', $_ENV['LOCAL_MSC_GT']);
            $intlMscGtArray = explode(',', $_ENV['INTL_MSC_GT']);
            $localMscGt = $this->faker->randomElement($localMscGtArray);
            $intlMscGt = $this->faker->randomElement($intlMscGtArray);
            // Determine the MSC_GT values based on whether DA is local or international
            $mscGt = ($trafficType === 'local') ? $localMscGt : $intlMscGt;
            $trafficType = (substr($da, 0, 2) === '94') ? 'local' : 'international';

            $dateRange = $this->generateDateRange($startDate, $endDate);
            $createdAt = $this->faker->dateTimeBetween($dateRange['start'], $dateRange['end']);
            $reference = $this->generateReference($createdAt);

            $dcs = $this->faker->numberBetween(0, 241);
            $pid = $this->faker->numberBetween(0, 64);
            $sar_ref = $this->faker->numberBetween(1, 128);

            $numParts = rand(1, 4);
            $isUnicode = (bool)rand(0, 1);
            $messageContents = $this->generateSMSContent($numParts, $isUnicode);
            $records = [];

            foreach ($messageContents as $contentData) {
                $id = $this->generateAutoIncrementId();
                $records[] =  [
                    'id' => $id,
                    'created_at' => $createdAt->format('Y-m-d H:i:s'),
                    'protocol' => 'ss7',
                    'type' => 'smsmo',
                    'reference' => $reference,
                    'sri_time' => "\N",
                    'sri_calling_gt' => "\N",
                    'sri_map_gt' => "\N",
                    'imsi' => $this->generateIMSI($oa),
                    'virtual_imsi' => "\N",
                    'virtual_vlr_gt' => "\N",
                    'fwdsm_time' => $createdAt->format('Y-m-d H:i:s'),
                    'fwdsm_calling_gt' => $mscGt,
                    'fwdsm_map_gt' => $mscGt,
                    'esme_ip' => "\N",
                    'esme_port' => "\N",
                    'smsc_ip' => "\N",
                    'smsc_port' => "\N",
                    'system_id' => "\N",
                    'message_id' => "\N",
                    'dlr_time' => "\N",
                    'dlr_status' => "\N",
                    'oa' => $oa,
                    'da' => $da,
                    'dcs' => $dcs,
                    'pid' => $pid,
                    'tpdu_length' => $contentData['tpdu_length'],
                    'sar_ref' => $sar_ref,
                    'msg_part' => $contentData['msg_part'],
                    'msg_parts' => $contentData['msg_parts'],
                    'status' =>  'success',
                    'error_major' => "\N",
                    'error_minor' => "\N",
                    'error_description' => "\N",
                    'content' => $contentData['content'],
                    'rule_id' => "\N",
                    'action_id' => 0,
                    'node_id' => $this->faker->randomElement($this->nodeIds),
                    'traffic_type' => $trafficType
                ];
            }

            return $records;
        } catch (Exception $e) {
            Logging::logError('Error generating SMSMO fields: ' . $e->getMessage());
            throw new Exception('Error generating SMSMO fields: ' . $e->getMessage());
        }
    }

    /**
     * Generates SRI (Status Report Indication) fields based on common values.
     *
     * @param array $commonValues An associative array containing common values for the SRI fields.
     * @return array An associative array containing the generated SRI fields.
     */
    private function generateSRIFields(array $commonValues): array
    {
        try {
            $id = $this->generateAutoIncrementId();

            return [
                'id' => $id,
                'created_at' => $commonValues['sri_created_at']->format('Y-m-d H:i:s'),
                'protocol' => 'ss7',
                'type' => 'sri',
                'reference' => $commonValues['reference'],
                'sri_time' => $commonValues['sri_created_at']->format('Y-m-d H:i:s'),
                'sri_calling_gt' => $commonValues['smscGt'],
                'sri_map_gt' => $commonValues['smscGt'],
                'imsi' => $commonValues['imsi'],
                'virtual_imsi' => $commonValues['virtual_imsi'],
                'virtual_vlr_gt' => "\N",
                'fwdsm_time' => "\N",
                'fwdsm_calling_gt' => "\N",
                'fwdsm_map_gt' => "\N",
                'esme_ip' => "\N",
                'esme_port' => "\N",
                'smsc_ip' => "\N",
                'smsc_port' => "\N",
                'system_id' => "\N",
                'message_id' => "\N",
                'dlr_time' => "\N",
                'dlr_status' => "\N",
                'oa' => "\N",
                'da' => $commonValues['da'],
                'dcs' => "\N",
                'pid' => "\N",
                'tpdu_length' => "\N",
                'sar_ref' => "\N",
                'msg_part' => "\N",
                'msg_parts' => "\N",
                'status' => 'success',
                'error_major' => "\N",
                'error_minor' => "\N",
                'error_description' => "\N",
                'content' => "\N",
                'rule_id' => "\N",
                'action_id' => 0,
                'node_id' => $this->faker->randomElement($this->nodeIds),
                'traffic_type' => "\N"
            ];
        } catch (Exception $e) {
            Logging::logError('Error generating SRI fields: ' . $e->getMessage());
            throw new Exception('Error generating SRI fields: ' . $e->getMessage());
        }
    }

    /**
     * Generates fields for SMSMT records.
     *
     * @param array $commonValues Common values shared between SRI and SMSMT records.
     * @return array An array of associative arrays, each representing an SMSMT record. 
     */
    private function generateSMSMTFields(array $commonValues): array
    {
        try {

            $trafficType = (substr($commonValues['smscGt'], 0, 2) === '94') ? 'local' : 'international';
            $smsmtCreatedAt = clone $commonValues['sri_created_at'];
            $randomSeconds = rand(0, 2);
            $smsmtCreatedAt->modify("+{$randomSeconds} seconds");
            $smsfwGtArray = explode(',', $_ENV['SMSFW_GT']);
            $smsfwGt = $this->faker->randomElement($smsfwGtArray);

            $dcs = $this->faker->numberBetween(0, 241);
            $pid = $this->faker->numberBetween(0, 64);
            $sar_ref = $this->faker->numberBetween(1, 128);

            $numParts = rand(1, 4);
            $isUnicode = (bool)rand(0, 1);
            $messageContents = $this->generateSMSContent($numParts, $isUnicode);

            $records = [];
            foreach ($messageContents as $contentData) {
                $id = $this->generateAutoIncrementId();
                $records[] = [
                    'id' => $id,
                    'created_at' => $smsmtCreatedAt->format('Y-m-d H:i:s'),
                    'protocol' => 'ss7',
                    'type' => 'smsmt',
                    'reference' => $commonValues['reference'],
                    'sri_time' => "\N",
                    'sri_calling_gt' => "\N",
                    'sri_map_gt' => "\N",
                    'imsi' => $commonValues['imsi'],
                    'virtual_imsi' => $commonValues['virtual_imsi'],
                    'virtual_vlr_gt' => $smsfwGt,
                    'fwdsm_time' => $smsmtCreatedAt->format('Y-m-d H:i:s'),
                    'fwdsm_calling_gt' => $commonValues['smscGt'],
                    'fwdsm_map_gt' => $commonValues['smscGt'],
                    'esme_ip' => "\N",
                    'esme_port' => "\N",
                    'smsc_ip' => "\N",
                    'smsc_port' => "\N",
                    'system_id' => "\N",
                    'message_id' => "\N",
                    'dlr_time' => "\N",
                    'dlr_status' => "\N",
                    'oa' => $commonValues['oa'],
                    'da' => $commonValues['da'],
                    'dcs' => $dcs,
                    'pid' => $pid,
                    'tpdu_length' => $contentData['tpdu_length'],
                    'sar_ref' => $sar_ref,
                    'msg_part' => $contentData['msg_part'],
                    'msg_parts' => $contentData['msg_parts'],
                    'status' => 'success',
                    'error_major' => "\N",
                    'error_minor' => "\N",
                    'error_description' => "\N",
                    'content' => $contentData['content'],
                    'rule_id' => "\N",
                    'action_id' => 0,
                    'node_id' => $this->faker->randomElement($this->nodeIds),
                    'traffic_type' => $trafficType
                ];
            }

            return $records;
        } catch (Exception $e) {
            Logging::logError('Error generating SMSMT fields: ' . $e->getMessage());
            throw new Exception('Error generating SMSMT fields: ' . $e->getMessage());
        }
    }



    /**
     * Generates common values used for both SMSMT and SRI records.
     * These values include originator address (OA), destination address (DA),
     * IMSI, virtual IMSI, SMSC GT, and timestamp for SRI creation.
     * @param string $trafficType The type of traffic, used to determine the origin address (OA) format.
     * @param string $startDate The start date of the range for generating timestamps.
     * @param string $endDate The end date of the range for generating timestamps.
     *
     * @return array An associative array containing common values for SMSMT records. 
     */
    private function generateCommonSMSMTValues(string $trafficType, string $startDate, string $endDate): array
    {
        try {
            $dateRange = $this->generateDateRange($startDate, $endDate);

            $useName = $this->faker->boolean($_ENV['OA_NAMES_PERCENTAGE']);

            if ($useName) {

                $oaName = $this->faker->randomElement(explode(',', $_ENV['OA_NAMES']));
                $oa = $oaName;
            } else {

                $oaMSISDN = $this->generateMSISDN($trafficType);
                $oa = $oaMSISDN;
            }

            $oaNames = explode(',', $_ENV['OA_NAMES']);
            $oaType = (substr($oa, 0, 2) === '94' || in_array($oa, $oaNames)) ? 'local' : 'international';

            $localSmscGtArray = explode(',', $_ENV['OLO_SMSC_GT']);
            $intlSmscGtArray = explode(',', $_ENV['INTL_SMSC_GT']);
            $localSmscGt = $this->faker->randomElement($localSmscGtArray);
            $intlSmscGt = $this->faker->randomElement($intlSmscGtArray);
            $smscGt = ($oaType === 'local') ? $localSmscGt : $intlSmscGt;

            $da = $this->generateMSISDN('local_onnet');

            $sriCreatedAt = $this->faker->dateTimeBetween($dateRange['start'], $dateRange['end']);
            $virtualImsi = $this->generateVirtualIMSI();

            // Adjust the created_at timestamp if the virtual IMSI has been used recently
            $sriCreatedAt = isset($this->lastUsedTimestamps[$virtualImsi]) &&
                ($sriCreatedAt->getTimestamp() - $this->lastUsedTimestamps[$virtualImsi]->getTimestamp() < 1800)
                ? $sriCreatedAt->modify('+30 minutes')
                : $sriCreatedAt;

            // Update the last used timestamp for this virtual IMSI
            $this->lastUsedTimestamps[$virtualImsi] = $sriCreatedAt;

            return [
                'reference' => $this->generateReference($sriCreatedAt),
                'imsi' => $this->generateIMSI($da),
                'virtual_imsi' => $virtualImsi,
                'da' => $da,
                'oa' => $oa,
                'smscGt' => $smscGt, //Decided the smsgt based on oa(local, intl)
                'sri_created_at' => $sriCreatedAt
            ];
        } catch (Exception $e) {
            Logging::logError('Error generating common SRI SMSMT fields: ' . $e->getMessage());
            throw new Exception('Error generating common SRI SMSMT fields: ' . $e->getMessage());
        }
    }


    /**
     * Generate SMPP fields for SMS records within a specified date range.
     *
     * @param string $smpptrafficType The type of SMPP traffic ('local' or 'international').
     * @param string $startDate The start date for the record generation.
     * @param string $endDate The end date for the record generation.
     * @return array The generated records for SMPP fields.
     */
    private function generateSMPPFields(string $smpptrafficType, string $startDate, string $endDate): array
    {
        try {
            // Determine if the traffic is local or international
            $isLocal = $smpptrafficType === 'local';

            // Retrieve the corresponding values from the .env file
            $systemIds = explode(',', $isLocal ? $_ENV['LOCAL_SYSTEM_IDS'] : $_ENV['INTL_SYSTEM_IDS']);
            $esmeIps = explode(',', $isLocal ? $_ENV['LOCAL_ESME_IPS'] : $_ENV['INTL_ESME_IPS']);
            $virtualVlrGts = explode(',', $isLocal ? $_ENV['LOCAL_VIRTUAL_VLR_GT'] : $_ENV['INTL_VIRTUAL_VLR_GT']);
            $oas = explode(',', $isLocal ? $_ENV['SMPP_OA_LOCAL'] : $_ENV['SMPP_OA_INTL']);

            //Randomly select a system_id, esme_ip, and virtual_vlr_gt ensuring they are unique to each other
            // $selectedIndex = array_rand($systemIds);
            // $systemId = $systemIds[$selectedIndex];
            // $esmeIp = $esmeIps[$selectedIndex];
            // $virtualVlrGt = $virtualVlrGts[$selectedIndex];
            // $oa = $oas[$selectedIndex];

            $smppMapping = [];
            foreach ($systemIds as $index => $systemId) {
                $smppMapping[$systemId] = [
                    'esme_ip' => $esmeIps[$index],
                    'virtual_vlr_gt' => $virtualVlrGts[$index],
                    'oa' => $oas[$index]
                ];
            }

            // Generate the date range
            $dateRange = $this->generateDateRange($startDate, $endDate);
            $createdAt =  $this->faker->dateTimeBetween($dateRange['start'], $dateRange['end']);
            $sarRef = $this->faker->numberBetween(1, 128);
            $messageId = $this->generateReference($createdAt);

            // Generate message content
            $numParts = rand(1, 4);
            $isUnicode = (bool)rand(0, 1);
            $messageContents = $this->generateSMSContent($numParts, $isUnicode);

            $records = [];
            foreach ($messageContents as $contentData) {
                $id = $this->generateAutoIncrementId();

                // Randomly select a system_id and its corresponding esme_ip and virtual_vlr_gt
                $systemId = $this->faker->randomElement(array_keys($smppMapping));
                $esmeIp = $smppMapping[$systemId]['esme_ip'];
                $virtualVlrGt = $smppMapping[$systemId]['virtual_vlr_gt'];
                $oa =  $smppMapping[$systemId]['oa'];

                $trafficType = (in_array($virtualVlrGt, explode(',', $_ENV['LOCAL_VIRTUAL_VLR_GT']))) ? 'local' : 'international';

                $records[] = [
                    'id' => $id,
                    'created_at' => $createdAt->format('Y-m-d H:i:s'),
                    'protocol' => 'smpp',
                    'type' => 'smsmt',
                    'reference' => "\N",
                    'sri_time' => "\N",
                    'sri_calling_gt' => "\N",
                    'sri_map_gt' => "\N",
                    'imsi' => "\N",
                    'virtual_imsi' => "\N",
                    'virtual_vlr_gt' => $virtualVlrGt,
                    'fwdsm_time' => "\N",
                    'fwdsm_calling_gt' => "\N",
                    'fwdsm_map_gt' => "\N",
                    'esme_ip' => $esmeIp,
                    'esme_port' => "\N",
                    'smsc_ip' => "\N",
                    'smsc_port' => "\N",
                    'system_id' => $systemId,
                    'message_id' => $messageId,
                    'dlr_time' => "\N",
                    'dlr_status' => "\N",
                    'oa' => $oa,
                    'da' => $this->generateMSISDN('local_onnet'),
                    'dcs' => "\N",
                    'pid' => "\N",
                    'tpdu_length' => $contentData['tpdu_length'],
                    'sar_ref' => $sarRef,
                    'msg_part' => $contentData['msg_part'],
                    'msg_parts' => $contentData['msg_parts'],
                    'status' => 'success',
                    'error_major' => "\N",
                    'error_minor' => "\N",
                    'error_description' => "\N",
                    'content' => $contentData['content'],
                    'rule_id' => "\N",
                    'action_id' => 0,
                    'node_id' => $this->faker->randomElement($this->nodeIds),
                    'traffic_type' => $trafficType
                ];
            }

            return $records;
        } catch (Exception $e) {
            Logging::logError('Failed to generate SMPP fields: ' . $e->getMessage());
            throw new Exception('Failed to generate SMPP fields: ' . $e->getMessage());
        }
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
     * Generates a CSV file with the specified number of cdr_sms records.
     * It creates records based on percentages defined in the environment variables.
     *
     * @param int $rowCount The total number of rows to generate in the CSV file.
     * @return string The path to the generated CSV file.
     */
    public function generateCSV(int $rowCount, string $startDate, string $endDate): string
    {
        try {
            $this->getNodeIds();
            $fileName = "cdr_sms_" . uniqid() . '.csv';
            $filePath = $_ENV['FILE_PATH'] . "/{$fileName}";

            $csvFile = fopen($filePath, 'w');

            if ($csvFile === false) {
                throw new Exception('Failed to open file for writing.');
            }

            $header = [
                'id',
                'created_at',
                'protocol',
                'type',
                'reference',
                'sri_time',
                'sri_calling_gt',
                'sri_map_gt',
                'imsi',
                'virtual_imsi',
                'virtual_vlr_gt',
                'fwdsm_time',
                'fwdsm_calling_gt',
                'fwdsm_map_gt',
                'esme_ip',
                'esme_port',
                'smsc_ip',
                'smsc_port',
                'system_id',
                'message_id',
                'dlr_time',
                'dlr_status',
                'oa',
                'da',
                'dcs',
                'pid',
                'tpdu_length',
                'sar_ref',
                'msg_part',
                'msg_parts',
                'status',
                'error_major',
                'error_minor',
                'error_description',
                'content',
                'rule_id',
                'action_id',
                'node_id',
                'traffic_type'
            ];

            fputcsv($csvFile, $header);

            $recordTypePercentages = [
                'smsmo' => $_ENV['SMSMO'],
                'srismsmt' => $_ENV['SRI_SMSMT'],
                'smpp' => $_ENV['SMPP']
            ];

            $trafficTypePercentagesSMSMO = [
                'local_onnet' => $_ENV['SMSMO_LOCAL_ONNET'],
                'local_olo' => $_ENV['SMSMO_LOCAL_OLO'],
                'international' => $_ENV['SMSMO_INTERNATIONAL']
            ];

            $trafficTypePercentagesSRISMSMT = [
                'local_olo' => $_ENV['SRI_SMSMT_LOCAL_OLO'],
                'international' => $_ENV['SRI_SMSMT_INTERNATIONAL']
            ];

            $trafficTypePercentagesSMPP = [
                'local' => $_ENV['SMPP_LOCAL'],
                'international' => $_ENV['SMPP_INTERNATIONAL']
            ];

            // Calculate the distribution of records for SMSMO, SRI and SMSMT by traffic type
            $numSMSMO = round($rowCount * ($recordTypePercentages['smsmo'] / 100));
            $numSriSmsmtPairs = round($rowCount * ($recordTypePercentages['srismsmt'] / 100));
            $numSMPP = round($rowCount * ($recordTypePercentages['smpp'] / 100));

            $numSmsmoOnnet = round($numSMSMO * ($trafficTypePercentagesSMSMO['local_onnet'] / 100));
            $numSmsmoOlo = round($numSMSMO * ($trafficTypePercentagesSMSMO['local_olo'] / 100));
            $numSmsmoIntl = round($numSMSMO * ($trafficTypePercentagesSMSMO['international'] / 100));

            $numSmsmtOnnet = round($numSriSmsmtPairs * ($trafficTypePercentagesSRISMSMT['local_olo'] / 100));
            $numSmsmtIntl = round($numSriSmsmtPairs * ($trafficTypePercentagesSRISMSMT['international'] / 100));

            $numSmppLocal = round($numSMPP * ($trafficTypePercentagesSMPP['local'] / 100));
            $numSmppIntl = round($numSMPP * ($trafficTypePercentagesSMPP['international'] / 100));

            // Generate SMSMO records
            $this->generateSMSMORecords($numSmsmoOnnet, 'local_onnet', $startDate, $endDate, $csvFile);
            $this->generateSMSMORecords($numSmsmoOlo, 'local_olo', $startDate, $endDate, $csvFile);
            $this->generateSMSMORecords($numSmsmoIntl, 'international', $startDate, $endDate, $csvFile);

            // Generate pairs of SRI and SMSMT records
            $this->generateSRISMSMTRecords($numSmsmtOnnet, 'local_olo', $startDate, $endDate, $csvFile);
            $this->generateSRISMSMTRecords($numSmsmtIntl, 'international', $startDate, $endDate, $csvFile);

            // Generate SMPP records
            $this->generateSMPPRecords($numSmppLocal, 'local', $startDate, $endDate, $csvFile);
            $this->generateSMPPRecords($numSmppIntl, 'international', $startDate, $endDate, $csvFile);


            fclose($csvFile);

            Logging::logInfo("CSV file generated at: " . $filePath);
            echo "CSV file generated at: {$filePath}";


            $this->uploadCSV($filePath, implode(',', $header));

            return $filePath;
        } catch (Exception $e) {
            Logging::logError('Error generating CSV: ' . $e->getMessage());
            throw new Exception('Error generating CSV: ' . $e->getMessage());
        }
    }

    /**
     * Uploads data from a CSV file to the 'cdr_sms' table in the database.
     *
     * @param string $filePath The path to the CSV file.
     * @param string $header The column names in the CSV file, used for the SQL query.
     * 
     * @return void
     * 
     * @throws Exception If the file does not exist or there is a database error.
     */
    public function uploadCSV(string $filePath, string $header): void
    {
        try {
            // Check if file exists
            if (!file_exists($filePath)) {
                throw new Exception('CSV file does not exist.');
            }

            // Define the SQL query for loading data
            $importQuery = "
            LOAD DATA INFILE '{$filePath}'
            INTO TABLE cdr_sms
            FIELDS TERMINATED BY ',' 
            ENCLOSED BY '\"'
            LINES TERMINATED BY '\\n'
            IGNORE 1 LINES
            ({$header})
            ";

            // Prepare and execute the query
            $stmt = $this->pdo->prepare($importQuery);
            $stmt->execute();

            // Log success message
            Logging::logInfo('CSV file uploaded to database successfully.');
        } catch (PDOException $e) {
            // Handle PDO exceptions
            Logging::logError('Database error: ' . $e->getMessage());
            throw new Exception('Database error: ' . $e->getMessage());
        }
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
}
