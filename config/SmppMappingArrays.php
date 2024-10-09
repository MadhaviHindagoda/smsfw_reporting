<?php

namespace config;

class SmppMappingArrays
{
    public static $local_smpp_mapping = [
        [
            'system_id' => 'smppsysidlocal1',
            'virtual_gt' => '94720000501',
            'esme_ip' => '192.168.1.1',
            'esme_port' => '27751',
        ],
        [
            'system_id' => 'smppsysidlocal2',
            'virtual_gt' => '94720000502',
            'esme_ip' => '192.168.1.2',
            'esme_port' => '27752',
        ],
        [
            'system_id' => 'smppsysidlocal3',
            'virtual_gt' => '94720000503',
            'esme_ip' => '192.168.1.3',
            'esme_port' => '27753',
        ],
        [
            'system_id' => 'smppsysidlocal4',
            'virtual_gt' => '94720000504',
            'esme_ip' => '192.168.1.4',
            'esme_port' => '27754',
        ],
        [
            'system_id' => 'smppsysidlocal5',
            'virtual_gt' => '94720000505',
            'esme_ip' => '192.168.1.5',
            'esme_port' => '27755',
        ],
        [
            'system_id' => 'smppsysidlocal6',
            'virtual_gt' => '94720000506',
            'esme_ip' => '192.168.1.6',
            'esme_port' => '27756',
        ],
        [
            'system_id' => 'smppsysidlocal7',
            'virtual_gt' => '94720000507',
            'esme_ip' => '192.168.1.7',
            'esme_port' => '27757',
        ],
        [
            'system_id' => 'smppsysidlocal8',
            'virtual_gt' => '94720000508',
            'esme_ip' => '192.168.1.8',
            'esme_port' => '27758',
        ],
        [
            'system_id' => 'smppsysidlocal9',
            'virtual_gt' => '94720000509',
            'esme_ip' => '192.168.1.9',
            'esme_port' => '27759',
        ],
        [
            'system_id' => 'smppsysidlocal10',
            'virtual_gt' => '94720000701',
            'esme_ip' => '192.168.2.1',
            'esme_port' => '27760',
        ],
        [
            'system_id' => 'smppsysidlocal11',
            'virtual_gt' => '94720000702',
            'esme_ip' => '192.168.2.2',
            'esme_port' => '27761',
        ],
        [
            'system_id' => 'smppsysidlocal12',
            'virtual_gt' => '94720000703',
            'esme_ip' => '192.168.2.3',
            'esme_port' => '27762',
        ],
        [
            'system_id' => 'smppsysidlocal13',
            'virtual_gt' => '94720000704',
            'esme_ip' => '192.168.2.4',
            'esme_port' => '2775',
        ],
        [
            'system_id' => 'smppsysidlocal14',
            'virtual_gt' => '94720000801',
            'esme_ip' => '192.168.1.30',
            'esme_port' => '2775',
        ],
        [
            'system_id' => 'smppsysidlocal15',
            'virtual_gt' => '94720000802',
            'esme_ip' => '192.168.1.31',
            'esme_port' => '2775',
        ],
        [
            'system_id' => 'smppsysidlocal16',
            'virtual_gt' => '94720000803',
            'esme_ip' => '192.168.1.32',
            'esme_port' => '2775',
        ],
        [
            'system_id' => 'smppsysidlocal17',
            'virtual_gt' => '94720000804',
            'esme_ip' => '192.168.1.33',
            'esme_port' => '2775',
        ],
        [
            'system_id' => 'smppsysidlocal18',
            'virtual_gt' => '94720000805',
            'esme_ip' => '192.168.1.34',
            'esme_port' => '2775',
        ],
    ];

