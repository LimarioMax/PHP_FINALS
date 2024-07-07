<?php
session_start();
$_SESSION['loggedin'] = false;
header('Location: index.php'); // Redirect to login page
exit;
?>