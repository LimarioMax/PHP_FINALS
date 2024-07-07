<?php
session_start();

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "video_rental_system";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT COUNT(*) FROM users");
if ($result && $result->fetch_row()[0] == 0) {
    $default_name = "Master Admin";
    $default_address = "Admin Address";
    $default_phone_number = "123456789";
    $default_email = "admin@example.com";
    $default_username = "admin";
    $default_password = "PUIHAHAadmin";

    $stmt = $conn->prepare("INSERT INTO users (name, address, phone, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $default_name, $default_address, $default_phone_number, $default_email, $default_username, $default_password);
    $stmt->execute();
    $stmt->close();
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($db_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if ($password == $db_password) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;

            if ($username == 'admin') {
                $_SESSION['role'] = 'admin';
            } else {
                $_SESSION['role'] = 'user';
            }

            header('Location: index.php');
            exit;
        } else {
            $login_error = 'Incorrect username or password.';
        }
    } else {
        $login_error = 'Incorrect username or password.';
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <script src="login.js"></script>
   
</head>
<body>
   
<div class="login-box">
    <div class="card login-card-body">
        <div class="login-logo">
            <a href="#"><i class="fab fa-login"></i> Sign In</a>
        </div>
        <p class="login-box-msg">Welcome to CineSaga</p>
        <?php if ($login_error != ''): ?>
            <div class="alert alert-danger"><?= $login_error ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="input-group mb-3" id="button">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3" id="button">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
            </div>
        </form>
        <p class="mb-1 text-left hover-red">
            <a href="registration.php">Create an Account</a>
        </p>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>