<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory;

function generateAndGetRandomXmlData(): SimpleXMLElement
{
    $faker = Faker\Factory::create();

    $active_MSC1_AS_PSP1 = ($faker->numberBetween(0, 1) === 0) ? 'true' : 'false';
    $active_MSC1_AS_PSP2 = ($faker->numberBetween(0, 1) === 0) ? 'true' : 'false';
    $active_MSC2_AS_PSP1 = ($faker->numberBetween(0, 1) === 0) ? 'true' : 'false';
    $active_MSC2_AS_PSP2 = ($faker->numberBetween(0, 1) === 0) ? 'true' : 'false';

    $xmlCode = new SimpleXMLElement(<<<XML
    <?xml version='1.0'?>
    <STATUS>
        <AS name='MSC1_AS' LocalRC='12021' RemoteRC='12021' in_active_threads='0' in_queue='0' internal='0' node_id='0'>
            <PSP name='MSC1_AS_PSP1' RemoteIP='100.64.1.163' RemoteIP2='null' RemotePort='3003' Init='true' Active='{$active_MSC1_AS_PSP1}' Locked='false' LocalIP1='10.111.3.35' LocalIP2='N/A' LocalPort='3003'></PSP>
            <PSP name='MSC1_AS_PSP2' RemoteIP='100.64.1.178' RemoteIP2='null' RemotePort='3004' Init='true' Active='{$active_MSC1_AS_PSP2}' Locked='false' LocalIP1='10.111.3.35' LocalIP2='N/A' LocalPort='3004'></PSP>
        </AS>
        <AS name='MSC2_AS' LocalRC='12021' RemoteRC='12021' in_active_threads='0' in_queue='0' internal='0' node_id='0'>
            <PSP name='MSC2_AS_PSP1' RemoteIP='100.64.9.163' RemoteIP2='null' RemotePort='3003' Init='true' Active='{$active_MSC2_AS_PSP1}' Locked='false' LocalIP1='10.111.3.35' LocalIP2='N/A' LocalPort='3007'></PSP>
            <PSP name='MSC2_AS_PSP2' RemoteIP='100.64.9.177' RemoteIP2='null' RemotePort='3004' Init='true' Active='{$active_MSC2_AS_PSP2}' Locked='false' LocalIP1='10.111.3.35' LocalIP2='N/A' LocalPort='3008'></PSP>
        </AS>
    </STATUS>
    XML);

    return $xmlCode;
}


header('Content-Type: application/xml');
$xmlCode = generateAndGetRandomXmlData();

// Output the XML
echo $xmlCode->asXML();





