<?php

namespace app\src\Controllers;

use Exception;
use Faker\Factory;
use app\src\Models\Model;
use DateTime;
use config\Logging;
use Faker\Guesser\Name;
use PDOException;


require_once __DIR__ . '/../../../vendor/autoload.php';

class CsvGeneratorController extends Model
{
    protected $faker;
    protected $csvFile;
    private $tablename;
    private $filePath;
    private $nodeIds = [];
    private $uniqueDates;
    private $originalTablename;
    public $dailyTable;
    private $startDate;
    private $endDate;

    public function __construct($tablename)
    {
        parent::__construct();
        $this->faker = Factory::create();
        $this->tablename = $tablename;
        $this->originalTablename = $tablename;
        $this->uniqueDates = []; 
       
    }

    public function generateData($rowCount, $dailyTable )
    {
        $fileName = "{$this->tablename}_" . uniqid() .'.csv';
        $this->filePath = $_ENV['FILE_PATH'] . "/{$fileName}" ;

        $this->csvFile = fopen($this->filePath, 'w');
        $headers = $this->getHeadersOfTable();
        fputcsv($this->csvFile, $headers);
        if ($this->csvFile === false) {
            throw new Exception('Failed to open file for writing.');
        }

        //$dataArr = [];   
        for ($i = 1; $i <= $rowCount; $i++) {
            $data = $this->generateRowData($i);
            fputcsv($this->csvFile, $data);
            //$dataArr[$i] = $data;
        }

        fclose($this->csvFile);

        if ($dailyTable) {
            $this->dailyTable();
        } else {
            
            $this->startDate = new DateTime('-7 days');
            $this->endDate = new DateTime('now');
            $startDate = $this->startDate->format('Y-m-d H:i:s');
            $endDate = $this->endDate->format('Y-m-d H:i:s');
            $this->partitionTable($startDate, $endDate);
        }

        //var_dump($dataArr);
        $this->uploadCsv();       
        
    }

    //joined table
    public function joinedTable() {
        switch ($this->tablename) {
            case 'cdr_sms':
                for ($i = 1; $i <= 10; $i++) {
                    $tableName = 'nodes';
                    $data = [
                        'id' => $i +3344,
                        'name' => $this->faker->company
                    ];
                    if (!$this->dataExists($tableName, 'id', $data['id'])) {
                        $this->insertData($tableName, $data);
                    }
                    $this->nodeIds[] = $i + 3344;
                }
                break;                      
        }
    }

   
    public function generateRowData($index)

