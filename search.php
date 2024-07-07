<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection
$conn = new mysqli('localhost', 'root', '', 'video_rental_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Include the file where displayVideos() is defined
require_once 'functions.php';

// Process search term if provided
$searchTerm = isset($_GET['search']) ? $_GET['search'] : "";

// Display videos based on search term
displayVideos($conn, $searchTerm);

$conn->close(); // Close the database connection
?>
