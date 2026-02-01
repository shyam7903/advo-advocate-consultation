<?php
include '../includes/header.php';
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Login first.'); window.location.href = '../users/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$upload_dir = '../documents/uploads/';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $appointment_id = $_POST['appointment_id'] ?? null;
    $description = trim($_POST['description'] ?? '');
    $file = $_FILES['document'];

    // ✅ Check if user is part of the selected appointment
    $check_stmt = $conn->prepare("
        SELECT id FROM appointments 
        WHERE id = ? AND (client_id = ? OR advocate_id = ?)
    ");
    $check_stmt->bind_param("iii", $appointment_id, $user_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        $error = "Invalid appointment selected.";
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Error uploading file.";
    } else {
        $filename = basename($file['name']);
        $safe_filename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "_", $filename);
        $file_path = $upload_dir . $safe_filename;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $conn->prepare("
                INSERT INTO documents (appointment_id, uploaded_by, file_path) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iis", $appointment_id, $user_id, $file_path);
            $stmt->execute();
            $success = "Document uploaded successfully.";
        } else {
            $error = "Failed to move file to upload directory.";
        }
    }
}
?>

<div class="upload-container">
    <h2>Upload Document</h2>

    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php elseif ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Select Appointment:</label>
        <select name="appointment_id" required>
            <option value="" disabled selected>-- Select an appointment --</option>
            <?php
            $appt_stmt = $conn->prepare("
                SELECT id, appointment_time 
                FROM appointments 
                WHERE client_id = ? OR advocate_id = ?
                ORDER BY appointment_time DESC
            ");
            $appt_stmt->bind_param("ii", $user_id, $user_id);
            $appt_stmt->execute();
            $appts = $appt_stmt->get_result();

            while ($appt = $appts->fetch_assoc()):
            ?>
                <option value="<?= $appt['id'] ?>">
                    <?= date("d M Y, h:i A", strtotime($appt['appointment_time'])) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="file" name="document" required>
        <textarea name="description" placeholder="Optional description..." rows="3"></textarea>
        <button type="submit">Upload</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