    {      
        switch ($this->tablename) {
            case 'full_summary':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'smpp_international' => $this->faker->numberBetween(1000, 9999),
                    'smpp_local' => $this->faker->numberBetween(1000, 9999),
                    'ss7_mt_international' => $this->faker->numberBetween(1000, 9999),
                    'ss7_mt_local' => $this->faker->numberBetween(1000, 9999),
                    'ss7_mo' => $this->faker->numberBetween(1000, 9999),
                    'total' => $this->faker->numberBetween(1000, 9999),
                    'full_summarycol' => $this->faker->text(45)
                ];
            case 'smpp_mt_summary':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'system_id' => $this->faker->word,
                    'submit_count' => $this->faker->numberBetween(1000, 9999),
                    'submit_success_count' => $this->faker->numberBetween(1000, 9999),
                    'submit_reject_count' => $this->faker->numberBetween(1000, 9999)
                ];
            case 'smpp_local':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'type' => $this->faker->randomElement(['submit','delivered']),
                    'system_id' => $this->faker->word,
                    'count' => $this->faker->numberBetween(1000, 9999)
                ];
            case 'smpp_international':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'type' => $this->faker->randomElement(['submit','delivered']),
                    'system_id' => $this->faker->unique()->numberBetween(1111, 9999999),
                    'count' => $this->faker->numberBetween(1000, 99999)
                ];
            case 'smpp_oa_report':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'system_id' => $this->faker->unique()->numberBetween(1111, 9999999),
                    'type' => $this->faker->randomElement(['local','international']),
                    'oa' => $this->faker->text(32),
                    'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
                    'unique_da_count' => $this->faker->numberBetween(1000, 9999),
                    'sample_content' => $this->faker->text(45)
                ];
            case 'ss7_mt_summary':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'calling_gt' => $this->faker->numerify('##########'),
                    'sri_for_sm_count' => $this->faker->randomNumber(),
                    'forward_sm_count' => $this->faker->randomNumber()
                ];
            case 'ss7_mt_leakage':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'calling_gt' => $this->faker->numerify('##########'),
                    'oa' => $this->faker->word,
                    'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
                    'unique_da_count' => $this->faker->numberBetween(1000, 9999),
                    'sample_content' => $this->faker->text(45)
                ];
            case 'ss7_mt_oa_report':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'calling_gt' => $this->faker->numerify('##########'),
                    'oa' => $this->faker->text(32),
                    'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
                    'unique_da_count' => $this->faker->numberBetween(1000, 9999),
                    'sample_content' => $this->faker->text(45)
                ];
            case 'top_brands':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'brand' => $this->faker->company,
                    'traffic_type' => $this->faker->randomElement(['local', 'international']),
                    'total' => $this->faker->numberBetween(1000, 9999),
                    'created_at' => $this->faker->dateTime->format('Y-m-d H:i:s'),
                    'updated_at' => $this->faker->dateTime->format('Y-m-d H:i:s')
                ];
            
            case 'cdr_sms':           

                return [
                    
                    'id' => $index ,
                    'created_at' => $this->faker->dateTimeBetween('-7 days')->format('Y-m-d H:i:s'),
                    'protocol' => $this->faker->randomElement(['smpp', 'ss7']),
                    'type' => $this->faker->randomElement(['sri','smsmt','smsmo']),
                    'reference' => $this->faker->unique()->numberBetween(111,999999999),
                    'sri_time' => $this->faker->dateTime->format('Y-m-d H:i:s'),
                    'sri_calling_gt' => $this->faker->numerify('##########'),
                    'sri_map_gt' => $this->faker->numerify('##########'),
                    'imsi' => $this->faker->numerify('##########'),
                    'virtual_imsi' => $this->faker->numerify('##########'),
                    'virtual_vlr_gt' => $this->faker->numerify('##########'),
                    'fwdsm_time' => $this->faker->dateTime->format('Y-m-d H:i:s'),
                    'fwdsm_calling_gt' => $this->faker->numerify('##########'),
                    'fwdsm_map_gt' => $this->faker->numerify('##########'),
                    'esme_ip' => $this->faker->ipv4,
                    'esme_port' => $this->faker->numberBetween(1000, 9999),
                    'smsc_ip' => $this->faker->ipv4,
                    'smsc_port' => $this->faker->numberBetween(1000, 9999),
                    'system_id' => $this->faker->unique()->text(32),
                    'message_id' => $this->faker->uuid,
                    'dlr_time' => $this->faker->dateTime->format('Y-m-d H:i:s'),
                    'dlr_status' => $this->faker->randomElement(['success','failure']),
                    'oa' => $this->faker->text(32),
                    'da' => $this->faker->text(32),
                    'dcs' => $this->faker->numberBetween(0, 1000),
                    'pid' => $this->faker->numberBetween(0, 10000),
                    'tpdu_length' => $this->faker->numberBetween(1, 100000),
                    'sar_ref' => $this->faker->numberBetween(1, 100000),
                    'msg_part' => $this->faker->numberBetween(1, 10),
                    'msg_parts' => $this->faker->numberBetween(1, 10),
                    'status' => $this->faker->randomElement(['blocked','success','failed']),
                    'error_major' => $this->faker->numberBetween(1, 99),
                    'error_minor' => $this->faker->numberBetween(1, 99),
                    'error_description' => $this->faker->text(64),
                    'content' => $this->faker->text(50),
                    'rule_id' => $this->faker->unique()->numberBetween(1, 9999999),
                    'action_id' => $this->faker->unique()->numberBetween(1, 9999999),
                    'node_id' => $this->faker->randomElement($this->nodeIds),
                    'traffic_type' => $this->faker->randomElement(['local', 'international'])
                ];
            case 'ss7_mo_summary':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'calling_gt' => $this->faker->numerify('##########'),
                    'submit_sm_count' => $this->faker->randomNumber()
                ];
            case 'ss7_mo_oa_report':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'oa' => $this->faker->text(45),
                    'sms_attempt_count' => $this->faker->numberBetween(10, 99),
                    'unique_da_count' => $this->faker->unique()->numberBetween(1000, 9999999),
                    'sample_content' => $this->faker->text(45)
                ];
            case 'ss7_mo_leakage':
                return [
                    'id' => $index,
                    'date' => $this->faker->date,
                    'oa' => $this->faker->text(45),
                    'sms_attempt_count' => $this->faker->numberBetween(10, 99),
                    'unique_da_count' => $this->faker->unique()->numberBetween(1000, 9999999),
                    'sample_content' => $this->faker->text(45)
                ];
            case 'smpp_mt_leakage':
                return [
                    'id' => $index,
                    'date' => $this->faker->datedateTimeBetween('-30 days','-1 days')->format('Y-m-d'),
                    'system_id' => $this->faker->unique()->text(32),
                    'oa' => $this->faker->word,
                    'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
                    'unique_da_count' => $this->faker->unique()->numberBetween(1000, 999999),
                    'sample_content' => $this->faker->text(45)
                ];
            case 'bulk_contents':   

                return [
                    'date' => $this->generateUniqueDate(),
                    'protocol' => $this->faker->randomElement(['smpp', 'ss7']),
                    'type' => $this->faker->randomElement(['sri', 'smsmt', 'smsmo']),
                    'content' => $this->faker->text(160),
                    'count' => $this->faker->randomNumber()
            
                ];
                                               
            case 'spam_sms':
                return [
                    'date' => $this->generateUniqueDate(),
                    'protocol' => $this->faker->randomElement(['smpp', 'ss7']),
                    'type' => $this->faker->randomElement(['sri', 'smsmt', 'smsmo']),
                    'oa' => $this->faker->text(32),
                    'calling_gt' => $this->faker->text(32),
                    'content' => $this->faker->text(160),
                    'uniq_da_count' => $this->faker->unique()->randomNumber(),
                    'count' => $this->faker->randomNumber()
                ];
            default:
                throw new Exception('Invalid table name provided.');
        }
    }

    public function generateUniqueDate()
    {
        do {
            $date = $this->faker->unique()->dateTimeBetween('-30 days','-1 days')->format('Y-m-d');
        } while (in_array($date, $this->uniqueDates));

        $this->uniqueDates[] = $date;
        
        return $date;
    }


    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getHeadersOfTable()
    {
        switch ($this->originalTablename) {
            case 'full_summary':
                return ['id', 'date', 'smpp_international', 'smpp_local', 'ss7_mt_international', 'ss7_mt_local', 'ss7_mo', 'total', 'full_summarycol'];
            case 'smpp_mt_summary':
                return ['id', 'date', 'system_id', 'submit_count', 'submit_success_count', 'submit_reject_count'];
            case 'smpp_local':
                return ['id', 'date', 'type', 'system_id', 'count'];
            case 'smpp_international':
                return ['id', 'date', 'type', 'system_id', 'count'];
            case 'smpp_oa_report':
                return ['id', 'date', 'system_id', 'type', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
            case 'ss7_mt_summary':
                return ['id', 'date', 'calling_gt', 'sri_for_sm_count', 'forward_sm_count'];
            case 'ss7_mt_leakage':
                return ['id', 'date', 'calling_gt', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
            case 'ss7_mt_oa_report':
                return ['id', 'date', 'calling_gt', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
            case 'top_brands':
                return ['id', 'date', 'brand', 'traffic_type', 'total', 'created_at', 'updated_at'];  
            case 'cdr_sms':
                return ['id', 'created_at', 'protocol', 'type', 'reference', 'sri_time', 'sri_calling_gt', 'sri_map_gt', 'imsi', 'virtual_imsi', 'virtual_vlr_gt', 'fwdsm_time', 'fwdsm_calling_gt', 'fwdsm_map_gt', 'esme_ip', 'esme_port', 'smsc_ip', 'smsc_port', 'system_id', 'message_id', 'dlr_time', 'dlr_status', 'oa', 'da', 'dcs', 'pid', 'tpdu_length', 'sar_ref', 'msg_part', 'msg_parts', 'status', 'error_major', 'error_minor', 'error_description', 'content', 'rule_id', 'action_id', 'node_id', 'traffic_type'];
            case 'ss7_mo_summary':
                return ['id', 'date', 'calling_gt', 'submit_sm_count'];
            case 'ss7_mo_oa_report':
                return ['id', 'date', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
            case 'ss7_mo_leakage':
                return ['id', 'date', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
            case 'smpp_mt_leakage':
                return ['id', 'date', 'system_id', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
            case 'bulk_contents':
                return ['date', 'protocol', 'type', 'content', 'count'];
            case 'spam_sms':
                return ['date', 'protocol', 'type', 'oa', 'calling_gt', 'content', 'uniq_da_count', 'count'];
            default:
                throw new Exception("Unknown table name: {$this->tablename}");
        }
    }

       

    public function uploadCsv()
    {
        $fieldsList = $this->getHeadersOfTable();
        $fieldsList = implode(',', $fieldsList);
        $filePath = str_replace("\\", "/", $this->filePath);

        
        $importQuery = "
            LOAD DATA INFILE '{$this->filePath}'
            INTO TABLE {$this->tablename}
            FIELDS TERMINATED BY ','
            ENCLOSED BY '\"'
            LINES TERMINATED BY '\\n'
            IGNORE 1 LINES
            ({$fieldsList})
        ";
         
       
        $stmt = $this->pdo->prepare($importQuery);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error executing query: " . $errorInfo[2]);
        }
    
    }

    public function dailyTable()
    {
        $currentDate = date('Ymd');
        $newTableName = "cdr_sms_{$currentDate}";

        
        $createQuery = "CREATE TABLE IF NOT EXISTS {$newTableName} LIKE cdr_sms";
        $stmt = $this->pdo->prepare($createQuery);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error creating daily table: " . $errorInfo[2]);
        }

        $this->tablename = $newTableName;
        Logging::logInfo("Daily Table created successfully: " . $newTableName . "\n");
    }

    public function partitionTable($startDate, $endDate)
    {
        $startDateTime = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);

        $partitionQuery = "
            ALTER TABLE {$this->tablename}
            PARTITION BY RANGE (UNIX_TIMESTAMP(created_at)) (
        ";

        // Loop through each day in the date range
        $currentDateTime = clone $startDateTime;
        while ($currentDateTime <= $endDateTime) {
            $nextDateTime = clone $currentDateTime;
            $nextDateTime->modify('+1 day');
            $partitionName = 'p' . $currentDateTime->format('Ymd');
            $partitionValue = $nextDateTime->format('Y-m-d 00:00:00');

            // Convert partition value to UNIX timestamp for comparison
            $partitionValueTimestamp = $nextDateTime->getTimestamp();

            $partitionQuery .= "PARTITION $partitionName VALUES LESS THAN ($partitionValueTimestamp), ";

            $currentDateTime->modify('+1 day');
        }

        // Remove the last comma and space, and close the parenthesis
        $partitionQuery = rtrim($partitionQuery, ', ') . ")";

        // Prepare and execute the query
        $stmt = $this->pdo->prepare($partitionQuery);

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error creating partitions: " . $errorInfo[2]);
        }

        Logging::logInfo("Partitions created successfully: " . $this->tablename . "\n");
}

}


