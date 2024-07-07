<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

$username = $_SESSION['username'];
$user = getUserByUsername($username);
$userId = $user['user_id'];

$rentedVideos = getRentedVideosByUser($userId);

// Update status to Expired for digital rentals if dueInDays < 0
foreach ($rentedVideos as &$video) {
    if ($video['format'] == 'digital' && $video['status'] == 'Valid') {
        $dueInDays = intval($video['due_in_days']);
        if ($dueInDays < 0) {
            // Update status to Expired
            $video['status'] = 'Expired';
        }
    }
}
unset($video); // Unset the reference

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTED VIDEOS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="rented_videos.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }

        h1 {
            margin-top: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .rented-videos {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .rented-video {
            position: relative;
            width: 200px;
            margin: 10px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .rented-video:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .rented-video img {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ddd;
        }

        .rented-video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.6);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .rented-video:hover .rented-video-overlay {
            opacity: 1;
        }

        .rented-video-button {
            color: #fff;
            background-color: #007bff;
            border: none;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin-top: 10px;
            cursor: pointer;
        }

        .rented-video p {
            text-align: center;
            margin: 10px 0;
            font-size: 14px;
        }

        .qr-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            text-align: center;
            padding-top: 60px;
        }

        .qr-modal-content {
            margin: 5% auto;
            padding: 20px;
            border: none;
            width: 60%;
            max-width: 600px;
            background-color: #fff;
            position: relative;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .qr-modal-content .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            color: #aaa;
            cursor: pointer;
        }

        .qr-modal-content img {
            max-width: 80%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .qr-modal-content p {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Rented Videos</h1>
        <?php if (!empty($rentedVideos)): ?>
            <div class="rented-videos">
                <?php foreach ($rentedVideos as $video): ?>
                    <?php if (($video['format'] == 'blu_ray' || $video['format'] == 'dvd') && $video['status'] == 'Rented'): ?>
                     <div class="rented-video">
                            <div class="rented-video-overlay">
                                <a class="rented-video-button" href="index.php?page=return_video&id=<?= $video['rental_id'] ?>">Return</a>
                            </div>
                            <img src="uploads/<?= htmlspecialchars($video['image']) ?>" alt="<?= htmlspecialchars($video['title']) ?>">
                            <p><?= htmlspecialchars($video['title']) ?></p>
                            <p><?= htmlspecialchars(ucwords(str_replace('_', ' ', $video['format']))) ?></p>
                            <p>Quantity: <?= htmlspecialchars($video['quantity']) ?></p>
                            <?php
                            $dueInDays = htmlspecialchars($video['due_in_days']);
                            if ($dueInDays < 0) {
                                echo "<p>Overdue by " . abs($dueInDays) . " day(s)</p>";
                            } else {
                                echo "<p>Due in " . $dueInDays . " day(s)</p>";
                            }
                            ?>
                        </div>
                    <?php elseif ($video['format'] == 'digital' && ($video['status'] == 'Valid' || $video['status'] == 'Expired')): ?>
                        <div class="rented-video">
                            <div class="rented-video-overlay">
                                <?php if ($video['status'] == 'Valid'): ?>
                                    <button class="rented-video-button" onclick="showQRCode('<?= htmlspecialchars($video['digital_link']) ?>')">View Link</button>
                                <?php elseif ($video['status'] == 'Expired'): ?>
                                    <p class="expired-message">Expired</p>
                                <?php endif; ?>
                            </div>
                            <img src="uploads/<?= htmlspecialchars($video['image']) ?>" alt="<?= htmlspecialchars($video['title']) ?>">
                            <p><?= htmlspecialchars($video['title']) ?></p>
                            <p>Digital</p>
                            <?php
                            $dueInDays = intval($video['due_in_days']);
                            if ($dueInDays < 0) {
                                echo "<p>Expired</p>";
                            } else {
                                echo "<p>Valid till " . $dueInDays . " day(s)</p>";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No videos rented.</p>
        <?php endif; ?>
    </div>


    <div id="qrModal" class="qr-modal">
        <div class="qr-modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="qrImage" src="img/LinkQR.png" alt="QR Code">
            <p id="digitalLink"></p>
        </div>
    </div>

    <script>
        function showQRCode(link) {
            document.getElementById('digitalLink').innerText = link;
            document.getElementById('qrModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('qrModal').style.display = "none";
        }

        window.onclick = function(event) {
            var modal = document.getElementById('qrModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
