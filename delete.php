<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php';

// Function to set session alerts
function setAlert($message, $type = 'success') {
    $_SESSION['alert'] = ['message' => $message, 'type' => $type];
}

// Check if a valid video ID is passed and deletion has not yet been confirmed
if (isset($_GET['id']) && !isset($_GET['confirm'])) {
    $videoId = htmlspecialchars($_GET['id']);
    $video = getVideoById($videoId); // Retrieve video details

    if ($video) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Delete Video</title>
            <!-- AdminLTE CSS -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
            <link rel="stylesheet" href="delete.css">
        </head>
        <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Add your page content here -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Delete Video</h3>
                        </div>
                        <div class="card-body">
                            <p>Are you sure you want to delete this video?</p>
                            <div class="card">
                                <div class="card-body">
                                    <div class="details">
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Title:</strong> <?= htmlspecialchars($video['title']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Production:</strong> <?= htmlspecialchars($video['production']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Release Year:</strong> <?= htmlspecialchars($video['release_year']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Genre:</strong> <?= htmlspecialchars($video['genre']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Trailer Link:</strong> <?= htmlspecialchars($video['trailer_link']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Duration:</strong> <?= htmlspecialchars($video['duration']) ?> minutes</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Plot:</strong> <?= htmlspecialchars($video['plot']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Blu-ray Copies:</strong> <?= htmlspecialchars($video['blu_ray_copies']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Blu-ray Price:</strong> <?= htmlspecialchars($video['blu_ray_price']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Blu-ray Late Fee:</strong> <?= htmlspecialchars($video['blu_ray_late_fee']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>DVD Copies:</strong> <?= htmlspecialchars($video['dvd_copies']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>DVD Price:</strong> <?= htmlspecialchars($video['dvd_price']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>DVD Late Fee:</strong> <?= htmlspecialchars($video['dvd_late_fee']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Digital Link:</strong> <?= htmlspecialchars($video['digital_link']) ?></p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="card-text"><strong>Digital Price:</strong> <?= htmlspecialchars($video['digital_price']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="delete.php?confirm=yes&id=<?= $videoId; ?>" class="btn btn-danger">Delete</a>
                                <a href="index.php?page=view" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- AdminLTE JS -->
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
        </body>
        </html>
        <?php
    } else {
        setAlert("Video not found.", "danger");
        header('Location: index.php?page=view');
        exit();
    }
} elseif (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) {
    // Confirm deletion
    if (deleteVideo($_GET['id'])) {
        setAlert('Video deleted successfully.', 'success');
    } else {
        setAlert('Failed to delete video. Video not found.', 'danger');
    }
    header('Location: index.php?page=view');
    exit();
} else {
    setAlert('No video ID specified.', 'danger');
    header('Location: index.php?page=view');
    exit();
}
?>