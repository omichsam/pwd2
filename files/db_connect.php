<?php
$host = "localhost"; // or 127.0.0.1
$user = "root";      // DB username
$password = "";          // DB password
$dbname = "pwd";      //  database name

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} 