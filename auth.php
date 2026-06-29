<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sessionUsername = htmlspecialchars($_SESSION['username'] ?? 'User');
$sessionRole = htmlspecialchars($_SESSION['role'] ?? 'Staff');