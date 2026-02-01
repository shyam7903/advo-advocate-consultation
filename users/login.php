<?php
include '../includes/header.php';
include '../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch user from users table
    $stmt = $conn->prepare("SELECT id, name, password, role, profile_pic FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $hashed_password, $role, $profile_pic);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id']       = $id;
            $_SESSION['user_name']     = $name;
            $_SESSION['role']          = $role;
            $_SESSION['profile_pic']   = $profile_pic ?? 'default.png';

            // Redirect based on role
            switch ($role) {
                case 'client':
                    header("Location: /advo/users/dashboard.php");
                    break;
                case 'advocate':
                    header("Location: /advo/advocate/dashboard.php");
                    break;
                case 'admin':
                    header("Location: /advo/admin/dashboard.php");
                    break;
                default:
                    $error = "Unknown role. Contact admin.";
                    break;
            }
            exit;

        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!-- HTML Starts -->
<div class="auth-container">
  <h2>Login</h2>

  <?php if ($error): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
