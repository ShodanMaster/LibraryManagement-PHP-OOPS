<?php
session_start();
require_once("../models/Librarian.php");

class LibrarianController extends Librarian{
    
    private $userId;

    public function __construct(){

        if(isset($_SESSION["role"]) && !$_SESSION["role"] === "admin"){
            header("index.php");
        }
    }

    public function createLibrarian(){
        
    }

}