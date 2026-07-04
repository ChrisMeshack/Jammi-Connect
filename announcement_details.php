<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch announcement details
$stmt = $conn->prepare('SELECT a.*, u.full_name FROM announcements a LEFT JOIN users u ON a.user_id = u.id WHERE a.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Announcement not found.");
}
$announcement = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($announcement['title']); ?> - Jamii Connect</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <a class="navbar-brand" href="index.php">Jamii Connect</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="announcements.php">Announcements</a></li>
            <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5 flex-grow-1">
        <a href="announcements.php" class="btn btn-secondary mb-3">&larr; Back to Announcements</a>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><?php echo htmlspecialchars($announcement['title']); ?></h3>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Posted on <?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?> 
                    <?php if ($announcement['full_name']) echo 'by ' . htmlspecialchars($announcement['full_name']); ?>
                </p>
                <hr>
                <p class="lead" style="white-space: pre-wrap;"><?php echo htmlspecialchars($announcement['content']); ?></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Kitale National Polytechnic | Jamii Connect.</p>
        </div>
    </footer>

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="app.js"></script>
</body>
</html>
