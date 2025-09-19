<?php
session_start();
include 'database.php';
removeAll();
header("location: ../../home.php");
?>