<?php

class User
{
    private $con;
    private  $errorArray = array();
    public function __construct($con)
    {
        $this->con = $con;
    }
    public function register($username, $password)
    {
        //usernameのバリデーション
        $this->validateUsername($username);


        if (empty($this->errorArray)) {
            return $this->insertUser($username, $password);
        }
        return false;
    }
    private function insertUser($un, $pw)
    {
        $pw = hash("sha512", $pw);

        $query = $this->con->prepare("INSERT INTO users (username, password) 
                                    VALUES (:un,:pw)");

        $query->bindValue(":un", $un);
        $query->bindValue(":pw", $pw);
        return $query->execute();

        // データベースのdebug↓
        // $query->execute();
        // var_dump($query->errorInfo());
        // return false;
    }
    public function login($username, $password)
    {
        $password = hash("sha512", $password);
        $query = $this->con->prepare("SELECT * FROM users 
                                    WHERE username=:username 
                                    AND password=:password");
        $query->bindValue(":username", $username);
        $query->bindValue(":password", $password);
        $query->execute();
        if ($query->rowCount() == 1) {
            return true;
        }
        array_push($this->errorArray, Constants::$loginFailed);
        return false;
    }
    public function getError($error)
    {
        if (in_array($error, $this->errorArray)) {
            return "<span class='errorMessage'>$error</span>";
        }
    }
    private function validateUsername($un)
    {
        if (strlen($un) < 2 || strlen($un) > 25) {
            array_push($this->errorArray, Constants::$usernameCharacters);
            return;
        }

        $query = $this->con->prepare("SELECT * FROM users WHERE username = :un");
        $query->bindValue(":un", $un);

        $query->execute();
        if ($query->rowCount() != 0) {
            array_push($this->errorArray, Constants::$usernameTaken);
        }
    }
}
