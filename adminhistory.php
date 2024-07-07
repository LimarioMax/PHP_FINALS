<?php

require_once 'config.php';
require_once 'functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated
if (isset($_SESSION['username'])) {
    // Define default sorting column and order
    $sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'rental_id';
    $sortOrder = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

    // SQL query to fetch data from rentals, videos, and users tables for all users with sorting
    $sql = "SELECT r.rental_id, v.title AS video_title, DATE_FORMAT(r.rental_date, '%Y-%m-%d') AS rental_date, DATE_FORMAT(r.return_date, '%Y-%m-%d') AS return_date, r.format, r.quantity, r.total_amount, r.status, u.username
            FROM rentals r
            INNER JOIN videos v ON r.video_id = v.video_id
            INNER JOIN users u ON r.user_id = u.user_id
            ORDER BY $sortColumn $sortOrder";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table id="rentalsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th data-sort="rental_id"><a href="#">Rental ID</a></th>
                        <th data-sort="username"><a href="#">Username</a></th>
                        <th data-sort="video_title"><a href="#">Video Title</a></th>
                        <th data-sort="rental_date"><a href="#">Rental Date</a></th>
                        <th data-sort="return_date"><a href="#">Due Date</a></th>
                        <th data-sort="format"><a href="#">Format</a></th>
                        <th data-sort="quantity"><a href="#">Quantity</a></th>
                        <th data-sort="total_amount"><a href="#">Total Amount</a></th>
                        <th data-sort="status"><a href="#">Status</a></th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['rental_id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['username']) . '</td>';
            echo '<td>' . htmlspecialchars($row['video_title']) . '</td>';
            echo '<td>' . htmlspecialchars($row['rental_date']) . '</td>';
            echo '<td>' . htmlspecialchars($row['return_date']) . '</td>';
            echo '<td>' . htmlspecialchars(ucwords(str_replace('_', ' ', $row['format']))) . '</td>';
            echo '<td>' . htmlspecialchars($row['quantity']) . '</td>';
            echo '<td>' . htmlspecialchars($row['total_amount']) . '</td>';

            // Adjusted switch statement for status color coding
            $status = strtoupper($row['status']);
            switch ($status) {
                case 'RENTED':
                    $statusColor = 'red';
                    break;
                case 'RETURNED':
                    $statusColor = 'green';
                    break;
                case 'VALID':
                    $statusColor = 'green';
                    break;
                case 'EXPIRED':
                    $statusColor = 'red';
                    break;
                default:
                    $statusColor = 'red';
                    break;
            }

            echo '<td style="color: ' . $statusColor . '; font-weight: bold;">' . htmlspecialchars($status) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        // Close the statement
        $stmt->close();
    } else {
        echo '<p>No records found.</p>';
    }
} else {
    echo '<p>User not authenticated.</p>';
}



?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="adminhistory.css">
<script>
    $(document).ready(function() {
        $('#rentalsTable th').click(function() {
            var sortColumn = $(this).data('sort');
            var currentOrder = $(this).hasClass('asc') ? 'desc' : 'asc';

            $('#rentalsTable th').removeClass('asc desc');
            $(this).addClass(currentOrder);

            $.ajax({
                url: window.location.href,
                type: 'GET',
                data: {
                    sort: sortColumn,
                    order: currentOrder
                },
                success: function(response) {
                    var tableBody = $(response).find('#rentalsTable tbody');
                    $('#rentalsTable tbody').html(tableBody.html());
                }
            });
        });
    });
</script>
