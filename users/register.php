<?php
include '../includes/header.php';
include '../db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            $stmt->execute();

            $user_id = $stmt->insert_id;

            // For advocates: insert into advocates table with basic info
            if ($role === 'advocate') {
                $default_spec   = '';
                $default_city   = '';
                $default_gender = '';
                $default_exp    = 0;
                $default_fees   = 0;
                $default_pic    = 'default.png';
                $default_bio    = '';
                $default_from   = '09:00';
                $default_to     = '18:00';

                $adv_stmt = $conn->prepare("INSERT INTO advocates (user_id, name, email, specialization, city, gender, experience, fees, profile_pic, bio, available_from, available_to)
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $adv_stmt->bind_param("isssssiddsss", 
                    $user_id, $name, $email, 
                    $default_spec, $default_city, $default_gender, 
                    $default_exp, $default_fees, 
                    $default_pic, $default_bio, $default_from, $default_to
                );
                $adv_stmt->execute();
            }

            // For clients: insert into clients table
            if ($role === 'client') {
                $default_city   = '';
                $default_gender = '';
                $default_pic    = 'default.png';
                $default_prof   = '';

                $client_stmt = $conn->prepare("INSERT INTO clients (user_id, city, gender, profile_pic, profession) VALUES (?, ?, ?, ?, ?)");
                $client_stmt->bind_param("issss", $user_id, $default_city, $default_gender, $default_pic, $default_prof);
                $client_stmt->execute();
            }

            $success = "Registration successful. You can now login.";
        }
    }
}
?>

<div class="auth-container">
  <h2>Register</h2>

  <?php if ($success): ?>
    <p style="color:lightgreen;"><?= $success ?></p>
  <?php elseif ($error): ?>
    <p style="color:red;"><?= $error ?></p>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    
    <select name="role" required>
      <option value="" disabled selected>Select Role</option>
      <option value="client">Client</option>
      <option value="advocate">Advocate</option>
    </select>

    <button type="submit">Register</button>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
