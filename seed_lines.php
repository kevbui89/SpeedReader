<?php

set_time_limit(2400);

require_once("class.SpeedreaderDAO.php");
$speedreaderDAO = new SpeedreaderDAO("anskztvrxtipbh", "aafbbf2a76a1c3f5186af82e8485e03ada3c3ee3934409a348a2cd6a9e42887b",
    "ec2-54-163-233-201.compute-1.amazonaws.com", "d9cmde3clp58hb");
$speedreaderDAO->executeQuery("DROP TABLE IF EXISTS aesop");
$speedreaderDAO->executeQuery("CREATE TABLE aesop (line_number INTEGER, line varchar(300))");

$file = fopen("aesop11.txt", "r");
$line_number = 0;
while (!feof($file)) {
    // Seed the file
    $line = trim(fgets($file));
    if ($line != '') {
        $line_number++;
        $speedreaderDAO->insertLines($line_number, $line);
        print($line_number. "\n");
    }
}
print ("done");
?>