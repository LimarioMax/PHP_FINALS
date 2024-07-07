<?php

include_once 'functions.php'; // Include your functions file where updateVideo() is defined

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Include your database connection
$conn = new mysqli('localhost', 'root', '', 'video_rental_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get video ID from URL
if (isset($_GET['id'])) {
    $videoId = $_GET['id'];
} else {
    echo "Video ID not provided";
    exit;
}

// Fetch video details
$sql = "SELECT * FROM videos WHERE video_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $videoId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $video = $result->fetch_assoc();
} else {
    echo "No video found";
    exit;
}

$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $production = $_POST['production'];
    $release_year = $_POST['release_year'];
    $genre = $_POST['genre'];
    $trailer_link = $_POST['trailer_link'];
    $duration = $_POST['duration'];
    $plot = $_POST['plot'];
    $image = $video['image'];
    $blu_ray_copies = $_POST['blu_ray_copies'];
    $blu_ray_price = $_POST['blu_ray_price'];
    $blu_ray_late_fee = $_POST['blu_ray_late_fee'];
    $dvd_copies = $_POST['dvd_copies'];
    $dvd_price = $_POST['dvd_price'];
    $dvd_late_fee = $_POST['dvd_late_fee'];
    $digital_link = $_POST['digital_link'];
    $digital_price = $_POST['digital_price'];

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $target = 'uploads/' . basename($image);

        // Attempt to move the uploaded file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            echo "Failed to upload image";
            exit;
        }
    }

    // Call the updateVideo function
    updateVideo($videoId, $title, $production, $release_year, $genre, $trailer_link, $duration, $plot, $image, $blu_ray_copies, $blu_ray_price, $blu_ray_late_fee, $dvd_copies, $dvd_price, $dvd_late_fee, $digital_link, $digital_price);
    
    // Redirect to view the updated video
    header('Location: index.php?page=view_single&id=' . $videoId);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Video</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="edit.css">
</head>
<body class="hold-transition sidebar-mini">

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Add your page content here -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Video</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to edit the video -->
                        <form id="editVideoForm" action="edit.php?id=<?php echo htmlspecialchars($video['video_id']); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($video['title']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Production</label>
                                <input type="text" class="form-control" name="production" value="<?php echo htmlspecialchars($video['production']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Release Year</label>
                                <input type="number" class="form-control" name="release_year" value="<?php echo htmlspecialchars($video['release_year']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Genre</label>
                                <input type="text" class="form-control" name="genre" value="<?php echo htmlspecialchars($video['genre']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Trailer Link (YouTube link)</label>
                                <input type="url" class="form-control" name="trailer_link" value="<?php echo htmlspecialchars($video['trailer_link']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Duration</label>
                                <input type="text" class="form-control" name="duration" value="<?php echo htmlspecialchars($video['duration']); ?>">
                            </div>
                            <div class="form-group">
                                <label>Plot or Synopsis</label>
                                <textarea class="form-control" name="plot"><?php echo htmlspecialchars($video['plot']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" class="form-control-file" name="image" accept="image/*">
                                <p>Current Image: <img src="uploads/<?php echo htmlspecialchars($video['image']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" style="max-width: 100px; max-height: 100px;"></p>
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
                                            <input type="number" class="form-control" name="blu_ray_copies" value="<?php echo htmlspecialchars($video['blu_ray_copies']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="number" step="0.01" class="form-control" name="blu_ray_price" value="<?php echo htmlspecialchars($video['blu_ray_price']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Late Fee Price</label>
                                            <input type="number" step="0.01" class="form-control" name="blu_ray_late_fee" value="<?php echo htmlspecialchars($video['blu_ray_late_fee']); ?>">
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
                                            <input type="number" class="form-control" name="dvd_copies" value="<?php echo htmlspecialchars($video['dvd_copies']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="number" step="0.01" class="form-control" name="dvd_price" value="<?php echo htmlspecialchars($video['dvd_price']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Late Fee Price</label>
                                            <input type="number" step="0.01" class="form-control" name="dvd_late_fee" value="<?php echo htmlspecialchars($video['dvd_late_fee']); ?>">
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
                                            <input type="url" class="form-control" name="digital_link" value="<?php echo htmlspecialchars($video['digital_link']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Price</label>
                                            <input type="number" step="0.01" class="form-control" name="digital_price" value="<?php echo htmlspecialchars($video['digital_price']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Video</button>
                            <a href="index.php?page=view_single&id=<?php echo htmlspecialchars($video['video_id']); ?>" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

    <script>
        // JavaScript for form submission confirmation
        document.getElementById('editVideoForm').addEventListener('submit', function(event) {
            if (!confirm('Are you sure about the details?')) {
                event.preventDefault();
            }
        });
    </script>
</div>
<!-- ./wrapper -->
</body>
</html>
