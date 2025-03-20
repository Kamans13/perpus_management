<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "db_perpus"; # sesuai nama di database

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


?>
