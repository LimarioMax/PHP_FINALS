<?php
include_once 'functions.php'; 

// Check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'video_rental_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $production = $_POST['production'];
    $release_year = $_POST['release_year'];
    $genre = $_POST['genre'];
    $trailer_link = $_POST['trailer_link'];
    $duration = $_POST['duration'];
    $plot = $_POST['plot'];
    $image = $_FILES['image']['name'];
    $target = 'uploads/' . basename($image);

    $blu_ray_copies = $_POST['blu_ray_copies'];
    $blu_ray_price = $_POST['blu_ray_price'];
    $blu_ray_late_fee = $_POST['blu_ray_late_fee'];

    $dvd_copies = $_POST['dvd_copies'];
    $dvd_price = $_POST['dvd_price'];
    $dvd_late_fee = $_POST['dvd_late_fee'];

    $digital_link = $_POST['digital_link'];
    $digital_price = $_POST['digital_price'];

    // Check if a video with the same title already exists
    $sql = "SELECT * FROM videos WHERE title = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();

        // Check if uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Call the addVideo function with the new parameters
            addVideo($title, $production, $release_year, $genre, $trailer_link, $duration, $plot, $image, $blu_ray_copies, $blu_ray_price, $blu_ray_late_fee, $dvd_copies, $dvd_price, $dvd_late_fee, $digital_link, $digital_price);
            echo "<script>alert('Video added successfully');</script>";
            exit; // Always exit after a header redirect
        } else {
            echo "Failed to upload image";
            error_log("Failed to move uploaded file: " . $_FILES['image']['error']);
        }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Video</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="add.css">
    
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Main Sidebar Container -->
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Add your page content here -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">VIDEO INFORMATION</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to add a new video -->
                        <form id="addVideoForm" action="index.php?page=add" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="form-group">
                                <label>Production</label>
                                <input type="text" class="form-control" name="production" required>
                            </div>
                            <div class="form-group">
                                <label>Release Year</label>
                                <input type="number" class="form-control" name="release_year" required>
                            </div>
                            <div class="form-group">
                                <label>Genre</label>
                                <input type="text" class="form-control" name="genre" required>
                            </div>
                            <div class="form-group">
                                <label>Trailer Link (YouTube link)</label>
                                <input type="url" class="form-control" name="trailer_link">
                            </div>
                            <div class="form-group">
                                <label>Duration</label>
                                <input type="text" class="form-control" name="duration">
                            </div>
                            <div class="form-group">
                                <label>Plot or Synopsis</label>
                                <textarea class="form-control" name="plot"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" class="form-control-file" name="image" accept="image/*" required>
                            </div>

                            <!-- Stock Information -->
                            <div class="form-group">
                                <h4>Stock Information</h4>

                                <!-- Blu-ray -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Blu-ray</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Copies</label>
                                            <input type="number" class="form-control" name="blu_ray_copies">
                                        </div>
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="number" step="0.01" class="form-control" name="blu_ray_price">
                                        </div>
                                        <div class="form-group">
                                            <label>Late Fee Price</label>
                                            <input type="number" step="0.01" class="form-control" name="blu_ray_late_fee">
                                        </div>
                                    </div>
                                </div>

                                <!-- DVD -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">DVD</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Copies</label>
                                            <input type="number" class="form-control" name="dvd_copies">
                                        </div>
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="number" step="0.01" class="form-control" name="dvd_price">
                                        </div>
                                        <div class="form-group">
                                            <label>Late Fee Price</label>
                                            <input type="number" step="0.01" class="form-control" name="dvd_late_fee">
                                        </div>
                                    </div>
                                </div>

                                <!-- Digital -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Digital</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Video Link (Dummy)</label>
                                            <input type="url" class="form-control" name="digital_link">
                                        </div>
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="number" step="0.01" class="form-control" name="digital_price">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">Add Video</button>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
    <script>
        // JavaScript for form submission confirmation
        document.getElementById('addVideoForm').addEventListener('submit', function(event) {
            if (!confirm('Are you sure about the details?')) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
