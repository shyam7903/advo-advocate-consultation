<?php
include '../includes/header.php';
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    echo "<script>alert('Access denied. Clients only.'); window.location.href='login.php';</script>";
    exit;
}

$client_id = $_SESSION['user_id'];
$name = $_SESSION['user_name'] ?? 'Client';

// Fetch profile from users and clients
$query = $conn->prepare("SELECT u.email, u.name, c.city, c.gender, c.profession, c.profile_pic 
                         FROM users u 
                         JOIN clients c ON u.id = c.user_id 
                         WHERE u.id = ?");
$query->bind_param("i", $client_id);
$query->execute();
$result = $query->get_result();
$client = $result->fetch_assoc();
?>

<div class="dashboard-section">
  <h2 class="dashboard-title">Welcome, <?php echo htmlspecialchars($client['name']); ?></h2>

  <h3>Your Profile</h3>
  <p><strong>Email:</strong> <?= htmlspecialchars($client['email']); ?></p>
  <p><strong>City:</strong> <?= htmlspecialchars($client['city']); ?></p>
  <p><strong>Gender:</strong> <?= htmlspecialchars($client['gender']); ?></p>
  <p><strong>Profession:</strong> <?= htmlspecialchars($client['profession']); ?></p>

  <?php if ($client['profile_pic']): ?>
    <p><strong>Photo:</strong><br><img src="../uploads/<?= htmlspecialchars($client['profile_pic']); ?>" style="max-width: 150px;"></p>
  <?php endif; ?>

  <a href="edit_client_profile.php" style="display:block; margin-top:20px;">✏ Edit Profile</a>
</div>

<?php include '../includes/footer.php'; ?>