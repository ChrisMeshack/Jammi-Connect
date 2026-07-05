<?php
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jamii Connect - Home</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
          <ul class="navbar-nav ms-auto align-items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-line me-1"></i> Dashboard</a></li>
                <li class="nav-item dropdown ms-2">
                  <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-circle-user fa-lg"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                  </ul>
                </li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="login.php"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Login</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php"><i class="fa-solid fa-user-plus me-1"></i> Register</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container animate-on-scroll">
            <h1>Stay updated, stay connected.</h1>
            <p class="lead">Your one-stop community resource and information portal.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-light btn-lg me-2 fw-bold text-primary"><i class="fa-solid fa-rocket"></i> Get Started</a>
                <a href="login.php" class="btn btn-outline-light btn-lg"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-light btn-lg fw-bold text-primary"><i class="fa-solid fa-gauge-high"></i> Go to Dashboard</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Impact Gallery Section -->
    <div class="container mt-5 animate-on-scroll">
        <h3 class="fw-bold mb-4 text-center" style="color: var(--color-rose);"><i class="fa-solid fa-camera-retro"></i> Our Impact</h3>
        <div class="card glass-card border-0 p-2 shadow-lg">
            <div id="impactCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#impactCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#impactCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#impactCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    <button type="button" data-bs-target="#impactCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                </div>
                <div class="carousel-inner" style="border-radius: 1.2rem; overflow: hidden;">
                    <div class="carousel-item active" data-bs-interval="4000">
                        <img src="uploads/Image1.png" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Community Impact 1">
                        <div class="carousel-caption d-none d-md-block glass-card p-3 mb-4" style="border-radius: 1rem;">
                            <h5 class="fw-bold m-0" style="color: var(--color-dark);">Connecting the Community</h5>
                        </div>
                    </div>
                    <div class="carousel-item" data-bs-interval="4000">
                        <img src="uploads/Image2.png" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Community Impact 2">
                        <div class="carousel-caption d-none d-md-block glass-card p-3 mb-4" style="border-radius: 1rem;">
                            <h5 class="fw-bold m-0" style="color: var(--color-dark);">Empowering People</h5>
                        </div>
                    </div>
                    <div class="carousel-item" data-bs-interval="4000">
                        <img src="uploads/Image3.png" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Community Impact 3">
                        <div class="carousel-caption d-none d-md-block glass-card p-3 mb-4" style="border-radius: 1rem;">
                            <h5 class="fw-bold m-0" style="color: var(--color-dark);">Driving Change</h5>
                        </div>
                    </div>
                    <div class="carousel-item" data-bs-interval="4000">
                        <img src="uploads/Image4.png" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Community Impact 4">
                        <div class="carousel-caption d-none d-md-block glass-card p-3 mb-4" style="border-radius: 1rem;">
                            <h5 class="fw-bold m-0" style="color: var(--color-dark);">Building a Brighter Future</h5>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#impactCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: var(--color-dark); border-radius: 50%; padding: 1.5rem;"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#impactCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: var(--color-dark); border-radius: 50%; padding: 1.5rem;"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Announcements Ticker -->
    <div class="container mt-4 animate-on-scroll">
        <div class="ticker-wrap">
            <div class="ticker-track">
                <?php
                $ann_ticker = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
                if ($ann_ticker->num_rows > 0) {
                    while ($row = $ann_ticker->fetch_assoc()) {
                        echo '<div class="ticker-item"><span style="color:var(--primary-color);">📢</span> <strong>' . htmlspecialchars($row['title']) . '</strong> - ' . htmlspecialchars(substr($row['content'], 0, 50)) . '... <a href="announcement_details.php?id=' . $row['id'] . '" class="text-decoration-none">Read More</a></div>';
                    }
                } else {
                    echo '<div class="ticker-item">No new announcements at this time.</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5 flex-grow-1">
        <div class="row">
            
            <!-- Announcements Feed -->
            <div class="col-md-6 mb-5 animate-on-scroll">
                <h3 class="fw-bold mb-4" style="color: var(--primary-color);">
                    <svg width="28" height="28" class="me-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    Latest Announcements
                </h3>
                <div class="card glass-card border-0 p-3">
                    <div class="scrollable-feed">
                        <?php
                        $ann = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
                        if ($ann->num_rows > 0) {
                            while ($row = $ann->fetch_assoc()) {
                                ?>
                                <div class="feed-item">
                                    <h5 class="mb-1 text-dark fw-bold"><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <div class="feed-date mb-2">🗓️ <?php echo date('F j, Y', strtotime($row['created_at'])); ?></div>
                                    <p class="mb-2 text-muted"><?php echo htmlspecialchars(substr($row['content'], 0, 80)); ?>...</p>
                                    <a href="announcement_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p class="text-muted p-3">No announcements at this time.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Events Section -->
            <div class="col-md-6 mb-5 animate-on-scroll">
                <h3 class="fw-bold mb-4" style="color: var(--accent-green);">
                    <svg width="28" height="28" class="me-2 svg-rotate" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Upcoming Events
                </h3>
                <div class="row">
                    <?php
                    $ev = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 4");
                    if ($ev->num_rows > 0) {
                        while ($row = $ev->fetch_assoc()) {
                            ?>
                            <div class="col-12 mb-3">
                                <div class="card glass-card border-0 h-100 p-3" style="border-left: 4px solid #10b981 !important;">
                                    <h5 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <div class="feed-date mb-2 text-success" style="color: var(--accent-green) !important;">
                                        <svg width="16" height="16" class="me-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <?php echo htmlspecialchars($row['venue']); ?> | 🗓️ <?php echo htmlspecialchars($row['event_date']); ?>
                                    </div>
                                    <div class="mt-2">
                                        <a href="event_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success text-white" style="background-color: var(--accent-green); border: none;">RSVP / Book Seat</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p class="text-muted p-3">No upcoming events scheduled.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="mt-4 mb-5 animate-on-scroll">
            <h3 class="fw-bold mb-4 text-center" style="color: var(--accent-gold);">🏢 Public Services Directory</h3>
            <div class="services-grid">
                <?php
                $srv = $conn->query("SELECT * FROM services ORDER BY name ASC");
                if ($srv->num_rows > 0) {
                    while ($row = $srv->fetch_assoc()) {
                        ?>
                        <div class="card glass-card border-0 p-4 text-center">
                            <div class="icon-circle icon-gold mx-auto mb-3 text-dark">
                                <svg class="svg-pulse" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <h5 class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="text-muted small mb-2">🕒 <?php echo htmlspecialchars($row['schedule']); ?></p>
                            <p class="text-muted small mb-3">📍 <?php echo htmlspecialchars($row['location']); ?></p>
                            <a href="tel:<?php echo htmlspecialchars($row['contact']); ?>" class="btn btn-outline-warning text-dark w-100" style="border-color: var(--accent-gold);">Contact: <?php echo htmlspecialchars($row['contact']); ?></a>
                        </div>
                        <?php
                    }
                }
                ?>
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
