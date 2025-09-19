<?php
session_start();
include 'assets/php/database.php';
$data = [];
$plantAmount = 0;
$stockAmount = 0;
$priceAmount = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searchTerm = $_POST['search'] ?? '';
    $data = searchPlant($searchTerm);
} else {
    $data = getAllPlant();
}

// Calculate totals if data is available
if ($data) {
    foreach ($data as $row) {
        $plantAmount += 1;
        $stockAmount += $row['stock'];
        $priceAmount += ($row['stock'] * $row['price']);
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
    <link rel="stylesheet" href="assets/css/home.css">
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
            <h2>Welcome back ! <?= $_SESSION["userName"]?> <i class="ri-emotion-line"></i></h2>
            <h5 class='mt-5'>Analytics <i class="ri-line-chart-fill"></i></h5>
            <p>The analytics card will change according to your search.</p>

                <div class="container mt-3">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Plant and Herbs</h5>
                                    <p class="card-text text-center"><?= $plantAmount ?></p>
                                </div>
                            </div>
                         </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Stock</h5>
                                    <p class="card-text text-center"><?= $stockAmount ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                 <div class="card-body">
                                    <h5 class="card-title">Total Price(RM)</h5>
                                    <p class="card-text text-center"><?= $priceAmount ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

    </div>

    <div class="container my-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-4">
                <h5>My Database <i class="ri-database-2-fill"></i></h5>
            </div>
            <div class="col-12 col-md-4 mt-3 mt-md-0">
                <form class="d-flex" role="search" method="post">
                    <input name="search" class="form-control me-2 searchbar" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success custom" type="submit"><i class="ri-search-fill"></i></button>
                </form>
            </div>
            <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end">
                    <a class="btn btn-custom me-2" href="newPlant.php" role="button">New Plant</a>
                    <a class="btn btn-custom del" href="assets/php/clear.php" role="button">Clear</a>
                </div>
            </div>
        </div>
    </div>


    

    <div class="container container-table mb-5">
        <table class="table">
            <thead>
                <tr>
                    <th>Plant No.</th>
                    <th>Common Name</th>
                    <th>Scientific Name</th>
                    <th>Stock</th>
                    <th>price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if ($data) {
                        $counter = 0;
                        foreach ($data as $row) {
                            $counter += 1;
                            echo "<tr>
                                    <td>$counter</td>
                                    <td>{$row['commonName']}</td>
                                    <td>{$row['scientificName']}</td>
                                    <td>{$row['stock']}</td>
                                    <td>{$row['price']}</td>
                                    <td>
                                        <a class='btn btn-primary btn-sm' href='editPlant.php?id=$row[plantID]'>Edit</a>
                                        <a class='btn btn-danger btn-sm' href='assets/php/delete.php?id=$row[plantID]'>Delete</a>
                                </tr>";
                        }
                    } else {
                        echo "<p>No data found. Add some new record to your database</p>";
                    }
                ?>
            </tbody>
        </table>
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
