<?php
session_start();
include 'database.php';
if(isset($_GET['id'])){
    $id = $_GET['id'];
    removePlant($id);
}
header("location: ../../home.php");
?>
