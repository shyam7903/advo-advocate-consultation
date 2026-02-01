<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied.'); window.location.href='../users/login.php';</script>";
    exit;
}
include '../includes/header.php';
include '../db.php';
?>

<div class="dashboard-section">
  <h3>Moderate Reviews</h3>

  <?php
  $sql = "SELECT reviews.id, reviews.rating, reviews.comment, reviews.created_at,
                 users.name AS client_name, advocates.name AS advocate_name
          FROM reviews
          JOIN users ON reviews.user_id = users.id
          JOIN advocates ON reviews.advocate_id = advocates.id
          ORDER BY reviews.created_at DESC";
  $result = $conn->query($sql);

  if ($result->num_rows > 0): ?>
    <table class="dashboard-table">
      <tr>
        <th>Client</th>
        <th>Advocate</th>
        <th>Rating</th>
        <th>Comment</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['client_name']) ?></td>
          <td><?= htmlspecialchars($row['advocate_name']) ?></td>
          <td><?= $row['rating'] ?>/5</td>
          <td><?= htmlspecialchars($row['comment']) ?></td>
          <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
          <td>
            <form method="POST" action="delete_review.php" onsubmit="return confirm('Delete this review?');">
              <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
              <button type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No reviews found.</p>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
