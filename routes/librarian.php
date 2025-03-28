<?php

require_once("../controllers/librarianController.php");
$librarian = new librarianController();

$action = $_REQUEST['action'] ??'';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response = $librarian->getLibrarians();

    if (!json_decode($response, true)) {
        echo json_encode(["status" => 500, "message" => "Invalid JSON response", "debug" => $response]);
        exit;
    }

    echo $response;
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if($action == 'add'){
        // print_r($_POST);exit;
        $response = $librarian->createLibrarian($_POST);
        echo $response;
    }
    
    if ($action == 'edit') {
        // print_r($_POST);exit;
        $response = $librarian->updateLibrarian($_POST);
        echo $response;
    }
}