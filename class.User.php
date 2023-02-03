<?php

/**
 * Class User
 */
class User{
    private $user_id;
    private $hashed_password;
    private $last_line;
    private $wpm;
    private $timeout_time;
    private $login_attempts;
    private $timedout;

    public function __construct($user_id = "", $hashed_password = "", $last_line = 1, $wpm = 0, $timeout_time = 0, $login_attempts = 0, $timedout = 0){
        $this->user_id = $user_id;
        $this->hashed_password = $hashed_password;
        $this->last_line = $last_line;
        $this->wpm = $wpm;
        $this->$timeout_time = $timeout_time;
        $this->login_attempts = $login_attempts;
        $this->timedout = $timedout;
    }

    public function getUserId(){
        return $this->user_id;
    }
    public function setUserId($user_id){
        $this->user_id = $user_id;
    }

    public function getHashedPassword(){
        return $this->hashed_password;
    }
    public function setHashedPassword($hashed_password){
        $this->hashed_password = $hashed_password;
    }
    
    public function getLastLine(){
        return $this->last_line;
    }
    
    public function setLastLine($last_line){
        $this->last_line = $last_line;
    }
    
    public function getLastWordsPerMinute(){
        return $this->wpm;
    }
    
    public function setLastWordsPerMinute($wpm){
        $this->wpm = $wpm;
    }

    public function getTimeOutTime(){
        return $this->timeout_time;
    }

    public function setTimeOutTime($timeout_time){
        $this->timeout = $timeout_time;
    }

    public function getLoginAttempts(){
        return $this->login_attempts;
    }

    public function setLoginAttempts($login_attempts){
        $this->login_attempts = $login_attempts;
    }

    public function getTimedOut(){
        return $this->timedout;
    }

    public function setTimedOut($timedout){
        $this->timedout = $timedout;
    }
}
 ?>
