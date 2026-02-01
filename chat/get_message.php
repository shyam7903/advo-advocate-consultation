<?php
// get_messages.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "<p class='no-messages'>Not logged in.</p>";
    exit;
}
$currentUserId = (int)$_SESSION['user_id'];

$appointmentId = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;
$chatWith = isset($_GET['chat_with']) ? intval($_GET['chat_with']) : 0;

if ($appointmentId <= 0 || $chatWith <= 0) {
    echo "<p class='no-messages'>Select a user to start chatting.</p>";
    exit;
}

// Verify appointment belongs to the user and is confirmed/accepted
$vk = $conn->prepare("SELECT id, client_id, advocate_id, status FROM appointments WHERE id = ? LIMIT 1");
$vk->bind_param("i", $appointmentId);
$vk->execute();
$rv = $vk->get_result();
if ($rv->num_rows === 0) {
    echo "<p class='no-messages'>Appointment not found.</p>";
    exit;
}
$apt = $rv->fetch_assoc();
if (!in_array($apt['status'], ['confirmed','accepted'])) {
    echo "<p class='no-messages'>Appointment not confirmed.</p>";
    exit;
}
if (!($currentUserId == (int)$apt['client_id'] || $currentUserId == (int)$apt['advocate_id'])) {
    echo "<p class='no-messages'>You are not part of this chat.</p>";
    exit;
}
$vk->close();

// Fetch messages
$stmt = $conn->prepare("
    SELECT m.id, m.sender_id, m.receiver_id, m.message, m.image, m.sent_at, u.name AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.appointment_id = ?
      AND ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
    ORDER BY m.sent_at ASC
");
$stmt->bind_param("iiiii", $appointmentId, $currentUserId, $chatWith, $chatWith, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='no-messages'>No messages yet. Say hello 👋</p>";
    exit;
}

while ($row = $result->fetch_assoc()) {
    $isSent = ($row['sender_id'] == $currentUserId) ? 'sent' : 'received';
    echo "<div class='chat-message {$isSent}'>";
    if (!empty($row['message'])) {
        echo "<p>" . nl2br(htmlspecialchars($row['message'])) . "</p>";
    }
    if (!empty($row['image'])) {
        // ensure correct root path for image
        $imgSrc = '/' . trim($row['image'], '/'); // row['image'] should be like "advo/uploads/..."
        // If stored as "uploads/..." we want "/advo/uploads/..."
        if (strpos($imgSrc, '/advo/') !== 0) {
            $imgSrc = '/advo/' . ltrim($row['image'], '/');
        }
        echo "<p><img src=\"" . htmlspecialchars($imgSrc) . "\" alt='chat image'></p>";
    }
    echo "<small>" . date("d M Y, h:i A", strtotime($row['sent_at'])) . "</small>";
    echo "</div>";
}
$stmt->close();
$conn->close();
?>
