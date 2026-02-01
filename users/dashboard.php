<?php
include '../includes/header.php';
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    echo "<script>alert('Access denied. Clients only.'); window.location.href='../users/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$name    = $_SESSION['user_name'] ?? 'Client';

// Get appointments for the logged-in client
$query = $conn->prepare("
    SELECT a.id, a.appointment_time, a.consultation_type, a.status,
           u.name AS advocate_name
    FROM appointments a
    JOIN users u ON a.advocate_id = u.id
    WHERE a.client_id = ?
    ORDER BY a.appointment_time DESC
");
$query->bind_param("i", $user_id);
$query->execute();
$appointments = $query->get_result();
?>

<h2 class="dashboard-title">Welcome, <?= htmlspecialchars($name) ?></h2>

<div class="dashboard-section">
  <h3>Your Appointments</h3>
  <?php if ($appointments->num_rows > 0): ?>
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Advocate</th>
          <th>Type</th>
          <th>Date/Time</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $appointments->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['advocate_name']) ?></td>
            <td><?= ucfirst($row['consultation_type']) ?></td>
            <td><?= date("d M Y, h:i A", strtotime($row['appointment_time'])) ?></td>
            <td><?= ucfirst($row['status']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="color:#ccc;">No appointments yet.</p>
  <?php endif; ?>
</div>

<div class="dashboard-section">
  <h3>Document Center</h3>
  <a href="/advo/documents/upload.php" class="btn-doc">📤 Upload Document</a>
  <a href="/advo/documents/view.php" class="btn-doc">📄 View My Documents</a>
</div>

<?php include '../includes/footer.php'; ?>
