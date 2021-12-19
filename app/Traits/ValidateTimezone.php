<?php

    namespace App\Traits;

    trait ValidateTimezone {

        public function validateTimezone($timezone) {
            $fileName = 'timezones.json';
            $fileHandler = fopen($fileName, 'r') or die('unable to open file');
            $data = fread($fileHandler, filesize($fileName));
            $timezones = json_decode($data, true);
            fclose($fileHandler);
            return in_array($timezone, $timezones);
        }

    }

?>