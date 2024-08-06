<?php

// namespace Models;

// use Exception;
// use Faker\Factory;
// use PDOException;
// use Models\Model;
// use Log;

// require_once __DIR__ . '/../vendor/autoload.php';
// require_once __DIR__ . '/DbConnection.php';
// require_once __DIR__ . '/DbConnectionReporting.php';
// require_once __DIR__ . '/../Models/Model.php';

// class CsvGenerator extends Model
// {
//     protected $faker;
//     protected $csvFile;
//     private $tablename;
//     private $filePath;

//     public function __construct($tablename)
//     {
//         $this->faker = Factory::create();
//         $this->tablename = $tablename;

//         $fileName = "{$this->tablename}_" . uniqid() . '.csv';
//         $this->filePath = "/../output/$fileName";
//         $this->csvFile = fopen($this->filePath, 'w');
//         if ($this->csvFile === false) {
//             throw new Exception('Failed to open file for writing.');
//         }
//         $this->getFeildsOfTable();

        
//     }

//     public function getFeildsOfTable($csv = false)
//     {
        
//         switch ($this->tablename) {
//             case 'full_summary':
//                 $columns = ['id', 'date', 'smpp_international', 'smpp_local', 'ss7_mt_international', 'ss7_mt_local', 'ss7_mo', 'total', 'full_summarycol'];
//                 if ($csv) {
//                     $headers[] = fputcsv( $this->csvFile, $columns);
//                 }
//                 else{
//                     $headers[] = return $columns ;
//                 };
//                 $headers = $csv ? fputcsv( $this->csvFile, $columns) : return $columns;

//                 //fputcsv($this->csvFile, ['id', 'date', 'smpp_international', 'smpp_local', 'ss7_mt_international', 'ss7_mt_local', 'ss7_mo', 'total', 'full_summarycol']);
//                 break;
//             case 'smpp_mt_summary':
//                 fputcsv($this->csvFile, ['id', 'date', 'system_id', 'submit_count', 'submit_success_count', 'submit_reject_count']);
//                 break;
//             case 'smpp_local':
//                 fputcsv($this->csvFile, ['id', 'date', 'type', 'system_id', 'count']);
//                 break;
//             case 'smpp_international':
//                 fputcsv($this->csvFile, ['id', 'date', 'type', 'system_id', 'count']);
//                 break;
//             case 'smpp_oa_report':
//                 fputcsv($this->csvFile, ['id', 'date', 'system_id', 'type', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content']);
//                 break;
//             case 'ss7_mt_summary':
//                 fputcsv($this->csvFile, ['id', 'date', 'calling_gt', 'sri_for_sm_count', 'forward_sm_count']);
//                 break;
//             case 'ss7_mt_leakage':
//                 fputcsv($this->csvFile, ['id', 'date', 'calling_gt', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content']);
//                 break;
//             case 'ss7_mt_oa_report':
//                 fputcsv($this->csvFile, ['id', 'date', 'calling_gt', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content']);
//                 break;
//             case 'top_brands':
//                 fputcsv($this->csvFile, ['id', 'date', 'brand', 'traffic_type', 'total', 'created_at', 'updated_at']);
//                 break;
//             case 'cdr_sms':
//                 fputcsv($this->csvFile, ['id', 'created_at', 'protocol', 'type', 'reference', 'sri_time', 'sri_calling_gt', 'sri_map_gt', 'imsi', 'virtual_imsi', 'virtual_vlr_gt', 'fwdsm_time', 'fwdsm_calling_gt', 'fwdsm_map_gt', 'esme_ip', 'esme_port', 'smsc_ip', 'smsc_port', 'system_id', 'message_id', 'dlr_time', 'dlr_status', 'oa', 'da', 'dcs', 'pid', 'tpdu_length', 'sar_ref', 'msg_part', 'msg_parts', 'status', 'error_major', 'error_minor', 'error_description', 'content', 'rule_id', 'action_id', 'node_id', 'traffic_type']);
//                 break;
//             case 'ss7_mo_summary':
//                 fputcsv($this->csvFile, ['id', 'date', 'calling_gt', 'submit_sm_count']);
//                 break;
//             case 'ss7_mo_oa_report':
//                 fputcsv($this->csvFile, ['id', 'date', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content']);
//                 break;
//             case 'ss7_mo_leakage':
//                 fputcsv($this->csvFile, ['id', 'date', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content']);
//                 break;
//             case 'smpp_mt_leakage':
//                 fputcsv($this->csvFile, ['id', 'date', 'system_id', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content']);
//                 break;
//             case 'bulk_contents':
//                 fputcsv($this->csvFile, ['date', 'protocol', 'type', 'content', 'count']);
//                 break;
//             case 'spam_sms':
//                 fputcsv($this->csvFile, ['date', 'protocol', 'type', 'oa', 'calling_gt', 'content', 'uniq_da_count', 'count']);
//                 break;
//             default:
//                 throw new Exception('Invalid table name provided.');
//         }
//     }

