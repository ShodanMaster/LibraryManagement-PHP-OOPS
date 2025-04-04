<?php 

include_once("config/session.php");

$act = isset($_GET["act"]) ? $_GET["act"] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    
    <script src="js/jquery/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
    
    <script src="js/sweetalert/sweetalert.min.js"></script>

</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">LM</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Masters
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?act=librarian">Librarian Master</a></li>
                            <li><a class="dropdown-item" href="index.php?act=member">Member Master</a></li>
                            <li><a class="dropdown-item" href="index.php?act=category">Category Master</a></li>
                            <li><a class="dropdown-item" href="index.php?act=author">Author Master</a></li>
                            <li><a class="dropdown-item" href="index.php?act=book">Book Master</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Transactions
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?act=issuebook">Issue Book</a></li>
                            <li><a class="dropdown-item" href="index.php?act=returnbook">Return Book</a></li>
                            <li><a class="dropdown-item" href="index.php?act=renewbook">Renew Book</a></li>
                            <li><a class="dropdown-item" href="index.php?act=finebook">Fine Book</a></li>
                        </ul>
                    </li>
                </ul>

                <form class="d-flex" action="routes/login.php?action=logout" method="POST">
                    <button class="btn btn-outline-danger" type="submit">Logout</button>
                </form>
            </div>

        </div>
    </nav>
    <div class="container mt-5">
    <?php 

        switch ($act) {
            //master
            case "":
                    echo "<h1>Library Management</h1>";
                    break;
            case "librarian": 
                include "views/masters/librarian.php";
                break;
            case "member": 
                include "views/masters/member.php";
                break;
            case "category": 
                include "views/masters/category.php";
                break;
            case "author": 
                include "views/masters/author.php";
                break;
            case "book": 
                include "views/masters/book.php";
                break;

            //transactions
            case "issuebook":
                include "views/transactions/issueBook.php";
                break;
            case "returnbook":
                include "views/transactions/returnBook.php";
                break;
            case "renewbook":
                include "views/transactions/renewBook.php";
                break;
            case "finebook":
                include "views/transactions/fineBook.php";
                break;
        }

    
    ?>

    </div>
    <script src="bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>