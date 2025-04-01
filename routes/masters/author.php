<?php

require_once("../../controllers/Masters/AuthorController.php");
$author = new AuthorController();

$action = $_REQUEST['action'] ??'';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response = $author->getAuthors();

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
        $response = $author->createAuthor($_POST);
        echo $response;
    }
    
    if ($action == 'edit') {
        // print_r($_POST);exit;
        $response = $author->updateAuthor($_POST);
        echo $response;
    }
    
    if($action == 'delete'){
        // print_r($_POST);exit;
        $response = $author->deleteAuthor($_POST);
        echo $response;
    }
}