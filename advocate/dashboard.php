<?php
include '../includes/header.php';
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'advocate') {
    echo "<script>alert('Access denied. Advocates only.'); window.location.href='../users/login.php';</script>";
    exit;
}

$advocate_id = $_SESSION['user_id'];
$name = $_SESSION['user_name'] ?? 'Advocate';

// Fetch advocate profile
$profile_query = $conn->prepare("SELECT specialization, city, fees FROM advocates WHERE user_id = ?");
$profile_query->bind_param("i", $advocate_id);
$profile_query->execute();
$profile = $profile_query->get_result()->fetch_assoc();

// Fetch appointments
$appt_query = $conn->prepare("
  SELECT a.id, a.appointment_time, a.consultation_type, a.status,
         u.name AS client_name
  FROM appointments a
  JOIN users u ON a.client_id = u.id
  WHERE a.advocate_id = ?
  ORDER BY a.appointment_time DESC
");
$appt_query->bind_param("i", $advocate_id);
$appt_query->execute();
$appointments = $appt_query->get_result();
?>

<h2 class="dashboard-title">Welcome, <?php echo htmlspecialchars($name); ?></h2>

<div class="dashboard-section">
  <h3>Your Profile</h3>
  <p><strong>Specialization:</strong> <?php echo htmlspecialchars($profile['specialization'] ?? 'N/A'); ?></p>
  <p><strong>City:</strong> <?php echo htmlspecialchars($profile['city'] ?? 'N/A'); ?></p>
  <p><strong>Fees:</strong> ₹<?php echo htmlspecialchars($profile['fees'] ?? '0'); ?></p>
</div>

<div class="dashboard-section">
  <h3>Appointments</h3>
  <?php if ($appointments->num_rows > 0): ?>
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Client</th>
          <th>Type</th>
          <th>Date/Time</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $appointments->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['client_name']); ?></td>
            <td><?php echo ucfirst($row['consultation_type']); ?></td>
            <td><?php echo date("d M Y, h:i A", strtotime($row['appointment_time'])); ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td>
              <?php if ($row['status'] == 'pending'): ?>
                <form action="update_status.php" method="POST" style="display:inline;">
                  <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                  <button type="submit" name="action" value="accept">✅ Accept</button>
                  <button type="submit" name="action" value="reject">❌ Reject</button>
                </form>
              <?php else: ?>
                <span style="color:#aaa;"><?php echo ucfirst($row['status']); ?></span>
              <?php endif; ?>
            </td>
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
