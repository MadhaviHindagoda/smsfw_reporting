<?php

namespace config;

class SmppMappingArrays
{
    public static function getSmppMapping()
    {
        return [
            'localSmppMapping' => [
                // Local OAs
                [
                    'oa' => 'BOC',
                    'system_id' => 'smppsysidlocal1',
                    'vlr_gt' => '94720000501',
                    'esme_ip' => '192.168.1.1',
                ],
                [
                    'oa' => 'PeoplesBank',
                    'system_id' => 'smppsysidlocal2',
                    'vlr_gt' => '94720000502',
                    'esme_ip' => '192.168.1.2',
                ],
                [
                    'oa' => 'SampathBank',
                    'system_id' => 'smppsysidlocal3',
                    'vlr_gt' => '94720000503',
                    'esme_ip' => '192.168.1.3',
                ],
                [
                    'oa' => 'Keels',
                    'system_id' => 'smppsysidlocal4',
                    'vlr_gt' => '94720000504',
                    'esme_ip' => '192.168.1.4',
                ],
                [
                    'oa' => 'Hutch',
                    'system_id' => 'smppsysidlocal5',
                    'vlr_gt' => '94720000505',
                    'esme_ip' => '192.168.1.5',
                ],
                [
                    'oa' => 'Mobitel',
                    'system_id' => 'smppsysidlocal6',
                    'vlr_gt' => '94720000506',
                    'esme_ip' => '192.168.1.6',
                ],
                [
                    'oa' => 'Dialog',
                    'system_id' => 'smppsysidlocal7',
                    'vlr_gt' => '94720000507',
                    'esme_ip' => '192.168.1.7',
                ],
                [
                    'oa' => 'PickMe',
                    'system_id' => 'smppsysidlocal8',
                    'vlr_gt' => '94720000508',
                    'esme_ip' => '192.168.1.8',
                ],
                [
                    'oa' => 'CEB',
                    'system_id' => 'smppsysidlocal9',
                    'vlr_gt' => '94720000509',
                    'esme_ip' => '192.168.1.9',
                ],
                // Short Codes
                [
                    'oa' => '177',
                    'system_id' => 'smppsysidshort1',
                    'vlr_gt' => '94720000701',
                    'esme_ip' => '203.0.113.1',
                ],
                [
                    'oa' => '7788',
                    'system_id' => 'smppsysidshort2',
                    'vlr_gt' => '94720000702',
                    'esme_ip' => '203.0.113.2',
                ],
                [
                    'oa' => '232',
                    'system_id' => 'smppsysidshrtcd3',
                    'vlr_gt' => '94720000703',
                    'esme_ip' => '203.0.113.3',
                ],
                [
                    'oa' => '1234',
                    'system_id' => 'smppsysidshrtcd4',
                    'vlr_gt' => '94720000704',
                    'esme_ip' => '203.0.113.4',
                ],
                // MSISDNs
                [
                    'oa' => '0711234567',
                    'system_id' => 'smppsysidmsisdn1',
                    'vlr_gt' => '94720000801',
                    'esme_ip' => '203.0.113.30',
                ],
                [
                    'oa' => '0721122334',
                    'system_id' => 'smppsysidmsisdn2',
                    'vlr_gt' => '94720000802',
                    'esme_ip' => '203.0.113.31',
                ],
                [
                    'oa' => '94771234567',
                    'system_id' => 'smppsysidmsisdn3',
                    'vlr_gt' => '94720000803',
                    'esme_ip' => '203.0.113.32',
                ],
                [
                    'oa' => '94761122334',
                    'system_id' => 'smppsysidmsisdn4',
                    'vlr_gt' => '94720000804',
                    'esme_ip' => '203.0.113.33',
                ],
                [
                    'oa' => '94772233445',
                    'system_id' => 'smppsysidmsisdn5',
                    'vlr_gt' => '94720000805',
                    'esme_ip' => '203.0.113.34',
                ],
            ],
            'intlSmppMapping' => [
                // International OAs
                [
                    'oa' => 'Google',
                    'system_id' => 'smppsysidintl1',
                    'vlr_gt' => '94720000601',
                    'esme_ip' => '192.168.1.20',
                ],
                [
                    'oa' => 'FaceBook',
                    'system_id' => 'smppsysidintl2',
                    'vlr_gt' => '94720000602',
                    'esme_ip' => '192.168.1.21',
                ],
                [
                    'oa' => 'TikTok',
                    'system_id' => 'smppsysidintl3',
                    'vlr_gt' => '94720000603',
                    'esme_ip' => '192.168.1.22',
                ],
                [
                    'oa' => 'Twitter',
                    'system_id' => 'smppsysidintl4',
                    'vlr_gt' => '94720000604',
                    'esme_ip' => '192.168.1.23',
                ],
                [
                    'oa' => 'Instagram',
                    'system_id' => 'smppsysidintl5',
                    'vlr_gt' => '94720000605',
                    'esme_ip' => '192.168.1.24',
                ],
                [
                    'oa' => 'LinkedIn',
                    'system_id' => 'smppsysidintl6',
                    'vlr_gt' => '94720000606',
                    'esme_ip' => '192.168.1.25',
                ],
                [
                    'oa' => 'WhatsApp',
                    'system_id' => 'smppsysidintl7',
                    'vlr_gt' => '94720000607',
                    'esme_ip' => '192.168.1.26',
                ],
                [
                    'oa' => 'Snapchat',
                    'system_id' => 'smppsysidintl8',
                    'vlr_gt' => '94720000608',
                    'esme_ip' => '192.168.1.27',
                ],
                [
                    'oa' => 'YouTube',
                    'system_id' => 'smppsysidintl9',
                    'vlr_gt' => '94720000609',
                    'esme_ip' => '192.168.1.28',
                ],
            ],

            'esmeToSmsfw' => [
                ['esmeIp' => '192.168.1.1,192.168.1.2', 'smsfwIpandPort' => '10.211.3.1:10000'],
                // ['esmeIp' => '192.168.1.2', 'smsfwIpandPort' => '10.211.3.2:10001'],
                ['esmeIp' => '192.168.1.3,192.168.1.4,192.168.1.5,192.168.1.6,192.168.1.7,192.168.1.8,192.168.1.9', 'smsfwIpandPort' => '10.211.3.3:10002'],
                // ['esmeIp' => '192.168.1.4', 'smsfwIpandPort' => '10.211.3.4:10003'],
                // ['esmeIp' => '192.168.1.5', 'smsfwIpandPort' => '10.211.3.5:10004'],
                // ['esmeIp' => '192.168.1.6', 'smsfwIpandPort' => '10.211.3.6:10005'],
                // ['esmeIp' => '192.168.1.7', 'smsfwIpandPort' => '10.211.3.7:10006'],
                // ['esmeIp' => '192.168.1.8', 'smsfwIpandPort' => '10.211.3.8:10007'],
                // ['esmeIp' => '192.168.1.9', 'smsfwIpandPort' => '10.211.3.9:10008'],
                ['esmeIp' => '192.168.1.20', 'smsfwIpandPort' => '10.211.3.10:10009'],
                ['esmeIp' => '192.168.1.21', 'smsfwIpandPort' => '10.211.3.11:10010'],
                ['esmeIp' => '192.168.1.22', 'smsfwIpandPort' => '10.211.3.12:10011'],
                ['esmeIp' => '192.168.1.23', 'smsfwIpandPort' => '10.211.3.13:10012'],
                ['esmeIp' => '192.168.1.24', 'smsfwIpandPort' => '10.211.3.14:10013'],
                ['esmeIp' => '192.168.1.25', 'smsfwIpandPort' => '10.211.3.15:10014'],
                ['esmeIp' => '192.168.1.26', 'smsfwIpandPort' => '10.211.3.16:10015'],
                ['esmeIp' => '192.168.1.27', 'smsfwIpandPort' => '10.211.3.17:10016'],
                ['esmeIp' => '192.168.1.28', 'smsfwIpandPort' => '10.211.3.18:10017'],
                ['esmeIp' => '203.0.113.1', 'smsfwIpandPort' => '10.211.3.16:10015'],
                ['esmeIp' => '203.0.113.2', 'smsfwIpandPort' => '10.211.3.17:10016'],
                ['esmeIp' => '203.0.113.3', 'smsfwIpandPort' => '10.211.3.18:10017'],
                ['esmeIp' => '203.0.113.4', 'smsfwIpandPort' => '10.211.3.19:10018'],
                ['esmeIp' => '203.0.113.30', 'smsfwIpandPort' => '10.211.3.20:10019'],
                ['esmeIp' => '203.0.113.31', 'smsfwIpandPort' => '10.211.3.21:10020'],
                ['esmeIp' => '203.0.113.32', 'smsfwIpandPort' => '10.211.3.22:10021'],
                ['esmeIp' => '203.0.113.33', 'smsfwIpandPort' => '10.211.3.23:10022'],
                ['esmeIp' => '203.0.113.34', 'smsfwIpandPort' => '10.211.3.24:10023'],
            ],

            'smsfwToSmsc' => [
                ['smsfwIpandPort' => '10.211.3.1:10000', 'smscIp' => '10.201.3.1', 'smscPort' => '20000'],
                ['smsfwIpandPort' => '10.211.3.2:10001', 'smscIp' => '10.201.3.2', 'smscPort' => '20001'],
                ['smsfwIpandPort' => '10.211.3.3:10002', 'smscIp' => '10.201.3.3', 'smscPort' => '20002'],
                ['smsfwIpandPort' => '10.211.3.4:10003', 'smscIp' => '10.201.3.4', 'smscPort' => '20003'],
                ['smsfwIpandPort' => '10.211.3.5:10004', 'smscIp' => '10.201.3.5', 'smscPort' => '20004'],
                ['smsfwIpandPort' => '10.211.3.6:10005', 'smscIp' => '10.201.3.6', 'smscPort' => '20005'],
                ['smsfwIpandPort' => '10.211.3.7:10006', 'smscIp' => '10.201.3.7', 'smscPort' => '20006'],
                ['smsfwIpandPort' => '10.211.3.8:10007', 'smscIp' => '10.201.3.8', 'smscPort' => '20007'],
                ['smsfwIpandPort' => '10.211.3.9:10008', 'smscIp' => '10.201.3.9', 'smscPort' => '20008'],
                ['smsfwIpandPort' => '10.211.3.10:10009', 'smscIp' => '10.201.3.10', 'smscPort' => '20008'],
                ['smsfwIpandPort' => '10.211.3.11:10010', 'smscIp' => '10.201.3.11', 'smscPort' => '20009'],
                ['smsfwIpandPort' => '10.211.3.12:10011', 'smscIp' => '10.201.3.12', 'smscPort' => '20010'],
                ['smsfwIpandPort' => '10.211.3.13:10012', 'smscIp' => '10.201.3.13', 'smscPort' => '20011'],
                ['smsfwIpandPort' => '10.211.3.14:10013', 'smscIp' => '10.201.3.14', 'smscPort' => '20012'],
                ['smsfwIpandPort' => '10.211.3.15:10014', 'smscIp' => '10.201.3.15', 'smscPort' => '20013'],
                ['smsfwIpandPort' => '10.211.3.16:10015', 'smscIp' => '10.201.3.16', 'smscPort' => '20014'],
                ['smsfwIpandPort' => '10.211.3.17:10016', 'smscIp' => '10.201.3.17', 'smscPort' => '20015'],
                ['smsfwIpandPort' => '10.211.3.18:10017', 'smscIp' => '10.201.3.18', 'smscPort' => '20016'],
                ['smsfwIpandPort' => '10.211.3.19:10018', 'smscIp' => '10.201.3.19', 'smscPort' => '20017'],
                ['smsfwIpandPort' => '10.211.3.20:10019', 'smscIp' => '10.201.3.20', 'smscPort' => '20018'],
                ['smsfwIpandPort' => '10.211.3.21:10020', 'smscIp' => '10.201.3.21', 'smscPort' => '20019'],
                ['smsfwIpandPort' => '10.211.3.22:10021', 'smscIp' => '10.201.3.22', 'smscPort' => '20020'],
                ['smsfwIpandPort' => '10.211.3.23:10022', 'smscIp' => '10.201.3.23', 'smscPort' => '20021'],
                ['smsfwIpandPort' => '10.211.3.24:10023', 'smscIp' => '10.201.3.24', 'smscPort' => '20022'],
            ],
        ];
    }
}
