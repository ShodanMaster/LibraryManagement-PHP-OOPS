<?php
// session_start();
require_once("../../config/Dbconfig.php");

class BookTransaction extends Dbconfig{

    public function __construct(){
        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    protected function bookTransaction($member, $book){
        
    }
}