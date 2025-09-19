<?php
session_start();
include 'assets/php/database.php';

$name = "";
$phone = "";
$nameError = "";
$phoneError = "";
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    if (empty($name)) {
        $nameError = "The Name is required";
        $error = true;
    }

    if (empty($phone)) {
        $phoneError = "The Phone Number is required";
        $error = true;
    }

    if (!preg_match("/^(\+60|0)(1[0-9]{1}|([2-9]{1}[0-9]{1}))?-?\d{7,8}$/", $phone)) {
        $phoneError = "Phone format is not valid (MY)";
        $error = true;
    }

    if (!$error) {
        if (setDatabaseUser($name, $phone)) {
            $_SESSION['userName'] = $name;
            $_SESSION['userPhone'] = $phone;
            header("location: profile.php");
            exit(); // Ensure no further code is executed after redirection
        } else {
            $phoneError = "Failed to update profile. Please try again.";
        }
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
        <h2>Edit Profile<i class="ri-user-fill"></i></h2>
    </div>

    <div class="container bigContainer">
        <div class="formContainer">
            <h2>My Profile</h2>
            <span>Please replace the existing information to update.</span>
            <form method="post" id="newPlantForm">
                <h5>Name</h5>
                <input type="text" name="name" id="name" placeholder="<?= htmlspecialchars($_SESSION['userName']) ?>" value="<?= htmlspecialchars($_SESSION['userName']) ?>">
                <span id="nameError" class="text-danger"><?= $nameError ?></span>

                <h5>Email</h5>
                <input type="text" name="email" id="email" placeholder="<?= htmlspecialchars($_SESSION['userEmail']) ?>" value="<?= htmlspecialchars($_SESSION['userEmail']) ?>" readonly>

                <h5>Phone Number</h5>
                <input type="text" name="phone" id="phone" placeholder="<?= htmlspecialchars($_SESSION['userPhone']) ?>" value="<?= htmlspecialchars($_SESSION['userPhone']) ?>">
                <span id="phoneError" class="text-danger"><?= $phoneError ?></span>
                
                <button id="addBtn" type="submit" class="btn btn-primary">Confirm</button>
                <a href="profile.php" class="btn mt-3">Cancel</a>
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
