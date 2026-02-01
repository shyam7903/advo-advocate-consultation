<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advo.com - Legal Consultation Platform</title>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@700&family=Inter&display=swap" rel="stylesheet">
   <style>
    *{
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background-color: #121212;
      color: #f0f0f0;
      scroll-behavior: smooth;
    }

    header {
      background-color: #1e1e1e;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    header h1 {
      font-family: 'Libre Baskerville', serif;
      color: #00bfff;
    }

    nav a {
      color: #ccc;
      margin-left: 20px;
      text-decoration: none;
      font-weight: bold;
    }

    nav a:hover {
      color: #00bfff;
    }

    .container {
      max-width: 1200px;
      margin: auto;
      padding: 0 20px;
    }

    section {
      padding: 60px 0;
    }

    .search-bar {
      text-align: center;
      background-color: #1e1e1e;
      padding: 60px 20px;
    }

    .search-bar input[type="text"] {
      padding: 12px;
      width: 60%;
      max-width: 500px;
      border-radius: 5px;
      border: none;
      outline: none;
    }

    .search-bar button {
      padding: 12px 24px;
      background-color: #00bfff;
      border: none;
      color: white;
      margin-left: 10px;
      border-radius: 5px;
      cursor: pointer;
    }

    .section-title {
      font-family: 'Libre Baskerville', serif;
      font-size: 26px;
      color: #00bfff;
      text-align: center;
      margin-bottom: 30px;
    }

    .cards-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .card {
      flex: 1 1 300px;
      max-width: 350px;
      background-color: #1e1e1e;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .testimonials blockquote {
      font-style: italic;
      border-left: 4px solid #00bfff;
      margin: 20px auto;
      padding-left: 15px;
      max-width: 800px;
      color: #ccc;
    }

    footer {
      background-color: #1e1e1e;
      padding: 30px;
      text-align: center;
      font-size: 14px;
      color: #aaa;
    }

    .profile-icon {
      display: inline-block;
      margin-left: 15px;
    }

    .profile-icon img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      vertical-align: middle;
    }
  </style> 
</head>
<body>
  
<header>
  <h1>Advo.com</h1>
    <nav>
      <a href="/advo/search/find.php">Find Advocates</a>
      <a href="/advo/appointments/book.php">Consult Now</a>

      <?php if (isset($_SESSION['user_id'])): ?>
        <?php
          $role = $_SESSION['role'] ?? '';
          if ($role === 'client') {
              $profileLink = "/advo/users/client_profile.php";
          } elseif ($role === 'advocate') {
              $profileLink = "/advo/advocate/advocate_profile.php";
          } elseif ($role === 'admin') {
              $profileLink = "/advo/admin/dashboard.php";
          } else {
              $profileLink = "#";
          }
        ?>
        <a href="<?= $profileLink ?>" class="profile-icon" title="View Profile">
          <img src="/advo/assets/img/default.png" alt="Profile">
        </a>
        <a href="/advo/users/logout.php">Logout</a>
      <?php else: ?>
        <a href="/advo/users/login.php">Login</a>
      <?php endif; ?>
    </nav>
  </header>

   <!-- Search Bar -->
  <section style="text-align:center; padding: 40px;">
    <form action="/advo/search/find.php" method="get" style="display:inline-flex; max-width: 600px; width:100%;">
      <input type="text" name="query" placeholder="Search city, advocate, specialization..." 
             style="flex:1; padding:10px; border-radius:5px 0 0 5px; border:none;">
      <button type="submit" style="padding:10px 20px; border:none; background:#00bfff; color:white; border-radius:0 5px 5px 0; cursor:pointer;">
        Search
      </button>
    </form>
  </section>

  <section class="quick-cards">
    <div class="container">
      <div class="section-title">Quick Services</div>
      <div class="cards-container">
        <div class="card">
          <h3>Instant Chat</h3>
          <p>Connect with an advocate instantly for online consultation.</p>
        </div>
        <div class="card">
          <h3>Find Nearby</h3>
          <p>Book physical visits to advocates in your locality.</p>
        </div>
        <div class="card">
          <h3>Upload Documents</h3>
          <p>Send documents securely when requested by your advocate.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="specializations">
    <div class="container">
      <div class="section-title">Specializations</div>
      <div class="cards-container">
        <div class="card">Civil Law</div>
        <div class="card">Criminal Law</div>
        <div class="card">Family Law</div>
        <div class="card">Corporate Law</div>
        <div class="card">Property Disputes</div>
      </div>
    </div>
  </section>

  <section class="clinic-appointments">
    <div class="container">
      <div class="section-title">Book Physical Consultations</div>
        <div class="cards-container">
          <?php
          include 'db.php';
          $stmt = $conn->prepare("
            SELECT a.user_id, u.name, a.specialization, a.fees
            FROM advocates a
            JOIN users u ON a.user_id = u.id
            LIMIT 2
          ");
          $stmt->execute();
          $results = $stmt->get_result();
          while ($advocate = $results->fetch_assoc()):
          ?>
           <div class="card">
            <h3>Adv. <?= htmlspecialchars($advocate['name']) ?></h3>
            <p>Specialization: <?= htmlspecialchars($advocate['specialization']) ?></p>
            <p>Fee: ₹<?= htmlspecialchars($advocate['fees']) ?></p>
            <a href="/advo/appointments/book.php?advocate_id=<?= $advocate['user_id'] ?>"><button>Book Now</button></a>
           </div>
          <?php endwhile; ?>
        </div>
      </div>
  </section>


  <section class="testimonials">
    <div class="container">
      <div class="section-title">What Our Clients Say</div>
      <blockquote>
        “Amazing service! Was able to get connected to a top lawyer within minutes.” – Priya K.
      </blockquote>
      <blockquote>
        “The chat and document upload features make legal help super easy.” – Raj M.
      </blockquote>
    </div>
  </section>

  <footer>
    &copy; 2025 advo.com. All rights reserved. | Privacy Policy | Terms of Use
  </footer>
</body>
</html>
