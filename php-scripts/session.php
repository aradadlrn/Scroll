<?php
session_start();

$response = ['success' => false, 'error' => ''];

if (isset($_SESSION['username'])) {
    $response['success'] = true;
    $response['username'] = $_SESSION['username'];
    $response['displayName'] = $_SESSION['displayName'] ?? $_SESSION['username']; // Default to username if displayName is not set
} else {
    $response['error'] = 'Not authenticated.';
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>