<?php
session_start();

// Include your database connection file
include 'connect.php';

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user is authenticated
    if (!isset($_SESSION['username'])) {
        $response['message'] = 'Not authenticated.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    $username = $_SESSION['username']; // Use the username from the session

    // Handle display name update
    if (isset($_POST['newDisplayName'])) {
        $newDisplayName = $_POST['newDisplayName'] ?? '';

        if (empty($newDisplayName)) {
            $response['message'] = '';
        } else {
            try {
                // Update the display name in the database
                $updateSql = "UPDATE userregister SET DisplayName = ? WHERE Username = ?";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->execute([$newDisplayName, $username]);

                $response['status'] = 'success';
                $response['message'] = 'Display name updated successfully!';
            } catch (PDOException $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
        }
    }

    // Handle password update
    elseif (isset($_POST['current-password'])) {
        $currentPassword = $_POST['current-password'] ?? '';
        $newPassword = $_POST['new-password'] ?? '';
        $confirmPassword = $_POST['confirm-password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $response['message'] = 'All fields are required.';
        } elseif ($newPassword !== $confirmPassword) {
            $response['message'] = 'New password and confirm password do not match.';
        } else {
            try {
                // Fetch the current password from the database
                $sql = "SELECT Password FROM userregister WHERE Username = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Verify the current password
                    if (password_verify($currentPassword, $user['Password'])) {
                        // Hash the new password
                        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                        // Update the password in the database
                        $updateSql = "UPDATE userregister SET Password = ? WHERE Username = ?";
                        $updateStmt = $db->prepare($updateSql);
                        $updateStmt->execute([$newHashedPassword, $username]);

                        $response['status'] = 'success';
                        $response['message'] = 'Password updated successfully!';
                    } else {
                        $response['message'] = 'Current password is incorrect.';
                    }
                } else {
                    $response['message'] = 'User not found.';
                }
            } catch (PDOException $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
        }
    } else {
        $response['message'] = 'Invalid request.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>