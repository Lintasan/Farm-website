<?php
// Include the database configuration
require 'config.php';

// Initialize variables for form inputs
$category = $_POST['category'] ?? '';
$item = $_POST['item'] ?? '';
$quantity = $_POST['quantity'] ?? 0;
$unit = $_POST['unit'] ?? '';

// Validate inputs
if (!in_array($category, ['Cereal', 'Legumes', 'Meat', 'Poultry', 'Vegetables']) ||
    empty($item) || !is_numeric($quantity) || $quantity <= 0 || empty($unit)) {
    $message = "Invalid input. Please check your data.";
} else {
    try {
        // Prepare the SQL statement
        $sql = "INSERT INTO orders (category, item, quantity, unit) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // Execute the statement with the provided data
        $stmt->execute([$category, $item, $quantity, $unit]);
        $message = "Order placed successfully!";
    } catch (\PDOException $e) {
        // Handle errors
        $message = "Error: " . $e->getMessage();
    }
}

echo $message;
?>
