<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied.'); window.location.href='../users/login.php';</script>";
    exit;
}
include '../includes/header.php';
include '../db.php';

// Fetch all users (clients and advocates only)
$sql = "SELECT id, name, email, role, created_at FROM users WHERE role IN ('client', 'advocate') ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container">
  <h2 style="text-align:center; margin-bottom:30px;">Manage Users</h2>

  <table class="dashboard-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Joined On</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td><?php echo htmlspecialchars($row['email']); ?></td>
          <td style="text-transform:capitalize;"><?php echo $row['role']; ?></td>
          <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
          <td>
            <form method="post" action="delete_user.php" onsubmit="return confirm('Are you sure?');">
              <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
              <button type="submit">Delete</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../includes/footer.php'; ?>
