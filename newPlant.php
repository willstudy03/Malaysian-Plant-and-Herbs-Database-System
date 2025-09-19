<?php
session_start();
include 'assets/php/database.php';

$cName = "";
$sName = "";
$stock = 0;
$price = 0;
$cNameError = "";
$sNameError = "";
$stockError = "";
$priceError = "";
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cName = $_POST['cName'];
    $sName = $_POST['sName'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    if (empty($cName)) {
        $cNameError = "The Common Name is required";
        $error = true;
    }

    if (empty($sName)) {
        $sNameError = "The Scientific Name is required";
        $error = true;
    }

    if (filter_var($stock, FILTER_VALIDATE_INT) === false) {
        $stockError = "Please insert an integer as quantity";
        $error = true;
    } elseif ($stock < 0) {
        $stockError = "The stock can't be less than 0";
        $error = true;
    }

    if (filter_var($price, FILTER_VALIDATE_INT) === false) {
        $priceError = "Please insert an integer as the price";
        $error = true;
    } elseif ($price < 0) {
        $priceError = "The price can't be less than 0";
        $error = true;
    }

    if (!$error) {
        addPlant($cName, $sName, $stock, $price);
        header("location: home.php");
        exit(); // Ensure no further code is executed after redirection
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MPAH - Malaysian Medical Plants and Herbs Database</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/newPlant.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <script type="text/javascript" src="bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>
</head>
<body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand logoContainer" href="#top">
        <img class="navLogo" src="assets/images/MPAHdb.png" alt="MPAHdb Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
            <a class="nav-link" href="home.php">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="profile.php">My Profile</a>
            </li>
            <li class="nav-item d-flex align-items-center">
            <a href="assets/php/logout.php" type="button" id="LogOutButton" class="btn btn-danger ms-lg-3" >Log Out</a>
            </li>
        </ul>
        </div>
    </div>
    </nav>

    <div class="container my-5">
            <h2>Add New Plant <i class="ri-plant-fill"></i></h2>
    </div>


    <div class="container bigContainer">
        <div class="formContainer">
            <h2>New Plant</h2>
            <span>Please fill in the required information for add a new plant or herb.</span>
            <form method="post" id="newPlantForm">
                <h5>Plant Name</h5>
                <input type="text" name="cName" id="cName" placeholder="Common Name">
                <span id="cnameError" class="text-danger"><?=$cNameError?></span>
                <input type="text" name="sName" id="sName" placeholder="Scientific Name">
                <span id="snameError" class="text-danger"><?$sNameError?></span>
                <h5>Stock</h5>
                <input type="text" name="stock" id="stock" placeholder="0">
                <span id="phoneError" class="text-danger"><?=$stockError?></span>
                <h5>Price(RM)</h5>
                <input type="text" name="price" id="price" placeholder="0">
                <span id="passwordError" class="text-danger"><?=$priceError?></span>
                <button id="addBtn" type="submit">Add</button>
                <a href="home.php" class="btn mt-3">Cancel</a>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark mt-auto">
        <div class="footer-top">
            <div class="container">
                <div class="row gy-5">
                    <div class="col-lg-3 col-sm-6">
                        <div class="line"></div>
                        <p>TWT2231-WEB TECH AND APPLIC PROJECT. MPAHdb</p>
                        <div class="social-icons">
                            <a href="#"><i class="ri-twitter-fill"></i></a>
                            <a href="#"><i class="ri-instagram-fill"></i></a>
                            <a href="#"><i class="ri-github-fill"></i></a>
                            <a href="#"><i class="ri-dribbble-fill"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <h5 class="mb-0 text-white">SUBJECT</h5>
                        <div class="line"></div>
                        <ul>
                            <li><a href="#">TWT2231-WEB TECH AND APPLIC</a></li>

                        </ul>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <h5 class="mb-0 text-white">MEMBER</h5>
                        <div class="line"></div>
                        <ul>
                            <li><a href="#">William Theo Wei Loon</a></li>
                            <li><a href="#">Desmond Pang Kai Cheng</a></li>
                            <li><a href="#">Khoo Kian Hong</a></li>
                            <li><a href="#">Chong Ding Zhe</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <h5 class="mb-0 text-white">CONTACT</h5>
                        <div class="line"></div>
                        <ul>
                            <li><a href="mailto:1211103037@student.mmu.edu.my">1211103037@student.mmu.edu.my</a></li>
                            <li><a href="mailto:1211103038@student.mmu.edu.my">1211103038@student.mmu.edu.my</a></li>
                            <li><a href="mailto:1211102657@student.mmu.edu.my">1211102657@student.mmu.edu.my</a></li>
                            <li><a href="mailto:1211102360@student.mmu.edu.my">1211102360@student.mmu.edu.my</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row g-4 justify-content-between">
                    <div class="col-auto">
                        <p class="mb-0">© Copyright MPAHdb All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>