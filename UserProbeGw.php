<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory;

function addAttribiutesForXmlNode(SimpleXMLElement $node, array $attributes): void
{
    foreach ($attributes as $key => $attribute) {
        $node->addAttribute($key, $attribute);
    }
}

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
        <AS>
            <PSP></PSP>
            <PSP></PSP>
        </AS>
        <AS>
            <PSP></PSP>
            <PSP></PSP>
        </AS>
    </STATUS>
    XML);

    $attributes_MSC1_AS_PSP1 = ['name' => "MSC1_AS_PSP1", 'RemoteIP' => "100.64.1.163", 'RemoteIP2' => "null", 'RemotePort' => "3003", 'Init' => "true", 'Active' => $active_MSC1_AS_PSP1, 'Locked' => "false", 'LocalIP1' => "10.111.3.35", 'LocalIP2' => "N/A", 'LocalPort' => "3003"];
    $attributes_MSC1_AS_PSP2 = ['name' => "MSC1_AS_PSP2", 'RemoteIP' => "100.64.1.178", 'RemoteIP2' => "null", 'RemotePort' => "3004", 'Init' => "true", 'Active' => $active_MSC1_AS_PSP2, 'Locked' => "false", 'LocalIP1' => "10.111.3.35", 'LocalIP2' => "N/A", 'LocalPort' => "3004"];
    $attributes_MSC2_AS_PSP1 = ['name' => "MSC2_AS_PSP1", 'RemoteIP' => "100.64.9.163", 'RemoteIP2' => "null", 'RemotePort' => "3003", 'Init' => "true", 'Active' => $active_MSC2_AS_PSP1, 'Locked' => "false", 'LocalIP1' => "10.111.3.35", 'LocalIP2' => "N/A", 'LocalPort' => "3007"];
    $attributes_MSC2_AS_PSP2 = ['name' => "MSC2_AS_PSP2", 'RemoteIP' => "100.64.9.177", 'RemoteIP2' => "null", 'RemotePort' => "3004", 'Init' => "true", 'Active' => $active_MSC2_AS_PSP2, 'Locked' => "false", 'LocalIP1' => "10.111.3.35", 'LocalIP2' => "N/A", 'LocalPort' => "3008"];

    $attributes_MSC1_AS = ['name' => "MSC1_AS", 'LocalRC' => "12021", 'RemoteRC' => '12021', 'in_active_threads' => "0", 'in_queue' => "0", 'internal' => "0", 'node_id' => "0"];
    $attributes_MSC2_AS = ['name' => "MSC2_AS", 'LocalRC' => "12021", 'RemoteRC' => '12021', 'in_active_threads' => "0", 'in_queue' => "0", 'internal' => "0", 'node_id' => "0"];

    addAttribiutesForXmlNode($xmlCode->AS[0]->PSP[0], $attributes_MSC1_AS_PSP1);
    addAttribiutesForXmlNode($xmlCode->AS[0]->PSP[1], $attributes_MSC1_AS_PSP2);
    addAttribiutesForXmlNode($xmlCode->AS[1]->PSP[0], $attributes_MSC2_AS_PSP1);
    addAttribiutesForXmlNode($xmlCode->AS[1]->PSP[1], $attributes_MSC2_AS_PSP2);

    addAttribiutesForXmlNode($xmlCode->AS[0], $attributes_MSC1_AS);
    addAttribiutesForXmlNode($xmlCode->AS[1], $attributes_MSC2_AS);

    return $xmlCode;
}


header('Content-Type: application/xml');
$xmlCode = generateAndGetRandomXmlData();

// Output the XML
echo $xmlCode->asXML();





