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
    $stmt = $conn->prepare('DELETE FROM announcements WHERE id = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header('Location: announcements.php?msg=deleted');
        exit();
    }
}

// Check for messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $msg = 'Record deleted successfully.';
        $msg_type = 'danger';
    } elseif ($_GET['msg'] === 'added') {
        $msg = 'Record added successfully.';
        $msg_type = 'success';
    } elseif ($_GET['msg'] === 'updated') {
        $msg = 'Record updated successfully.';
        $msg_type = 'success';
    }
}

// Variables for form
$edit_id = 0;
$edit_title = '';
$edit_content = '';
$is_edit = false;

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_id = intval($_GET['id']);
    $is_edit = true;
    $stmt = $conn->prepare('SELECT title, content FROM announcements WHERE id = ?');
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $edit_title = $row['title'];
        $edit_content = $row['content'];
    }
}

// Handle POST (Create / Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die('Unauthorized action.');
    }
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $id = intval($_POST['id'] ?? 0);

    if (empty($title) || empty($content)) {
        $msg = 'Please fill all required fields.';
        $msg_type = 'warning';
    } else {
        if ($id > 0) {
            // UPDATE
            $stmt = $conn->prepare('UPDATE announcements SET title=?, content=? WHERE id=?');
            $stmt->bind_param('ssi', $title, $content, $id);
            if ($stmt->execute()) {
                header('Location: announcements.php?msg=updated');
                exit();
            }
        } else {
            // INSERT
            $stmt = $conn->prepare('INSERT INTO announcements (title, content, user_id) VALUES (?, ?, ?)');
            $stmt->bind_param('ssi', $title, $content, $_SESSION['user_id']);
            if ($stmt->execute()) {
                header('Location: announcements.php?msg=added');
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
    <title>Manage Announcements - Jamii Connect</title>
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
        <h2>Manage Announcements</h2>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo $is_edit ? 'Edit Announcement' : 'Add New Announcement'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="announcements.php">
                            <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($edit_title); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea name="content" id="content" class="form-control" rows="4" required><?php echo htmlspecialchars($edit_content); ?></textarea>
                            </div>
                            <button type="submit" class="btn <?php echo $is_edit ? 'btn-warning' : 'btn-primary'; ?> w-100">
                                <?php echo $is_edit ? 'Update Record' : 'Add Record'; ?>
                            </button>
                            <?php if ($is_edit): ?>
                                <a href="announcements.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'col-md-8' : 'col-md-12'; ?>">
                <div class="card shadow-sm">
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query('SELECT * FROM announcements ORDER BY created_at DESC');
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['title']) . '</td>';
                                        echo '<td>' . htmlspecialchars(substr($row['content'], 0, 50)) . '...</td>';
                                        echo '<td>' . date('Y-m-d', strtotime($row['created_at'])) . '</td>';
                                        echo '<td class="text-end text-nowrap">';
                                        echo '<a href="announcement_details.php?id=' . $row['id'] . '" class="btn btn-sm btn-info me-1 text-white">View Details</a>';
                                        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                                            echo '<a href="?action=edit&id=' . $row['id'] . '" class="btn btn-sm btn-warning me-1">Edit</a>';
                                            echo '<a href="?action=delete&id=' . $row['id'] . '" class="btn btn-sm btn-danger delete-btn">Delete</a>';
                                        }
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="text-center">No announcements found.</td></tr>';
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
    <script src="app.js"></script>
</body>
</html>
