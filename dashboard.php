<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'db.php';

// Fetch summary counts
$announcements_count = 0;
$result = $conn->query("SELECT COUNT(*) AS count FROM announcements");
if ($result) {
    $row = $result->fetch_assoc();
    $announcements_count = $row['count'];
}

$events_count = 0;
$result2 = $conn->query("SELECT COUNT(*) AS count FROM events");
if ($result2) {
    $row2 = $result2->fetch_assoc();
    $events_count = $row2['count'];
}

$services_count = 0;
$result3 = $conn->query("SELECT COUNT(*) AS count FROM services");
if ($result3) {
    $row3 = $result3->fetch_assoc();
    $services_count = $row3['count'];
}

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Jamii Connect</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Jamii Connect</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="announcements.php">Announcements</a></li>
            <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4 animate-on-scroll">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! <span class="badge-sparkle" style="font-size: 1.2rem;">🏆</span></h2>
        </div>

        <?php if ($is_admin): ?>
        <!-- Quick Actions Bar -->
        <div class="card dashboard-card mb-4 border-0 animate-on-scroll">
            <div class="card-body bg-white rounded shadow-sm d-flex gap-3 align-items-center flex-wrap">
                <h5 class="mb-0 me-3 text-muted">Quick Actions:</h5>
                <a href="announcements.php" class="btn btn-primary">➕ Add Announcement</a>
                <a href="events.php" class="btn btn-success" style="background-color: var(--accent-green); border: none;">📅 Create Event</a>
                <a href="services.php" class="btn btn-warning text-dark" style="background-color: var(--accent-gold); border: none;">⚙️ Manage Services</a>
            </div>
        </div>

        <!-- Notifications Panel -->
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4 animate-on-scroll" role="alert" style="background-color: #e2e8f0; color: #2d3748;">
            <span class="fs-4 me-3">🔔</span>
            <div>
                <strong>System Notification:</strong> Everything is running smoothly. Ensure upcoming events are updated.
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Announcements Card -->
            <div class="col-md-4 mb-4 animate-on-scroll">
                <div class="card dashboard-card h-100 bg-white">
                    <div class="card-header-bold p-3 text-center">
                        <h4 class="mb-0">Announcements</h4>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="icon-circle icon-blue mb-3">
                            <svg class="svg-pulse" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <h1 class="display-5 fw-bold text-primary"><?php echo $announcements_count; ?></h1>
                        <p class="text-muted">Active Announcements</p>
                        <a href="announcements.php" class="btn btn-outline-primary mt-auto w-100">View All</a>
                    </div>
                </div>
            </div>

            <!-- Events Calendar Card -->
            <div class="col-md-4 mb-4 animate-on-scroll">
                <div class="card dashboard-card h-100 bg-white">
                    <div class="card-header-bold p-3 text-center" style="background-color: var(--accent-green);">
                        <h4 class="mb-0">Upcoming Events</h4>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="icon-circle icon-green mb-3">
                            <svg class="svg-rotate" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h1 class="display-5 fw-bold" style="color: var(--accent-green);"><?php echo $events_count; ?></h1>
                        <p class="text-muted">Scheduled Events</p>
                        <a href="events.php" class="btn btn-outline-success mt-auto w-100" style="color: var(--accent-green); border-color: var(--accent-green);">Manage Calendar</a>
                    </div>
                </div>
            </div>

            <!-- Services Grid Card -->
            <div class="col-md-4 mb-4 animate-on-scroll">
                <div class="card dashboard-card h-100 bg-white">
                    <div class="card-header-bold p-3 text-center" style="background-color: var(--accent-gold); color: #2d3748;">
                        <h4 class="mb-0">Public Services</h4>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="icon-circle icon-gold mb-3 text-dark">
                            <svg class="svg-pulse" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h1 class="display-5 fw-bold" style="color: var(--accent-gold);"><?php echo $services_count; ?></h1>
                        <p class="text-muted">Services Listed</p>
                        <a href="services.php" class="btn btn-outline-warning text-dark mt-auto w-100" style="border-color: var(--accent-gold);">View Directory</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Kitale National Polytechnic | Jamii Connect.</p>
        </div>
    </footer>

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="app.js"></script>
</body>
</html>