//     public function generateData($rowCount = 1000)
//     {
//         for ($i = 1; $i <= $rowCount; $i++) {
//             $data = $this->generateRowData($i);
//             fputcsv($this->csvFile, $data);
//         }

//     }

//     public function generateRowData($index)
//     {
//         switch ($this->tablename) {
//             case 'full_summary':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'smpp_international' => $this->faker->numberBetween(1000, 9999),
//                     'smpp_local' => $this->faker->numberBetween(1000, 9999),
//                     'ss7_mt_international' => $this->faker->numberBetween(1000, 9999),
//                     'ss7_mt_local' => $this->faker->numberBetween(1000, 9999),
//                     'ss7_mo' => $this->faker->numberBetween(1000, 9999),
//                     'total' => $this->faker->numberBetween(1000, 9999),
//                     'full_summarycol' => $this->faker->text(45)
//                 ];
//             case 'smpp_mt_summary':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'system_id' => $this->faker->word,
//                     'submit_count' => $this->faker->numberBetween(1000, 9999),
//                     'submit_success_count' => $this->faker->numberBetween(1000, 9999),
//                     'submit_reject_count' => $this->faker->numberBetween(1000, 9999)
//                 ];
//             case 'smpp_local':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'type' => $this->faker->randomElement(['submit','delivered']),
//                     'system_id' => $this->faker->word,
//                     'count' => $this->faker->numberBetween(1000, 9999)
//                 ];
//             case 'smpp_international':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'type' => $this->faker->randomElement(['submit','delivered']),
//                     'system_id' => $this->faker->word,
//                     'count' => $this->faker->numberBetween(1000, 99999)
//                 ];
//             case 'smpp_oa_report':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'system_id' => $this->faker->text(45),
//                     'type' => $this->faker->randomElement(['local','international']),
//                     'oa' => $this->faker->word,
//                     'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
//                     'unique_da_count' => $this->faker->numberBetween(1000, 9999),
//                     'sample_content' => $this->faker->text(45)
//                 ];
//             case 'ss7_mt_summary':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'calling_gt' => $this->faker->numerify('##########'),
//                     'sri_for_sm_count' => $this->faker->randomNumber(),
//                     'forward_sm_count' => $this->faker->randomNumber()
//                 ];
//             case 'ss7_mt_leakage':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'calling_gt' => $this->faker->numerify('##########'),
//                     'oa' => $this->faker->word,
//                     'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
//                     'unique_da_count' => $this->faker->numberBetween(1000, 9999),
//                     'sample_content' => $this->faker->text(45)
//                 ];
//             case 'ss7_mt_oa_report':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'calling_gt' => $this->faker->numerify('##########'),
//                     'oa' => $this->faker->word,
//                     'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
//                     'unique_da_count' => $this->faker->numberBetween(1000, 9999),
//                     'sample_content' => $this->faker->text(45)
//                 ];
//             case 'top_brands':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'brand' => $this->faker->company,
//                     'traffic_type' => $this->faker->randomElement(['local', 'international']),
//                     'total' => $this->faker->numberBetween(1000, 9999),
//                     'created_at' => $this->faker->dateTime,
//                     'updated_at' => $this->faker->dateTime
//                 ];
//             case 'cdr_sms':
//                 return [
//                     'id' => $index,
//                     'created_at' => $this->faker->dateTime,
//                     'protocol' => $this->faker->randomElement(['smpp', 'ss7']),
//                     'type' => $this->faker->randomElement(['submit_sm', 'deliver_sm', 'submit_sm_resp', 'deliver_sm_resp']),
//                     'reference' => $this->faker->uuid,
//                     'sri_time' => $this->faker->dateTime,
//                     'sri_calling_gt' => $this->faker->numerify('##########'),
//                     'sri_map_gt' => $this->faker->numerify('##########'),
//                     'imsi' => $this->faker->numerify('##########'),
//                     'virtual_imsi' => $this->faker->numerify('##########'),
//                     'virtual_vlr_gt' => $this->faker->numerify('##########'),
//                     'fwdsm_time' => $this->faker->dateTime,
//                     'fwdsm_calling_gt' => $this->faker->numerify('##########'),
//                     'fwdsm_map_gt' => $this->faker->numerify('##########'),
//                     'esme_ip' => $this->faker->ipv4,
//                     'esme_port' => $this->faker->numberBetween(1000, 9999),
//                     'smsc_ip' => $this->faker->ipv4,
//                     'smsc_port' => $this->faker->numberBetween(1000, 9999),
//                     'system_id' => $this->faker->word,
//                     'message_id' => $this->faker->uuid,
//                     'dlr_time' => $this->faker->dateTime,
//                     'dlr_status' => $this->faker->randomElement(['delivered', 'undelivered']),
//                     'oa' => $this->faker->word,
//                     'da' => $this->faker->word,
//                     'dcs' => $this->faker->numberBetween(0, 255),
//                     'pid' => $this->faker->numberBetween(0, 255),
//                     'tpdu_length' => $this->faker->numberBetween(1, 140),
//                     'sar_ref' => $this->faker->uuid,
//                     'msg_part' => $this->faker->numberBetween(1, 10),
//                     'msg_parts' => $this->faker->numberBetween(1, 10),
//                     'status' => $this->faker->randomElement(['success', 'failure']),
//                     'error_major' => $this->faker->numberBetween(1, 99),
//                     'error_minor' => $this->faker->numberBetween(1, 99),
//                     'error_description' => $this->faker->text(45),
//                     'content' => $this->faker->text(160),
//                     'rule_id' => $this->faker->numberBetween(1, 1000),
//                     'action_id' => $this->faker->numberBetween(1, 1000),
//                     'node_id' => $this->faker->numberBetween(1, 1000),
//                     'traffic_type' => $this->faker->randomElement(['local', 'international'])
//                 ];
//             case 'ss7_mo_summary':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'calling_gt' => $this->faker->numerify('##########'),
//                     'submit_sm_count' => $this->faker->randomNumber()
//                 ];
//             case 'ss7_mo_oa_report':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'oa' => $this->faker->word,
//                     'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
//                     'unique_da_count' => $this->faker->numberBetween(1000, 9999),
//                     'sample_content' => $this->faker->text(45)
//                 ];
//             case 'ss7_mo_leakage':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'oa' => $this->faker->word,
//                     'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
//                     'unique_da_count' => $this->faker->numberBetween(1000, 9999),
//                     'sample_content' => $this->faker->text(45)
//                 ];
//             case 'smpp_mt_leakage':
//                 return [
//                     'id' => $index,
//                     'date' => $this->faker->date,
//                     'system_id' => $this->faker->word,
//                     'oa' => $this->faker->word,
//                     'sms_attempt_count' => $this->faker->numberBetween(1000, 9999),
//                     'unique_da_count' => $this->faker->numberBetween(1000, 9999),
//                     'sample_content' => $this->faker->text(45)
//                 ];
//             case 'bulk_contents':
//                 return [
//                     'date' => $this->faker->date,
//                     'protocol' => $this->faker->randomElement(['smpp', 'ss7']),
//                     'type' => $this->faker->randomElement(['submit_sm', 'deliver_sm']),
//                     'content' => $this->faker->text(160),
//                     'count' => $this->faker->randomNumber()
//                 ];
//             case 'spam_sms':
//                 return [
//                     'date' => $this->faker->date,
//                     'protocol' => $this->faker->randomElement(['smpp', 'ss7']),
//                     'type' => $this->faker->randomElement(['submit_sm', 'deliver_sm']),
//                     'oa' => $this->faker->word,
//                     'calling_gt' => $this->faker->numerify('##########'),
//                     'content' => $this->faker->text(160),
//                     'uniq_da_count' => $this->faker->randomNumber(),
//                     'count' => $this->faker->randomNumber()
//                 ];
//             default:
//                 throw new Exception('Invalid table name provided.');
//         }
//     }

