<?php
require_once("class.User.php");
require_once 'class.SpeedreaderDAO.php';
$speedreaderDAO = new SpeedreaderDAO("anskztvrxtipbh", "aafbbf2a76a1c3f5186af82e8485e03ada3c3ee3934409a348a2cd6a9e42887b",
    "ec2-54-163-233-201.compute-1.amazonaws.com", "d9cmde3clp58hb");
$user = $speedreaderDAO->getUser("minhvu");

var_dump($user);
?>