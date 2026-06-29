<?php
/**
 * login_process.php — Authentication handler
 * Validates credentials against users table via prepared statement
 */
session_start();
 
// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}
 
require_once 'db.php';
 
// Sanitize inputs
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
 
// Basic server-side presence check
if (empty($username) || empty($password)) {
    header("Location: login.php?error=" . urlencode("Username and password are required."));
    exit;
}
 
// Prepared statement — prevents SQL injection
$sql  = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
 
if (!$stmt) {
    header("Location: login.php?error=" . urlencode("System error. Please try again."));
    exit;
}
 
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
 
// Verify password hash
if ($user && password_verify($password, $user['password'])) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
 
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];
 
    header("Location: index.php");
    exit;
} else {
    header("Location: login.php?error=" . urlencode("Invalid username or password."));
    exit;
}