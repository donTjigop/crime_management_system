<?php
include 'includes/header.php';
include 'config_db.php';

// Existing PHP logic remains unchanged (filtering, pagination, etc.)
?>

<!-- HTML Structure -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 bg-dark text-white vh-100 p-4">
            <!-- Sidebar Content -->
            <div class="text-center mb-4">
                <img src="../assets/crime-icon-removebg-preview.png" alt="Logo" class="img-fluid rounded-circle" style="max-width: 120px;">
            </div>
            <h2 class="text-center text-uppercase mb-4">CRMS</h2>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-white btn btn-outline-light" href="dashboard.php">Dashboard</a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white btn btn-outline-light" href="users.php">Manage Users</a>
                </li>
                <?php endif; ?>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white btn btn-outline-light" href="complaints.php">Complaints</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white btn btn-outline-light" href="reports.php">Reports</a>
                </li>
                <li class="nav-item mt-auto">
                    <a class="nav-link text-danger btn btn-outline-danger" href="/logout.php">Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 bg-light p-4">
            <h2 class="mb-4 text-primary border-bottom pb-2">Generate Crime Report</h2>

            <!-- Filter Form with Enhanced Design -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">Filter Complaints</div>
                <div class="card-body">
                    <form method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Complaint Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="Pending" <?= isset($_GET['status']) && $_GET['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="In Progress" <?= isset($_GET['status']) && $_GET['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="Resolved" <?= isset($_GET['status']) && $_GET['status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            // Example: Assuming you have already set up the database connection and applied filters.

            // Filter query
            $where_clause = "1"; // Default to show all complaints
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $status = $_GET['status'];
                $where_clause .= " AND status = :status";
            }
            if (isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
                $start_date = $_GET['start_date'];
                $end_date = $_GET['end_date'];
                $where_clause .= " AND created_at BETWEEN :start_date AND :end_date";
            }

            // Pagination setup
            $perPage = 10;
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($currentPage - 1) * $perPage;

            // Get total number of complaints based on the filter criteria
            $totalComplaintsQuery = $conn->prepare("SELECT COUNT(*) FROM complaints WHERE " . $where_clause);
            if (isset($status)) {
                $totalComplaintsQuery->bindParam(':status', $status);
            }
            if (isset($start_date) && isset($end_date)) {
                $totalComplaintsQuery->bindParam(':start_date', $start_date);
                $totalComplaintsQuery->bindParam(':end_date', $end_date);
            }
            $totalComplaintsQuery->execute();
            $totalComplaints = (int)$totalComplaintsQuery->fetchColumn();

            // Calculate total pages based on the number of complaints
            $totalPages = ($totalComplaints > 0) ? ceil($totalComplaints / $perPage) : 1;

            // Fetch complaints based on filters and pagination
            $sql = "SELECT * FROM complaints WHERE " . $where_clause . " ORDER BY created_at DESC LIMIT :offset, :perPage";
            $stmt = $conn->prepare($sql);

            // Bind parameters for the query
            if (isset($status)) {
                $stmt->bindParam(':status', $status);
            }
            if (isset($start_date) && isset($end_date)) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <!-- Complaints Table with Scroll -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">Complaints Report</div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-hover table-bordered table-responsive">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Complainant Name</th>
                                <th>Complaint Details</th>
                                <th>Status</th>
                                <th>Date Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($complaints): ?>
                                <?php foreach ($complaints as $complaint): ?>
                                    <tr>
                                        <td><?= $complaint['id']; ?></td>
                                        <td><?= htmlspecialchars($complaint['complainant_name']); ?></td>
                                        <td><?= htmlspecialchars($complaint['complaint_details']); ?></td>
                                        <td><span class="badge bg-info"><?= htmlspecialchars($complaint['status']); ?></span></td>
                                        <td><?= date('d M Y, h:i A', strtotime($complaint['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No complaints found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            


                                

