<?php

require_once("../models/User.php");

class LoginController extends User{

    public function authenticate($username, $password) {
        $authenticated = $this->authenticated($username, $password);
        // print_r( $authenticated);

        if ($authenticated['status'] === 200) {
            return json_encode([
                'status' => 200,
                'message' => 'Logged In',
                'url' => 'index.php'
            ]);
        } else {
            return json_encode($authenticated);
        }
    }
    
    public function register($username, $password, $confirmPassword) {
        $validation = $this->validate($username, $password, $confirmPassword);
        // print_r($validation);exit;
        if ($validation['status'] !== 200) {
            return json_encode($validation);
        }
    
        $registered = $this->registered($username, $password);
        return json_encode($registered);
    }
    
    public function validate($username, $password, $confirmPassword = null) {
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