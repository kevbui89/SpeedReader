<?php
require_once 'class.SpeedreaderDAO.php';
$speedreaderDAO = new SpeedreaderDAO("anskztvrxtipbh", "aafbbf2a76a1c3f5186af82e8485e03ada3c3ee3934409a348a2cd6a9e42887b",
    "ec2-54-163-233-201.compute-1.amazonaws.com", "d9cmde3clp58hb");

if($_SERVER["REQUEST_METHOD"] === 'POST'){
    if(isset($_POST['login']) & isset($_POST['password'])){
        if(!empty($_POST['login']) & !empty($_POST['password'])){
            $user_id = htmlentities($_POST['login']);
            $password = htmlentities($_POST['password']);
            $timeout = $speedreaderDAO->is_user_timedout($user_id);
            $timeout_time = $speedreaderDAO->get_user_timeout_time($user_id);

            // Checks if the user was timed out (1 = true)
            if ($timeout == 1) {
                // UNIX comparison checking if the current time is bigger than the timeout time
                if(time() > strtotime($timeout_time)){
                    // Reset the user if it is the case
                    $speedreaderDAO->reset_user($user_id);
                }else{
                    echo "timeout";
                    // Display the timeout page
                    header('Location: ./timeout.html');
                }
            }
            // Checks if the user was timed out (0 = false)
            if(($speedreaderDAO->is_user_timedout($user_id)) == 0){
                // Returns true if the login attempt was successful
                if($speedreaderDAO->check_login($user_id, $password)){
                    // Reset the user to initial values
                    $speedreaderDAO->reset_user($user_id);
                    // Start the session
                    session_start();
                    $_SESSION['user_id'] = $user_id;
                    // Login was successful, redirect to speedreader
                    //header("Location: ./speedreader.php");
                    header('location: ./speedreader.php');
                } else {
                    // Login has failed, redirect to the index
                    header('Location: ./index.php');
                }
            }
        } else {
            // login or password were/was empty
            header('Location: ./index.php');
        }
    }
}
?>
