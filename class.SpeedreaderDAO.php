<?php
/**
 *  This class is responsible for all database manipulation
 */
require_once("class.User.php");
class SpeedreaderDAO {
    private $user;
    private $password;
    private $host;
    private $database;

    /**
     * SpeedreaderDAO constructor.
     * @param $user     The database user
     * @param $password The database password
     * @param $host     The database host
     * @param $database The database
     */
    public function __construct($user, $password, $host, $database) {
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->database = $database;
    }
    
    /**
     * Registers the user in the database.
     * @return boolean if register was success or not.
     * @param  $user   user object to be inserted into the database
     **/
    public function register_user($user) {
        $query = 'INSERT INTO reader_users (user_id, hashed_password, last_line, wpm, timeout_time, login_attempts, timedout) VALUES(?,?,?,?,?,?,?)';
        // Check if a the user is null
        if (!$user) {
            return false;
        }

        // Get the user properties
        $user_id = $user->getUserId();
        $hashed_password = $user->getHashedPassword();
        $last_line = $user->getLastLine();
        $wpm = $user->getLastWordsPerMinute();
        $tot = $user->getTimeOutTime();
        $login_attempts = $user->getLoginAttempts();
        $timedout = $user->getTimedOut();

        // Printing to see what is inside the user
        var_dump($user);

        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->bindParam(2, $hashed_password);
            $stmt->bindParam(3, $last_line);
            $stmt->bindParam(4, $wpm);
            $stmt->bindParam(5,$tot);
            $stmt->bindParam(6, $login_attempts);
            $stmt->bindParam(7, $timedout);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
    }

    /**
     * Checks if the username is available to be registered with
     * @param $user_id  The user id to be verified
     * @return bool     A boolean representation of the availability of the user id
     */
    public function user_available($user_id){
        $query = "SELECT user_id FROM reader_users WHERE user_id = ?";
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->execute();
            $rows = count($stmt->fetchAll(PDO::FETCH_ASSOC));
            if($rows == 0){
                // No user was found
                return true;
            }else{
                // A user was found
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
    }
    
    /**
     *  Verify if the user entered the right credentials to login
     *  @param  @user_id    The user id
     *  @param  @password   The user password
     *  @return boolean representing whether the login was successful or not
     */
    public function verify_login($user_id, $password) {
        $login_successful = false;
        $query = 'SELECT * FROM reader_users WHERE user_id = ?';
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
            $stmt->execute();
            
            // Create the user
            $user = $stmt->fetch();
            // Check if the user is null
            if (!$user) {
                $login_successful = false;
            }
            // Debug
            var_dump($user);
            
            $hashed_password = $user->getHashedPassword();
            // Check if the password matches the hashed password
            if (password_verify($password, $hashed_password)) {
                $login_successful = true;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
        return $login_successful;
    }
    
    /**
     *  Fetches the line where the user was at
     *  @param  @line_number    The user line number
     *  @return @line           The line
     */
    public function getUserLine($line_number) {
        $query = 'SELECT line FROM aesop WHERE line_number = ?';
        $line = NULL;
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $line_number);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                $line = $row['line'];
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
        return $line;
    }

    /**
     * Updates a line of the user
     * @param $line_number  The line to be updated
     * @param $user_id      The user id
     */
    public function updateUserLine($line_number, $user_id) {
        $query = 'UPDATE reader_users SET last_line = ? WHERE user_id = ?';
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $line_number);
            $stmt->bindParam(2, $user_id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
    }

    /**
     * Updates a line of the user
     * @param $line_number  The line to be updated
     * @param $user_id      The user id
     */
    public function updateWpm($wpm, $user_id) {
        $query = 'UPDATE reader_users SET wpm = ? WHERE user_id = ?';
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $wpm);
            $stmt->bindParam(2, $user_id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
    }
    
    /**
     *  Fetches the user object from the database
     *  @param  @user_id    The user id
     *  @return 
     */
    public function getUser($user_id) {
        $query = 'SELECT * FROM reader_users WHERE user_id = ?';
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
            $stmt->execute();
            $user = $stmt->fetch();
            return $user;
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
    }

    /**
     * Inserts a line into the database
     * @param $line_number  The line number
     * @param $line         The line
     */
    public function insertLines($line_number, $line) {
        $query = 'INSERT INTO aesop (line_number, line) VALUES (?,?)';
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $line_number);
            $stmt->bindParam(2, $line);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
    }

    /**
     * Executes a query
     * @param $query    The query to be execute
     */
    public function executeQuery($query) {
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $pdo->exec ($query);
        } catch (PDOException $e){
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
    }

