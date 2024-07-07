<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $user = getUserByUsername($username);
    $userId = $user['user_id'];
    $videoId = $_POST['video_id'];
    $format = $_POST['format'];
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
    $rentalDate = date('Y-m-d');
    $returnDate = date('Y-m-d', strtotime($rentalDate . ' + 7 days'));

    $video = getVideoById($videoId);

    if ($video) {
        // Initialize variables for stock and price
        $availableStock = 0;
        $price = 0;
        $status = '';

        // Determine stock, price, and status based on format
        switch ($format) {
            case 'blu_ray':
                $availableStock = $video['blu_ray_copies'];
                $price = $video['blu_ray_price'];
                $status = 'Rented';
                break;
            case 'dvd':
                $availableStock = $video['dvd_copies'];
                $price = $video['dvd_price'];
                $status = 'Rented';
                break;
            case 'digital':
                // For digital format, use the base price from the videos table
                $availableStock = PHP_INT_MAX; // Set to a very large number or handle differently based on business logic
                $price = $video['digital_price'];
                $status = 'Valid';
                break;
            default:
                $_SESSION['alert'] = ['message' => 'Invalid format.', 'type' => 'danger'];
                header('Location: index.php?page=view');
                exit;
        }

        // Check if enough stock is available for physical formats
        if ($format !== 'digital') {
            if ($availableStock >= $quantity) {
                // Update stock
                $newStock = $availableStock - $quantity;
                $stmt = $conn->prepare("UPDATE videos SET {$format}_copies = ? WHERE video_id = ?");
                $stmt->bind_param("ii", $newStock, $videoId);
                $stmt->execute();
            } else {
                $_SESSION['alert'] = ['message' => 'Not enough stock available.', 'type' => 'danger'];
                header('Location: index.php?page=rent&id=' . $videoId);
                exit;
            }
        }

        // Calculate total price based on quantity and format
        if ($format === 'digital') {
            // For digital format, use the base price directly
            $totalPrice = $price;
        } else {
            // For physical formats, calculate total price
            $totalPrice = $price * $quantity;
        }

        // Insert rental record into database with status
        $stmt = $conn->prepare("INSERT INTO rentals (user_id, video_id, format, quantity, rental_date, return_date, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssss", $userId, $videoId, $format, $quantity, $rentalDate, $returnDate, $totalPrice, $status);
        $stmt->execute();

        // Set session alert and redirect to view page
        $_SESSION['alert'] = ['message' => 'Rental successful.', 'type' => 'success'];
        header('Location: index.php?page=view');
        exit;
    } else {
        $_SESSION['alert'] = ['message' => 'Video not found.', 'type' => 'danger'];
        header('Location: index.php?page=view');
        exit;
    }
} else {
    $_SESSION['alert'] = ['message' => 'Invalid request.', 'type' => 'danger'];
    header('Location: index.php?page=view');
    exit;
}
?>
