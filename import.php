<?php
require_once 'core/autoload.php';

$logger = new SysLogger;

$uploadFolder = "uploaded/";
$processedFolder = "processed/";
$taskname = "ImportEvents";

try {
    $logger->setTaskName($taskname);
    $check = $logger->checkIfTaskRunning();
    // print_r($check);
    if (!empty($check)) {
        throw new Exception("task: ".$taskname." is running");
    }
    $taskId = $logger->start();
    $logger->info("starting import");
} catch(Exception $e) {
    // echo $e->getMessage();
    $logger->info($e->getMessage());
    exit;
}

// exit;
if (!is_dir($uploadFolder)) {
    $logger->info("upload folder not found: ".$uploadFolder);
    exit;
}
//
//create processed folder if not exist
if (!file_exists($processedFolder)) {
    mkdir($processedFolder);
}

$dir = opendir($uploadFolder);
//Loop through each file
while (($file = readdir($dir)) !== false) {
    $fileProcessor = new FileProcessor($file);
    $logger->info("processing file: ".$uploadFolder.$file);

    if (!$fileProcessor->checkFileExtension()) {
        $logger->info("file: ".$file." is not a valid file extension");
        continue;   
    }

    $handle = fopen($uploadFolder.$file, "r");

    echo $file."\n";
    if (!$handle) {
        $logger->info("cannot process file: ".$uploadFolder.$file);
        continue;
    }

    $i = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        try {
            $fileProcessor->setRow($data);
            // check column headers
            if ($i == 0) {
                $fileProcessor->checkHeaders();
                $i++;
                continue;
            }

            //import the body data
            $fileProcessor->importData();

        } catch(Exception $e) {
            echo $e->getMessage()."\n";
            $logger->info("taskId :".$taskId." [Error] - file: ".$file." ".$e->getMessage());
            continue;
        }
        $logger->info("moving file: ".$file." to processed");
        $i++;
    }

    rename($uploadFolder.$file, $processedFolder.$file);

}

$logger->endJob($taskId);
$logger->info("taskId: ".$taskId." is complete");

