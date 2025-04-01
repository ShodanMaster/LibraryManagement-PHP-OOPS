<?php

require_once("../../controllers/Masters/MemberController.php");
$member = new MemberController();

$action = $_REQUEST['action'] ??'';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $response = $member->getMembers();

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
        $response = $member->createMember($_POST);
        echo $response;
    }
    
    if ($action == 'edit') {
        // print_r($_POST);exit;
        $response = $member->updateMember($_POST);
        echo $response;
    }
    
    // if($action == 'delete'){
    //     // print_r($_POST);exit;
    //     $response = $member->deleteMember($_POST);
    //     echo $response;
    // }
}