    public static $intl_smpp_mapping = [
        [
            'system_id' => 'smppsysidintl1',
            'virtual_gt' => '94720000601',
            'esme_ip' => '203.0.113.20',
            'esme_port' => '2775',
        ],
        [
            'system_id' => 'smppsysidintl2',
            'virtual_gt' => '94720000602',
            'esme_ip' => '203.0.113.21',
            'esme_port' => '2776',
        ],
        [
            'system_id' => 'smppsysidintl3',
            'virtual_gt' => '94720000603',
            'esme_ip' => '203.0.113.22',
            'esme_port' => '2777',
        ],
        [
            'system_id' => 'smppsysidintl4',
            'virtual_gt' => '94720000604',
            'esme_ip' => '203.0.113.23',
            'esme_port' => '2778',
        ],
        [
            'system_id' => 'smppsysidintl5',
            'virtual_gt' => '94720000605',
            'esme_ip' => '203.0.113.24',
            'esme_port' => '2779',
        ],
        [
            'system_id' => 'smppsysidintl6',
            'virtual_gt' => '94720000606',
            'esme_ip' => '203.0.113.25',
            'esme_port' => '2780',
        ],
        [
            'system_id' => 'smppsysidintl7',
            'virtual_gt' => '94720000607',
            'esme_ip' => '203.0.113.26',
            'esme_port' => '2781',
        ],
        [
            'system_id' => 'smppsysidintl8',
            'virtual_gt' => '94720000608',
            'esme_ip' => '203.0.113.27',
            'esme_port' => '2782',
        ],
        [
            'system_id' => 'smppsysidintl9',
            'virtual_gt' => '94720000609',
            'esme_ip' => '203.0.113.28',
            'esme_port' => '2783',
        ],
    ];
    public static $esme_to_smsfw = [
        ['esme_ip' => '192.168.1.1', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.1:10000'],
        ['esme_ip' => '192.168.1.2', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.1:10000'],
        ['esme_ip' => '192.168.1.3', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.3:10002'],
        ['esme_ip' => '192.168.1.4', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.3:10002'],
        ['esme_ip' => '192.168.1.5', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.3:10002'],
        ['esme_ip' => '192.168.1.6', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.3:10002'],
        ['esme_ip' => '192.168.1.7', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.3:10002'],
        ['esme_ip' => '192.168.1.8', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.3:10002'],
        ['esme_ip' => '192.168.1.9', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.3:10002'],
    
        // International entries
        ['esme_ip' => '203.0.113.20', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.10:10009'],
        ['esme_ip' => '203.0.113.21', 'esme_port' => '2776', 'smsfw_ip_and_port' => '10.211.3.11:10010'],
        ['esme_ip' => '203.0.113.22', 'esme_port' => '2777', 'smsfw_ip_and_port' => '10.211.3.12:10011'],
        ['esme_ip' => '203.0.113.23', 'esme_port' => '2778', 'smsfw_ip_and_port' => '10.211.3.13:10012'],
        ['esme_ip' => '203.0.113.24', 'esme_port' => '2779', 'smsfw_ip_and_port' => '10.211.3.14:10013'],
        ['esme_ip' => '203.0.113.25', 'esme_port' => '2780', 'smsfw_ip_and_port' => '10.211.3.15:10014'],
        ['esme_ip' => '203.0.113.26', 'esme_port' => '2781', 'smsfw_ip_and_port' => '10.211.3.16:10115'],
        ['esme_ip' => '203.0.113.27', 'esme_port' => '2782', 'smsfw_ip_and_port' => '10.211.3.17:10016'],
        ['esme_ip' => '203.0.113.28', 'esme_port' => '2783', 'smsfw_ip_and_port' => '10.211.3.18:10017'],
    
        // Shortcode entries
        ['esme_ip' => '192.168.2.1', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.16:10015'],
        ['esme_ip' => '192.168.2.2', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.17:10016'],
        ['esme_ip' => '192.168.2.3', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.18:10017'],
        ['esme_ip' => '192.168.2.4', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.19:10018'],
    
        // MSISDN entries
        ['esme_ip' => '192.168.1.30', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.20:10019'],
        ['esme_ip' => '192.168.1.31', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.21:10020'],
        ['esme_ip' => '192.168.1.32', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.22:10021'],
        ['esme_ip' => '192.168.1.33', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.23:10022'],
        ['esme_ip' => '192.168.1.34', 'esme_port' => '2775', 'smsfw_ip_and_port' => '10.211.3.24:10023'],
    ];
    
    public static $smsfw_to_smsc = [
        ['smsfw_ip_and_port' => '10.211.3.1:10000', 'smsc_ip' => '10.201.3.1', 'smsc_port' => '20000'],
        ['smsfw_ip_and_port' => '10.211.3.2:10001', 'smsc_ip' => '10.201.3.2', 'smsc_port' => '20001'],
        ['smsfw_ip_and_port' => '10.211.3.3:10002', 'smsc_ip' => '10.201.3.3', 'smsc_port' => '20002'],
        ['smsfw_ip_and_port' => '10.211.3.4:10003', 'smsc_ip' => '10.201.3.4', 'smsc_port' => '20003'],
        ['smsfw_ip_and_port' => '10.211.3.5:10004', 'smsc_ip' => '10.201.3.5', 'smsc_port' => '20004'],
        ['smsfw_ip_and_port' => '10.211.3.6:10005', 'smsc_ip' => '10.201.3.6', 'smsc_port' => '20005'],
        ['smsfw_ip_and_port' => '10.211.3.7:10006', 'smsc_ip' => '10.201.3.7', 'smsc_port' => '20006'],
        ['smsfw_ip_and_port' => '10.211.3.8:10007', 'smsc_ip' => '10.201.3.8', 'smsc_port' => '20007'],
        ['smsfw_ip_and_port' => '10.211.3.9:10008', 'smsc_ip' => '10.201.3.9', 'smsc_port' => '20008'],
        ['smsfw_ip_and_port' => '10.211.3.10:10009', 'smsc_ip' => '10.201.3.10', 'smsc_port' => '20008'],
        ['smsfw_ip_and_port' => '10.211.3.11:10010', 'smsc_ip' => '10.201.3.11', 'smsc_port' => '20009'],
        ['smsfw_ip_and_port' => '10.211.3.12:10011', 'smsc_ip' => '10.201.3.12', 'smsc_port' => '20010'],
        ['smsfw_ip_and_port' => '10.211.3.13:10012', 'smsc_ip' => '10.201.3.13', 'smsc_port' => '20011'],
        ['smsfw_ip_and_port' => '10.211.3.14:10013', 'smsc_ip' => '10.201.3.14', 'smsc_port' => '20012'],
        ['smsfw_ip_and_port' => '10.211.3.15:10014', 'smsc_ip' => '10.201.3.15', 'smsc_port' => '20013'],
        ['smsfw_ip_and_port' => '10.211.3.16:10015', 'smsc_ip' => '10.201.3.16', 'smsc_port' => '20014'],
        ['smsfw_ip_and_port' => '10.211.3.17:10016', 'smsc_ip' => '10.201.3.17', 'smsc_port' => '20015'],
        ['smsfw_ip_and_port' => '10.211.3.18:10017', 'smsc_ip' => '10.201.3.18', 'smsc_port' => '20016'],
        ['smsfw_ip_and_port' => '10.211.3.19:10018', 'smsc_ip' => '10.201.3.19', 'smsc_port' => '20017'],
        ['smsfw_ip_and_port' => '10.211.3.20:10019', 'smsc_ip' => '10.201.3.20', 'smsc_port' => '20018'],
        ['smsfw_ip_and_port' => '10.211.3.21:10020', 'smsc_ip' => '10.201.3.21', 'smsc_port' => '20019'],
        ['smsfw_ip_and_port' => '10.211.3.22:10021', 'smsc_ip' => '10.201.3.22', 'smsc_port' => '20020'],
        ['smsfw_ip_and_port' => '10.211.3.23:10022', 'smsc_ip' => '10.201.3.23', 'smsc_port' => '20021'],
        ['smsfw_ip_and_port' => '10.211.3.24:10023', 'smsc_ip' => '10.201.3.24', 'smsc_port' => '20022'],
    ];

    public static $local_oa_pool = [
        'BOC',
        'PeoplesBank',
        'SampathBank',
        'Keels',
        'Hutch',
        'Mobitel',
        'Dialog',
        'PickMe',
        'CEB',
        '177',
        '7788',
        '1234',
        '9900',
        '0711234567',
        '0721122334',
        '94771234567',
        '94772233445'
    ];

    public static $intl_oa_pool = [
        'Google',
        'FaceBook',
        'TikTok',
        'Twitter',
        'Instagram',
        'LinkedIn',
        'WhatsApp',
        'Snapchat',
        'YouTube'
    ];

}
