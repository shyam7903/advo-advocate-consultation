<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied.'); window.location.href='../users/login.php';</script>";
    exit;
}

include '../db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT filename FROM uploads WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($filename);
        $stmt->fetch();

        $filePath = "../uploads/" . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $deleteStmt = $conn->prepare("DELETE FROM uploads WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        $deleteStmt->execute();
    }
}

header("Location: view_documents.php");
exit;
