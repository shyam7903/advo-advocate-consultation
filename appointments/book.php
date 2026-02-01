<?php
include '../includes/header.php';
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    echo "<script>alert('Access denied. Clients only.'); window.location.href='login.php';</script>";
    exit;
}

$client_id = $_SESSION['user_id'];
$advocate_id = $_GET['advocate_id'] ?? null;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consultation_type = $_POST['consultation_type'];
    $appointment_time = $_POST['appointment_time'];

    if (empty($consultation_type) || empty($appointment_time)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (client_id, advocate_id, consultation_type, appointment_time, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iiss", $client_id, $advocate_id, $consultation_type, $appointment_time);

        if ($stmt->execute()) {
            $success = "Appointment requested successfully!";
        } else {
            $error = "Error booking appointment. Please try again.";
        }
    }
}
?>

<div class="auth-container">
    <h2>Book Appointment</h2>

    <?php if ($success): ?>
        <p style="color:lightgreen;"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="consultation_type">Consultation Type:</label>
        <select name="consultation_type" required>
            <option value="" disabled selected>Select Type</option>
            <option value="chat">Chat</option>
            <option value="physical">Physical</option>
        </select>

        <label for="appointment_time">Preferred Date & Time:</label>
        <input type="datetime-local" name="appointment_time" required>

        <button type="submit">Book Now</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
