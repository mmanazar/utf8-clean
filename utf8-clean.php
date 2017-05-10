#! /usr/bin/php
<?php

$dir = $argv[1];
//$account_id = $argv[1];

// writes errors to errorfile
function errorMessageFWrite($error_message, $file, $counter){
    $date = new DateTime(null, new DateTimeZone('America/New_York'));
    $datestring = $date->format('Y-m-d\TH:i:s');
    fwrite($file, $datestring . "\t" . $error_message . $counter . "\n");
};

//variables to be used for error files and directories
$error_directory = $dir."/error_files/";
$exceptions_directory = $dir."/exceptions_files/";
$cleaned_directory = $dir."/cleaned_files/";

    if (!file_exists($error_directory)){
        mkdir($error_directory);
    }
    if (!file_exists($exceptions_directory)){
        mkdir($exceptions_directory);
    }
    if (!file_exists($cleaned_directory)){
        mkdir($cleaned_directory);
    }
$files = scandir($dir);
$file_info = array();

foreach($files as $file_to_process){

    if(strpos($file_to_process, ".del") !== FALSE ){

        echo "Starting File: " . $file_to_process . "\n";

        $filewithdir = $dir .'/'. $file_to_process;
        $filename = $file_to_process;
        $temp = explode(".", $filename);
        $filewithoutext = $temp[0];
        $errorfilename = $filewithoutext . "_error.txt";
        $exceptionsfilename = $filewithoutext . "_exceptions.txt";
        $cleanedfilename = $filewithoutext . "_cleaned.txt";
        $temp2 = explode(".", $filename);
        $fileext = $temp2[1];
        $errorfilewithdir = $error_directory . $errorfilename;
        // line counters for error messages
            $counter_start = 1;
            $counter = 1;
            $errorcounter = 0;

            //open the error file
            $file = fopen($errorfilewithdir, 'w+');

        // get contents of file
            $contents = fopen($filewithdir, "r");
            $exceptions_file = fopen($exceptions_directory . $exceptionsfilename, "w");
        $cleaned_file = fopen($cleaned_directory . $cleanedfilename, "w");

            while(!feof($contents)){

                $line = fgets($contents);

                if(!feof($contents)){

                    $retval = mb_check_encoding($line, 'UTF-8');

                        if($retval != 1){
                            $error_message = "Invalid UTF-8 Encoding at Line Number ";
                            errorMessageFWrite($error_message, $file, $counter);
                            fwrite($exceptions_file, $line);
                            $errorcounter++;
                        } // ending if for UTF-8 check
                        else{
                            fwrite($cleaned_file, $line);
                        }
                    $counter += 1;
                }// ending if for end of file
            }// ending while through file
    echo "File Complete: " . $file_to_process . "\n";
    array_push($file_info, array($file_to_process, $counter - 1, $errorcounter));
    }
}
foreach($file_info as $info){
    echo $info[0] . " contains " . $info[1] . " lines and " . $info[2] . " errors.\n";
}
?>
