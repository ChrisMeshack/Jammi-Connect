<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'db.php';

// Fetch summary counts for the rings
$announcements_count = 0;
$result = $conn->query("SELECT COUNT(*) AS count FROM announcements");
if ($result) { $announcements_count = $result->fetch_assoc()['count']; }

$events_count = 0;
$result2 = $conn->query("SELECT COUNT(*) AS count FROM events");
if ($result2) { $events_count = $result2->fetch_assoc()['count']; }

$services_count = 0;
$result3 = $conn->query("SELECT COUNT(*) AS count FROM services");
if ($result3) { $services_count = $result3->fetch_assoc()['count']; }

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Jamii Connect</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg shadow-sm">
      <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Jamii Connect</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fa-solid fa-chart-line me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="announcements.php"><i class="fa-solid fa-bullhorn me-1"></i> Announcements</a></li>
            <li class="nav-item"><a class="nav-link" href="events.php"><i class="fa-regular fa-calendar me-1"></i> Events</a></li>
            
            <li class="nav-item dropdown ms-2">
              <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-circle-user fa-lg"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gear me-2"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4 animate-on-scroll is-visible">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! <span>👋</span></h2>
        </div>

        <?php if ($is_admin): ?>
        <!-- Quick Actions Bar -->
        <div class="card glass-card mb-4 border-0 animate-on-scroll is-visible stagger-1">
            <div class="card-body d-flex gap-3 align-items-center flex-wrap">
                <h5 class="mb-0 me-3"><i class="fa-solid fa-bolt text-warning"></i> Admin Actions:</h5>
                <a href="announcements.php" class="btn btn-primary" aria-label="Manage Announcements"><i class="fa-solid fa-plus me-1"></i> Manage Announcements</a>
                <a href="events.php" class="btn btn-primary" aria-label="Manage Events"><i class="fa-solid fa-calendar-plus me-1"></i> Manage Events</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Advanced Analytics Row -->
        <div class="row mb-4" id="analyticsRow">
            <div class="col-md-4 mb-3 animate-on-scroll is-visible stagger-2">
                <div class="card glass-card h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase mb-2"><i class="fa-solid fa-eye me-1 text-primary"></i> Total Portal Views</h6>
                        <h2 class="display-6 fw-bold" style="color: var(--color-orange);" id="statViews">Loading...</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 animate-on-scroll is-visible stagger-3">
                <div class="card glass-card h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase mb-2"><i class="fa-solid fa-users me-1 text-primary"></i> Active Users (24h)</h6>
                        <h2 class="display-6 fw-bold" style="color: var(--color-gold);" id="statUsers">Loading...</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 animate-on-scroll is-visible stagger-4">
                <div class="card glass-card h-100">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase mb-2"><i class="fa-solid fa-fire me-1 text-primary"></i> Most Popular Item</h6>
                        <h5 class="fw-bold mt-3 text-danger" id="statPopularTitle">Loading...</h5>
                        <p class="small m-0" id="statPopularViews"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Announcements Card -->
            <div class="col-md-4 mb-4 animate-on-scroll is-visible stagger-5">
                <div class="card glass-card h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center py-4">
                        <div class="progress-ring color-orange mb-3">
                            <svg>
                                <circle class="progress-ring-bg" cx="60" cy="60" r="50"></circle>
                                <circle class="progress-ring-path" cx="60" cy="60" r="50" style="stroke-dashoffset: <?php echo 314 - (314 * min($announcements_count*10, 100)) / 100; ?>;"></circle>
                            </svg>
                            <div class="progress-ring-text"><?php echo $announcements_count; ?></div>
                        </div>
                        <h5 class="fw-bold">Announcements</h5>
                        <a href="announcements.php" class="btn btn-primary mt-3 w-100" aria-label="View Announcements"><i class="fa-solid fa-arrow-right"></i> View All</a>
                    </div>
                </div>
            </div>

            <!-- Events Calendar Card -->
            <div class="col-md-4 mb-4 animate-on-scroll is-visible stagger-5">
                <div class="card glass-card h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center py-4">
                        <div class="progress-ring color-rose mb-3">
                            <svg>
                                <circle class="progress-ring-bg" cx="60" cy="60" r="50"></circle>
                                <circle class="progress-ring-path" cx="60" cy="60" r="50" style="stroke-dashoffset: <?php echo 314 - (314 * min($events_count*10, 100)) / 100; ?>;"></circle>
                            </svg>
                            <div class="progress-ring-text"><?php echo $events_count; ?></div>
                        </div>
                        <h5 class="fw-bold">Events</h5>
                        <a href="events.php" class="btn btn-primary mt-3 w-100" aria-label="View Events"><i class="fa-solid fa-arrow-right"></i> View Calendar</a>
                    </div>
                </div>
            </div>

            <!-- Services Grid Card -->
            <div class="col-md-4 mb-4 animate-on-scroll is-visible stagger-5">
                <div class="card glass-card h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center py-4">
                        <div class="progress-ring color-orange mb-3">
                            <svg>
                                <circle class="progress-ring-bg" cx="60" cy="60" r="50"></circle>
                                <circle class="progress-ring-path" cx="60" cy="60" r="50" style="stroke-dashoffset: <?php echo 314 - (314 * min($services_count*10, 100)) / 100; ?>;"></circle>
                            </svg>
                            <div class="progress-ring-text"><?php echo $services_count; ?></div>
                        </div>
                        <h5 class="fw-bold">Services</h5>
                        <a href="services.php" class="btn btn-primary mt-3 w-100" aria-label="View Services"><i class="fa-solid fa-arrow-right"></i> Directory</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Kitale National Polytechnic | Jamii Connect.</p>
        </div>
    </footer>

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="app.js?v=2"></script>
    <script>
        // Fetch Advanced Analytics
        fetch('api_handler.php?action=get_analytics')
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    const d = res.data;
                    document.getElementById('statViews').innerText = d.total_views;
                    document.getElementById('statUsers').innerText = d.active_users;
                    document.getElementById('statPopularTitle').innerText = d.most_viewed.title;
                    document.getElementById('statPopularViews').innerText = d.most_viewed.views + ' views (' + d.most_viewed.type + ')';
                }
            });
    </script>
</body>
</html>
