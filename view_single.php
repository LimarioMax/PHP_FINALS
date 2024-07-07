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
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="view_single.css">
</head>
<body>
    <div class="detail_page-infor">
        <div class="dp-i-content">
            <div class="dp-i-c-poster">
                <div class="film-poster mb-2">
                    <img src="uploads/<?php echo htmlspecialchars($video['image']); ?>" title="<?php echo htmlspecialchars($video['title']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" class="film-poster-img">
                </div>
            </div>
            <div class="dp-i-c-right">
                <h2 class="heading-name"><a href="view.php?id=<?php echo htmlspecialchars($video['video_id']); ?>"><?php echo htmlspecialchars($video['title']); ?></a></h2>
                <div class="dp-i-stats">
                    <span class="item mr-1"><button id="btn-trailer" data-toggle="modal" data-target="#modaltrailer" title="Trailer" class="btn btn-sm btn-trailer"><i class="fas fa-video mr-2"></i>Trailer</button></span>
                    <span class="item mr-1"><button class="btn btn-sm btn-quality"><strong>HD</strong></button></span>
                </div>
                <div class="description">
                    <?php echo htmlspecialchars($video['plot']); ?>
                </div>
                <div class="elements">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row-line"><span class="type"><strong>Released: </strong></span> <?php echo htmlspecialchars($video['release_year']); ?></div>
                            <div class="row-line"><span class="type"><strong>Genre: </strong></span> <?php echo htmlspecialchars($video['genre']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="row-line"><span class="type"><strong>Duration: </strong></span> <?php echo htmlspecialchars($video['duration']); ?> MIN</div>
                            <div class="row-line"><span class="type"><strong>Production: </strong></span> <?php echo htmlspecialchars($video['production']); ?> </div>
                        </div>
                    </div>

                <div class="dp-i-c-stick">
                    <?php if (isset($_SESSION['role'])): ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <div class="btn-actions">
                                <button class="btn btn-primary" onclick="window.location.href='index.php?page=edit&id=<?php echo htmlspecialchars($video['video_id']); ?>'">Edit</button>
                                <button class="btn btn-primary" onclick="window.location.href='index.php?page=delete&id=<?php echo htmlspecialchars($video['video_id']); ?>'">Delete</button>
                            </div>
                        <?php elseif ($_SESSION['role'] == 'user'): ?>
                            <div class="btn-actions">
                                <button class="btn btn-primary" onclick="window.location.href='index.php?page=rent&id=<?php echo htmlspecialchars($video['video_id']); ?>'">Rent</button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="btn-back">
                        <button class="btn btn-secondary" onclick="window.location.href='index.php?page=view'">Back</button>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trailer Modal -->
    <div class="modal fade" id="modaltrailer" tabindex="-1" role="dialog" aria-labelledby="modaltrailerLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modaltrailerLabel"><?php echo htmlspecialchars($video['title']); ?> - Trailer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Embedded YouTube Video Player -->
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="<?php echo htmlspecialchars($video['trailer_link']); ?>" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
