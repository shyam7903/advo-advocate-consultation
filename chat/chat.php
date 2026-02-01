<?php
// chat.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /advo/users/login.php');
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$currentUserRole = $_SESSION['role'] ?? 'client';

// read GET
$chatWith = isset($_GET['chat_with']) ? intval($_GET['chat_with']) : 0;
$appointmentId = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

// Build chat list: show confirmed/accepted appointments where current user participates
$chatList = [];
$q = "
SELECT a.id AS appointment_id,
       IF(a.client_id = ?, a.advocate_id, a.client_id) AS other_user_id,
       u.name
FROM appointments a
JOIN users u ON u.id = IF(a.client_id = ?, a.advocate_id, a.client_id)
WHERE (a.client_id = ? OR a.advocate_id = ?) AND a.status IN ('confirmed','accepted')
ORDER BY a.appointment_time DESC
";
$stmt = $conn->prepare($q);
$stmt->bind_param("iiii", $currentUserId, $currentUserId, $currentUserId, $currentUserId);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $chatList[] = $r;
}
$stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chat - Advo.com</title>
  <link rel="stylesheet" href="/advo/assets/css/chat.css">
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>

  <div class="chat-container">
    <div class="chat-sidebar">
      <h3>Messages</h3>

      <?php if (empty($chatList)): ?>
        <p class="no-messages">No confirmed appointments yet.</p>
      <?php else: ?>
        <?php foreach ($chatList as $chat): ?>
          <div class="chat-user <?= ($chat['other_user_id'] == $chatWith) ? 'active' : '' ?>">
            <a href="/advo/chat/chat.php?chat_with=<?= (int)$chat['other_user_id'] ?>&appointment_id=<?= (int)$chat['appointment_id'] ?>">
              <?= htmlspecialchars($chat['name']) ?>
            </a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="chat-window">
      <?php if ($chatWith && $appointmentId): ?>
        <form id="messageForm" class="chat-input" method="post" enctype="multipart/form-data">
          <!-- Hidden inputs (name attributes are REQUIRED so FormData(messageForm) includes them) -->
          <input type="hidden" id="appointment_id" name="appointment_id" value="<?= htmlspecialchars($appointmentId) ?>">
          <input type="hidden" id="chat_with" name="chat_with" value="<?= htmlspecialchars($chatWith) ?>">

          <div class="chat-messages" id="chatBox">
            <!-- messages will be loaded by JS via get_messages.php -->
          </div>

          <div style="display:flex; gap:8px; align-items:center; padding:12px;">
            <input type="text" name="message" id="message" placeholder="Type a message..." autocomplete="off" style="flex:1; padding:10px; border-radius:20px; border:1px solid #ccc;">
            <label for="image" style="cursor:pointer; padding:8px 12px; background:#0073b1; color:#fff; border-radius:12px;">📎</label>
            <input type="file" name="image" id="image" accept=".jpg,.jpeg" style="display:none;">
            <button type="submit" style="padding:8px 14px; background:#0073b1; color:#fff; border-radius:12px; border:none;">Send</button>
          </div>
        </form>
      <?php else: ?>
        <div class="no-messages">Select a user from the left to start chatting.</div>
      <?php endif; ?>
    </div>
  </div>

  <script src="/advo/assets/js/chat.js"></script>
</body>
</html>
