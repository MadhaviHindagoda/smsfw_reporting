<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Faker\Factory;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

class RandomServerData
{
    private $faker;
    private $smsmoLimit;
    private $smsmtLimit;
    private $smppLimit;

    function __construct()
    {
        $this->faker = Faker\Factory::create();
        $this->smsmoLimit = $_ENV['SMSMO_LIMIT'];
        $this->smsmtLimit = $_ENV['SMSMT_LIMIT'];
        $this->smppLimit = $_ENV['SMPP_LIMIT'];
    }

    private function getRelatedClass($part): string
    {
        if ($part >= 95) {
            return 'badge-soft-danger';
        } elseif ($part < 95 && $part > 75) {
            return 'badge-soft-warning';
        } else {
            return 'badge-soft-success';
        }
    }

    public function generateAndGetRandomServerData(): array
    {
        $cpu_data = $this->faker->randomFloat(2, 0, 100);
        $memory_data = $this->faker->randomFloat(2, 0, 100);
        $hdd_data1 = $this->faker->randomFloat(2, 0, 100);
        $hdd_data2 = $this->faker->randomFloat(2, 0, 100);

        $smsmo = $this->faker->randomFloat(2, 0, $this->smsmoLimit);
        $smsmt = $this->faker->randomFloat(2, 0, $this->smsmtLimit);
        $smpp = $this->faker->randomFloat(2, 0, $this->smppLimit);

        $count_cdr = $this->faker->numberBetween(0, 2);
        $count_smpp = $this->faker->numberBetween(0, 2);


        $cpu_difference = -$cpu_data;
        $cpu_icon = (true) ? 'ion-md-arrow-down' : 'ion-md-arrow-up';

        $ram_difference = -$memory_data;
        $ram_icon = (false) ? 'ion-md-arrow-down' : 'ion-md-arrow-up';

        $hdd_difference1 = -$hdd_data1;
        $hdd_icon1 = (true) ? 'ion-md-arrow-down' : 'ion-md-arrow-up';

        $hdd_difference2 = -$hdd_data2;
        $hdd_icon2 = (true) ? 'ion-md-arrow-down' : 'ion-md-arrow-up';


        $mo_difference = -$smsmo;
        $mo_icon = (true) ? 'ion-md-arrow-down' : 'ion-md-arrow-up';

        $mt_difference = -$smsmt;
        $mt_icon = (false) ? 'ion-md-arrow-down' : 'ion-md-arrow-up';

        $smpp_difference = -$smpp;
        $smpp_icon = (true) ? 'ion-md-arrow-down' : 'ion-md-arrow-up';

        // exec("pgrep httpd", $output, $get_httpd_rand);
        $get_httpd_rand = 0;
        $httpd_process = [0 => 'Running', 1 => 'Stoppred'];

        // exec("pgrep mysqld", $output, $get_mysql_rand);
        $get_mysql_rand = 1;
        $mysql_process = [0 => 'Running', 1 => 'Stopped'];


        $cdr_status = "";
        $cdr_class = "";
        if ($count_cdr == 0) {
            $cdr_status = "Stopped";
            $cdr_class = "badge-soft-danger";
        } elseif ($count_cdr == 1) {
            $cdr_status = "Running";
            $cdr_class = "badge-soft-success";
        } else {
            $cdr_status = "Warning";
            $cdr_class = "badge-soft-warning";
        }

        $smpp_status = "";
        $smpp_class = "";
        if ($count_smpp == 0) {
            $smpp_status = "Stopped";
            $smpp_class = "badge-soft-danger";
        } elseif ($count_smpp == 1) {
            $smpp_status = "Running";
            $smpp_class = "badge-soft-success";
        } else {
            $smpp_status = "Warning";
            $smpp_class = "badge-soft-warning";
        }

        $services = [
            0 => [
                'name' => 'Web server',
                'status' => $httpd_process[$get_httpd_rand],
                'class' => ($httpd_process[$get_httpd_rand] == 'Running') ? 'badge-soft-success' : 'badge-soft-danger'
            ],
            1 => [
                'name' => 'Database server',
                'status' => $mysql_process[$get_mysql_rand],
                'class' => ($mysql_process[$get_mysql_rand] == 'Running') ? 'badge-soft-success' : 'badge-soft-danger'
            ],
            2 => [
                'name' => 'SmsFW Service',
                'status' => $cdr_status,
                'class' => $cdr_class
            ],
            3 => [
                'name' => 'CPU',
                'status' => $cpu_data,
                'class' => $this->getRelatedClass($cpu_data)
            ],
            4 => [
                'name' => 'RAM',
                'status' => $memory_data,
                'class' => $this->getRelatedClass($memory_data)
            ],
            5 => [
                'name' => 'HDD1',
                'status' => $hdd_data1,
                'class' => $this->getRelatedClass($hdd_data1)
            ],
            6 => [
                'name' => 'HDD2',
                'status' => $hdd_data2,
                'class' => $this->getRelatedClass($hdd_data2)
            ],
            7 => [
                'name' => 'Smpp Service',
                'status' => $smpp_status,
                'class' => $smpp_class
            ],


        ];

        return [
            'cpu' => [$cpu_data, $cpu_difference, $cpu_icon],
            'ram' => [$memory_data, $ram_difference, $ram_icon],
            'hdd1' => [$hdd_data1, $hdd_difference1, $hdd_icon1],
            'hdd2' => [$hdd_data2, $hdd_difference2, $hdd_icon2],
            'services' => $services,
            'mo' => [$smsmo, $mo_difference, $mo_icon],
            'mt' => [$smsmt, $mt_difference, $mt_icon],
            'smpp' => [$smpp, $smpp_difference, $smpp_icon],
        ];

    }

}







