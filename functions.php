<?php
require 'config.php'; // Ensure this path is correct

// Fetch all users
function fetchAllUsers($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching users: " . $e->getMessage();
        return [];
    }
}

// Fetch all orders
function fetchAllOrders($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM orders");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching orders: " . $e->getMessage();
        return [];
    }
}

// Fetch all visits
function fetchAllVisits($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM visits");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching visits: " . $e->getMessage();
        return [];
    }
}

// Fetch all bookings
function fetchAllBookings($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM bookings");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching bookings: " . $e->getMessage();
        return [];
    }
}
?>
