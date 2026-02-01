<?php
include '../db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['message_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized request"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$message_id = intval($_POST['message_id']);

// Verify if the message belongs to the user
$check = $conn->prepare("SELECT id FROM messages WHERE id = ? AND sender_id = ?");
$check->bind_param("ii", $message_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "You can only delete your own messages."]);
    exit;
}

// Delete the message
$delete = $conn->prepare("DELETE FROM messages WHERE id = ?");
$delete->bind_param("i", $message_id);

if ($delete->execute()) {
    echo json_encode(["success" => true, "message" => "Message deleted"]);
} else {
    echo json_encode(["error" => "Failed to delete message"]);
}
?>
