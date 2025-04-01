<?php

require_once("../../controllers/Transactions/BookTransactionController.php");
$bookTransaction = new BookTransactionController();

$action = $_REQUEST['action'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($action == 'bookTransaction') {
        
       $response =  $bookTransaction->saveTransaction($_POST);
       echo $response;
    }
    
    
    if ($action == 'fetchData') {
        $member = $_POST['member'] ?? '';
        $book = $_POST['book'] ?? '';
    
    
        $dataResponse = $bookTransaction->fetchData($member, $book);
    
        echo $dataResponse;
    
    }
}