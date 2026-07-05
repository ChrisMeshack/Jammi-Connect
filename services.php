<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
require_once 'db.php';

$msg = '';
$msg_type = '';

// Handle DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die('Unauthorized action.');
    }
    $id = intval($_GET['id']);
    $stmt = $conn->prepare('DELETE FROM services WHERE id = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header('Location: services.php?msg=deleted');
        exit();
    }
}

// Check for messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $msg = 'Service deleted successfully.';
        $msg_type = 'danger';
    } elseif ($_GET['msg'] === 'added') {
        $msg = 'Service added successfully.';
        $msg_type = 'success';
    } elseif ($_GET['msg'] === 'updated') {
        $msg = 'Service updated successfully.';
        $msg_type = 'success';
    }
}

// Variables for form
$edit_id = 0;
$edit_name = '';
$edit_schedule = '';
$edit_location = '';
$edit_contact = '';
$is_edit = false;

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $edit_id = intval($_GET['id']);
        $is_edit = true;
        $stmt = $conn->prepare('SELECT name, schedule, location, contact FROM services WHERE id = ?');
        $stmt->bind_param('i', $edit_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $edit_name = $row['name'];
            $edit_schedule = $row['schedule'];
            $edit_location = $row['location'];
            $edit_contact = $row['contact'];
        }
    }
}

// Handle POST (Create / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die('Unauthorized action.');
    }
    $name = trim($_POST['name'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $id = intval($_POST['id'] ?? 0);

    if (empty($name)) {
        $msg = 'Please provide at least a service name.';
        $msg_type = 'warning';
    } else {
        if ($id > 0) {
            // UPDATE
            $stmt = $conn->prepare('UPDATE services SET name=?, schedule=?, location=?, contact=? WHERE id=?');
            $stmt->bind_param('ssssi', $name, $schedule, $location, $contact, $id);
            if ($stmt->execute()) {
                header('Location: services.php?msg=updated');
                exit();
            }
        } else {
            // INSERT
            $stmt = $conn->prepare('INSERT INTO services (name, schedule, location, contact) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $name, $schedule, $location, $contact);
            if ($stmt->execute()) {
                header('Location: services.php?msg=added');
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Services - Jamii Connect</title>
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
            <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
            <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5 flex-grow-1">
        <h2>Manage Services</h2>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="col-md-4 mb-4">
                <div class="card glass-card border-0 shadow-lg mb-4">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold" style="color: var(--color-dark);"><?php echo $is_edit ? 'Edit Service' : 'Add New Service'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="services.php" class="glass-form border-0 p-3 shadow-none">
                            <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name *</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($edit_name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="schedule" class="form-label">Schedule</label>
                                <input type="text" name="schedule" id="schedule" class="form-control" value="<?php echo htmlspecialchars($edit_schedule); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" name="location" id="location" class="form-control" value="<?php echo htmlspecialchars($edit_location); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact</label>
                                <input type="text" name="contact" id="contact" class="form-control" value="<?php echo htmlspecialchars($edit_contact); ?>">
                            </div>
                            <button type="submit" class="btn <?php echo $is_edit ? 'btn-warning' : 'btn-primary'; ?> w-100">
                                <?php echo $is_edit ? 'Update Service' : 'Add Service'; ?>
                            </button>
                            <?php if ($is_edit): ?>
                                <a href="services.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'col-md-8' : 'col-md-12'; ?>">
                <div class="card glass-card border-0 shadow-lg">
                    <div class="card-body p-0 table-responsive" style="border-radius: 1.5rem; overflow: hidden;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>Name</th>
                                    <th>Schedule</th>
                                    <th>Location</th>
                                    <th>Contact</th>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <th class="text-end">Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query('SELECT * FROM services ORDER BY name ASC');
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['schedule']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['location']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['contact']) . '</td>';
                                        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                                            echo '<td class="text-end text-nowrap">';
                                            echo '<a href="?action=edit&id=' . $row['id'] . '" class="btn btn-sm btn-warning me-1">Edit</a>';
                                            echo '<a href="?action=delete&id=' . $row['id'] . '" class="btn btn-sm btn-danger delete-btn">Delete</a>';
                                            echo '</td>';
                                        }
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">No services found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
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
    <script src="app.js?v=2"></script>
</body>
</html>
