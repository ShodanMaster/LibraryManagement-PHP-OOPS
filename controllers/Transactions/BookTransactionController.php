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

    public function issueBook($post){
        
        $memberId = $post["memberId"];
        $booksIds = $post["bookIds"];
        
        $bookIdsArray = json_decode($booksIds, true);
        
        
        $transactionSave = $this->bookIssue($memberId,$bookIdsArray);

        return json_encode($transactionSave);
    }

    public function fetchBooks($post){
        // print_r($post);
        $memberSerailNo = $post["memberSerialNo"];
        // echo $memberSerailNo;exit;
        $booksFetch = $this->booksFetch($memberSerailNo);

        return json_encode($booksFetch);
    }

    public function returnScan($post){
        $memberSerialNo = $post["memberSerialNo"];
        $bookSerialNo = $post["bookSerialNo"];

        $scanRetrun = $this->scanReturn($memberSerialNo, $bookSerialNo);

        return json_encode($scanRetrun);
    }

    public function renewScan($post){
        $memberSerialNo = $post["memberSerialNo"];
        $bookSerialNo = $post["bookSerialNo"];

        $scanRetrun = $this->scanRenew($memberSerialNo, $bookSerialNo);

        return json_encode($scanRetrun);
    }

}