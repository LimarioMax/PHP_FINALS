<!DOCTYPE html>
<html>
<head>
    <title>View Videos</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="view.css">

</head>
<body>
    <h1>VIEW VIDEOS</h1>
    <form onsubmit="searchVideos(); return false;" class="search-container">
        <input type="text" id="search" name="search" placeholder="Enter Title, Genre, Production, Year" autocomplete="off" class="form-control search-input">
        <button type="submit" class="btn btn-primary btn-primary-submit"><i class="fas fa-arrow-right"></i></button>
    </form>
    <hr>
    <div id="search-results">
        <?php
        $conn = new mysqli('localhost', 'root', '', 'video_rental_system');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        require_once 'functions.php';
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : "";
        displayVideos($conn, $searchTerm);
        $conn->close();
        ?>
    </div>
    <script>
        function searchVideos() {
            var searchTerm = document.getElementById('search').value.trim();
            $.ajax({
                url: 'search.php',
                method: 'GET',
                data: { search: searchTerm },
                success: function(response) {
                    document.getElementById('search-results').innerHTML = response;
                },
                error: function() {
                    console.error('Request failed');
                }
            });
        }
    </script>
</body>
</html>

