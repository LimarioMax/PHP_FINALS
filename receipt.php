<?php
require_once 'functions.php';

if (isset($_GET['rental_id'])) {
    $rentalId = $_GET['rental_id'];
    $rental = getRentalById($rentalId);

    if ($rental) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        </head>
        <body>
            <div class="container">
                <h1>Receipt</h1>
                <div class="card">
                    <div class="card-body">
                        <h4><?= htmlspecialchars($rental['title']) ?></h4>
                        <p>Format: <?= htmlspecialchars($rental['format']) ?></p>
                        <p>Quantity: <?= htmlspecialchars($rental['quantity']) ?></p>
                        <p>Total Price: $<?= htmlspecialchars($rental['total_price']) ?></p>
                        <p>Due Date: <?= htmlspecialchars($rental['due_date']) ?></p>
                        <button class="btn btn-primary" onclick="window.location.href='index.php?page=view'">Confirm</button>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Rental not found.";
    }
} else {
    echo "Invalid rental ID.";
}
?>
