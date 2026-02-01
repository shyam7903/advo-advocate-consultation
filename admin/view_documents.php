<?php
session_start();

// Allow only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied.'); window.location.href='../users/login.php';</script>";
    exit;
}

include '../includes/header.php';
include '../db.php';
?>

<div class="dashboard-section">
  <h3>Uploaded Legal Documents</h3>

  <table class="dashboard-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>File Name</th>
        <th>Uploaded At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT * FROM uploads ORDER BY uploaded_at DESC";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
      ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['user_id'] ?></td>
        <td><?= htmlspecialchars($row['filename']) ?></td>
        <td><?= $row['uploaded_at'] ?></td>
        <td>
          <a href="../uploads/<?= urlencode($row['filename']) ?>" target="_blank" style="color:#00bfff;">View</a> |
          <a href="delete_document.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure to delete this document?')" style="color:red;">Delete</a>
        </td>
      </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="5">No documents uploaded yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include '../includes/footer.php'; ?>