    /**
     * Gets a line from the text
     * @param $line_number  The line number
     * @return $line        The string representation of the line
     */
    public function getLine($line_number) {
        $query = 'SELECT line FROM aesop WHERE line_number = ?';
        $line = NULL;
        try {
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $line_number);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                $line = $row['line'];
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }

        return $line;
    }

    /**
     * Checks if the password matches the hashed password in the database
     * @return  Boolean representing a valid login or not
     */
    public function check_login($user_id, $password){
        $successful_login = false;
        $query = 'SELECT * FROM reader_users WHERE user_id = ?';
        try{
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1,$user_id);
            $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'User');
            $stmt -> execute();
            $user = $stmt->fetch();
            // Checks if user is null
            if(!$user){
                return $successful_login;
            }
            // Check the user properties. If it gets to this statement
            // The user should contain properties
            var_dump($user);

            // Get the hashed password
            $hashed_password = $user->getHashedPassword();

            // Check if the password matches the hashed password
            if(password_verify($password,$hashed_password)){
                $successful_login = true;
            } else{
                // Increase the number of attempts from the user
                $count = $this->increase_user_login_attempts($user);
                // If the user failed more than 3 times, time the user out
                if($count >= 3){
                    $this->timeout_user($user->getUserId());
                }
            }
        } catch(PDOEXception $e){
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
        return $successful_login;
    }

    /**
     * Checks if the user is timed out or not
     * The possible values are 1 (true) and 0 (false)
     * @return boolean representing if the user was timed out or not
     **/
    public function is_user_timedout($user_id){
        $query = 'SELECT timedout FROM reader_users WHERE user_id = ?';
        try{
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1,$user_id);
            $stmt->execute();
            return $stmt->fetch()[0];
        }catch(PDOException $e){
            $e->getMessage();
        } finally {
            unset($pdo);
        }
    }

    /**
     * Time the user out
     * @param $user_id  The user id to be timed out
     */
    public function timeout_user($user_id){
        $query = 'UPDATE reader_users SET timeout_time = ?, timedout = ? WHERE user_id = ?';
        // Add about 15 minutes, UNIX timestamp values when compairing inside the login.php file
        $timeout_time = date('Y-m-d H:i:s A',time() + 1500);
        try{
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(1,$timeout_time);
            // 0 = false, 1 = true
            $stmt->bindValue(2, 1);
            $stmt->bindValue(3, $user_id);
            $stmt->execute();
        }catch(PDOException $e){
            $e->getMessage();
        } finally {
            unset($pdo);
        }
    }

    /**
     * Reset the users settings to initial values
     * @param $user_id  The user id to update values to
     */
    public function reset_user($user_id){
        $query = 'UPDATE reader_users SET timeout_time = ?, login_attempts = ?, timedout = ? WHERE user_id = ?';
        try{
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            // Reset everything to initial values
            $stmt->bindValue(1, null);
            $stmt->bindValue(2, 0);
            // 0 = false, 1 = true
            $stmt->bindValue(3, 0);
            $stmt->bindValue(4, $user_id);
            $stmt->execute();
            echo "DONE UPDATING RESET_USER";
        }catch(PDOException $e){
            $e->getMessage();
        } finally {
            unset($pdo);
        }
    }

    /**
     * Returns the time out time of the user
     * @param $user_id
     * @return int
     */
    public function get_user_timeout_time($user_id){
        $query = 'SELECT timeout_time FROM reader_users WHERE user_id = ?';
        try{
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(1, $user_id);
            $stmt->execute();
            return $stmt -> fetch()[0];
        }catch(PDOException $e){
            return -1;
        } finally {
            unset($pdo);
        }
    }

    /**
     * Increases the attempts of the user and returns the final attempt counter
     * @param $user             The user to increment the attempts
     * @return $login_attempts  The number of attempts
     */
    public function increase_user_login_attempts($user){
        $login_attempts = $user->getLoginAttempts();
        $query = 'UPDATE reader_users SET login_attempts = ? WHERE user_id = ?';
        try{
            $pdo = new PDO("pgsql:dbname=".$this->database.";host=".$this->host, $this->user, $this->password);
            $stmt = $pdo->prepare($query);
            // Increment before updating the database
            $stmt->bindValue(1,++$login_attempts);
            $stmt->bindValue(2,$user->getUserId());
            $stmt->execute();
        }catch(PDOEXception $e){
            echo $e->getMessage();
        } finally {
            unset($pdo);
        }
        // Return the incremented attempt
        return $login_attempts;
    }
}