<?php

require_once("../../controllers/Transactions/BookTransactionController.php");
$bookTransaction = new BookTransactionController();

// print_r($_POST);

$act = $_REQUEST['action'] ?? '';

if ($act == 'fetchData') {
    $member = $_POST['member'] ?? '';
    $book = $_POST['book'] ?? '';


    $dataResponse = $bookTransaction->fetchData($member, $book);

    echo $dataResponse;

}