<?php

namespace app\src\Controllers;

require_once __DIR__ . '/../../../vendor/autoload.php';


use Faker\Factory as Faker;
use app\src\Traits\ValidDataGeneratorTrait;
use Exception;
use Dotenv\Dotenv;
use config\Logging;
use app\src\Models\Model;
use PDO;
use PDOException;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

class CdrSmsTableController extends Model {
    private $faker;
    private $validDataGenerator;
    public $filePath;
    public $header; 
    public $nodeIds = [];

    use ValidDataGeneratorTrait;

    public function __construct() {
        parent::__construct();
        $this->faker = Faker::create();
        
    }

    //Generates fields for SMSMO records
    private function generateSMSMOFields(int $index):array {
        $oa = $this->generateMSISDN('local'); 
        $daType = $this->faker->randomElement(['local', 'international']);   
        $da = $this->generateMSISDN($daType);

        // Determine the MSC_GT values based on whether DA is local or international
        $mscGt = ($daType === 'local') ? $_ENV['LOCAL_MSC_GT'] : $_ENV['INTL_MSC_GT'];
        $trafficType = (substr($da, 0, 2) === '94') ? 'local' : 'international';

        return [
            'id' => $index,
            'created_at' => $this->faker->dateTimeBetween($_ENV['DATE_RANGE'])->format('Y-m-d H:i:s'),
            'protocol' => 'ss7',
            'type' => 'smsmo',
            'reference' => $this->faker->unique()->numerify('##############'),
            'sri_time' => null,
            'sri_calling_gt' => null,
            'sri_map_gt' => null,
            'imsi' => $this->generateIMSI($oa), // Pass the OA MSISDN to generateIMSI
            'virtual_imsi' => null,
            'virtual_vlr_gt' => null,
            'fwdsm_time' => $this->faker->dateTimeBetween('-7 days')->format('Y-m-d H:i:s'),
            'fwdsm_calling_gt' => $mscGt,
            'fwdsm_map_gt' => $mscGt,
            'esme_ip' => null,
            'esme_port' => null,
            'smsc_ip' => null,
            'smsc_port' => null,
            'system_id' => null,
            'message_id' => null,
            'dlr_time' => null,
            'dlr_status' => null,
            'oa' => $oa,
            'da' => $da,
            'dcs' => $this->faker->numberBetween(0, 1000),
            'pid' => $this->faker->numberBetween(0, 10000),
            'tpdu_length' => $this->faker->numberBetween(1, 160),
            'sar_ref' => $this->faker->numberBetween(1, 128),
            'msg_part' => $this->faker->numberBetween(1, 10),
            'msg_parts' => $this->faker->numberBetween(1, 10),
            'status' =>  'success',
            'error_major' => $this->faker->numberBetween(1, 100),
            'error_minor' => $this->faker->numberBetween(1, 100),
            'error_description' => null,
            'content' => $this->faker->text($this->faker->numberBetween(5, 160)),
            'rule_id' => null,
            'action_id' => 0,
            'node_id' => $this->nodeIds[array_rand($this->nodeIds)],
            'traffic_type' => $trafficType
        ];
    }

    //Generates fields for SRI records
    private function generateSRIFields(int $index, array $commonValues): array {
        return [
            'id' => $index,
            'created_at' => $this->faker->dateTimeBetween($_ENV['DATE_RANGE'])->format('Y-m-d H:i:s'),
            'protocol' => 'ss7',
            'type' => 'sri',
            'reference' => $commonValues['reference'],
            'sri_time' => $this->faker->dateTimeBetween('-7 days')->format('Y-m-d H:i:s'),
            'sri_calling_gt' => $commonValues['smscGt'],
            'sri_map_gt' => $commonValues['smscGt'],
            'imsi' => $commonValues['imsi'],
            'virtual_imsi' => $commonValues['virtual_imsi'],
            'virtual_vlr_gt' => null,
            'fwdsm_time' => null,
            'fwdsm_calling_gt' => null,
            'fwdsm_map_gt' => null,
            'esme_ip' => null,
            'esme_port' => null,
            'smsc_ip' => null,
            'smsc_port' => null,
            'system_id' => null,
            'message_id' => null,
            'dlr_time' => null,
            'dlr_status' => null,
            'oa' => null,
            'da' => $commonValues['da'],
            'dcs' => null,
            'pid' => null,
            'tpdu_length' => null,
            'sar_ref' => null,
            'msg_part' => null,
            'msg_parts' => null,
            'status' => 'success',
            'error_major' => $this->faker->numberBetween(1, 100),
            'error_minor' => $this->faker->numberBetween(1, 100),
            'error_description' => null,
            'content' => null,
            'rule_id' => null,
            'action_id' => 1,
            'node_id' => $this->nodeIds[array_rand($this->nodeIds)],
            'traffic_type' => null
        ];
    }

