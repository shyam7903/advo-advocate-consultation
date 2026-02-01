<?php
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'advocate') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    if (!in_array($action, ['accept', 'reject'])) {
        die("Invalid action");
    }

    $status = ($action === 'accept') ? 'accepted' : 'rejected';

    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $appointment_id);
    $stmt->execute();

    echo "<script>alert('Appointment $status'); window.location.href='dashboard.php';</script>";
} else {
    echo "Invalid request.";
}
?>
