// C:/xampp/htdocs/advo/admin/delete_user.php
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../users/login.php");
    exit;
}
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    $conn->query("DELETE FROM users WHERE id = $userId AND role != 'admin'");
    header("Location: view_users.php");
    exit;
}
?>
