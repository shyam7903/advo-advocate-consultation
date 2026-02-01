<?php
include '../db.php';

session_start();

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = "User not logged in.";
    echo json_encode($response);
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;
$appointment_id = $_POST['appointment_id'] ?? null;

if (!$receiver_id || !$appointment_id) {
    $response['message'] = "Missing receiver or appointment.";
    echo json_encode($response);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = "Image upload failed.";
    echo json_encode($response);
    exit;
}

// Validate file type
$allowed_ext = ['jpg', 'jpeg'];
$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed_ext)) {
    $response['message'] = "Only JPG images are allowed.";
    echo json_encode($response);
    exit;
}

// Prepare upload path
$uploadDir = '../uploads/chat_images/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // create folder if not exists
}

$uniqueName = uniqid('chat_', true) . '.' . $ext;
$uploadPath = $uploadDir . $uniqueName;
$relativePath = 'uploads/chat_images/' . $uniqueName;

// Move file
if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
    $response['message'] = "Error saving the uploaded image.";
    echo json_encode($response);
    exit;
}

// Save to messages table
$stmt = $conn->prepare("INSERT INTO messages (appointment_id, sender_id, receiver_id, image, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiis", $appointment_id, $sender_id, $receiver_id, $relativePath);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Image uploaded successfully.";
    $response['image_path'] = $relativePath;
} else {
    $response['message'] = "Failed to save image to database.";
}

echo json_encode($response);
