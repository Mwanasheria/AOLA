<?php
$servername = "localhost";
$username = "AOLA";
$password = "LOAN";
$dbname = "AOLA";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>