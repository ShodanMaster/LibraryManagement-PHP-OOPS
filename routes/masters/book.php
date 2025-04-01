<?php

require_once("../../controllers/Masters/BookController.php");
$book = new BookController();

$action = $_REQUEST['action'] ??'';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response = $book->getbooks();

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
        $response = $book->createBook($_POST);
        echo $response;
    }
    
    if ($action == 'edit') {
        // print_r($_POST);exit;
        $response = $book->updateBook($_POST);
        echo $response;
    }
    
    if($action == 'delete'){
        // print_r($_POST);exit;
        $response = $book->deleteBook($_POST);
        echo $response;
    }
}