//     public function __destruct()
//     {
//         fclose($this->csvFile);
//     }

//     public function getFilePath()
//     {
//         return $this->filePath;
//     }

//     public function getFieldsList()
//     {
//         switch ($this->tablename) {
//             case 'full_summary':
//                 return ['id', 'date', 'smpp_international', 'smpp_local', 'ss7_mt_international', 'ss7_mt_local', 'ss7_mo', 'total', 'full_summarycol'];
//             case 'smpp_mt_summary':
//                 return ['id', 'date', 'system_id', 'submit_count', 'submit_success_count', 'submit_reject_count'];
//             case 'smpp_local':
//                 return ['id', 'date', 'type', 'system_id', 'count'];
//             case 'smpp_international':
//                 return ['id', 'date', 'type', 'system_id', 'count'];
//             case 'smpp_oa_report':
//                 return ['id', 'date', 'system_id', 'type', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
//             case 'ss7_mt_summary':
//                 return ['id', 'date', 'calling_gt', 'sri_for_sm_count', 'forward_sm_count'];
//             case 'ss7_mt_leakage':
//                 return ['id', 'date', 'calling_gt', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
//             case 'ss7_mt_oa_report':
//                 return ['id', 'date', 'calling_gt', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
//             case 'top_brands':
//                 return ['id', 'date', 'brand', 'traffic_type', 'total', 'created_at', 'updated_at'];
//             case 'cdr_sms':
//                 return ['id', 'created_at', 'protocol', 'type', 'reference', 'sri_time', 'sri_calling_gt', 'sri_map_gt', 'imsi', 'virtual_imsi', 'virtual_vlr_gt', 'fwdsm_time', 'fwdsm_calling_gt', 'fwdsm_map_gt', 'esme_ip', 'esme_port', 'smsc_ip', 'smsc_port', 'system_id', 'message_id', 'dlr_time', 'dlr_status', 'oa', 'da', 'dcs', 'pid', 'tpdu_length', 'sar_ref', 'msg_part', 'msg_parts', 'status', 'error_major', 'error_minor', 'error_description', 'content', 'rule_id', 'action_id', 'node_id', 'traffic_type'];
//             case 'ss7_mo_summary':
//                 return ['id', 'date', 'calling_gt', 'submit_sm_count'];
//             case 'ss7_mo_oa_report':
//                 return ['id', 'date', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
//             case 'ss7_mo_leakage':
//                 return ['id', 'date', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
//             case 'smpp_mt_leakage':
//                 return ['id', 'date', 'system_id', 'oa', 'sms_attempt_count', 'unique_da_count', 'sample_content'];
//             case 'bulk_contents':
//                 return ['date', 'protocol', 'type', 'content', 'count'];
//             case 'spam_sms':
//                 return ['date', 'protocol', 'type', 'oa', 'calling_gt', 'content', 'uniq_da_count', 'count'];
//             default:
//                 throw new Exception("Unknown table name: {$this->tablename}");
//         }
//     }

