<?php

class ValidDataGeneratorController {
    private $existingMSISDNs = [];
    private $existingIMSIs = [];
    

    public function generateMSISDN() {
        $cc = '94';
        $nc = '72';
        $uniquePart = $this->generateUniqueDigits(7, $this->existingMSISDNs); 
        $msisdn = $cc . $nc . $uniquePart;

        $this->existingMSISDNs[] = $msisdn;
        return $msisdn;
    }

    public function generateIMSI() {
        $mcc = '403';
        $mnc = '08';
        $uniquePart = $this->generateUniqueDigits(10, $this->existingIMSIs); 
        $imsi = $mcc. $mnc . $uniquePart;

        $this->existingIMSIs[] = $imsi;
        return $imsi;
    }

    private function generateUniqueDigits($length, $existingArray) {
        do {
            $uniquePart = '';
            for ($i = 0; $i < $length; $i++) {
                $uniquePart .= mt_rand(0, 9);
            }
        } while (in_array($uniquePart, $existingArray));

        return $uniquePart;
    }

    public function generateMSISDN_IMSI_Pair() {
        $msisdn = $this->generateMSISDN();
        $imsi = $this->generateIMSI();
        
        return [
            'msisdn' => $msisdn,
            'imsi' => $imsi
        ];
    }
}

// Example usage
$generator = new ValidDataGeneratorController();

for ($i = 0; $i < 10; $i++) { // Generate 10 pairs as an example
    $pair = $generator->generateMSISDN_IMSI_Pair();
    echo "MSISDN: " . $pair['msisdn'] . ", IMSI: " . $pair['imsi'] . "\n";
}