    //Generates fields for SMSMT records.
    private function generateSMSMTFields(int $index, array $commonValues): array {
        $oaType = $this->faker->randomElement(['local', 'international']);
      //  $oa = $this->generateMSISDN($oaType); // Generate OA based on the random type
        
        $trafficType = (substr($commonValues['oa'], 0, 2) === '94') ? 'local' : 'international';
        return [
            'id' => $index,
            'created_at' => $this->faker->dateTimeBetween($_ENV['DATE_RANGE'])->format('Y-m-d H:i:s'),
            'protocol' => 'ss7',
            'type' => 'smsmt',
            'reference' => $commonValues['reference'],
            'sri_time' => null,
            'sri_calling_gt' => null,
            'sri_map_gt' => null,
            'imsi' => $commonValues['imsi'],
            'virtual_imsi' => $commonValues['virtual_imsi'],
            'virtual_vlr_gt' => $this->generateMSISDN(),
            'fwdsm_time' => $this->faker->dateTimeBetween($_ENV['DATE_RANGE'])->format('Y-m-d H:i:s'),
            'fwdsm_calling_gt' => $commonValues['smscGt'],
            'fwdsm_map_gt' => $commonValues['smscGt'],
            'esme_ip' => null,
            'esme_port' => null,
            'smsc_ip' => null,
            'smsc_port' => null,
            'system_id' => null,
            'message_id' => null,
            'dlr_time' => null,
            'dlr_status' => null,
            'oa' => $commonValues['oa'],
            'da' => $commonValues['da'],
            'dcs' => $this->faker->numberBetween(0, 1000),
            'pid' => $this->faker->numberBetween(0, 10000),
            'tpdu_length' => $this->faker->numberBetween(1, 100000),
            'sar_ref' => $this->faker->numberBetween(1, 100000),
            'msg_part' => $this->faker->numberBetween(1, 10),
            'msg_parts' => $this->faker->numberBetween(1, 10),
            'status' => 'success', 
            'error_major' => $this->faker->numberBetween(1, 100),
            'error_minor' => $this->faker->numberBetween(1, 100),
            'error_description' => null,
            'content' => $this->faker->text($this->faker->numberBetween(5, 160)),
            'rule_id' => null,
            'action_id' => 1,
            'node_id' => $this->nodeIds[array_rand($this->nodeIds)],
            'traffic_type' => $trafficType
        ];
        
    }

    //Generates common values used for SMSMT and SRI records
    private function generateCommonSMSMTValues(): array {
        $oaType = $this->faker->randomElement(['local', 'international']);
        $oa = $this->generateMSISDN($oaType); // Generate OA based on the random type
        $smscGt = ($oaType === 'local') ? $_ENV['LOCAL_SMSC_GT'] : $_ENV['INTL_SMSC_GT'];
    
        $msisdn = $this->generateMSISDN('local');
        $da = $this->generateMSISDN('local');
        
        return [
            'reference' => $this->faker->unique()->numerify('##############'),
            'imsi' => $this->generateIMSI($da),
            'virtual_imsi' => $this->generateIMSI($msisdn),
            'da' => $da,
            'oa' => $oa, //  OA used in SMSMTFields
            'smscGt' => $smscGt //Decided the smsgt based on oa(local, intl)
        ];
    }

    //Write data to the csv file
    public function generateCSV(int $rowCount): String {

        $this->joinedTable();
        $fileName = "cdr_sms_" . uniqid() . '.csv';
        $filePath = $_ENV['FILE_PATH'] . "/{$fileName}";

        $csvFile = fopen($filePath, 'w');

        if ($csvFile === false) {
            throw new Exception('Failed to open file for writing.');
        }

        $header = [
            'id', 'created_at', 'protocol', 'type', 'reference', 'sri_time', 'sri_calling_gt',
            'sri_map_gt', 'imsi', 'virtual_imsi', 'virtual_vlr_gt', 'fwdsm_time',
            'fwdsm_calling_gt', 'fwdsm_map_gt', 'esme_ip', 'esme_port', 'smsc_ip',
            'smsc_port', 'system_id', 'message_id', 'dlr_time', 'dlr_status', 'oa',
            'da', 'dcs', 'pid', 'tpdu_length', 'sar_ref', 'msg_part', 'msg_parts',
            'status', 'error_major', 'error_minor', 'error_description', 'content',
            'rule_id', 'action_id', 'node_id', 'traffic_type'
        ];

        fputcsv($csvFile, $header);

        $index = 1;
        $smsmoPercentage = 50;
        $sriAndSmsmtPercentage = 100 - $smsmoPercentage;

        $numSMSMO = round($rowCount * $smsmoPercentage / 100);
        $numPairsSriSmsmt = round($rowCount * $sriAndSmsmtPercentage / 100);

        // Generate SMSMO records
        for ($i = 0; $i < $numSMSMO; $i++) {
            fputcsv($csvFile, $this->generateSMSMOFields($index++));
        }

        // Generate pairs of SRI and SMSMT records
        for ($i = 0; $i < $numPairsSriSmsmt; $i++) {
            $commonValues = $this->generateCommonSMSMTValues();
            fputcsv($csvFile, $this->generateSRIFields($index++, $commonValues));
            fputcsv($csvFile, $this->generateSMSMTFields($index++, $commonValues));
        }

        fclose($csvFile);

        // Call uploadCSV before returning
        $this->uploadCSV($filePath, implode(',', $header));

        Logging::logInfo("CSV file generated at: " . $filePath );
        echo "CSV file generated at: " . $filePath;

        return $filePath;
      
    }
    public function uploadCSV(string $filePath, string $header): void {
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
    
        $stmt = $this->pdo->prepare($importQuery);
        $stmt->execute();

        Logging::logInfo('CSV file uploaded to database successfully.');
    
    }

    public function joinedTable() {
        $tableName = 'nodes';
        for ($i = 1; $i <= 3; $i++) {
            $data = [
                'id' => $i ,
                'name' => $this->faker->company
            ];
            if (!$this->dataExists($tableName, 'id', $data['id'])) {
                $this->insertData($tableName, $data);
            }
            $this->nodeIds[] = $data['id'];
        }
    }
}