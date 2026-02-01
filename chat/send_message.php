<?php
// send_message.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db.php';

// JSON response
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status"=>"error","message"=>"Not logged in"]);
    exit;
}
$currentUserId = (int)$_SESSION['user_id'];

$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
$chat_with = isset($_POST['chat_with']) ? intval($_POST['chat_with']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate IDs
if ($appointment_id <= 0 || $chat_with <= 0) {
    echo json_encode(["status"=>"error","message"=>"Invalid chat request"]);
    exit;
}

// Confirm appointment exists and user participates
$check = $conn->prepare("SELECT client_id, advocate_id, status FROM appointments WHERE id = ? LIMIT 1");
$check->bind_param("i", $appointment_id);
$check->execute();
$cr = $check->get_result();
if ($cr->num_rows === 0) {
    echo json_encode(["status"=>"error","message"=>"Appointment not found"]);
    exit;
}
$apt = $cr->fetch_assoc();
if (!in_array($apt['status'], ['confirmed','accepted'])) {
    echo json_encode(["status"=>"error","message"=>"Appointment not confirmed"]);
    exit;
}
if (!($currentUserId === (int)$apt['client_id'] || $currentUserId === (int)$apt['advocate_id'])) {
    echo json_encode(["status"=>"error","message"=>"You are not part of this appointment"]);
    exit;
}
$check->close();

// Image upload (optional)
$image_path = null;
if (!empty($_FILES['image']['name'])) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg'])) {
        echo json_encode(["status"=>"error","message"=>"Only JPG/JPEG allowed"]);
        exit;
    }
    $uploadDir = __DIR__ . '/../uploads/chat_images/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $fname = uniqid('chat_') . '.' . $ext;
    $dest = $uploadDir . $fname;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        echo json_encode(["status"=>"error","message"=>"Image saving failed"]);
        exit;
    }
    // Save DB path relative to project root (so get_messages can build correct URL)
    $image_path = 'uploads/chat_images/' . $fname; // when outputting, we prefix /advo/
}

// require at least text or image
if ($message === '' && $image_path === null) {
    echo json_encode(["status"=>"error","message"=>"Message cannot be empty"]);
    exit;
}

// Insert row
$ins = $conn->prepare("INSERT INTO messages (appointment_id, sender_id, receiver_id, message, image, is_read, sent_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
$ins->bind_param("iiiss", $appointment_id, $currentUserId, $chat_with, $message, $image_path);
if ($ins->execute()) {
    echo json_encode(["status"=>"success"]);
} else {
    echo json_encode(["status"=>"error","message"=>$ins->error]);
}
$ins->close();
$conn->close();
