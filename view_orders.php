<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'Farm';
$user = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];

// Fetch completed orders and rejected orders from the database
$sql = "SELECT o.item, o.quantity, o.unit, o.category, o.price AS unit_price, o.order_date, o.status 
        FROM orders o 
        WHERE o.user_id = ? 
        ORDER BY o.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

if (!$stmt->execute()) {
    die("Query failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Calculate total price and gather order information
$totalPrice = 0;
$totalRejectedPrice = 0;
$orders = [];
$rejectedOrders = [];

while ($row = $result->fetch_assoc()) {
    $row['total_price'] = $row['quantity'] * $row['unit_price'];
    
    if (strtolower($row['status']) === 'rejected') {
        $totalRejectedPrice += $row['total_price'];
        $rejectedOrders[] = $row;
    } else {
        $totalPrice += $row['total_price'];
        $orders[] = $row;
    }
}

// Close statement and connection
$stmt->close();
$conn->close();

function renderOrdersTable($orders, $totalPrice, $tableTitle, $tableClass) {
    if (count($orders) > 0) {
        echo "<h2>$tableTitle</h2>";
        echo '<table class="table table-bordered">';
        echo '<thead class="thead-light">';
        echo '<tr>';
        echo '<th>Item</th>';
        echo '<th>Quantity</th>';
        echo '<th>Unit</th>';
        echo '<th>Category</th>';
        echo '<th>Order Date</th>';
        echo '<th>Status</th>';
        echo '<th>Unit Price</th>';
        echo '<th>Total Price</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($orders as $order) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($order['item']) . '</td>';
            echo '<td>' . htmlspecialchars($order['quantity']) . '</td>';
            echo '<td>' . htmlspecialchars($order['unit']) . '</td>';
            echo '<td>' . htmlspecialchars($order['category']) . '</td>';
            echo '<td>' . htmlspecialchars($order['order_date']) . '</td>';
            echo '<td>' . htmlspecialchars(ucfirst($order['status'])) . '</td>';
            echo '<td>' . htmlspecialchars($order['unit_price']) . '</td>';
            echo '<td>' . htmlspecialchars($order['total_price']) . '</td>';
            echo '</tr>';
        }
        echo '<tr class="' . htmlspecialchars($tableClass) . '">';
        echo '<td colspan="7" class="text-right">Total Price:</td>';
        echo '<td>' . htmlspecialchars($totalPrice) . '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<div class="alert alert-info text-center">No orders found.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Fresh - View Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
    }
    h1 {
        color: #4CAF50;
        margin: 30px 0;
    }
    .table th, .table td {
        text-align: center;
    }
    .bg-green-custom {
        background-color: #38a169; /* Updated green color */
    }
    .nav-link {
        color: white !important; /* Set the color to white */
    }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-green-custom">
    <a class="navbar-brand" href="index.php"><i class="fas fa-tractor"></i> Farm Fresh</a>
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

<div class="container mt-5">
    <h1>Your Orders</h1>
    
    <?php 
        renderOrdersTable($orders, $totalPrice, 'Completed Orders', 'table-info');
    ?>
    
    <!-- Payment Section -->
    <?php if ($totalPrice > 0): ?>
    <div class="text-center">
        <h3>Total Amount Due: <?= htmlspecialchars($totalPrice) ?></h3>
        <form action="process_payment.php" method="POST">
            <input type="hidden" name="total_amount" value="<?= htmlspecialchars($totalPrice) ?>">
            <button type="submit" class="btn btn-primary">Make Payment</button>
        </form>
    </div>
    <?php endif; ?>
    
    <?php 
        renderOrdersTable($rejectedOrders, $totalRejectedPrice, 'Rejected Orders', 'table-warning');
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
