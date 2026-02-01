<?php
include '../includes/header.php';
include '../db.php';

// ✅ Make sure only logged-in advocates can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'advocate') {
    echo "<script>alert('Access denied. Advocates only.'); window.location.href='../users/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch advocate details based on user_id
$stmt = $conn->prepare("SELECT * FROM advocates WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$advocate = $result->fetch_assoc();

// ✅ Safety check
if (!$advocate) {
    echo "<p style='color:red; text-align:center;'>Error: Advocate profile not found.</p>";
    include '../includes/footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Advocate Profile</title>
  <link rel="stylesheet" href="/advo/assets/css/main.css">
  <style>
    .profile-container {
        max-width: 800px;
        margin: 40px auto;
        background: #1e1e1e;
        padding: 30px;
        border-radius: 12px;
        color: #fff;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
    }
    .profile-container h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    .profile-container p {
        margin: 10px 0;
    }
    .edit-btn {
        display: inline-block;
        margin-top: 20px;
        background: #4caf50;
        color: white;
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s;
    }
    .edit-btn:hover {
        background: #45a049;
    }
    .profile-pic {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        display: block;
        margin: 0 auto 20px;
    }
  </style>
</head>
<body>

<div class="profile-container">
  <h2>Advocate Profile</h2>

  <img class="profile-pic" src="/advo/uploads/<?php echo htmlspecialchars($advocate['profile_pic']); ?>" alt="Profile Picture">

  <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
  <p><strong>Email:</strong> <?php echo htmlspecialchars($advocate['email']); ?></p>
  <p><strong>Specialization:</strong> <?php echo htmlspecialchars($advocate['specialization']); ?></p>
  <p><strong>City:</strong> <?php echo htmlspecialchars($advocate['city']); ?></p>
  <p><strong>Gender:</strong> <?php echo htmlspecialchars($advocate['gender']); ?></p>
  <p><strong>Experience:</strong> <?php echo htmlspecialchars($advocate['experience']); ?> years</p>
  <p><strong>Fees:</strong> ₹<?php echo htmlspecialchars($advocate['fees']); ?></p>
  <p><strong>Availability:</strong> <?php echo htmlspecialchars($advocate['available_from']); ?> to <?php echo htmlspecialchars($advocate['available_to']); ?></p>
  <p><strong>Bio:</strong> <?php echo htmlspecialchars($advocate['bio']); ?></p>

  <a class="edit-btn" href="edit_advocate_profile.php">Edit Profile</a>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
