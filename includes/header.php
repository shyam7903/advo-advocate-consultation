<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Advo.com</title>
  <link rel="stylesheet" href="/advo/assets/css/main.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Libre+Baskerville&display=swap" rel="stylesheet">
</head>
<body>
  <div class="navbar">
    <div class="logo">
      <a href="/advo/index.php" id="nav-logo">Advo.com</a>
    </div>

    <div class="nav-links">
      <a href="/advo/search/find.php">Find Advocate</a>
      <a href="/advo/appointments/book.php">Book Appointment</a>
      <a href="/advo/chat/chat.php">Chat</a>

      <?php if (isset($_SESSION['user_id'])): ?>
        <?php
          // Determine role
          $role = $_SESSION['role'] ?? 'client';
          $dashboardLink = "/advo/users/dashboard.php";  // default

          if ($role === 'admin') {
              $dashboardLink = "/advo/admin/dashboard.php";
              $profileLink = "#"; // Admin has no separate profile
          } elseif ($role === 'advocate') {
              $dashboardLink = "/advo/advocate/dashboard.php";
              $profileLink = "/advo/advocate/advocate_profile.php";
          } else {
              $dashboardLink = "/advo/users/dashboard.php";
              $profileLink = "/advo/users/client_profile.php";
          }
        ?>

        <!-- Profile Icon -->
        <a href="<?= $profileLink ?>">
          <img src="/advo/uploads/<?= $_SESSION['profile_pic'] ?? 'default.png' ?>" 
               alt="Profile" 
               class="profile-icon" 
               style="width:32px; height:32px; border-radius:50%; object-fit:cover;" />
        </a>

        <a href="<?= $dashboardLink ?>">Dashboard</a>
        <a href="/advo/users/logout.php">Logout</a>

      <?php else: ?>
        <a href="/advo/users/login.php">Login</a>
        <a href="/advo/users/register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
