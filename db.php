<?php
$host = 'localhost';
$dbname = 'jamii_connect_db';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Migration: add role column if not exists
$result = $conn->query("SHOW COLUMNS FROM `users` LIKE 'role'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE `users` ADD `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
}

// Create event_bookings table
$conn->query("CREATE TABLE IF NOT EXISTS event_bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_booking (event_id, user_id),
    CONSTRAINT fk_booking_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Ensure SINGLE admin user exists
$admin_email = 'chrismeshack24@gmail.com';
$admin_pass = 'Admin@1234';
$hashed = password_hash($admin_pass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES ('Super Admin', ?, ?, 'admin')");
    $stmt->bind_param("ss", $admin_email, $hashed);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("UPDATE users SET role = 'admin', password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed, $admin_email);
    $stmt->execute();
}

// Delete the old admin
$conn->query("DELETE FROM users WHERE email = 'jamiiconnect@gmail.com'");
?>
