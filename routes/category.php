<?php

require_once("../controllers/Masters/CategoryController.php");
$category = new CategoryController();

$action = $_REQUEST['action'] ??'';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response = $category->getCategories();

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
        $response = $category->createCategory($_POST);
        echo $response;
    }
    
    if ($action == 'edit') {
        // print_r($_POST);exit;
        $response = $category->updateCategory($_POST);
        echo $response;
    }
    
    if($action == 'delete'){
        // print_r($_POST);exit;
        $response = $category->deleteCategory($_POST);
        echo $response;
    }
}