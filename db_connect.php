<?php
$host = 'localhost';
$username = 'appuser';
$password = 'MGroad001';
$database = 'quotation_system';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
