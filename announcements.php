<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Announcements - Jamii Connect</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand" href="index.php">Jamii Connect</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-line me-1"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="announcements.php"><i class="fa-solid fa-bullhorn me-1"></i> Announcements</a></li>
            <li class="nav-item"><a class="nav-link" href="events.php"><i class="fa-regular fa-calendar me-1"></i> Events</a></li>
            
            <li class="nav-item dropdown ms-2">
              <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-circle-user fa-lg"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container mt-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2>Announcements</h2>
            <div class="d-flex gap-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Search..." aria-label="Search announcements">
                <select id="categoryFilter" class="form-select" aria-label="Filter by category">
                    <option value="All">All Categories</option>
                    <option value="General">General</option>
                    <option value="Urgent">Urgent</option>
                    <option value="Community">Community</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
        </div>

        <div class="row">
            <?php if ($is_admin): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Publish Announcement</h5>
                    </div>
                    <div class="card-body">
                        <form id="createForm">
                            <input type="hidden" name="action" value="create_announcement">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-select" required>
                                    <option value="General">General</option>
                                    <option value="Urgent">Urgent</option>
                                    <option value="Community">Community</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="attachment" class="form-label">Poster/Document (Optional)</label>
                                <input type="file" name="attachment" id="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Publish</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="<?php echo $is_admin ? 'col-md-8' : 'col-md-12'; ?>">
                <div class="card shadow-sm">
                    <div class="card-body p-0 table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Title & Category</th>
                                    <th>Content</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr><td colspan="4" class="text-center py-5">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5 pb-3">
        <div class="container text-center">
            <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> Kitale National Polytechnic | Jamii Connect.</p>
        </div>
    </footer>

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
        timerProgressBar: true, background: 'var(--glass-bg)', color: 'var(--text-color)'
    });

    function loadData() {
        const kw = document.getElementById('searchInput').value;
        const cat = document.getElementById('categoryFilter').value;
        fetch(`api_handler.php?action=get_announcements&keyword=${encodeURIComponent(kw)}&category=${encodeURIComponent(cat)}`)
            .then(res => res.json())
            .then(res => {
                const tbody = document.getElementById('tableBody');
                tbody.innerHTML = '';
                if(res.status === 'success') {
                    if(res.data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-5"><p class="text-muted">No announcements found.</p></td></tr>`;
                    } else {
                        res.data.forEach((item, index) => {
                            const stagger = Math.min(index + 1, 5);
                            let tr = document.createElement('tr');
                            tr.className = `animate-on-scroll is-visible stagger-${stagger}`;
                            
                            let actions = `<a href="announcement_details.php?id=${item.id}" class="btn btn-sm btn-info text-white me-1" aria-label="View Details">View</a>`;
                            if(item.attachment_path) {
                                actions += `<a href="${item.attachment_path}" target="_blank" class="btn btn-sm btn-secondary me-1" aria-label="View Attachment">📎 File</a>`;
                            }
                            if(res.is_admin) {
                                actions += `<button onclick="deleteItem(${item.id})" class="btn btn-sm btn-danger" aria-label="Delete">Delete</button>`;
                            }
                            
                            tr.innerHTML = `
                                <td>
                                    <strong>${item.title}</strong><br>
                                    <span class="badge bg-secondary text-light">${item.category}</span>
                                </td>
                                <td>${item.content.substring(0, 50)}...</td>
                                <td>${new Date(item.created_at).toLocaleDateString()}</td>
                                <td class="text-end text-nowrap">${actions}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                } else {
                    Toast.fire({ icon: 'error', title: 'Failed to load data' });
                }
            });
    }

    document.getElementById('searchInput').addEventListener('keyup', loadData);
    document.getElementById('categoryFilter').addEventListener('change', loadData);

    // Initial Load
    document.addEventListener('DOMContentLoaded', loadData);

    <?php if ($is_admin): ?>
    document.getElementById('createForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fetch('api_handler.php', { method: 'POST', body: fd })
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    Toast.fire({ icon: 'success', title: res.message });
                    this.reset();
                    loadData();
                } else {
                    Toast.fire({ icon: 'error', title: res.message });
                }
            });
    });

    function deleteItem(id) {
        Swal.fire({
            title: 'Delete Announcement?',
            text: "This action is logged and cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: 'rgba(0,0,0,0.1)',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('action', 'delete_announcement');
                fd.append('id', id);
                fetch('api_handler.php', { method: 'POST', body: fd })
                    .then(res => res.json())
                    .then(res => {
                        if(res.status === 'success') {
                            Toast.fire({ icon: 'success', title: res.message });
                            loadData();
                        } else {
                            Toast.fire({ icon: 'error', title: res.message });
                        }
                    });
            }
        });
    }
    <?php endif; ?>
    </script>
</body>
</html>
