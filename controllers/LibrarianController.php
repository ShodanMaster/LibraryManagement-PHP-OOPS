<?php
session_start();
require_once("../models/Librarian.php");

class LibrarianController extends Librarian{
    
    private $userId;

    public function __construct(){

        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    public function createLibrarian($username, $password, $confirmPassword){
        $validation = $this->validate($username, $password, $confirmPassword);

        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }

        $createLibrarian = $this->librarianCreate($username, $password, $confirmPassword);
        return json_encode($createLibrarian);
    }

    private function validate($username, $password, $confirmPassword = null) {
        if (empty($username) || empty($password)) {
            return ["status" => 400, "message" => "Username and Password are required!"];
        }
    
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            return ["status" => 400, "message" => "Username must be 3-20 characters long and contain only letters, numbers, and underscores!"];
        }
    
        if (strlen($password) < 6) {
            return ["status" => 400, "message" => "Password must be at least 6 characters long!"];
        }
    
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return ["status" => 400, "message" => "Password must include at least one uppercase letter and one number!"];
        }
    
        if ($confirmPassword !== null && $password !== $confirmPassword) {
            return ["status" => 400, "message" => "Passwords do not match!"];
        }
    
        return ["status" => 200, "message" => "Validation passed!"];
    }

}