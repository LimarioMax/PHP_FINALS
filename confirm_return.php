<?php
// Include your functions.php where update functions are defined
require_once 'functions.php';

// Check if rental ID is provided
if (isset($_GET['id'])) {
    $rentalId = htmlspecialchars($_GET['id']);

    // Fetch rental details by ID
    $rental = getRentalById($rentalId);

    if ($rental) {
        // Update rental status as returned in the database
        updateRentalStatus($rentalId, 'Returned');

        // Update video stock based on format
        $format = $rental['format'];
        $videoId = $rental['video_id'];
        $quantity = $rental['quantity']; // Fetch the quantity from the rental

        // Increment stock based on the returned format and quantity
        if ($format == 'blu_ray') {
            incrementStock($videoId, 'blu_ray_copies', $quantity);
        } elseif ($format == 'dvd') {
            incrementStock($videoId, 'dvd_copies', $quantity);
        } 

        header('Location: index.php?page=return_success');
        exit;
    } else {
        echo '<p>Rental not found.</p>';
    }
} else {
    echo '<p>No rental ID provided.</p>';
}

// Function to increment stock based on format
function incrementStock($videoId, $formatColumn, $quantity) {
    global $conn;
    $sql = "UPDATE videos SET $formatColumn = $formatColumn + ? WHERE video_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $quantity, $videoId);
    $stmt->execute();
    $stmt->close();
}
?>
