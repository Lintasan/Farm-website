<?php
session_start();
require 'config.php'; // Ensure this file contains PDO connection setup

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = '';

// Prices for each item
$prices = [
    'Cereal' => [
        'Wheat' => 1.00,
        'Rice' => 1.20,
        'Oats' => 0.90,
    ],
    'Legumes' => [
        'Beans' => 2.00,
        'Lentils' => 1.80,
        'Peas' => 1.60,
    ],
    'Meat' => [
        'Beef' => 12.00,
        'Pork' => 10.00,
        'Lamb' => 14.00,
    ],
    'Poultry' => [
        'Chicken' => 8.00,
        'Turkey' => 10.00,
        'Duck' => 12.00,
    ],
    'Vegetables' => [
        'Carrots' => 0.50,
        'Broccoli' => 1.00,
        'Spinach' => 1.20,
    ],
    'Fruits' => [
        'Apples' => 3.00,
        'Bananas' => 1.50,
        'Oranges' => 2.00,
    ],
];

// Check if the user is logged in
$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    $message = "You need to log in to place an order.";
} else {
    // Place order
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $category = $_POST['category'] ?? '';
        $item = $_POST['item'] ?? '';
        $quantity = $_POST['quantity'] ?? 0;
        $unit = $_POST['unit'] ?? '';

        // Validate inputs
        if (!array_key_exists($category, $prices) || empty($item) || !is_numeric($quantity) || $quantity <= 0 || empty($unit)) {
            $message = "Invalid input. Please check your data.";
        } else {
            // Get the price based on category and item
            $price = $prices[$category][$item] ?? 0;
            $totalPrice = $price * $quantity; // Calculate the total price

            try {
                // Prepare and execute SQL statement
                $sql = "INSERT INTO orders (user_id, category, item, quantity, unit, price) VALUES (:user_id, :category, :item, :quantity, :unit, :price)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':category', $category);
                $stmt->bindParam(':item', $item);
                $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $stmt->bindParam(':unit', $unit);
                $stmt->bindParam(':price', $totalPrice, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $message = "Order placed successfully!";
                } else {
                    $message = "Error: Could not execute the query.";
                }
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Fresh - Customer Order Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #38a169;
            --secondary-color: #45a049;
            --accent-color: #ff6f00;
            --background-color: #f4f4f4;
            --text-color: #333;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .navbar {
            background-color: var(--primary-color) !important;

        }

        .navbar-brand, .nav-link {
            color: white !important;
        }

        .container {
            margin-top: 30px;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 20px;
        }

        .order-btn {
            background-color: var(--accent-color);
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .order-btn:hover {
            background-color: #e65100;
        }

        .alert {
            border-radius: 10px;
        }

        /* Category Icons */
        .category-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            color: var(--primary-color);
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
                    <a class="nav-link" href="book_visit.php"><i class="fas fa-user mr-1"></i>Booking/Visit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center"><i class="fas fa-shopping-basket mr-2"></i> Customer Order Dashboard</h1>
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="row">
            <!-- Cereal -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cereal</h5>
                        <form action="" method="post">
                            <input type="hidden" name="category" value="Cereal">
                            <div class="form-group">
                                <label for="cerealItem">Select Item:</label>
                                <select id="cerealItem" class="form-control" name="item">
                                    <option value="Wheat">Wheat - $1.00</option>
                                    <option value="Rice">Rice - $1.20</option>
                                    <option value="Oats">Oats - $0.90</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cerealQuantity">Quantity:</label>
                                <input type="number" id="cerealQuantity" class="form-control" name="quantity" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="cerealUnit">Unit:</label>
                                <select id="cerealUnit" class="form-control" name="unit">
                                    <option value="kg">Kilograms</option>
                                    <option value="g">Grams</option>
                                </select>
                            </div>
                            <button type="submit" class="btn order-btn"><i class="fas fa-cart-plus mr-2"></i> Order</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Legumes -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Legumes</h5>
                        <form action="" method="post">
                            <input type="hidden" name="category" value="Legumes">
                            <div class="form-group">
                                <label for="legumesItem">Select Item:</label>
                                <select id="legumesItem" class="form-control" name="item">
                                    <option value="Beans">Beans - $2.00</option>
                                    <option value="Lentils">Lentils - $1.80</option>
                                    <option value="Peas">Peas - $1.60</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="legumesQuantity">Quantity:</label>
                                <input type="number" id="legumesQuantity" class="form-control" name="quantity" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="legumesUnit">Unit:</label>
                                <select id="legumesUnit" class="form-control" name="unit">
                                    <option value="kg">Kilograms</option>
                                    <option value="g">Grams</option>
                                </select>
                            </div>
                            <button type="submit" class="btn order-btn"><i class="fas fa-cart-plus mr-2"></i> Order</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Meat -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Meat</h5>
                        <form action="" method="post">
                            <input type="hidden" name="category" value="Meat">
                            <div class="form-group">
                                <label for="meatItem">Select Item:</label>
                                <select id="meatItem" class="form-control" name="item">
                                    <option value="Beef">Beef - $12.00</option>
                                    <option value="Pork">Pork - $10.00</option>
                                    <option value="Lamb">Lamb - $14.00</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="meatQuantity">Quantity:</label>
                                <input type="number" id="meatQuantity" class="form-control" name="quantity" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="meatUnit">Unit:</label>
                                <select id="meatUnit" class="form-control" name="unit">
                                    <option value="kg">Kilograms</option>
                                    <option value="g">Grams</option>
                                </select>
                            </div>
                            <button type="submit" class="btn order-btn"><i class="fas fa-cart-plus mr-2"></i> Order</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Poultry -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Poultry</h5>
                        <form action="" method="post">
                            <input type="hidden" name="category" value="Poultry">
                            <div class="form-group">
                                <label for="poultryItem">Select Item:</label>
                                <select id="poultryItem" class="form-control" name="item">
                                    <option value="Chicken">Chicken - $8.00</option>
                                    <option value="Turkey">Turkey - $10.00</option>
                                    <option value="Duck">Duck - $12.00</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="poultryQuantity">Quantity:</label>
                                <input type="number" id="poultryQuantity" class="form-control" name="quantity" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="poultryUnit">Unit:</label>
                                <select id="poultryUnit" class="form-control" name="unit">
                                    <option value="kg">Kilograms</option>
                                    <option value="g">Grams</option>
                                </select>
                            </div>
                            <button type="submit" class="btn order-btn"><i class="fas fa-cart-plus mr-2"></i> Order</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Vegetables -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Vegetables</h5>
                        <form action="" method="post">
                            <input type="hidden" name="category" value="Vegetables">
                            <div class="form-group">
                                <label for="vegetablesItem">Select Item:</label>
                                <select id="vegetablesItem" class="form-control" name="item">
                                    <option value="Carrots">Carrots - $0.50</option>
                                    <option value="Broccoli">Broccoli - $1.00</option>
                                    <option value="Spinach">Spinach - $1.20</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vegetablesQuantity">Quantity:</label>
                                <input type="number" id="vegetablesQuantity" class="form-control" name="quantity" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="vegetablesUnit">Unit:</label>
                                <select id="vegetablesUnit" class="form-control" name="unit">
                                    <option value="kg">Kilograms</option>
                                    <option value="g">Grams</option>
                                </select>
                            </div>
                            <button type="submit" class="btn order-btn"><i class="fas fa-cart-plus mr-2"></i> Order</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Fruits -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Fruits</h5>
                        <form action="" method="post">
                            <input type="hidden" name="category" value="Fruits">
                            <div class="form-group">
                                <label for="fruitsItem">Select Item:</label>
                                <select id="fruitsItem" class="form-control" name="item">
                                    <option value="Apples">Apples - $3.00</option>
                                    <option value="Bananas">Bananas - $1.50</option>
                                    <option value="Oranges">Oranges - $2.00</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fruitsQuantity">Quantity:</label>
                                <input type="number" id="fruitsQuantity" class="form-control" name="quantity" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="fruitsUnit">Unit:</label>
                                <select id="fruitsUnit" class="form-control" name="unit">
                                    <option value="kg">Kilograms</option>
                                    <option value="g">Grams</option>
                                </select>
                            </div>
                            <button type="submit" class="btn order-btn"><i class="fas fa-cart-plus mr-2"></i> Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
