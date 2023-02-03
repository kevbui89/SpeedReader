<?php
require_once('class.SpeedreaderDAO.php');
require_once('class.User.php');
$speedreaderDAO = new SpeedreaderDAO("anskztvrxtipbh", "aafbbf2a76a1c3f5186af82e8485e03ada3c3ee3934409a348a2cd6a9e42887b",
    "ec2-54-163-233-201.compute-1.amazonaws.com", "d9cmde3clp58hb");
session_start();
session_regenerate_id();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./index.php");
}

// Set the users variables
$user_id = $_SESSION['user_id'];
$user = $speedreaderDAO->getUser($user_id);
$wpm = $user->getLastWordsPerMinute();

// Log out
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ./index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./styles/style.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body id="speedreader">
<div id="signup_form">
    <div id="word_div">
        <p id="word"></p>
    </div>
    <br/>
    <p hidden id="user"><?php echo $user_id; ?></p>
    <p hidden id="wpm_text"><?php echo $wpm; ?></p>
    <div id="wpm_div">
        <p id="wpm_text_info">Current WPM: <?php echo $wpm; ?></p></p>
    </div>
    <form action="" method="POST">
        <label id="wpm_label" for="speed_input">Change speed:</label>
        <br/>
        <input type="text" name="wpm" class="speed-reader" id="wpm">
        <br/><br/>
        <button type="submit" class="btn btn-info" name="wpm_input">Set Speed</button>
    </form>
    <br/>
    <form action="" method="POST">
        <button type="submit" class="btn btn-danger" name="logout">Logout</button>
    </form>
    <br/>
    <div id="source_div">
        <p id="source">Source of the text: http://www.textfiles.com/etext </p>
    </div>
</div>
</body>
</html>

<?php
require_once('class.SpeedreaderDAO.php');
require_once('class.User.php');
$speedreaderDAO = new SpeedreaderDAO("anskztvrxtipbh", "aafbbf2a76a1c3f5186af82e8485e03ada3c3ee3934409a348a2cd6a9e42887b",
    "ec2-54-163-233-201.compute-1.amazonaws.com", "d9cmde3clp58hb");

// Get the prefered words per minute of the user
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    // Checks if the wpm is set
    if (isset($_POST['wpm'])) {
        // Checks if the value is numeric
        if (is_numeric($_POST['wpm'])) {
            // Checks the range
            if (($_POST['wpm']) >= 50 && ($_POST['wpm'] <= 2000) && (($_POST['wpm'] % 50) == 0)) {
                // HTMLentities to prevent injection
                $wpm = htmlentities($_POST['wpm']);
                // Update it in the database
                $speedreaderDAO->updateWpm($wpm, $user_id);
                header('location: ./speedreader.php');
            } else {
                echo "<br/>";
                echo "<p align=center style=color:red;>The WPM must be between 50 and 2000 and must be steps of 50 words per minute. </p> ";
            }
        }
    }
}
?>

<script>
    $(document).ready(function () {
        console.log("ready!");
        // Get the words per minute of the current user
        // I didn't get the wpm from the AJAX response :(
        // I made a page where the user sets his own WPM before starting to read...
        // And update it with PHP to the database
        // Please have pity :(
        // Don't know if I am just stupid or really lazy :(
        var wpm = document.getElementById("wpm_text").innerHTML;
        // Get the current line
        getLine();

        /**
         * Gets the current line to display
         */
        function getLine() {
            // The user id
            var user = document.getElementById("user").innerHTML;
            $.ajax({
                url: "get_line.php",
                data: {user: user},
                type: "GET",
                dataType: "json",
                success: function (json) {
                    var background = document.getElementById("word_div");
                    background.style.backgroundColor = 'black';
                    // Break the json response into an array of words
                    var word = json.valueOf().replace(/\n/g, " ").trim().split(" ");
                    var counter = 0;
                    var wordLoop = setInterval(function () {
                        // Checks if the word is null or not
                        if (word[counter]) {
                            $('#word').html(generateRedChar(word[counter]));
                        }
                        counter++;
                        // If the end of the word array is reached, get the new line
                        if (counter === word.length - 1) {
                            getLine();
                        }
                    }, (60000 / wpm));
                } // End success
            });
        }

        /**
         * Generates red center letter
         * @param word          The word the be displayed
         * @returns {string|*}  The word to be displayed
         */
        function generateRedChar(word) {
            var middle;
            var left, right;
            var wordLength = word.length;
            var result;
            var word_text = document.getElementById("word");

            // Find the somewhat center of the word... But not really the center...
            if (wordLength == 1) {
                middle = 0;
            } else if (wordLength >= 2 && wordLength <= 5) {
                middle = 1;
            } else if (wordLength >= 6 && wordLength <= 9) {
                middle = 2;
            } else if (wordLength >= 10 && wordLength <= 13) {
                middle = 3;
            } else {
                middle = 4;
            }

            // Colors the middle (not really) letter in red
            if (middle === 0) {
                word_text.innerHTML = "    " + '<span id = "red">' + word + '</span>';
                return result;
            } else if (middle === 1) {
                left = word.substring(0, middle);
                right = word.substring(middle + 1, wordLength);
                word_text.innerHTML = "   " + left + '<span id = "red">'
                    + word.charAt(middle) + '</span>' + right;
            } else if (middle === 2) {
                left = word.substring(0, middle);
                right = word.substring(middle + 1, wordLength);
                word_text.innerHTML = "    " + left + '<span id = "red">'
                    + word.charAt(middle) + '</span>' + right;
            } else if (middle === 3) {
                left = word.substring(0, middle);
                right = word.substring(middle + 1, wordLength);
                word_text.innerHTML = "    " + left + '<span id = "red">'
                    + word.charAt(middle) + '</span>' + right;
            } else {
                left = word.substring(0, middle);
                right = word.substring(middle + 1, wordLength);
                word_text.innerHTML = " " + left + '<span id = "red">'
                    + word.charAt(middle) + '</span>' + right;
            }

            return result;
        } // End function get red
    });
</script>

