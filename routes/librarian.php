<?php

require_once("../controllers/librarianController.php");
$librarian = new librarianController();

$action = $_REQUEST['action'] ??'';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['password_confirmation'] ?? '';
    
    if($action == 'add'){
       $response = $librarian->createLibrarian($username, $password, $confirmPassword);
       echo $response;
    }
    
    if($action == 'logout'){
        session_unset();
        session_destroy();
        header("Location: ../athenticate.php");
        exit();
    }
}