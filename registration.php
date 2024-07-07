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

$registration_error = '';
$registration_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $registration_error = 'Username or email already exists.';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, address, phone, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $address, $phone_number, $email, $username, $password);

        if ($stmt->execute()) {
            $registration_success = 'Registration successful. Please log in.';
            header('Location: login.php');
            exit;
        } else {
            $registration_error = 'Error: ' . $stmt->error;
        }
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
    <title>Registration Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="registration.css">
    <script src="registration.js"></script>
</head>
<body>
     <div class="bgvid-reg" >
        <video class="bgvidreg" controls height="490px" width="860px" loop autoplay muted>
            <source src="videos/arcane.mp4" type="video/mp4"></video>
    </div>
<div class="register-box">
    <div class="card register-card-body">
        <div class="register-logo">
            <a href="#"><b>SIGN</b> UP</a>
        </div>
        <p class="login-box-msg">Register a new membership
        <img src="img/reg_icon.png" class="reg-icon"></p>
        <?php if ($registration_error != ''): ?>
            <div class="alert alert-danger"><?= $registration_error ?></div>
        <?php endif; ?>

        <?php if ($registration_success != ''): ?>
            <div class="alert alert-success"><?= $registration_success ?></div>
        <?php endif; ?>

        <form action="registration.php" method="post">
            <div class="input-group mb-3" id="button">
                <input type="text" name="name" class="form-control" placeholder="Full name" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3" id="button">
                <input type="text" name="address" class="form-control" placeholder="Address" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-map-marker-alt"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3" id="button">
                <input type="text" name="phone_number" class="form-control" placeholder="Phone number" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-phone"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3" id="button">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
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
                <div class="col-12 text-center" > 
                    <button type="submit" class="btn btn-primary btn-block" id="reg"><span>Register</span></button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <a href="login.php" class="text-center">I already have a membership</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>