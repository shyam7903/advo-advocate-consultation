<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$appointment_id || !$action) {
        echo "<script>alert('Invalid request.'); window.history.back();</script>";
        exit;
    }

    // Validate action
    if (!in_array($action, ['accept', 'reject'])) {
        echo "<script>alert('Invalid action.'); window.history.back();</script>";
        exit;
    }

    // Determine status value
    $new_status = ($action === 'accept') ? 'confirmed' : 'rejected';

    // Update the appointment status
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $appointment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment status updated successfully.'); window.location.href='advocate/dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating appointment.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
}
?>
