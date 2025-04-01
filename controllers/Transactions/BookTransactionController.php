<?php
session_start();
require_once("../../models/transactions/BookTransaction.php");

class BookTransactionController extends BookTransaction{
    
    private $userId;

    public function __construct(){

        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    public function fetchData($member,$book){
        $dataJson = $this->dataFetch($member, $book);

        return json_encode($dataJson);
    }

    public function saveTransaction($post){
        
        $memberId = $post["memberId"];
        $booksIds = $post["bookIds"];
        
        $bookIdsArray = json_decode($booksIds, true);
        
        
        $transactionSave = $this->transactionSave($memberId,$bookIdsArray);

        return json_encode($transactionSave);
    }
}