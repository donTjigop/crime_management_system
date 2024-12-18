<?php
session_start();
include 'includes/header.php';
include 'config_db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: complaints.php'); // Redirect if no ID is provided
    exit();
}

$complaint_id = $_GET['id'];

// Fetch the existing complaint details
$stmt = $conn->prepare("SELECT * FROM complaints WHERE id = :id");
$stmt->execute([':id' => $complaint_id]);
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) {
    die("Complaint not found!");
}

// Handle form submission for updating the complaint
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complainant_name = htmlspecialchars(trim($_POST['complainant_name']));
    $complaint_details = htmlspecialchars(trim($_POST['complaint_details']));
    $status = htmlspecialchars(trim($_POST['status']));

    if (!empty($complainant_name) && !empty($complaint_details) && !empty($status)) {
        try {
            $update_stmt = $conn->prepare("UPDATE complaints SET complainant_name = :complainant_name, complaint_details = :complaint_details, status = :status WHERE id = :id");
            $update_stmt->execute([
                ':complainant_name' => $complainant_name,
                ':complaint_details' => $complaint_details,
                ':status' => $status,
                ':id' => $complaint_id
            ]);
            $success_message = "Complaint updated successfully.";
            header('Location: complaints.php'); // Redirect after successful update
            exit();
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<div class="container mt-4">
    <h2 class="text-primary border-bottom pb-2">Edit Complaint</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="complainant_name" class="form-label">Complainant Name</label>
            <input type="text" name="complainant_name" id="complainant_name" class="form-control" value="<?= htmlspecialchars($complaint['complainant_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="complaint_details" class="form-label">Complaint Details</label>
            <textarea name="complaint_details" id="complaint_details" rows="4" class="form-control" required><?= htmlspecialchars($complaint['complaint_details']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="Pending" <?= $complaint['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?= $complaint['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="Resolved" <?= $complaint['status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Complaint</button>
        <a href="complaints.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
