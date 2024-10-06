<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Farm System - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
        }
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/farm.jpg') no-repeat center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .hero-text h1 {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }
        .btn-custom {
            transition: all 0.3s ease;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 500;
            margin: 10px 0;
        }
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .fade-in {
            animation: fadeIn 1.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container fade-in">
            <div class="hero-text text-center">
                <h1 class="mb-5">Welcome to the Fresh Farm System</h1>
                <div class="d-grid gap-3 col-lg-6 mx-auto">
                    <a href="home.php" class="btn btn-primary btn-lg btn-custom">
                        <i class="fas fa-shopping-cart me-2"></i> Place an Order
                    </a>
                    <a href="book_visit.php" class="btn btn-success btn-lg btn-custom">
                        <i class="fas fa-calendar-check me-2"></i> Book a Visit
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-lg btn-custom">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>