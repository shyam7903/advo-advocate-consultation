<?php
include '../includes/header.php';
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    echo "<script>alert('Access denied. Clients only.'); window.location.href='login.php';</script>";
    exit;
}

$client_id = $_SESSION['user_id'];
$success = $error = '';

// Fetch existing profile data
$query = $conn->prepare("SELECT c.city, c.gender, c.profession, c.profile_pic 
                         FROM clients c 
                         WHERE c.user_id = ?");
$query->bind_param("i", $client_id);
$query->execute();
$result = $query->get_result();
$client = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = trim($_POST['city']);
    $gender = $_POST['gender'];
    $profession = trim($_POST['profession']);
    
    // Handle file upload if available
    $profile_pic = $client['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $newName = 'client_' . $client_id . '.' . $ext;
        $target = "../uploads/" . $newName;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
            $profile_pic = $newName;
        } else {
            $error = "Failed to upload profile picture.";
        }
    }

    // Update clients table
    $update = $conn->prepare("UPDATE clients SET city = ?, gender = ?, profession = ?, profile_pic = ? WHERE user_id = ?");
    $update->bind_param("ssssi", $city, $gender, $profession, $profile_pic, $client_id);
    
    if ($update->execute()) {
        $success = "Profile updated successfully.";
    } else {
        $error = "Update failed. Please try again.";
    }
}
?>

<div class="dashboard-section">
  <h2 class="dashboard-title">Edit Profile</h2>

  <?php if ($success): ?>
    <p style="color:lightgreen;"><?= $success ?></p>
  <?php elseif ($error): ?>
    <p style="color:red;"><?= $error ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>City:</label>
    <input type="text" name="city" value="<?= htmlspecialchars($client['city']) ?>" required>

    <label>Gender:</label>
    <select name="gender" required>
      <option value="">Select</option>
      <option value="male" <?= $client['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
      <option value="female" <?= $client['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
      <option value="other" <?= $client['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
    </select>

    <label>Profession:</label>
    <input type="text" name="profession" value="<?= htmlspecialchars($client['profession']) ?>" required>

    <label>Profile Picture:</label>
    <input type="file" name="profile_pic" accept="image/*">
    <?php if ($client['profile_pic']): ?>
      <p>Current: <img src="../uploads/<?= $client['profile_pic'] ?>" style="max-width:100px;"></p>
    <?php endif; ?>

    <button type="submit">Update Profile</button>
  </form>
</div>

<?php include '../includes/footer.php'; ?>