<?php

require_once("../controllers/LoginController.php");
$authenticate = new LoginController();

$action = $_REQUEST['action'] ??'';

header('Content-Type: application/json');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['password_confirmation'] ?? '';
    
    if($action == 'login'){
        $response = $authenticate->authenticate($username, $password);
        echo $response;
    }
    
    if($action == 'signup'){
       $response = $authenticate->register($username, $password, $confirmPassword);
       echo $response;
    }
    
    if($action == 'logout'){
        session_unset();
        session_destroy();
        header("Location: ../athenticate.php");
        exit();
    }
}