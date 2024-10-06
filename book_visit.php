<?php
include 'config.php'; // Include the database configuration
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Initialize booking details
$bookingDetails = [
    'customer_name' => '',
    'email' => '',
    'mobile_number' => '',
    'event' => '',
    'date' => '',
    'time' => '',
    'number_of_people' => ''
];

// Check if a booking ID is provided to fetch existing booking details
if (isset($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];

    // Fetch the booking details from the database
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$bookingId, $_SESSION['user_id']]);
    $bookingDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the booking exists
    if (!$bookingDetails) {
        // Booking not found, redirect or show an error
        header('Location: manage_bookings.php?error=not_found');
        exit;
    }
}

// Check if a booking request has been made
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Use values from the form, not from the fetched details
    $customerName = $_POST['customer_name'];
    $email = $_POST['email'];
    $mobileNumber = $_POST['mobile_number'];
    $event = $_POST['event'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $numberOfPeople = $_POST['number_of_people'];
    $userId = $_SESSION['user_id']; // Get the user ID from the session

    if (isset($bookingId)) {
        // Update existing booking if booking ID exists
        $stmt = $pdo->prepare("UPDATE bookings SET customer_name = ?, email = ?, mobile_number = ?, event = ?, date = ?, time = ?, number_of_people = ? WHERE id = ?");
        $stmt->execute([$customerName, $email, $mobileNumber, $event, $date, $time, $numberOfPeople, $bookingId]);
    } else {
        // Insert a new booking
        $stmt = $pdo->prepare("INSERT INTO bookings (customer_name, email, mobile_number, event, date, time, number_of_people, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customerName, $email, $mobileNumber, $event, $date, $time, $numberOfPeople, $userId]);
    }

    // Redirect to manage bookings page with a success message
    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Visit</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        .custom-navbar {
            background-color: #38a169; /* Navbar background color */
        }
        .custom-navbar .nav-link {
            color: #fff; /* Navbar text color */
        }
        .form-group label {
            font-weight: 500;
        }
        .form-control {
            border-radius: 4px;
        }
        .input-group-text {
            background-color: #38a169; /* Icon background color */
            color: white;
            border: none;
        }
        .btn-primary {
            background-color: #38a169;
            border-color: #38a169;
        }
        .btn-primary:hover {
            background-color: #2f855a;
            border-color: #2f855a;
        }
    </style>
</head>
<body>
    <!-- Main Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <a class="navbar-brand" href="#"><i class="fas fa-tractor mr-2"></i> Farm Fresh</a>
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
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <h2><?php echo isset($bookingId) ? 'Edit Booking' : 'Book a Visit'; ?></h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control" name="customer_name" value="<?php echo htmlspecialchars($bookingDetails['customer_name']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($bookingDetails['email']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="mobile_number">Mobile Number</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    </div>
                    <input type="text" class="form-control" name="mobile_number" value="<?php echo htmlspecialchars($bookingDetails['mobile_number']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="event">Event</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <input type="text" class="form-control" name="event" value="<?php echo htmlspecialchars($bookingDetails['event']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($bookingDetails['date']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="time">Time</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                    </div>
                    <input type="time" class="form-control" name="time" value="<?php echo htmlspecialchars($bookingDetails['time']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="number_of_people">Number of People</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                    </div>
                    <input type="number" class="form-control" name="number_of_people" value="<?php echo htmlspecialchars($bookingDetails['number_of_people']); ?>" required min="1">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle mr-2"></i><?php echo isset($bookingId) ? 'Update Booking' : 'Book Now'; ?></button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
