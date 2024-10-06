<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Project001";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

// Validate input
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO queries (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);

// Execute
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}

$stmt->close();
$conn->close();
?>
