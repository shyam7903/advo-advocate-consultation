<?php
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];

if (!isset($_FILES['file'])) {
    die("No file uploaded.");
}

$file = $_FILES['file'];
$filename = basename($file['name']);
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed_types)) {
    die("Only PDF, JPG, JPEG, PNG allowed.");
}

$unique_name = time() . "_" . $filename;
$target_path = "../uploads/" . $unique_name;

if (move_uploaded_file($file['tmp_name'], $target_path)) {
    $stmt = $conn->prepare("INSERT INTO uploads (user_id, filename) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $unique_name);
    $stmt->execute();
    echo "<script>alert('File uploaded successfully'); window.location.href='dashboard.php';</script>";
} else {
    echo "<script>alert('File upload failed'); window.location.href='dashboard.php';</script>";
}
?>
