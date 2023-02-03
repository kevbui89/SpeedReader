<?php
require_once("class.SpeedreaderDAO.php");
$speedreaderDAO = new SpeedreaderDAO("anskztvrxtipbh", "aafbbf2a76a1c3f5186af82e8485e03ada3c3ee3934409a348a2cd6a9e42887b",
    "ec2-54-163-233-201.compute-1.amazonaws.com", "d9cmde3clp58hb");
$speedreaderDAO->executeQuery("DROP TABLE IF EXISTS reader_users");
$speedreaderDAO->executeQuery("CREATE TABLE reader_users (user_id varchar(100), hashed_password varchar(100), 
last_line varchar(300), wpm INTEGER, timeout_time DATE, login_attempts INTEGER, timedout INTEGER)");
print ("done");
?>