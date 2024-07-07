<?php
require_once 'config.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  
    header("Location: login.php");
    exit();
}


$totalRevenue = 0.0;
$sql = "SELECT SUM(total_amount) AS total_revenue FROM rentals";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalRevenue = $row['total_revenue'];
}


$transactions = [];
$sql = "SELECT rental_id, total_amount FROM rentals";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Financial Report</title>
    <link rel="stylesheet" href="reports.css">
</head>
<body class="hold-transition">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Total Revenue</h3>
                </div>
                <div class="card-body">
                    <h1 class="display-4">$<?php echo number_format($totalRevenue, 2); ?></h1>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mt-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Transactions</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Rental ID</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaction['rental_id']); ?></td>
                                    <td>$<?php echo number_format($transaction['total_amount'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
