<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    <title>Farm Fresh - Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            padding-top: 50px;
        }
        .payment-card {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .payment-card h3 {
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .payment-card .paybill-info {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        .payment-card .paybill-info strong {
            font-weight: 600;
            color: #4CAF50;
        }
        .payment-card .btn-pay {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .payment-card .btn-pay:hover {
            background-color: #45a049;
        }
        .text-center {
            text-align: center;
        }
        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="payment-card">
    <h3 class="text-center"><i class="fas fa-money-bill-wave"></i> Payment Information</h3>
    <div class="paybill-info">
        <strong>Paybill Number:</strong> 890989
    </div>
    <div class="paybill-info">
        <strong>Account Name:</strong> Fresh Farm
    </div>
    <form action="process_payment.php" method="POST">
        <div class="form-group">
            <label for="amount">Enter Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
        </div>
        <button type="submit" class="btn-pay btn-block">Make Payment</button>
    </form>
    <div class="text-center btn-back">
        <a href="view_orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
