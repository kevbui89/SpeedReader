<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="./styles/style.css">
</head>
<body id="registration">
<div class="col-md-3 center-block" id="signup_form">
    <br/>
    <form action="" method="POST">
        <div class="form-group">
            <label id="signup_text">Username:</label>
            <input id="username_text" type="text" name="register_login" class="form-control">
            <label id="signup_text" for="pwd">Password:</label>
            <input id="password_text" type="password" name="register_password" class="form-control" id="pwd">
            <label id="signup_text" for="pwd">Re-enter Password:</label>
            <input type="password" name="register_password2" class="form-control" id="pwd">
        </div>
        <button type="submit" class="btn btn-default">Register!</button>
    </form>
</div>
<br/>
<br/>
<img id="satisfaction" src="res/satisfaction.png" alt="Satisfaction">
</body>
</html>

<?php
require_once('class.User.php');
require_once('class.SpeedreaderDAO.php');
$speedreaderDAO = new SpeedreaderDAO("anskztvrxtipbh", "aafbbf2a76a1c3f5186af82e8485e03ada3c3ee3934409a348a2cd6a9e42887b",
    "ec2-54-163-233-201.compute-1.amazonaws.com", "d9cmde3clp58hb");

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    if (isset($_POST['register_login']) & isset($_POST['register_password']) & isset($_POST['register_password2'])) {
        if (!empty($_POST['register_login']) & !empty($_POST['register_password']) & !empty($_POST['register_password2'])) {
            // Checks if both password were identical
            if (strcmp($_POST['register_password'], $_POST['register_password2']) == 0) {
                $user_id = htmlentities($_POST['register_login']);
                // register user if login doesnt exist.
                if (($speedreaderDAO->user_available($user_id)) == true) {
                    // Set all the variables to create the User object
                    $user_id = htmlentities($_POST['register_login']);
                    $password = htmlentities($_POST['register_password']);
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $last_line = 1;
                    $wpm = 100;
                    $to = time() - 1500;
                    $login_attempts = 0;
                    $timedout = 0;
                    $user = new User($user_id, $hashed_password, $last_line, $wpm, $to, $login_attempts, $timedout);
                    $speedreaderDAO->register_user($user);
                    echo "You have been registered.";
                    session_start();
                    $_SESSION['user_id'] = $user_id;
                    // Change the page to the wpm setter
                    header('location: ./speedreader.php');
                } else {
                    echo "<p align=center>Username is already taken, please choose another one. </p> ";
                }
            } else {
                echo "<p align=center>The passwords do not match. </p> ";
            }
        }
    }
}
?>