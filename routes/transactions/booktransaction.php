<?php

require_once("../../controllers/Transactions/BookTransactionController.php");
$bookTransaction = new BookTransactionController();

$action = $_REQUEST['action'] ?? '';

if ($action == 'bookTransaction') {
    
   $response =  $bookTransaction->saveTransaction($_POST);
   echo $response;
}