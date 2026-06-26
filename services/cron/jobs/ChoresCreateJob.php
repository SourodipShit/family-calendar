<?php

require_once __DIR__ . '/../../../classes/Chore.php';

class ChoresCreateJob
{
    public static function run()
    {
        echo "Starting ChoresCreateJob...<br>\n";
        
        $result = Chore::runCronJob();
        
        if ($result['status'] === 'success') {
            echo "ChoresCreateJob completed successfully.<br>\n";
        } else {
            echo "ChoresCreateJob failed: " . $result['msg'] . "<br>\n";
        }
    }
}
