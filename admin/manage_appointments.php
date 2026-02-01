<?php

include '../includes/header.php';
include '../db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied. Only admins allowed.'); window.location.href = '/advo/users/login.php';</script>";
    exit;
}

// Handle deletion of appointment
if (isset($_GET['delete'])) {
    $appointmentId = intval($_GET['delete']);
    $conn->query("DELETE FROM appointments WHERE id = $appointmentId");
    echo "<script>alert('Appointment deleted successfully'); window.location.href='manage_appointments.php';</script>";
    exit;
}

// Fetch all appointments
$sql = "SELECT 
            a.id AS appointment_id,
            a.appointment_time,
            a.consultation_type,
            u.name AS client_name,
            adv.name AS advocate_name,
            adv.specialization
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN advocates adv ON a.advocate_id = adv.id
        ORDER BY a.appointment_time DESC";

$result = $conn->query($sql);
?>

<div class="dashboard-section">
  <h2 class="dashboard-title">Manage Appointments</h2>

  <?php if ($result->num_rows > 0): ?>
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Client Name</th>
          <th>Advocate</th>
          <th>Specialization</th>
          <th>Consult Type</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['client_name']) ?></td>
            <td><?= htmlspecialchars($row['advocate_name']) ?></td>
            <td><?= htmlspecialchars($row['specialization']) ?></td>
            <td><?= ucfirst($row['consultation_type']) ?></td>
            <td><?= date("d M Y, h:i A", strtotime($row['appointment_time'])) ?></td>
            <td>
              <a href="?delete=<?= $row['appointment_id'] ?>" onclick="return confirm('Are you sure you want to delete this appointment?')" style="color:red;">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="color: #ccc;">No appointments found.</p>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
