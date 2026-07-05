<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$action = $_REQUEST['action'] ?? '';

// Helper function to log audit
function log_audit($conn, $user_id, $action, $table_name, $record_id) {
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $user_id, $action, $table_name, $record_id);
    $stmt->execute();
}

// Helper to handle file upload
function handle_upload($file) {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $file['size'] <= 5000000) { // 5MB limit
            $filename = uniqid() . '.' . $ext;
            $dest = 'uploads/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                return $dest;
            }
        }
    }
    return null;
}

try {
    switch ($action) {
        // --- ANNOUNCEMENTS ---
        case 'get_announcements':
            $keyword = $_GET['keyword'] ?? '';
            $category = $_GET['category'] ?? '';
            
            $sql = "SELECT a.*, u.full_name as author_name FROM announcements a LEFT JOIN users u ON a.user_id = u.id WHERE 1=1";
            $params = [];
            $types = "";
            
            if ($keyword) {
                $sql .= " AND (a.title LIKE ? OR a.content LIKE ?)";
                $params[] = "%$keyword%";
                $params[] = "%$keyword%";
                $types .= "ss";
            }
            if ($category && $category !== 'All') {
                $sql .= " AND a.category = ?";
                $params[] = $category;
                $types .= "s";
            }
            $sql .= " ORDER BY a.created_at DESC";
            
            $stmt = $conn->prepare($sql);
            if ($types) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(['status' => 'success', 'data' => $data, 'is_admin' => $is_admin]);
            break;

        case 'create_announcement':
            if (!$is_admin) { echo json_encode(['status' => 'error', 'message' => 'Forbidden']); exit; }
            $title = $_POST['title'] ?? '';
            $category = $_POST['category'] ?? 'General';
            $content = $_POST['content'] ?? '';
            
            $attachment_path = handle_upload($_FILES['attachment'] ?? null);
            
            $stmt = $conn->prepare("INSERT INTO announcements (title, category, content, user_id, attachment_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $title, $category, $content, $user_id, $attachment_path);
            if ($stmt->execute()) {
                $new_id = $stmt->insert_id;
                log_audit($conn, $user_id, 'CREATE', 'announcements', $new_id);
                echo json_encode(['status' => 'success', 'message' => 'Announcement created']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error']);
            }
            break;
            
        case 'delete_announcement':
            if (!$is_admin) { echo json_encode(['status' => 'error', 'message' => 'Forbidden']); exit; }
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                log_audit($conn, $user_id, 'DELETE', 'announcements', $id);
                echo json_encode(['status' => 'success', 'message' => 'Announcement deleted']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error']);
            }
            break;
            
        // --- EVENTS ---
        case 'get_events':
            $keyword = $_GET['keyword'] ?? '';
            $category = $_GET['category'] ?? '';
            
            $sql = "SELECT * FROM events WHERE 1=1";
            $params = [];
            $types = "";
            
            if ($keyword) {
                $sql .= " AND (title LIKE ? OR description LIKE ? OR venue LIKE ?)";
                $params[] = "%$keyword%";
                $params[] = "%$keyword%";
                $params[] = "%$keyword%";
                $types .= "sss";
            }
            if ($category && $category !== 'All') {
                $sql .= " AND category = ?";
                $params[] = $category;
                $types .= "s";
            }
            $sql .= " ORDER BY event_date ASC";
            
            $stmt = $conn->prepare($sql);
            if ($types) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(['status' => 'success', 'data' => $data, 'is_admin' => $is_admin]);
            break;
            
        case 'create_event':
            if (!$is_admin) { echo json_encode(['status' => 'error', 'message' => 'Forbidden']); exit; }
            $title = $_POST['title'] ?? '';
            $category = $_POST['category'] ?? 'General';
            $description = $_POST['description'] ?? '';
            $event_date = $_POST['event_date'] ?? '';
            $venue = $_POST['venue'] ?? '';
            
            $attachment_path = handle_upload($_FILES['attachment'] ?? null);
            
            $stmt = $conn->prepare("INSERT INTO events (title, category, description, event_date, venue, attachment_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $category, $description, $event_date, $venue, $attachment_path);
            if ($stmt->execute()) {
                $new_id = $stmt->insert_id;
                log_audit($conn, $user_id, 'CREATE', 'events', $new_id);
                echo json_encode(['status' => 'success', 'message' => 'Event created']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error']);
            }
            break;

        case 'delete_event':
            if (!$is_admin) { echo json_encode(['status' => 'error', 'message' => 'Forbidden']); exit; }
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                log_audit($conn, $user_id, 'DELETE', 'events', $id);
                echo json_encode(['status' => 'success', 'message' => 'Event deleted']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error']);
            }
            break;
            
        // --- ANALYTICS ---
        case 'get_analytics':
            $analytics = [];
            // Total Announcements Views
            $res = $conn->query("SELECT SUM(views) as v FROM announcements");
            $analytics['announcements_views'] = $res->fetch_assoc()['v'] ?? 0;
            // Total Events Views
            $res = $conn->query("SELECT SUM(views) as v FROM events");
            $analytics['events_views'] = $res->fetch_assoc()['v'] ?? 0;
            
            $analytics['total_views'] = $analytics['announcements_views'] + $analytics['events_views'];
            
            // Active users in last 24 hours
            $res = $conn->query("SELECT COUNT(*) as c FROM users WHERE last_login > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $analytics['active_users'] = $res->fetch_assoc()['c'] ?? 0;
            
            // Most viewed item (combining announcements and events)
            $res1 = $conn->query("SELECT 'Announcement' as type, title, views FROM announcements ORDER BY views DESC LIMIT 1");
            $res2 = $conn->query("SELECT 'Event' as type, title, views FROM events ORDER BY views DESC LIMIT 1");
            $a1 = $res1->fetch_assoc();
            $e1 = $res2->fetch_assoc();
            
            $most_viewed = null;
            if ($a1 && $e1) {
                $most_viewed = ($a1['views'] > $e1['views']) ? $a1 : $e1;
            } else {
                $most_viewed = $a1 ?: $e1;
            }
            $analytics['most_viewed'] = $most_viewed ?: ['title' => 'None', 'views' => 0];
            
            echo json_encode(['status' => 'success', 'data' => $analytics]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
