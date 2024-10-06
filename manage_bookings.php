<?php
include 'config.php'; // Include the database configuration

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the user's role from the session
$userRole = $_SESSION['user_role']; // Assuming 'admin' or 'customer'
$customerEmail = $_SESSION['user_email'];

// Fetch bookings based on the user role
if ($userRole === 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM bookings ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE email = ? ORDER BY created_at DESC");
    $stmt->execute([$customerEmail]);
}

// Execute for admin or user
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if there are bookings
if (!$bookings) {
    $errorMessage = "No bookings found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg custom-navbar">
        <a class="navbar-brand" href="#"><i class="fas fa-tractor mr-2"></i> Farm Fresh</a>
        <!-- Other navbar content -->
    </nav>
    
    <div class="container mt-5">
        <h2>My Bookings</h2>
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?= $errorMessage; ?></div>
        <?php endif; ?>

        <?php if ($bookings): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Number of People</th>
                        <th>Status</th>
                        <?php if ($userRole === 'admin'): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['id']); ?></td>
                            <td><?= htmlspecialchars($booking['customer_name']); ?></td>
                            <td><?= htmlspecialchars($booking['email']); ?></td>
                            <td><?= htmlspecialchars($booking['mobile_number']); ?></td>
                            <td><?= htmlspecialchars($booking['event']); ?></td>
                            <td><?= htmlspecialchars($booking['date']); ?></td>
                            <td><?= htmlspecialchars($booking['time']); ?></td>
                            <td><?= htmlspecialchars($booking['number_of_people']); ?></td>
                            <td><?= htmlspecialchars($booking['status']); ?></td>
                            <?php if ($userRole === 'admin'): ?>
                                <td>
                                    <a href="booking_form.php?booking_id=<?= $booking['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="delete_booking.php?booking_id=<?= $booking['id']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                    <a href="approve_booking.php?booking_id=<?= $booking['id']; ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i></a>
                                    <a href="reject_booking.php?booking_id=<?= $booking['id']; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
