<?php

// namespace app\src\Models;

// use Exception;
// use PDOException;
// use app\src\Models\Model;

// require_once 'vendor/autoload.php';


// class CsvUploader extends Model
// {
   
//     private $tableName;

//     public function __construct($dbConnection, $tableName)
//     {
//         parent::__construct($dbConnection);
//         $this->tableName = $tableName;
//     }

//     public function getFieldsForTable()
//     {
//         switch ($this->tableName) {
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
//                 throw new Exception("Unknown table name: {$this->tableName}");
//         }
//     }

//     public function uploadCsv($fileName)
//     {
//         $fileName = "{$this->tableName}" . '_' . uniqid() . '.csv';
//         $csvFilePath = addslashes('output' . DIRECTORY_SEPARATOR . $fileName);
// //add a exeption here
//         if (!file_exists($csvFilePath)) {
//             echo "Error: The file $csvFilePath does not exist.";
//             return;
//         }

//         $fields = $this->getFieldsForTable();
//         $fieldsList = implode(', ', $fields);

//         $importQuery = "
//             LOAD DATA INFILE '{$csvFilePath}'
//             INTO TABLE {$this->tableName}
//             FIELDS TERMINATED BY ','
//             ENCLOSED BY '\"'
//             LINES TERMINATED BY '\\n'
//             IGNORE 1 LINES
//             ({$fieldsList})
//         ";

//         try {
//             $stmt = $this->pdo->prepare($importQuery);
//             if ($stmt->execute()) {
//                 echo "Data imported successfully from {$csvFilePath} into MySQL table {$this->tableName}.";
//             } else {
//                 $errorInfo = $stmt->errorInfo();
//                 echo "Error executing query: " . $errorInfo[2];
//             }
//         } catch (PDOException $e) {
//             echo "Error in import: " . $e->getMessage();
//         }
//     }
// }

// if ($argc < 2) {
//     echo "Usage: php script.php <tablename> <filename> <directory>\n";
//     exit(1);
// }

// $tableName = $argv[1];

// try {
//     $csvUploader = new CsvUploader($dbConnection, $tableName);
//     $csvUploader->uploadCsv($tableName);
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

