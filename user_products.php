<?php
session_start();
require 'config.php'; // Ensure this path is correct

$userId = $_SESSION['user_id'];

// Fetch products submitted by the logged-in user
$sql = "SELECT * FROM products WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Products</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Your Products</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Product ID</th>
                    <th class="border border-gray-300 px-4 py-2">Name</th>
                    <th class="border border-gray-300 px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($product['id']) ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($product['name']) ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($product['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
