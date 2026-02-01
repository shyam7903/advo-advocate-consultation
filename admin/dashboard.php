<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied.'); window.location.href='../users/login.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | advo.com</title>
  <link rel="stylesheet" href="../assets/main.css">
  <style>
    .admin-dashboard {
      max-width: 1100px;
      margin: 40px auto;
      background-color: #1e1e1e;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.5);
    }
    .admin-dashboard h1 {
      text-align: center;
      margin-bottom: 30px;
    }
    .admin-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    .admin-card {
      background: #2a2a2a;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      text-align: center;
      transition: transform 0.3s;
    }
    .admin-card:hover {
      transform: translateY(-5px);
    }
    .admin-card h3 {
      margin-bottom: 10px;
      color: #00bfff;
    }
    .admin-card a {
      color: #fff;
      background: #00bfff;
      padding: 8px 16px;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
      margin-top: 10px;
    }
    .admin-card a:hover {
      background: #009fd1;
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    <div class="admin-grid">

      <div class="admin-card">
        <h3>Manage Users</h3>
        <p>View or remove registered clients and advocates.</p>
        <a href="view_users.php">View Users</a>
        <a href="delete_users.php">Delete Users</a>
      </div>

      <div class="admin-card">
        <h3>Manage Appointments</h3>
        <p>Track and control all legal consultation appointments.</p>
        <a href="manage_appointments.php">Manage Appointments</a>
      </div>

      <div class="admin-card">
        <h3>Review Moderation</h3>
        <p>See all advocate reviews and manage negative feedback.</p>
        <a href="moderate_reviews.php">Moderate Reviews</a>
        <a href="delete_reviews.php">Delete Reviews</a>
      </div>

      <div class="admin-card">
        <h3>Registered Advocates</h3>
        <p>Approve or suspend listed advocates on the platform.</p>
        <a href="manage_advocates.php">Manage Advocates</a>
      </div>

      <div class="admin-card">
        <h3>Document Requests</h3>
        <p>Oversee uploaded documents between users and advocates.</p>
        <a href="view_documents.php">View Documents</a>
        <a href="delete_documents.php">Delete Documents</a>

      </div>

      <div class="admin-card">
        <h3>Logout</h3>
        <p>End admin session and return to login.</p>
        <a href="../users/logout.php">Logout</a>
      </div>

    </div>
  </div>

  <?php include '../includes/footer.php'; ?>
</body>
</html>
