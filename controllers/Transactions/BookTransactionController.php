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
}