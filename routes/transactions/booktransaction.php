<?php

require_once("../../controllers/Transactions/BookTransactionController.php");
$bookTransaction = new BookTransactionController();

$action = $_REQUEST['action'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($action == 'issueBook') {
        
       $response =  $bookTransaction->issueBook($_POST);
       echo $response;
    }
    
    
    if ($action == 'fetchData') {
        $member = $_POST['member'] ?? '';
        $book = $_POST['book'] ?? '';
    
    
        $dataResponse = $bookTransaction->fetchData($member, $book);
    
        echo $dataResponse;
    
    }

    if ($action == 'returnBook') {
        
        $response = $bookTransaction->fetchBooks($_POST);

        echo $response;
    }

    if($action == 'returnScan'){

        $response = $bookTransaction->returnScan($_POST);

        echo $response;
    }

    if($action == 'renewScan'){

        $response = $bookTransaction->renewScan($_POST);

        echo $response;
    }

    if($action == 'fineBooks'){
        
        $response = $bookTransaction->fineBooks($_POST);

        echo $response;
    }

    if($action == 'fineScan'){
        
        $response = $bookTransaction->fineScan($_POST);

        echo $response;
    }
}