<?php
/**
 * Gets the current user line, increments the line after ajax requested the line
 */
require_once 'class.SpeedreaderDAO.php';
$speedreaderDAO = new SpeedreaderDAO("anskztvrxtipbh", "aafbbf2a76a1c3f5186af82e8485e03ada3c3ee3934409a348a2cd6a9e42887b",
    "ec2-54-163-233-201.compute-1.amazonaws.com", "d9cmde3clp58hb");

if(isset($_GET['user'])){
    $user = $speedreaderDAO->getUser($_GET['user']);
    // Get the user line number
    $line = $user->getLastLine();
    // Get the line (string)
    $line_reader = $speedreaderDAO->getUserLine($line);
    echo json_encode($line_reader);
    // Increment the line to grab
    $speedreaderDAO->updateUserLine(++$line, $_GET['user']);
} else {
    echo "failed json encoding";
}
?>