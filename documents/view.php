<?php
include '../includes/header.php';
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Login first.'); window.location.href = '../users/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch documents only related to appointments involving this user
$stmt = $conn->prepare("
  SELECT d.id, d.file_path, d.uploaded_at
  FROM documents d
  JOIN appointments a ON d.appointment_id = a.id
  WHERE (a.client_id = ? OR a.advocate_id = ?)
  ORDER BY d.uploaded_at DESC
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="view-docs">
    <h2>Appointment Documents</h2>
    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($doc = $result->fetch_assoc()): ?>
                <li>
                    <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank">📄 View</a> |
                    Uploaded: <?= date('d M Y, h:i A', strtotime($doc['uploaded_at'])) ?>

                    <!-- Optional: Only show delete if this user uploaded it -->
                    <form method="POST" action="delete_document.php" style="display:inline;">
                      <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                      <button type="submit" onclick="return confirm('Delete this file?')">🗑️ Delete</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No documents available.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
