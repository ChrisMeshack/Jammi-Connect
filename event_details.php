<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg = '';
$msg_type = '';
$is_booked = false;

// Handle Booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_event'])) {
    // Check if already booked
    $stmt = $conn->prepare('SELECT id FROM event_bookings WHERE event_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $id, $_SESSION['user_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $msg = 'You have already booked a seat for this event.';
        $msg_type = 'warning';
    } else {
        $stmt = $conn->prepare('INSERT INTO event_bookings (event_id, user_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $msg = 'Seat booked successfully!';
            $msg_type = 'success';
        } else {
            $msg = 'Error booking seat. Please try again.';
            $msg_type = 'danger';
        }
    }
}

// Check booking status
$stmt = $conn->prepare('SELECT id FROM event_bookings WHERE event_id = ? AND user_id = ?');
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
$stmt->execute();
$is_booked = $stmt->get_result()->num_rows > 0;

// Fetch event details
$stmt = $conn->prepare('SELECT * FROM events WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Event not found.");
}
$event = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($event['title']); ?> - Jamii Connect</title>
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
            <li class="nav-item"><a class="nav-link" href="announcements.php">Announcements</a></li>
            <li class="nav-item"><a class="nav-link active" href="events.php">Events</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5 flex-grow-1">
        <a href="events.php" class="btn btn-secondary mb-3">&larr; Back to Events</a>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><?php echo htmlspecialchars($event['title']); ?></h3>
            </div>
            <div class="card-body">
                <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                <hr>
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

                <div class="mt-4">
                    <?php if ($is_booked): ?>
                        <button class="btn btn-success" disabled>Seat Booked ✓</button>
                    <?php else: ?>
                        <form method="post">
                            <button type="submit" name="book_event" class="btn btn-primary">Book a Seat</button>
                        </form>
                    <?php endif; ?>
                </div>
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
