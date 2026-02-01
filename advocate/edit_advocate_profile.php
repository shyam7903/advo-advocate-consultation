<?php
include '../includes/header.php';
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'advocate') {
    echo "<script>alert('Access denied. Advocates only.'); window.location.href='../users/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// Fetch current data
$stmt = $conn->prepare("SELECT * FROM advocates WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$advocate = $result->fetch_assoc();

if (!$advocate) {
    echo "<p style='color:red; text-align:center;'>Profile not found.</p>";
    include '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialization = $_POST['specialization'];
    $city           = $_POST['city'];
    $gender         = $_POST['gender'];
    $experience     = $_POST['experience'];
    $fees           = $_POST['fees'];
    $available_from = $_POST['available_from'];
    $available_to   = $_POST['available_to'];
    $bio            = $_POST['bio'];

    // ✅ File Upload
    $profile_pic = $advocate['profile_pic']; // default
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
                $profile_pic = $fileName;
            } else {
                $error = "Failed to upload new profile picture.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    if (!$error) {
        $update = $conn->prepare("UPDATE advocates SET specialization=?, city=?, gender=?, experience=?, fees=?, available_from=?, available_to=?, bio=?, profile_pic=? WHERE user_id=?");
        $update->bind_param("sssidssssi", $specialization, $city, $gender, $experience, $fees, $available_from, $available_to, $bio, $profile_pic, $user_id);
        $update->execute();

        $success = "Profile updated successfully!";
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM advocates WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $advocate = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Advocate Profile</title>
  <link rel="stylesheet" href="/advo/assets/css/main.css">
  <style>
    .edit-container {
        max-width: 800px;
        margin: 40px auto;
        background: #1e1e1e;
        padding: 30px;
        border-radius: 12px;
        color: #fff;
        box-shadow: 0 0 15px rgba(255,255,255,0.1);
    }
    input, textarea, select {
        width: 100%;
        padding: 10px;
        margin: 10px 0 15px;
        border-radius: 6px;
        border: none;
        background: #2e2e2e;
        color: #fff;
    }
    button {
        background: #4caf50;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    button:hover {
        background: #45a049;
    }
    .profile-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        display: block;
        margin-bottom: 15px;
    }
  </style>
</head>
<body>

<div class="edit-container">
  <h2>Edit Advocate Profile</h2>

  <?php if ($success): ?><p style="color:lightgreen;"><?= $success ?></p><?php endif; ?>
  <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Current Profile Picture:</label><br>
    <img class="profile-preview" src="/advo/uploads/<?= htmlspecialchars($advocate['profile_pic']) ?>" alt="Profile Picture">
    <input type="file" name="profile_pic" accept="image/*">

    <label>Specialization:</label>
    <input type="text" name="specialization" value="<?= htmlspecialchars($advocate['specialization']) ?>" required>

    <label>City:</label>
    <input type="text" name="city" value="<?= htmlspecialchars($advocate['city']) ?>" required>

    <label>Gender:</label>
    <select name="gender" required>
      <option value="Male" <?= $advocate['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
      <option value="Female" <?= $advocate['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
      <option value="Other" <?= $advocate['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
    </select>

    <label>Experience (in years):</label>
    <input type="number" name="experience" value="<?= htmlspecialchars($advocate['experience']) ?>" min="0" required>

    <label>Fees (in ₹):</label>
    <input type="number" name="fees" value="<?= htmlspecialchars($advocate['fees']) ?>" min="0" required>

    <label>Available From:</label>
    <input type="time" name="available_from" value="<?= htmlspecialchars($advocate['available_from']) ?>" required>

    <label>Available To:</label>
    <input type="time" name="available_to" value="<?= htmlspecialchars($advocate['available_to']) ?>" required>

    <label>Bio:</label>
    <textarea name="bio" rows="4"><?= htmlspecialchars($advocate['bio']) ?></textarea>

    <button type="submit">Update Profile</button>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