//     public function uploadCsv()
//     {
//         $fieldsList = $this->getFieldsList();
//         $fieldsList = json_encode($fieldsList);
//         $filePath = realpath($this->filePath);

//         $importQuery = "
//             LOAD DATA INFILE '{$filePath}'
//             INTO TABLE {$this->tablename}
//             FIELDS TERMINATED BY ','
//             ENCLOSED BY '\"'
//             LINES TERMINATED BY '\\n'
//             IGNORE 1 LINES
//             ({$fieldsList})
//         ";

//         try {
//             $stmt = $this->pdo->prepare($importQuery);
//             if ($stmt->execute()) {
//                 Log::logInfo("Data imported successfully from {$filePath} into MySQL table {$this->tablename}.");
//                 echo "Data imported successfully from {$filePath} into MySQL table {$this->tablename}.";
//             } else {
//                 $errorInfo = $stmt->errorInfo();
//                 Log::logInfo("Error executing query: " . $errorInfo[2]);
//                 echo "Error executing query: " . $errorInfo[2];
//             }
//         } catch (PDOException $e) {
//             Log::logInfo("Error in import: " . $e->getMessage());
//             echo "Error in import: " . $e->getMessage();
//         }
//     }

    
// }

// $tablename = $argv[1];

// try {
//     $csvGenerator = new CsvGenerator($tablename);
//     $csvGenerator->generateData();
//     $csvGenerator->uploadCsv();
//     echo "CSV file generated and uploaded successfully: " . $csvGenerator->getFilePath() . "\n";
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage() . "\n";
//     exit(1);
// }