<?php
require 'db.php';

$queries = [
    // Categories and File paths
    "ALTER TABLE announcements ADD COLUMN category VARCHAR(100) DEFAULT 'General' AFTER title",
    "ALTER TABLE announcements ADD COLUMN attachment_path VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE announcements ADD COLUMN views INT DEFAULT 0",
    "ALTER TABLE events ADD COLUMN category VARCHAR(100) DEFAULT 'General' AFTER title",
    "ALTER TABLE events ADD COLUMN attachment_path VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE events ADD COLUMN views INT DEFAULT 0",
    "ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL",
    
    // Audit logs table
    "CREATE TABLE IF NOT EXISTS audit_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        table_name VARCHAR(50) NOT NULL,
        record_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "Successfully executed: " . substr($q, 0, 40) . "...\n";
    } else {
        echo "Error/Warning: " . $conn->error . "\n";
    }
}

// Insert admin user
$email = 'jamiiconnect@gmail.com';
$password = 'Admin@1234';
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $ins = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES ('Super Admin', ?, ?, 'admin')");
    $ins->bind_param("ss", $email, $hashed);
    if ($ins->execute()) {
        echo "Admin user created successfully.\n";
    } else {
        echo "Failed to create admin user.\n";
    }
} else {
    echo "Admin user already exists.\n";
}

if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
    echo "Created uploads directory.\n";
}
