<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

// Check if rental ID is provided
if (isset($_GET['id'])) {
    $rentalId = htmlspecialchars($_GET['id']);
    $rental = getRentalById($rentalId);

    if ($rental) {
        $videoId = $rental['video_id'];
        $video = getVideoById($videoId);

        // Calculate due date and check if overdue
        $dueDate = strtotime($rental['return_date']);
        $today = strtotime(date('Y-m-d'));
        $daysLate = max(0, floor(($today - $dueDate) / (60 * 60 * 24)));
        $isOverdue = ($daysLate > 0);

        // Determine action button text and link based on overdue status
        if ($isOverdue) {
            $actionText = "Pay Late Fee";
            $actionLink = "index.php?page=pay_late_fee&id={$rentalId}";
        } else {
            $actionText = "Confirm Return";
            $actionLink = "confirm_return.php?id={$rentalId}";
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Video</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="return_video.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .video-details {
            display: flex;
            align-items: center;
        }
        .video-details img {
            max-width: 150px;
            margin-right: 20px;
        }
        .status {
            font-weight: bold;
        }
        .status.overdue {
            color: red;
        }
        .status.not-overdue {
            color: green;
        }
        .btn-primary {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>RETURN VIDEO</h1>
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="uploads/<?= htmlspecialchars($video['image']) ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($video['title']) ?>">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($video['title']) ?></h5>
                        <p class="card-text">Format: <?= htmlspecialchars(ucwords(str_replace('_', ' ', $rental['format']))) ?></p>
                        <p class="card-text">Quantity: <?= htmlspecialchars($rental['quantity']) ?></p>
                        <p class="card-text">Due Date: <?= date('Y-m-d', $dueDate) ?></p>
                        <p class="card-text">Days Late: <?= $daysLate ?></p>
                        <p class="card-text status <?= $isOverdue ? 'overdue' : 'not-overdue' ?>"><?= $isOverdue ? 'Overdue' : 'Not Overdue' ?></p>
                        <a href="<?= $actionLink ?>" class="btn btn-primary"><?= $actionText ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
    } else {
        echo '<p>Rental not found.</p>';
    }
} else {
    echo '<p>No rental ID provided.</p>';
}
?>
