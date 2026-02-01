<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/header.php';
include '../db.php';

// Initialize filters
$city = $_GET['city'] ?? '';
$specialization = $_GET['specialization'] ?? '';
$gender = $_GET['gender'] ?? '';

// Build SQL dynamically
$sql = "SELECT a.*, u.id as user_id FROM advocates a JOIN users u ON a.user_id = u.id WHERE 1";
$filters = [];
$types = "";

if (!empty($city)) {
    $sql .= " AND a.city = ?";
    $filters[] = $city;
    $types .= "s";
}
if (!empty($specialization)) {
    $sql .= " AND a.specialization = ?";
    $filters[] = $specialization;
    $types .= "s";
}
if (!empty($gender)) {
    $sql .= " AND a.gender = ?";
    $filters[] = $gender;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if (!empty($filters)) {
    $stmt->bind_param($types, ...$filters);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="search-container">
  <h2>Find Advocates</h2>
  <form method="GET">
    <input type="text" name="city" placeholder="Enter City" value="<?= htmlspecialchars($city) ?>">
    <input type="text" name="specialization" placeholder="Specialization" value="<?= htmlspecialchars($specialization) ?>">
    <select name="gender">
      <option value="">Select Gender</option>
      <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
      <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
      <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
    </select>
    <button type="submit">Search</button>
  </form>
</div>

<div class="container">
  <h2>Advocate Results</h2>
  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="card">
        <h3><?= htmlspecialchars($row['name']) ?> - <?= htmlspecialchars($row['specialization']) ?></h3>
        <p><strong>City:</strong> <?= htmlspecialchars($row['city']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
        <p><strong>Experience:</strong> <?= htmlspecialchars($row['experience']) ?> years</p>
        <p><strong>Fees:</strong> ₹<?= htmlspecialchars($row['fees']) ?></p>
        <p><strong>Available:</strong> <?= date("g:i A", strtotime($row['available_from'])) ?> - <?= date("g:i A", strtotime($row['available_to'])) ?></p>
        <a href="../appointments/book.php?advocate_id=<?= $row['user_id'] ?>" class="btn btn-primary">Book Appointment</a>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="color: #aaa;">No advocates found matching your criteria.</p>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
