<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied.'); window.location.href='../users/login.php';</script>";
    exit;
}

include '../includes/header.php';
include '../db.php';

if (isset($_GET['action'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ? AND role = 'advocate'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($action === 'suspend') {
        $stmt = $conn->prepare("UPDATE users SET status = 'suspended' WHERE id = ? AND role = 'advocate'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'advocate'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    header("Location: manage_advocates.php");
    exit;
}

$result = $conn->query("SELECT id, name, email, phone, status FROM users WHERE role = 'advocate'");
?>

<div class="dashboard-section">
  <h3>Manage Advocates</h3>
  <table class="dashboard-table">
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td>
          <?php if ($row['status'] === 'approved'): ?>
            <a href="?action=suspend&id=<?= $row['id'] ?>" onclick="return confirm('Suspend this advocate?')">
              <button>Suspend</button>
            </a>
          <?php else: ?>
            <a href="?action=approve&id=<?= $row['id'] ?>" onclick="return confirm('Approve this advocate?')">
              <button>Approve</button>
            </a>
          <?php endif; ?>
          <a href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure to delete?')">
            <button style="background:#cc0000;">Delete</button>
          </a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

<?php include '../includes/footer.php'; ?>
