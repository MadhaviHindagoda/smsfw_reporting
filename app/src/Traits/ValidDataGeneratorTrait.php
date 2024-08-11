<?php

namespace app\src\Traits;
require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

trait ValidDataGeneratorTrait {
    
    //Generates an MSISDN based on the type (local or international).
    public function generateMSISDN(String $type = 'local'):String {
        if ($type === 'international') {
            $cc = $_ENV['INTL_CC'];
            $ncArray = explode(',', $_ENV['INTL_NC']);
        } else {
            $cc = $_ENV['LOCAL_CC'];
            $ncArray = explode(',', $_ENV['LOACL_NC']);
        }
    
        $nc = trim($ncArray[array_rand($ncArray)]);
    
        
        $hash = md5(mt_rand(), true);
        $numericHash = substr(preg_replace("/[^0-9]/", '', $hash), 0, 7);
    
        $msisdnPrefix = "{$cc}{$nc}";
        $msisdnSuffix = str_pad($numericHash, 7, '0', STR_PAD_LEFT);
    
        $msisdn = "{$msisdnPrefix}{$msisdnSuffix}";
       
        return $msisdn;
    }
    

    public function generateIMSI(String $msisdn):String {
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
    
        // Combine prefix and suffix to form the IMSI
        $imsiPrefix = "{$mcc}{$ncc}";
        $imsiSuffix = str_pad($numericHash, 10, '0', STR_PAD_LEFT);
        $imsi = "{$imsiPrefix}{$imsiSuffix}";
    
        // Ensure the IMSI has 15 digits
        $imsi = substr($imsi, 0, 15);
    
        return $imsi;
    }

}