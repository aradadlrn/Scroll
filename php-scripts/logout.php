<?php
session_start(); // Start the session

// Initialize the response array
$response = ['success' => false, 'error' => ''];

// Check if the user is logged in (optional, depending on your use case)
if (isset($_SESSION['username'])) {
    // Destroy the session to log the user out
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session

    // Set success to true
    $response['success'] = true;
} else {
    // If the user is not logged in, return an error
    $response['error'] = 'No active session to log out.';
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>