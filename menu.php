<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="menu.css">
    <style>
        /* Custom CSS for sidebar height */
        .sidebar {
            height: 100vh; /* Set sidebar height to full viewport height */
            overflow-y: auto; /* Enable vertical scrolling if needed */
        }
    </style>

</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=profile">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registration.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-link">
                <span class="brand-text font-weight-light">   SINE SAGA</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                            <div class="info">
                                <a href="#" class="d-block">Hello, <?php echo $_SESSION['username']; ?></a>
                            </div>
                        </div>
                        
                        <!-- Sidebar Menu for Admin -->
                        <nav class="mt-2">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                                <li class="nav-item">
                                    <a href="index.php?page=add" class="nav-link">
                                        <i class="nav-icon fas fa-plus"></i>
                                        <p>Add Video</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=view" class="nav-link">
                                        <i class="nav-icon fas fa-eye"></i>
                                        <p>View Videos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=reports" class="nav-link">
                                        <i class="nav-icon fas fa-chart-bar"></i>
                                        <p>Reports</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=adminhistory" class="nav-link">
                                        <i class="nav-icon fas fa-history"></i>
                                        <p>Transaction History</p>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <!-- /.sidebar-menu -->
                    <?php else: ?>
                        <!-- Sidebar Menu for Normal User -->
                        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                            <div class="info">
                                <a href="#" class="d-block">Hello, <?php echo $_SESSION['username']; ?>!</a>
                            </div>
                        </div>
                        
                        <nav class="mt-2">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                                <li class="nav-item">
                                    <a href="index.php?page=view" class="nav-link">
                                        <i class="nav-icon fas fa-eye"></i>
                                        <p>View Videos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=rented" class="nav-link">
                                        <i class="nav-icon fas fa-film"></i>
                                        <p>Rented Videos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=history" class="nav-link">
                                        <i class="nav-icon fas fa-history"></i>
                                        <p>Transaction History</p>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <!-- /.sidebar-menu -->
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <!-- /.sidebar -->
        </aside>
    </div>
</body>
</html>
