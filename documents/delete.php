<?php
include '../includes/header.php';
include '../db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Access denied. Please login.'); window.location.href='../users/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doc_id'])) {
    $doc_id = intval($_POST['doc_id']);

    // Validate that the document belongs to the current user
    $stmt = $conn->prepare("SELECT file_path FROM documents WHERE id = ? AND uploaded_by = ?");
    $stmt->bind_param("ii", $doc_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $doc = $result->fetch_assoc();
        $file_path = $doc['file_path'];

        // Delete the file from the server if it exists
        if (!empty($file_path) && file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete the document record from the database
        $del_stmt = $conn->prepare("DELETE FROM documents WHERE id = ?");
        $del_stmt->bind_param("i", $doc_id);
        $del_stmt->execute();

        echo "<script>alert('Document deleted successfully.'); window.location.href='view.php';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid document or permission denied.'); window.location.href='view.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='view.php';</script>";
    exit;
}
?>
