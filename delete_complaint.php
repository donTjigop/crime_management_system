<?php
include 'config_db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM complaints WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: complaints.php');
exit();
