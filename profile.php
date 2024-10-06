<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$bookings = [];

try {
    $pdo->beginTransaction();

    $user_query = "SELECT username, email, role, created_at FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($user_query);
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }

    $orders_query = "SELECT COUNT(*) AS total_orders FROM orders WHERE user_id = :user_id";
    $stmt = $pdo->prepare($orders_query);
    $stmt->execute([':user_id' => $user_id]);
    $total_orders = $stmt->fetchColumn();

    // Fetch total bookings
    $bookings_query = "SELECT COUNT(*) AS total_bookings FROM bookings WHERE email = :email";
    $stmt = $pdo->prepare($bookings_query);
    $stmt->execute([':email' => $user['email']]);
    $total_bookings = $stmt->fetchColumn();

    // Fetch actual booking details
    $details_query = "SELECT * FROM bookings WHERE email = :email";
    $stmt = $pdo->prepare($details_query);
    $stmt->execute([':email' => $user['email']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $new_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        $update_query = "UPDATE users SET username = :username, email = :email WHERE id = :user_id";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([
            ':username' => $new_username,
            ':email' => $new_email,
            ':user_id' => $user_id
        ]);

        $user['username'] = $new_username;
        $user['email'] = $new_email;
        $message = "Profile updated successfully.";
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }
        
        .navbar {
            background-color: #38a169 !important;
        }

        .navbar-brand, .nav-link {
            color: white !important;
        }
        
        .container {
            margin-top: 30px;
        }

        .alert {
            border-radius: 10px;
        }
        
        .profile-header {
            background-color: #2f855a;
            color: white;
            padding: 16px;
            text-align: center;
        }

        .booking-details {
            display: none;
            margin-top: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
    <script>
        function toggleBookings() {
            const bookingDetails = document.getElementById('bookingDetails');
            bookingDetails.style.display = bookingDetails.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#"><i class="fas fa-tractor mr-2"></i>Farm Fresh</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php"><i class="fas fa-home mr-1"></i> Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view_orders.php"><i class="fas fa-list-alt mr-1"></i> View Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php"><i class="fas fa-user mr-1"></i> Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="book_visit.php"><i class="fas fa-calendar-check mr-1"></i> Booking/Visit</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="profile-header">
        <h2><i class="fas fa-user-circle mr-2"></i>My Profile</h2>
    </div>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow-md p-4">
        <form action="profile.php" method="post" class="space-y-4">
            <div class="form-group">
                <label for="username"><i class="fas fa-user-edit mr-1"></i>Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope mr-1"></i>Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="form-control">
            </div>
            <button type="submit" class="btn btn-success btn-block">
                <i class="fas fa-save mr-1"></i> Update Profile
            </button>
        </form>

        <div class="mt-6 border-top">
            <h3 class="text-lg font-medium text-gray-900"><i class="fas fa-chart-line mr-1"></i>Account Statistics</h3>
            <dl class="grid grid-cols-2 gap-4 mt-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500"><i class="fas fa-shopping-cart mr-1"></i>Total Orders</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-600"><?= htmlspecialchars($total_orders) ?></dd>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500"><i class="fas fa-calendar-alt mr-1"></i>Total Bookings</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-600"><?= htmlspecialchars($total_bookings) ?></dd>
                </div>
            </dl>
            <button class="btn btn-info mt-4" onclick="toggleBookings()">
                <i class="fas fa-eye mr-1"></i> View Booking Details
            </button>
            <div id="bookingDetails" class="booking-details mt-3">
                <h4>Booking Details</h4>
                <?php if (count($bookings) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($bookings as $booking): ?>
                            <li class="list-group-item">
                                <strong>Booking ID:</strong> <?= htmlspecialchars($booking['id']) ?> <br>
                                <strong>Date:</strong> <?= htmlspecialchars($booking['date']) ?> <br>
                                <strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?> 
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No bookings found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
