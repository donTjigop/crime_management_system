<?php
session_start();
include 'includes/header.php'; 
include 'config_db.php'; 

// Handle form submission for adding a complaint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_complaint'])) {
    $complainant_name = htmlspecialchars(trim($_POST['complainant_name']));
    $complaint_details = htmlspecialchars(trim($_POST['complaint_details']));
    
    if (!empty($complainant_name) && !empty($complaint_details)) {
        try {
            $stmt = $conn->prepare("INSERT INTO complaints (complainant_name, complaint_details, status) VALUES (:complainant_name, :complaint_details, 'Pending')");
            $stmt->execute([
                ':complainant_name' => $complainant_name,
                ':complaint_details' => $complaint_details
            ]);
            $success_message = "Complaint added successfully.";
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "All fields are required.";
    }
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = htmlspecialchars($_POST['status']);
    $complaint_id = intval($_POST['complaint_id']);
    
    try {
        $stmt = $conn->prepare("UPDATE complaints SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $complaint_id]);
        $success_message = "Status updated successfully.";
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch complaints with optional search and pagination
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$where_clause = 'WHERE 1=1';
$params = [];

if ($search_query) {
    $where_clause .= " AND (complainant_name LIKE :search OR complaint_details LIKE :search)";
    $params[':search'] = "%$search_query%";
}

if ($status_filter && $status_filter !== 'All') {
    $where_clause .= " AND status = :status";
    $params[':status'] = $status_filter;
}

// Pagination setup
$limit = 5; // Number of complaints per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_complaints = $conn->prepare("SELECT COUNT(*) FROM complaints $where_clause");
$total_complaints->execute($params);
$total_pages = ceil($total_complaints->fetchColumn() / $limit);

$stmt = $conn->prepare("SELECT * FROM complaints $where_clause ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML Structure -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 bg-dark text-white vh-100 p-4">
            <div class="text-center mb-4">
                <img src="../assets/crime-icon-removebg-preview.png" alt="Logo" class="img-fluid rounded-circle" style="max-width: 120px;">
            </div>
            <h2 class="text-center text-uppercase mb-4">CRMS</h2>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a class="nav-link text-white btn btn-outline-light" href="dashboard.php">Dashboard</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item mb-2"><a class="nav-link text-white btn btn-outline-light" href="users.php">Manage Users</a></li>
                <?php endif; ?>
                <li class="nav-item mb-2"><a class="nav-link text-primary btn btn-outline-light" href="complaints.php">Complaints</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-white btn btn-outline-light" href="reports.php">Reports</a></li>
                <li class="nav-item mt-auto"><a class="nav-link text-danger btn btn-outline-danger" href="/logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 bg-light">
            <div class="container mt-4">
                <h2 class="mb-4 text-primary border-bottom pb-2">Manage Complaints</h2>

                <!-- Display Success or Error Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Add Complaint Form -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">Add New Complaint</div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="complainant_name" class="form-label">Complainant Name</label>
                                <input type="text" name="complainant_name" id="complainant_name" class="form-control" placeholder="Enter complainant's name" required>
                            </div>
                            <div class="mb-3">
                                <label for="complaint_details" class="form-label">Complaint Details</label>
                                <textarea name="complaint_details" id="complaint_details" rows="4" class="form-control" placeholder="Describe the complaint" required></textarea>
                            </div>
                            <button type="submit" name="add_complaint" class="btn btn-success btn-lg">Submit Complaint</button>
                        </form>
                    </div>
                </div>

                <!-- Search and Filter -->
                <form method="GET" class="mb-4 d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search complaints..." value="<?= htmlspecialchars($search_query); ?>">
                    <select name="status" class="form-select me-2">
                        <option value="All">All Statuses</option>
                        <option value="Pending" <?= $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Resolved" <?= $status_filter == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>

                <!-- Complaints Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">List of Complaints</div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Complainant Name</th>
                                    <th>Complaint Details</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($complaints as $complaint): ?>
                                    <tr>
                                        <td><?= $complaint['id']; ?></td>
                                        <td><?= htmlspecialchars($complaint['complainant_name']); ?></td>
                                        <td><?= htmlspecialchars($complaint['complaint_details']); ?></td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="complaint_id" value="<?= $complaint['id']; ?>">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit();">
                                                    <option value="Pending" <?= $complaint['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="In Progress" <?= $complaint['status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                    <option value="Resolved" <?= $complaint['status'] == 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td><?= date('d M Y, h:i A', strtotime($complaint['created_at'])); ?></td>
                                        <td>
                                            <a href="edit_complaint.php?id=<?= $complaint['id']; ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                                            <a href="delete_complaint.php?id=<?= $complaint['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this complaint?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-3">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?= $i; ?>&search=<?= urlencode($search_query); ?>&status=<?= urlencode($status_filter); ?>">
                                        <?= $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
