<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    try {
        // Verify user exists before logging activity
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        if ($stmt->fetch()) {
            // Only log activity if user exists
            $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'logout', 'User logged out')");
            $stmt->execute([$_SESSION['user_id']]);
        }
    } catch (Exception $e) {
        // Silently continue with logout even if logging fails
    }
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit; 