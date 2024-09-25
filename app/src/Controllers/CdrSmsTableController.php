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
    private $smppMapping = [];
    private $systemId;


    use ValidDataGeneratorTrait;

    public function __construct()
    {
        $dbConnection = DbConnectionReporting::getInstance();
        $this->pdo = $dbConnection->getConnection();
        $this->faker = Faker::create();
        // $this->duplicateChance = 0.5;

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
    public function generateSMSMOFields(string $trafficType, $startDate, $endDate): array
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
    private function generateSRIFields(array $commonValue): array
    {
        try {
            // Generate and return a single SRI record
            $id = $this->generateAutoIncrementId();

            return [
                'id' => $id,
                'created_at' => $commonValue['sri_created_at']->format('Y-m-d H:i:s'),
                'protocol' => 'ss7',
                'type' => 'sri',
                'reference' => $commonValue['reference'],
                'sri_time' => $commonValue['sri_created_at']->format('Y-m-d H:i:s'),
                'sri_calling_gt' => $commonValue['smscGt'],
                'sri_map_gt' => $commonValue['smscGt'],
                'imsi' => $commonValue['imsi'],
                'virtual_imsi' => $commonValue['virtual_imsi'],
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
                'da' => $commonValue['da'],
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
    private function generateSMSMTFields(array $commonValue): array
    {
        try {
            $smsmtRecords = [];
            $trafficType = (substr($commonValue['smscGt'], 0, 2) === '94') ? 'local' : 'international';
            $smsmtCreatedAt = clone $commonValue['sri_created_at'];
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

            foreach ($messageContents as $contentData) {
                $id = $this->generateAutoIncrementId();

                $smsmtRecords[] = [
                    'id' => $id,
                    'created_at' => $smsmtCreatedAt->format('Y-m-d H:i:s'),
                    'protocol' => 'ss7',
                    'type' => 'smsmt',
                    'reference' => $commonValue['reference'],
                    'sri_time' => "\N",
                    'sri_calling_gt' => "\N",
                    'sri_map_gt' => "\N",
                    'imsi' => $commonValue['imsi'],
                    'virtual_imsi' => $commonValue['virtual_imsi'],
                    'virtual_vlr_gt' => $smsfwGt,
                    'fwdsm_time' => $smsmtCreatedAt->format('Y-m-d H:i:s'),
                    'fwdsm_calling_gt' => $commonValue['smscGt'],
                    'fwdsm_map_gt' => $commonValue['smscGt'],
                    'esme_ip' => "\N",
                    'esme_port' => "\N",
                    'smsc_ip' => "\N",
                    'smsc_port' => "\N",
                    'system_id' => "\N",
                    'message_id' => "\N",
                    'dlr_time' => "\N",
                    'dlr_status' => "\N",
                    'oa' => $commonValue['oa'],
                    'da' => $commonValue['da'],
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

            return $smsmtRecords;
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
    public function generateCommonSMSMTValues(string $trafficType, string $startDate, string $endDate, int $numSriSmsmtPairs): array
    {
        try {
            $commonSMSMTValues = []; // Initialize array to store the values
            $lastUsedTimestamps = []; // To track timestamps for each virtual IMSI

            // Generate an array of sequential timestamps
            $timestamps = $this->generateSequentialTimestamps($startDate, $endDate, $numSriSmsmtPairs);

            foreach ($timestamps as $sriCreatedAt) {
                // Generate a new OA (originating address) for each pair
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

                // Generate a new DA (destination address) for each pair
                $da = $this->generateMSISDN('local_onnet');

                $localSmscGtArray = explode(',', $_ENV['OLO_SMSC_GT']);
                $intlSmscGtArray = explode(',', $_ENV['INTL_SMSC_GT']);
                $localSmscGt = $this->faker->randomElement($localSmscGtArray);
                $intlSmscGt = $this->faker->randomElement($intlSmscGtArray);
                $smscGt = ($oaType === 'local') ? $localSmscGt : $intlSmscGt;

                // Generate the virtual IMSI
                $virtualImsi = $this->generateVirtualIMSI();

                // Ensure the timestamp has a minimum 30-minute gap if needed
                if (isset($lastUsedTimestamps[$virtualImsi])) {
                    $lastUsedTimestamp = $lastUsedTimestamps[$virtualImsi];
                    $updatedTimestamp = (clone $lastUsedTimestamp)->modify('+30 minutes');

                    if ($sriCreatedAt < $updatedTimestamp) {
                        $sriCreatedAt = $updatedTimestamp;
                    }
                }

                // Update the last used timestamp for this virtual IMSI
                $lastUsedTimestamps[$virtualImsi] = $sriCreatedAt;

                // Build the common SMS-MT value set
                $commonSMSMTValues[] = [
                    'reference' => $this->generateReference($sriCreatedAt),
                    'imsi' => $this->generateIMSI($da),
                    'virtual_imsi' => $virtualImsi,
                    'da' => $da,
                    'oa' => $oa,
                    'smscGt' => $smscGt,
                    'sri_created_at' => $sriCreatedAt,
                ];
            }

            return $commonSMSMTValues;
        } catch (Exception $e) {
            Logging::logError('Error generating common SMS MT values: ' . $e->getMessage());
            throw new Exception('Error generating common SMS MT values: ' . $e->getMessage());
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
    public function generateSMPPFields(string $smpptrafficType, string $startDate, string $endDate): array
    {
        try {
            $isLocal = $smpptrafficType === 'local';
            $oaType = $isLocal ? $this->faker->randomElement(['oa', 'short_code', 'msisdn']) : 'oa';

            // Decode JSON data
            $localTrafficData = json_decode($_ENV['SMPP_OA_LOCAL_JSON'], true);
            $intlTrafficData = json_decode($_ENV['SMPP_OA_INTL_JSON'], true);
            $shortCodeData = json_decode($_ENV['SMPP_SHORT_CODES_JSON'], true);
            $msisdnData = json_decode($_ENV['SMPP_MSISDN_OA_LOCAL_JSON'], true);           

            // Select the appropriate traffic data
            switch ($oaType) {
                case 'oa':
                    $trafficData = $isLocal ? $localTrafficData : $intlTrafficData;
                    break;
                case 'short_code':
                    $trafficData = $shortCodeData;
                    break;
                case 'msisdn':
                    $trafficData = $msisdnData;
                    break;
                default:
                    $trafficData = $isLocal ? $localTrafficData : $intlTrafficData;
            }

            // Randomly select index from the selected traffic data
            $oaIndex = array_rand($trafficData['oa']);
            $oa = $trafficData['oa'][$oaIndex];
            $systemId = $trafficData['system_ids'][$oaIndex];
            $virtualVlrGt = $trafficData['vlr_gts'][$oaIndex];
            $esmeIp = $trafficData['esme_ips'][$oaIndex];
            $smscIp = $trafficData['smsc_ips'][$oaIndex];
            $smscPort = $trafficData['smsc_ports'][$oaIndex];

            $esmePorts = $this->generatePorts($_ENV['NUM_ESME_PORTS'], $_ENV['ESME_PORT_START'], $_ENV['ESME_PORT_END']);

            if (!isset($this->smppMapping[$systemId])) {

                $this->smppMapping[$systemId] = [
                    'esme_ip' => $esmeIp,
                    'virtual_vlr_gt' => $virtualVlrGt,
                    'oa' => $oa,
                    'esme_ports' => $esmePorts,
                    'smsc_ip' => $smscIp,
                    'smsc_port' => $smscPort,
                ];
            }

            $dateRange = $this->generateDateRange($startDate, $endDate);
            $createdAt = $this->faker->dateTimeBetween($dateRange['start'], $dateRange['end']);
            $dlrTime = (clone $createdAt)->modify('+1 second');

            $sarRef = $this->faker->numberBetween(1, 128);
            $messageId = $this->generateReference($createdAt);

            $numParts = rand(1, 4);
            $isUnicode = (bool)rand(0, 1);
            $messageContents = $this->generateSMSContent($numParts, $isUnicode);

            $da = $this->generateMSISDN('local_onnet');

            $esmeIp = $this->smppMapping[$systemId]['esme_ip'];
            $esmePort = $this->faker->randomElement($this->smppMapping[$systemId]['esme_ports']);

            $virtualVlrGt = $this->smppMapping[$systemId]['virtual_vlr_gt'];
            $oa = $this->smppMapping[$systemId]['oa'];

            $smscIp = $this->smppMapping[$systemId]['smsc_ip'];
            $smscPort = $this->smppMapping[$systemId]['smsc_port'];

            $records = [];
            foreach ($messageContents as $contentData) {

                $trafficType = $isLocal ? 'local' : 'international';

                $id = $this->generateAutoIncrementId();
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
                    'esme_port' => $esmePort,
                    'smsc_ip' => $smscIp,
                    'smsc_port' => $smscPort,
                    'system_id' => $systemId,
                    'message_id' => $messageId,
                    'dlr_time' => $dlrTime->format('Y-m-d H:i:s'),
                    'dlr_status' => "success",
                    'oa' => $oa,
                    'da' => $da,
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

            $numSmsmtOlo = round($numSriSmsmtPairs * ($trafficTypePercentagesSRISMSMT['local_olo'] / 100));
            $numSmsmtIntl = round($numSriSmsmtPairs * ($trafficTypePercentagesSRISMSMT['international'] / 100));

            $numSmppLocal = round($numSMPP * ($trafficTypePercentagesSMPP['local'] / 100));
            $numSmppIntl = round($numSMPP * ($trafficTypePercentagesSMPP['international'] / 100));

            // Generate SMSMO records
            $this->generateSMSMORecords($numSmsmoOnnet, 'local_onnet', $startDate, $endDate, $csvFile);
            $this->generateSMSMORecords($numSmsmoOlo, 'local_olo', $startDate, $endDate, $csvFile);
            $this->generateSMSMORecords($numSmsmoIntl, 'international', $startDate, $endDate, $csvFile);
            Logging::logInfo("SMSMO Records generated ");


            // Generate pairs of SRI and SMSMT records
            $this->generateSRISMSMTRecords('local_olo', $startDate, $endDate, $numSmsmtOlo, $csvFile);
            $this->generateSRISMSMTRecords('international', $startDate, $endDate, $numSmsmtIntl, $csvFile);
            Logging::logInfo("SRI and SMSMT Records generated");

            // Generate SMPP records
            $this->generateSMPPRecords($numSmppLocal, 'local', $startDate, $endDate, $csvFile);
            $this->generateSMPPRecords($numSmppIntl, 'international', $startDate, $endDate, $csvFile);
            Logging::logInfo("SMPP Records generated");

            fclose($csvFile);

            Logging::logInfo("CSV file generated at: {$filePath}");
            echo "CSV file generated at: {$filePath}";

            $this->uploadCSV($filePath, implode(',', $header));

            return $filePath;
        } catch (Exception $e) {
            Logging::logError('Error generating CSV: ' . $e->getMessage());
            throw new Exception('Error generating CSV: ' . $e->getMessage());
        }
    }

    /**
     * Generates a specified number of SMSMO records and writes them to the CSV file.
     *
     * @param int $numRecords The number of SMSMO records to generate.
     * @param string $trafficType The traffic type (local_onnet, local_olo, international).
     * @param resource $csvFile The open CSV file resource.
     */
    public function generateSMSMORecords(int $numRecords, string $trafficType, string $startDate, string $endDate, $csvFile): void
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
    public function generateSRISMSMTRecords($trafficType, string $startDate, string $endDate, $numSriSmsmtPairs, $csvFile)
    {

        // Generate common values used for both SRI and SMSMT
        $commonValues = $this->generateCommonSMSMTValues($trafficType, $startDate, $endDate, $numSriSmsmtPairs);

        foreach ($commonValues as $commonValue) {
            // Generate and write the SRI record
            $sriRecord = $this->generateSRIFields($commonValue);
            fputcsv($csvFile, $sriRecord);

            // Generate and write the related SMSMT records
            $smsmtRecords = $this->generateSMSMTFields($commonValue);
            foreach ($smsmtRecords as $smsmtRecord) {
                fputcsv($csvFile, $smsmtRecord);
            }
        }
    }


    public function generateSMPPRecords(int $numRecords, string $trafficType, string $startDate, string $endDate, $csvFile): void
    {
        for ($i = 0; $i < $numRecords; $i++) {
            $smppRecords = $this->generateSMPPFields($trafficType, $startDate, $endDate);
            foreach ($smppRecords as $smppRecord) {
                fputcsv($csvFile, $smppRecord);
            }
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
}
