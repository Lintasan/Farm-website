<?php
require 'config.php'; // Ensure this path is correct
require 'functions.php'; // Ensure this path is correct

session_start();

// Check if the user is an admin
if ($_SESSION['role'] != 'admin') {
    die("Access denied");
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // User Deletion
    if (isset($_POST['delete_user_id'])) {
        $userId = $_POST['delete_user_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            echo "Error deleting user: " . $e->getMessage();
        }
    }
    
    // User Update
    if (isset($_POST['update_user_id'])) {
        $userId = $_POST['update_user_id'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $role, $userId]);
        } catch (PDOException $e) {
            echo "Error updating user: " . $e->getMessage();
        }
    }
    
    // Order Approval/Rejection
    if (isset($_POST['order_id'])) {
        $orderId = $_POST['order_id'];
        $action = $_POST['action'];
        try {
            if ($action == 'approve') {
                $stmt = $pdo->prepare("UPDATE orders SET status = 'approved' WHERE id = ?");
                $stmt->execute([$orderId]);
            } elseif ($action == 'reject') {
                $stmt = $pdo->prepare("UPDATE orders SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$orderId]);
            }
        } catch (PDOException $e) {
            echo "Error updating order: " . $e->getMessage();
        }
    }
    
    // Booking Approval/Rejection
    if (isset($_POST['booking_id'])) {
        $bookingId = $_POST['booking_id'];
        $action = $_POST['action'];
        $reason = $_POST['reason'] ?? '';
        try {
            if ($action == 'approve') {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'approved' WHERE id = ?");
                $stmt->execute([$bookingId]);
            } elseif ($action == 'reject') {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'rejected', reason = ? WHERE id = ?");
                $stmt->execute([$reason, $bookingId]);
            }
        } catch (PDOException $e) {
            echo "Error updating booking: " . $e->getMessage();
        }
    }
}

// Fetch all data
$users = fetchAllUsers($pdo);
$orders = fetchAllOrders($pdo);
$bookings = fetchAllBookings($pdo);

// Count data for dashboard
$totalUsers = count($users);
$totalOrders = count($orders);
$totalBookings = count($bookings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Management - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Add your CSS styles from the previous code */
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --text-color: #333;
            --bg-color: #f4f4f4;
            --card-bg: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .container {
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: var(--primary-color);
            padding: 20px;
            position: fixed;
        }

        .sidebar h2 {
            color: white;
            margin-bottom: 30px;
            font-size: 24px;
            text-align: center;
        }

        .nav-item {
            margin-bottom: 15px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            transition: background-color 0.3s;
            border-radius: 5px;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--secondary-color);
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }

        h2 {
            margin-bottom: 30px;
            font-size: 28px;
            color: var(--primary-color);
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .card-text {
            font-size: 28px;
            font-weight: bold;
        }

        /* Additional styles for the user's activity, tables, and actions */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 5px 10px;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-edit {
            background-color: #007BFF;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-approve {
            background-color: #28a745;
        }
        .btn-reject {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Farm Management</h2>
            <ul class="nav-list">
                <li class="nav-item"><a href="#dashboard" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="nav-item"><a href="#users" class="nav-link"><i class="fas fa-users"></i> Manage Users</a></li>
                <li class="nav-item"><a href="#orders" class="nav-link"><i class="fas fa-shopping-cart"></i> Manage Orders</a></li>
                <li class="nav-item"><a href="#bookings" class="nav-link"><i class="fas fa-calendar-check"></i> Manage Bookings</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h2>Dashboard Overview</h2>
            <div class="dashboard-cards">
                <div class="card">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
                <div class="card">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text"><?php echo $totalOrders; ?></p>
                </div>
                <div class="card">
                    <h5 class="card-title">Total Bookings</h5>
                    <p class="card-text"><?php echo $totalBookings; ?></p>
                </div>
            </div>

            <!-- Manage Users -->
            <div id="users">
                <h2>Manage Users</h2>
                <!-- Form to Update User -->
                <div class="form-container">
                    <h3>Update User</h3>
                    <form method="post">
                        <label for="update_user_id">User ID:</label>
                        <input type="number" name="update_user_id" id="update_user_id" required>
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" required>
                        <label for="role">Role:</label>
                        <select name="role" id="role" required>
                            <option value="admin">Admin</option>
                            <option value="customer">Customer</option>
                        </select>
                        <input type="submit" value="Update User">
                    </form>
                </div>

                <!-- Table of Users -->
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-delete">Delete</button>
                                </form>
                                <button onclick="populateUpdateForm(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo $user['role']; ?>')" class="btn btn-edit">Edit</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Manage Orders -->
            <div id="orders">
                <h2>Manage Orders</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['category']); ?></td>
                        <td><?php echo htmlspecialchars($order['item']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($order['unit']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <?php if ($order['status'] == 'pending'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                            </form>
                            <?php else: ?>
                            <em><?php echo ucfirst($order['status']); ?></em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

           <!-- Manage Bookings -->
<div id="bookings">
    <h2>Manage Bookings</h2>
    <table>
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
            <th>Reason</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($bookings as $booking): ?>
        <tr>
            <td><?php echo htmlspecialchars($booking['id']); ?></td>
            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($booking['email']); ?></td>
            <td><?php echo htmlspecialchars($booking['mobile_number']); ?></td>
            <td><?php echo htmlspecialchars($booking['event']); ?></td>
            <td><?php echo htmlspecialchars($booking['date']); ?></td>
            <td><?php echo htmlspecialchars($booking['time']); ?></td>
            <td><?php echo htmlspecialchars($booking['number_of_people']); ?></td>
            <td><?php echo htmlspecialchars($booking['status']); ?></td>
            <td>
                <?php if ($booking['status'] == 'pending'): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <input type="text" name="reason" placeholder="Reason" style="display:inline;">
                    <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                    <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                </form>
                <?php else: ?>
                <em><?php echo ucfirst($booking['status']); ?></em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

    
    <script>
        function populateUpdateForm(id, username, role) {
            document.getElementById('update_user_id').value = id;
            document.getElementById('username').value = username;
            document.getElementById('role').value = role;
        }
    </script>
</body>
</html>
