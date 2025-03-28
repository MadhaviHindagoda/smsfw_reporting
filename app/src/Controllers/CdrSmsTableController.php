<?php

namespace app\src\Controllers;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/SmppMappingArrays.php';

use app\src\Models\DbConnectionReporting;
use app\src\Models\DbConnection;
use Faker\Factory as Faker;
use config\SmppMappingArrays;
use app\src\Traits\ValidDataGeneratorTrait;
use PDOException;
use Exception;
use Dotenv\Dotenv;
use config\Logging;
use PDO;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

class CdrSmsTableController
{
    private $faker;
    public $nodeIds = [];
    private $smppMapping = [];
    private $systemId;
    private $failureRate;
    private $pdoReporting;
    private $pdoMain;

    use ValidDataGeneratorTrait;

    public function __construct()
    {
        $dbConnection = DbConnectionReporting::getInstance();
        $this->pdoReporting = $dbConnection->getConnection();

        $dbConnectionMain = DbConnection::getInstance();
        $this->pdoMain = $dbConnectionMain->getConnection();

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
    public function generateSMSMOFields(string $trafficType, $startDate, $endDate, int $numMessages): array
    {
        try {
            $records = [];

            $timestamps = $this->generateRandomTimestamps($startDate, $endDate, $numMessages);

            foreach ($timestamps as $createdAt) {
                $oa = $this->generateMSISDN('local_onnet');
                $da = $this->generateMSISDN($trafficType);

                $traffictype = (substr($da, 0, 2) === '94') ? 'local' : 'international';

                $localMscGtArray = explode(',', $_ENV['LOCAL_MSC_GT']);
                $intlMscGtArray = explode(',', $_ENV['INTL_MSC_GT']);
                $localMscGt = $this->faker->randomElement($localMscGtArray);
                $intlMscGt = $this->faker->randomElement($intlMscGtArray);
                $mscGt = ($traffictype === 'local') ? $localMscGt : $intlMscGt;

                $reference = $this->generateReference(new \DateTime($createdAt));
                $numParts = rand(1, 4);
                $isUnicode = (bool)rand(0, 1);
                $messageContents = $this->generateSMSContent($numParts, $isUnicode);
                $sar_ref = $this->faker->numberBetween(1, 128);
                $dcs = $this->faker->numberBetween(0, 241);
                $pid = $this->faker->numberBetween(0, 64);


                foreach ($messageContents as $contentData) {
                    $id = $this->generateAutoIncrementId();

                    $records[] =  [
                        'id' => $id,
                        'created_at' => $createdAt,
                        'protocol' => 'ss7',
                        'type' => 'smsmo',
                        'reference' => $reference,
                        'sri_time' => "\N",
                        'sri_calling_gt' => "\N",
                        'sri_map_gt' => "\N",
                        'imsi' => $this->generateIMSI($oa),
                        'virtual_imsi' => "\N",
                        'virtual_vlr_gt' => "\N",
                        'fwdsm_time' => $createdAt,
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
                        'status' => 'success',
                        'error_major' => "\N",
                        'error_minor' => "\N",
                        'error_description' => "\N",
                        'content' => $contentData['content'],
                        'rule_id' => "\N",
                        'action_id' => 0,
                        'node_id' => $this->faker->randomElement($this->nodeIds),
                        'traffic_type' => $traffictype
                    ];
                }
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
    private function generateSRIFields(array $commonSMSMTValue): array
    {
        try {
            // Generate and return a single SRI record
            $id = $this->generateAutoIncrementId();

            return [
                'id' => $id,
                'created_at' => $commonSMSMTValue['sri_created_at'],
                'protocol' => 'ss7',
                'type' => 'sri',
                'reference' => $commonSMSMTValue['reference'],
                'sri_time' => $commonSMSMTValue['sri_created_at'],
                'sri_calling_gt' => $commonSMSMTValue['smscGt'],
                'sri_map_gt' => $commonSMSMTValue['smscGt'],
                'imsi' => $commonSMSMTValue['imsi'],
                'virtual_imsi' => $commonSMSMTValue['virtual_imsi'],
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
                'da' => $commonSMSMTValue['da'],
                'dcs' => "\N",
                'pid' => "\N",
                'tpdu_length' => "\N",
                'sar_ref' => "\N",
                'msg_part' => "\N",
                'msg_parts' => "\N",
                'status' => $commonSMSMTValue['status'],
                'error_major' => $commonSMSMTValue['sri_error_major'],
                'error_minor' => $commonSMSMTValue['sri_error_minor'],
                'error_description' => $commonSMSMTValue['sri_error_description'],
                'content' => "\N",
                'rule_id' => $commonSMSMTValue['sri_rule_id'],
                'action_id' => $commonSMSMTValue['sri_action_id'],
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
    private function generateSMSMTFields(array $commonSMSMTValue): array
    {
        try {
            $smsmtRecords = [];
            $trafficType = (substr($commonSMSMTValue['smscGt'], 0, 2) === '94') ? 'local' : 'international';

            $smsmtCreatedAt = is_string($commonSMSMTValue['sri_created_at'])
                ? new \DateTime($commonSMSMTValue['sri_created_at'])
                : $commonSMSMTValue['sri_created_at'];

            $smsmtCreatedAt = clone $smsmtCreatedAt;
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
                    'reference' => $commonSMSMTValue['reference'],
                    'sri_time' => "\N",
                    'sri_calling_gt' => "\N",
                    'sri_map_gt' => "\N",
                    'imsi' => $commonSMSMTValue['imsi'],
                    'virtual_imsi' => $commonSMSMTValue['virtual_imsi'],
                    'virtual_vlr_gt' => $smsfwGt,
                    'fwdsm_time' => $smsmtCreatedAt->format('Y-m-d H:i:s'),
                    'fwdsm_calling_gt' => $commonSMSMTValue['smscGt'],
                    'fwdsm_map_gt' => $commonSMSMTValue['smscGt'],
                    'esme_ip' => "\N",
                    'esme_port' => "\N",
                    'smsc_ip' => "\N",
                    'smsc_port' => "\N",
                    'system_id' => "\N",
                    'message_id' => "\N",
                    'dlr_time' => "\N",
                    'dlr_status' => "\N",
                    'oa' => $commonSMSMTValue['oa'],
                    'da' => $commonSMSMTValue['da'],
                    'dcs' => $dcs,
                    'pid' => $pid,
                    'tpdu_length' => $contentData['tpdu_length'],
                    'sar_ref' => $sar_ref,
                    'msg_part' => $contentData['msg_part'],
                    'msg_parts' => $contentData['msg_parts'],
                    'status' => $commonSMSMTValue['fwdsm_status'],
                    'error_major' => $commonSMSMTValue['error_major'],
                    'error_minor' => $commonSMSMTValue['error_minor'],
                    'error_description' => $commonSMSMTValue['error_description'],
                    'content' => $contentData['content'],
                    'rule_id' => $commonSMSMTValue['rule_id'],
                    'action_id' => $commonSMSMTValue['action_id'],
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
            $commonSMSMTValue = [];
            $lastUsedTimestamps = [];

            // Generate random timestamps, sort them to ensure they are sequential
            $timestamps = $this->generateRandomTimestamps($startDate, $endDate, $numSriSmsmtPairs);
            sort($timestamps);

            foreach ($timestamps as $sriCreatedAt) {
                if (is_string($sriCreatedAt)) {
                    $sriCreatedAt = new \DateTime($sriCreatedAt);
                }

                // Generate Originating Address (OA)
                $useName = $this->faker->boolean($_ENV['OA_NAMES_PERCENTAGE']);
                $oa = $useName ? $this->faker->randomElement(explode(',', $_ENV['OA_NAMES'])) : $this->generateMSISDN($trafficType);

                $oaNames = explode(',', $_ENV['OA_NAMES']);
                $oaType = (substr($oa, 0, 2) === '94' || in_array($oa, $oaNames)) ? 'local' : 'international';

                // Generate Destination Address (DA)
                $da = $this->generateMSISDN('local_onnet');

                $smscGt = $oaType === 'local'
                    ? $this->faker->randomElement(explode(',', $_ENV['OLO_SMSC_GT']))
                    : $this->faker->randomElement(explode(',', $_ENV['INTL_SMSC_GT']));

                $virtualImsi = $this->generateVirtualIMSI();

                // Ensure a 30-minute gap between timestamps
                if (isset($lastUsedTimestamps[$virtualImsi])) {
                    $lastUsedTimestamp = $lastUsedTimestamps[$virtualImsi];
                    if (is_string($lastUsedTimestamp)) {
                        $lastUsedTimestamp = new \DateTime($lastUsedTimestamp);
                    }

                    $updatedTimestamp = (clone $lastUsedTimestamp)->modify('+30 minutes');
                    if ($sriCreatedAt < $updatedTimestamp) {
                        $sriCreatedAt = $updatedTimestamp;
                    }
                }

                $lastUsedTimestamps[$virtualImsi] = $sriCreatedAt;

                $commonSMSMTValue[] = [
                    'reference' => $this->generateReference($sriCreatedAt),
                    'imsi' => $this->generateIMSI($da),
                    'virtual_imsi' => $virtualImsi,
                    'da' => $da,
                    'oa' => $oa,
                    'smscGt' => $smscGt,
                    'sri_created_at' => $sriCreatedAt->format('Y-m-d H:i:s'),

                    'status' => 'success',
                    'fwdsm_status' => 'success',
                    'rule_id' => "\N",
                    'sri_rule_id' => "\N",
                    'action_id' => 0,
                    'sri_action_id' => 0,
                    'error_major' => "\N",
                    'sri_error_major' => "\N",
                    'error_minor' => "\N",
                    'sri_error_minor' => "\N",
                    'error_description' => "\N",
                    'sri_error_description' => "\N",

                ];
            }

            return $commonSMSMTValue;
        } catch (Exception $e) {
            Logging::logError('Error generating common SMS MT values: ' . $e->getMessage());
            throw new Exception('Error generating common SMS MT values: ' . $e->getMessage());
        }
    }

    public function fetchDataFromTables(string $type): array
    {
        try {
            if ($type === 'mt' || $type === 'sri') {
                // Fetch data from legacy_rules table
                $legacyQuery = "SELECT * FROM legacy_rules WHERE type = :type";
                $legacyStmt = $this->pdoMain->prepare($legacyQuery);
                $legacyStmt->execute([':type' => $type]);
                $legacyRules = $legacyStmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($legacyRules)) {
                    throw new Exception("No legacy rules found for type '{$type}' in the database.");
                }

                // Fetch whitelist rules for filtering
                $whitelistQuery = "SELECT * FROM legacy_whitelists";
                $whitelistStmt = $this->pdoMain->prepare($whitelistQuery);
                $whitelistStmt->execute();
                $whitelistRules = $whitelistStmt->fetchAll(PDO::FETCH_ASSOC);

                // Filter legacy rules against the whitelist
                $failureRules = array_filter($legacyRules, function ($rule) use ($whitelistRules) {
                    foreach ($whitelistRules as $whitelist) {
                        if (
                            $rule['calling_gt'] === $whitelist['calling_gt'] &&
                            $rule['map_gt'] === $whitelist['map_gt'] &&
                            $rule['oa'] === $whitelist['oa'] &&
                            $rule['da'] === $whitelist['da'] &&
                            $rule['content'] === $whitelist['content']
                        ) {
                            return false; // Exclude if it matches the whitelist
                        }
                    }
                    return true;
                });

                return $failureRules; // Return filtered legacy rules
            } elseif ($type === 'whitelist') {
                // Fetch data from legacy_whitelists table
                $whitelistQuery = "SELECT * FROM legacy_whitelists";
                $whitelistStmt = $this->pdoMain->prepare($whitelistQuery);
                $whitelistStmt->execute();
                $whitelistRules = $whitelistStmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($whitelistRules)) {
                    throw new Exception("No whitelist rules found in the database.");
                }

                return $whitelistRules; // Return all whitelist rules
            } else {
                throw new Exception("Invalid type '{$type}' provided.");
            }
        } catch (Exception $e) {
            Logging::logError("Error fetching data: " . $e->getMessage());
            throw $e;
        }
    }


    public function generateCommonSMSMTValuesWithLegacyRules(string $trafficType, string $startDate, string $endDate, int $numPairs): array
    {
        try {
            $commonSMSMTValue = [];
            $lastUsedTimestamps = [];
            $failureRules = [];
            $whitelistRules = [];
            $actions = [
                -2 => 'Do Not Reply',
                -1 => 'Fake Delivery',
                1  => 'Unknown Number(1)',
                9  => 'Illegal Subscriber(9)',
                27 => 'Absent Subscriber(27)',
            ];

            $timestamps = $this->generateRandomTimestamps($startDate, $endDate, $numPairs);

            foreach ($timestamps as $recordCreatedAt) {
                if (is_string($recordCreatedAt)) {
                    $recordCreatedAt = new \DateTime($recordCreatedAt);
                }

                $weights = [
                    'mt' => $_ENV['MT_WITH_LEGACYRULES'],
                    'sri' => $_ENV['SRI_WITH_LEGACYRULES'],
                    'whitelist' => $_ENV['LEGACY_WHITELIST'],
                ];

                // Get record type using weighted random selection
                $recordType = $this->getWeightedRandomType($weights);

                // $recordType = $this->faker->randomElement(['mt', 'sri', 'whitelist']);
                // Select rule using switch case
                // $selectedRule = null;
                switch ($recordType) {
                    case 'mt':
                        $failureRules = $this->fetchDataFromTables('mt');
                        $selectedRule = $this->faker->randomElement($failureRules);
                        $sriStatus = 'success';
                        $fwdsmStatus = 'failed';
                        $sriErrorDescription = '\N';
                        $errorDescription = 'mterror';
                        $sriRuleId = '\N';
                        $ruleId = $selectedRule['id'];
                        $sriErrorMajor = "\N";
                        $errorMajor = 'errormajor';
                        $sriErrorMinor = "\N";
                        $errorMinor = "errorminor";
                        $sriActionId = 0;
                        $actionId = $selectedRule['action_id'];


                        break;

                    case 'sri':
                        $failureRules = $this->fetchDataFromTables('sri');
                        $selectedRule = $this->faker->randomElement($failureRules);
                        $sriStatus = 'failed';
                        $fwdsmStatus = '\N';
                        $errorDescription = 'sri_err_null';
                        $sriErrorDescription = 'srierror';
                        $sriRuleId = $selectedRule['id'];
                        $ruleId = "\N";
                        $sriErrorMajor = "sri_e_major";
                        $errorMajor = "\N";
                        $sriErrorMinor = "sri_e_minor";
                        $errorMinor = "\N";
                        $sriActionId = $selectedRule['action_id'] ?? 0;
                        $actionId = "\N";

                        break;

                    case 'whitelist':
                        $whitelistRules = $this->fetchDataFromTables('whitelist');
                        $selectedRule = $this->faker->randomElement($whitelistRules);
                        $sriStatus = 'success';
                        $fwdsmStatus = 'success';
                        $errorDescription = '\N';
                        $sriErrorDescription = '\N';
                        $sriRuleId = '\N';
                        $ruleId = '\N';
                        $sriErrorMajor = "\N";
                        $errorMajor = "\N";
                        $sriErrorMinor = "\N";
                        $errorMinor = "\N";
                        $sriActionId = $actionId = 0;

                        break;

                    default:
                        throw new Exception("Unsupported record type '{$recordType}'.");
                }

                if (!$selectedRule) {
                    throw new Exception("No rule found for the selected type '{$recordType}'.");
                }

                $callingGt = $selectedRule['calling_gt'];
                $da = $selectedRule['da'];
                $oa = $selectedRule['oa'];

                // var_dump($oa);
                // $oar = $oa !== '%' ? $this->replaceMsisdnWithRandomNumbers($oa): $oa;
                // var_dump($oar);

                $oaCommon = $this->generateMSISDN($trafficType);
                $daCommon = $this->generateMSISDN('local_onnet');

                $da = $da === '%' ? $daCommon : $da;
                $oa = $oa === '%' ? $oaCommon : $oa;

                $oaType = (substr($oa, 0, 2) === '94') ? 'local' : 'international';

                $smscGt = $oaType === 'local'
                    ? $this->faker->randomElement(explode(',', $_ENV['OLO_SMSC_GT']))
                    : $this->faker->randomElement(explode(',', $_ENV['INTL_SMSC_GT']));

                $virtualImsi = $this->generateVirtualIMSI();

                if (isset($lastUsedTimestamps[$virtualImsi])) {
                    $lastUsedTimestamp = $lastUsedTimestamps[$virtualImsi];
                    if (is_string($lastUsedTimestamp)) {
                        $lastUsedTimestamp = new \DateTime($lastUsedTimestamp);
                    }

                    $updatedTimestamp = (clone $lastUsedTimestamp)->modify('+30 minutes');
                    if ($recordCreatedAt < $updatedTimestamp) {
                        $recordCreatedAt = $updatedTimestamp;
                    }
                }
                $lastUsedTimestamps[$virtualImsi] = $recordCreatedAt;

                $record = [
                    'reference' => $this->generateReference($recordCreatedAt),
                    'imsi' => $this->generateIMSI($da),
                    'virtual_imsi' => $virtualImsi,
                    'da' => $da,
                    'oa' => $oa,
                    'smscGt' => $callingGt === '%' ? $smscGt : $callingGt,
                    'sri_created_at' => $recordCreatedAt,

                    'status' => $sriStatus,
                    'fwdsm_status' => $fwdsmStatus,
                    'rule_id' => $ruleId,
                    'sri_rule_id' => $sriRuleId,
                    'action_id' => $actionId,
                    'sri_action_id' => $sriActionId,
                    'error_major' => $errorMajor,
                    'sri_error_major' => $sriErrorMajor,
                    'error_minor' => $errorMinor,
                    'sri_error_minor' => $sriErrorMinor,
                    'error_description' => $errorDescription,
                    'sri_error_description' => $sriErrorDescription,
                ];

                $commonSMSMTValue[] = $record;
            }

            return $commonSMSMTValue;
        } catch (Exception $e) {
            Logging::logError('Error generating common values: ' . $e->getMessage());
            throw new Exception('Error generating common values: ' . $e->getMessage());
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
    public function generateSMPPFields(string $smppTrafficType, string $startDate, string $endDate, int $numMessages): array
    {
        try {

            // Mappings and pools initialization
            $localSmppMapping = SmppMappingArrays::$local_smpp_mapping;
            $intlSmppMapping = SmppMappingArrays::$intl_smpp_mapping;
            $esmeToSmsfw = SmppMappingArrays::$esme_to_smsfw;
            $smsfwToSmsc = SmppMappingArrays::$smsfw_to_smsc;
            $localOas = SmppMappingArrays::$local_oa_pool;
            $intlOas = SmppMappingArrays::$intl_oa_pool;

            $isLocal = $smppTrafficType === 'local';
            $smppMapping = $isLocal ? $localSmppMapping : $intlSmppMapping;
            $oaPool = $isLocal ? $localOas : $intlOas;

            // Generate timestamps for each message (one per message)
            $timestamps = $this->generateRandomTimestamps($startDate, $endDate, $numMessages);

            // Mapping ESME IPs to SMSFW and SMSC
            foreach ($smppMapping as &$entry) {

                $esmeIp = $entry['esme_ip'];

                $smsfwData = array_filter($esmeToSmsfw, function ($mapping) use ($esmeIp) {
                    return in_array($esmeIp, explode(',', $mapping['esme_ip']));
                });

                $smsfwData = reset($smsfwData);

                if ($smsfwData) {
                    $smsfwIpandPort = $smsfwData['smsfw_ip_and_port'];
                    $entry['smsfw_ip_and_port'] = $smsfwIpandPort;

                    $smscData = array_filter($smsfwToSmsc, function ($mapping) use ($smsfwIpandPort) {
                        return $mapping['smsfw_ip_and_port'] === $smsfwIpandPort;
                    });

                    $smscData = reset($smscData);
                    if ($smscData) {
                        $entry['smsc_mapping'] = [
                            'smsc_ip' => $smscData['smsc_ip'],
                            'smsc_port' => $smscData['smsc_port'],
                        ];
                    }
                }
            }

            $records = [];

            foreach ($timestamps as $createdAt) {

                $entryIndex = array_rand($smppMapping);
                $trafficEntry = $smppMapping[$entryIndex];

                $systemId = $trafficEntry['system_id'];
                $virtualVlrGt = $trafficEntry['virtual_gt'];
                $esmeIp = $trafficEntry['esme_ip'];
                $esmePort = $trafficEntry['esme_port'];
                $smscIp = $trafficEntry['smsc_mapping']['smsc_ip'] ?? '';
                $smscPort = $trafficEntry['smsc_mapping']['smsc_port'] ?? '';

                // Check for system ID mapping and set defaults if necessary
                if (!isset($this->smppMapping[$systemId])) {
                    $this->smppMapping[$systemId] = [
                        'esme_ip' => $esmeIp,
                        'virtual_vlr_gt' => $virtualVlrGt,
                        'esme_port' => $esmePort,
                        'smsc_ip' => $smscIp,
                        'smsc_port' => $smscPort,
                    ];
                }

                // DLR time (1 second after created_at)
                $dlrTime = (new \DateTime($createdAt))->modify('+1 second');

                // Generate message contents
                $sarRef = $this->faker->numberBetween(1, 128);
                $messageId = $this->generateReference(new \DateTime($createdAt));
                $numParts = rand(1, 4);
                $isUnicode = (bool)rand(0, 1);
                $messageContents = $this->generateSMSContent($numParts, $isUnicode);

                $weights = [
                    'mt' => $_ENV['MT_WITH_LEGACYRULES'],
                    'sri' => $_ENV['SRI_WITH_LEGACYRULES'],
                    'whitelist' => $_ENV['LEGACY_WHITELIST'],
                ];

                // Get record type using weighted random selection
                $recordType = $this->getWeightedRandomType($weights);

                switch ($recordType) {
                    case 'mt':
                        $failureRules = $this->fetchDataFromTables('mt');
                        $selectedRule = $this->faker->randomElement($failureRules);

                        $oa = $selectedRule['oa'];
                        $da = $selectedRule['da'] === '%' ? $this->generateMSISDN('local_onnet') : $selectedRule['da'];
                        $dlrStatus = 'failed';
                        $errorMajor = 'er mj';
                        $errorMinor = 'er minor';
                        $errorDescription = 'err description';
                        $ruleId = $selectedRule['id'];
                        $actionId = $selectedRule['action_id'];
                        $status = 'mtfailed';

                        break;

                    case 'whitelist':
                        $whitelistRules = $this->fetchDataFromTables('whitelist');
                        $selectedRule = $this->faker->randomElement($whitelistRules);

                        $oa = $selectedRule['oa'];
                        $da = $selectedRule['da'] === '%' ? $this->generateMSISDN('local_onnet') : $selectedRule['da'];
                        $dlrStatus = 'success';
                        $errorMajor = '\N';
                        $errorMinor = '\N';
                        $errorDescription = '\N';
                        $ruleId = '\N';
                        $actionId = 0;
                        $status = 'wsuccess';

                        break;

                    default:

                        // Randomly pick an OA and traffic entry
                        $oaIndex = array_rand($oaPool);
                        $oa = $oaPool[$oaIndex];
                        $da = $this->generateMSISDN('local_onnet');
                        $dlrStatus = 'success';
                        $errorMajor = '\N';
                        $errorMinor = '\N';
                        $errorDescription = '\N';
                        $ruleId = '\N';
                        $actionId = 0;
                        $status = 'success';

                        break;
                }

                $oaNamesLocalSMPP = explode(',', $_ENV['OA_NAMES_LOCAL_SMPP']);
                $trafficType = (substr($oa, 0, 2) === '94' || in_array($oa, $oaNamesLocalSMPP)) ? 'local' : 'international';
                // Loop through message parts and generate the record for each part
                foreach ($messageContents as $contentData) {
                    $id = $this->generateAutoIncrementId();

                    $content = isset($selectedRule['content']) && $selectedRule['content'] === '%'
                        ? $contentData['content']
                        : ($selectedRule['content'] ?? $contentData['content']);

                    $records[] = [
                        'id' => $id,
                        'created_at' => $createdAt,
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
                        'dlr_status' => $dlrStatus,
                        'oa' => $oa,
                        'da' => $da,
                        'dcs' => "\N",
                        'pid' => "\N",
                        'tpdu_length' => $contentData['tpdu_length'],
                        'sar_ref' => $sarRef,
                        'msg_part' => $contentData['msg_part'],
                        'msg_parts' => $contentData['msg_parts'],
                        'status' => $status,
                        'error_major' => $errorMajor,
                        'error_minor' => $errorMinor,
                        'error_description' => $errorDescription,
                        'content' => $content,
                        'rule_id' => $ruleId,
                        'action_id' => $actionId,
                        'node_id' => $this->faker->randomElement($this->nodeIds),
                        'traffic_type' => $trafficType,
                    ];
                }
            }

            return $records;
        } catch (Exception $e) {
            Logging::logError('Failed to generate SMPP fields: ' . $e->getMessage());
            throw new Exception('Failed to generate SMPP fields: ' . $e->getMessage());
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
        $smsmoRecords = $this->generateSMSMOFields($trafficType, $startDate, $endDate, $numRecords);

        foreach ($smsmoRecords as $smsmoRecord) {
            fputcsv($csvFile, $smsmoRecord);
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

    public function generateSRISMSMTRecordsWithLegacyRules($trafficType, string $startDate, string $endDate, $numSriSmsmtPairs, $csvFile)
    {
        try {
            // Generate common values using legacy rules
            $commonValues = $this->generateCommonSMSMTValuesWithLegacyRules($trafficType, $startDate, $endDate, $numSriSmsmtPairs);

            foreach ($commonValues as $commonValue) {

                $sriRecord = $this->generateSRIFields($commonValue);

                // Generate and write the SRI record
                $sriRecord = array_map(function ($value) {
                    return $value instanceof \DateTime ? $value->format('Y-m-d H:i:s') : $value;
                }, $sriRecord);

                fputcsv($csvFile, $sriRecord);

                // Only generate SMSMT records if the SRI record is successful    && !$commonValue['isWhitelist']
                if ($commonValue['status'] === 'success') {
                    $smsmtRecords = $this->generateSMSMTFields($commonValue);
                    foreach ($smsmtRecords as $smsmtRecord) {
                        fputcsv($csvFile, $smsmtRecord);
                    }
                }
            }
        } catch (Exception $e) {
            Logging::logError('Error generating SRI and SMSMT records with legacy rules: ' . $e->getMessage());
            throw new Exception('Error generating SRI and SMSMT records with legacy rules: ' . $e->getMessage());
        }
    }



    public function generateSMPPRecords(int $numRecords, string $trafficType, string $startDate, string $endDate, $csvFile): void
    {
        $smsmoRecords = $this->generateSMPPFields($trafficType, $startDate, $endDate, $numRecords);

        foreach ($smsmoRecords as $smsmoRecord) {
            fputcsv($csvFile, $smsmoRecord);
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

    public function uploadCSV(string $filePath, string $header, ?string $tableName): void
    {
        try {

            file_exists($filePath) ?: throw new Exception('CSV file does not exist.');

            $importQuery = "
        LOAD DATA INFILE '{$filePath}'
        INTO TABLE {$tableName}
        FIELDS TERMINATED BY ',' 
        ENCLOSED BY '\"'
        LINES TERMINATED BY '\\n'
        IGNORE 1 LINES
        ({$header})
        ";

            $stmt = $this->pdoReporting->prepare($importQuery);
            $stmt->execute();

            Logging::logInfo("CSV file uploaded to {$tableName} table successfully.");
        } catch (PDOException $e) {
            Logging::logError('Database error: ' . $e->getMessage());
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    private function createDailyTable(string $tableName): void
    {
        try {
            $createTableSQL = "CREATE TABLE IF NOT EXISTS {$tableName} LIKE cdr_sms";
            $stmt = $this->pdoReporting->prepare($createTableSQL);
            $stmt->execute();

            Logging::logInfo("Table {$tableName} created.");
        } catch (PDOException $e) {
            Logging::logError('Error creating table: ' . $e->getMessage());
            throw new Exception('Error creating table: ' . $e->getMessage());
        }
    }

    public function generateRecords(int $rowCount, string $startDate, string $endDate, bool $isDailyTable = false): void
    {
        try {
            $this->getNodeIds();
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

            if ($isDailyTable) {
                $currentDate = new \DateTime($startDate);
                $endDateObject = new \DateTime($endDate);
                $daysDiff = $endDateObject->diff($currentDate)->days + 1;
                $dailyCounts = $this->distributeRowCountRandomly($rowCount, $daysDiff);
                $dayIndex = 0;

                // Loop for each day in the range
                while ($currentDate <= $endDateObject) {
                    $dateString = $currentDate->format('Ymd');

                    // Set startDate and endDate for each daily table
                    $currentStartDate = $dateString;
                    $currentEndDate = $dateString;

                    $fileName = "cdr_sms_{$dateString}_" . uniqid() . ".csv";
                    $filePath = $_ENV['FILE_PATH'] . "/{$fileName}";
                    $csvFile = fopen($filePath, 'w');
                    if ($csvFile === false) {
                        throw new Exception('Failed to open file for writing.');
                    }

                    fputcsv($csvFile, $header);
                    $numRecords = $dailyCounts[$dayIndex];
                    $dayIndex++;

                    // Calculate record distribution
                    $numSMSMO = round($numRecords * ($_ENV['SMSMO'] / 100));
                    $numSriSmsmtPairs = round($numRecords * ($_ENV['SRI_SMSMT'] / 100));
                    $numSMPP = round($numRecords * ($_ENV['SMPP'] / 100));

                    $numSmsmoOnnet = round($numSMSMO * ($_ENV['SMSMO_LOCAL_ONNET'] / 100));
                    $numSmsmoOlo = round($numSMSMO * ($_ENV['SMSMO_LOCAL_OLO'] / 100));
                    $numSmsmoIntl = round($numSMSMO * ($_ENV['SMSMO_INTERNATIONAL'] / 100));

                    $numSmsmtOlo = round($numSriSmsmtPairs * ($_ENV['SRI_SMSMT_LOCAL_OLO'] / 100));
                    $numSmsmtIntl = round($numSriSmsmtPairs * ($_ENV['SRI_SMSMT_INTERNATIONAL'] / 100));

                    $numSmppLocal = round($numSMPP * ($_ENV['SMPP_LOCAL'] / 100));
                    $numSmppIntl = round($numSMPP * ($_ENV['SMPP_INTERNATIONAL'] / 100));

                    // Generate records for each traffic type
                    $this->generateSMSMORecords($numSmsmoOnnet, 'local_onnet', $currentStartDate, $currentEndDate, $csvFile);
                    $this->generateSMSMORecords($numSmsmoOlo, 'local_olo', $currentStartDate, $currentEndDate, $csvFile);
                    $this->generateSMSMORecords($numSmsmoIntl, 'international', $currentStartDate, $currentEndDate, $csvFile);

                    $this->generateSRISMSMTRecords('local_olo', $currentStartDate, $currentEndDate, $numSmsmtOlo, $csvFile);
                    $this->generateSRISMSMTRecords('international', $currentStartDate, $currentEndDate, $numSmsmtIntl, $csvFile);

                    $this->generateSRISMSMTRecordsWithLegacyRules('local_olo', $startDate, $endDate, $numSmsmtOlo, $csvFile);
                    $this->generateSRISMSMTRecordsWithLegacyRules('international', $startDate, $endDate, $numSmsmtIntl, $csvFile);

                    $this->generateSMPPRecords($numSmppLocal, 'local', $currentStartDate, $currentEndDate, $csvFile);
                    $this->generateSMPPRecords($numSmppIntl, 'international', $currentStartDate, $currentEndDate, $csvFile);

                    fclose($csvFile);

                    // Create daily table and upload CSV
                    $this->createDailyTable("cdr_sms_{$dateString}");
                    $this->uploadCSV($filePath, implode(',', $header), "cdr_sms_{$dateString}");
                    echo "csv file generated in $filePath and uploaded cdr_sms_{$dateString}.\n";
                    Logging::logInfo("csv file generated in $filePath and uploaded cdr_sms_{$dateString}\n");

                    // Move to the next day
                    $currentDate->modify('+1 day');
                }
            } else {
                // Generate cdr_sms table
                $fileName = "cdr_sms_" . uniqid() . '.csv';
                $filePath = $_ENV['FILE_PATH'] . "/{$fileName}";
                $csvFile = fopen($filePath, 'w');
                if ($csvFile === false) {
                    throw new Exception('Failed to open file for writing.');
                }

                fputcsv($csvFile, $header);

                // Calculate record distribution
                $numSMSMO = round($rowCount * ($_ENV['SMSMO'] / 100));
                $numSriSmsmtPairs = round($rowCount * ($_ENV['SRI_SMSMT'] / 100));
                $numSMPP = round($rowCount * ($_ENV['SMPP'] / 100));

                $numSmsmoOnnet = round($numSMSMO * ($_ENV['SMSMO_LOCAL_ONNET'] / 100));
                $numSmsmoOlo = round($numSMSMO * ($_ENV['SMSMO_LOCAL_OLO'] / 100));
                $numSmsmoIntl = round($numSMSMO * ($_ENV['SMSMO_INTERNATIONAL'] / 100));

                $numSmsmtOlo = round($numSriSmsmtPairs * ($_ENV['SRI_SMSMT_LOCAL_OLO'] / 100));
                $numSmsmtIntl = round($numSriSmsmtPairs * ($_ENV['SRI_SMSMT_INTERNATIONAL'] / 100));

                $numSmppLocal = round($numSMPP * ($_ENV['SMPP_LOCAL'] / 100));
                $numSmppIntl = round($numSMPP * ($_ENV['SMPP_INTERNATIONAL'] / 100));

                // Generate records for the entire range
                $this->generateSMSMORecords($numSmsmoOnnet, 'local_onnet', $startDate, $endDate, $csvFile);
                $this->generateSMSMORecords($numSmsmoOlo, 'local_olo', $startDate, $endDate, $csvFile);
                $this->generateSMSMORecords($numSmsmoIntl, 'international', $startDate, $endDate, $csvFile);

                $this->generateSRISMSMTRecords('local_olo', $startDate, $endDate, $numSmsmtOlo, $csvFile);
                $this->generateSRISMSMTRecords('international', $startDate, $endDate, $numSmsmtIntl, $csvFile);

                $this->generateSRISMSMTRecordsWithLegacyRules('local_olo', $startDate, $endDate, $numSmsmtOlo, $csvFile);
                $this->generateSRISMSMTRecordsWithLegacyRules('international', $startDate, $endDate, $numSmsmtIntl, $csvFile);

                $this->generateSMPPRecords($numSmppLocal, 'local', $startDate, $endDate, $csvFile);
                $this->generateSMPPRecords($numSmppIntl, 'international', $startDate, $endDate, $csvFile);

                fclose($csvFile);
                $this->uploadCSV($filePath, implode(',', $header), 'cdr_sms');
                Logging::logInfo("CSV file generated at: {$filePath}");
                echo "CSV file generated at: {$filePath}";
            }
        } catch (Exception $e) {
            Logging::logError('Error generating records: ' . $e->getMessage());
            throw new Exception('Error generating records: ' . $e->getMessage());
        }
    }
}
