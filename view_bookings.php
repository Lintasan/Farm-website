<?php
// manage_bookings.php

include 'config.php'; // Include the database configuration

// Fetch all bookings from the database
$stmt = $pdo->query("SELECT * FROM bookings");
$bookings = $stmt->fetchAll();

// Check if a delete request has been made
if (isset($_GET['delete'])) {
    $bookingId = $_GET['delete'];

    // Delete the booking from the database
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$bookingId]);

    // Redirect to the same page to see the changes
    header('Location: manage_bookings.php');
    exit;
}

// Check if an edit request has been made
if (isset($_POST['edit'])) {
    $bookingId = $_POST['booking_id'];
    $bookingStatus = $_POST['status'];
    $bookingNotes = $_POST['notes'];

    // Update the booking details in the database
    $stmt = $pdo->prepare("UPDATE bookings SET status = ?, notes = ? WHERE id = ?");
    $stmt->execute([$bookingStatus, $bookingNotes, $bookingId]);

    // Redirect to the same page to see the changes
    header('Location: manage_bookings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        table {
            width: 100%;
        }
        .action-btns {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Bookings</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Number of People</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['event']); ?></td>
                    <td><?php echo htmlspecialchars($booking['date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['time']); ?></td>
                    <td><?php echo htmlspecialchars($booking['number_of_people']); ?></td>
                    <td><?php echo htmlspecialchars($booking['status']); ?></td>
                    <td><?php echo htmlspecialchars($booking['notes']); ?></td>
                    <td class="action-btns">
                        <!-- Edit Button Trigger -->
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo $booking['id']; ?>">
                            Edit
                        </button>
                        
                        <!-- Delete Button -->
                        <a href="manage_bookings.php?delete=<?php echo $booking['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this booking?');">
                            Delete
                        </a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $booking['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Booking</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="manage_bookings.php">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="Pending" <?php if($booking['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="Confirmed" <?php if($booking['status'] == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                                            <option value="Cancelled" <?php if($booking['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control"><?php echo htmlspecialchars($booking['notes']); ?></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" name="edit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
