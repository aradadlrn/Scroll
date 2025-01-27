<?php
session_start();
include 'connect.php';

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the form submission
    $username = trim($_POST['Username'] ?? '');
    $password = trim($_POST['user_password'] ?? '');

    if (empty($username) || empty($password)) {
        $response['error'] = 'Both username and password are required.';
    } else {
        // Check for admin credentials
        if ($username === "admin" && $password === "admin123") {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables for admin
            $_SESSION['username'] = "admin";
            $_SESSION['displayName'] = "Admin";

            // Return session data in the response
            $response['success'] = true;
            $response['redirect'] = "admin.html"; // Redirect to admin page
            $response['username'] = "admin";
            $response['displayName'] = "Admin";
        } else {
            try {
                // Fetch user data including DisplayName
                $sql = "SELECT RegisterId, Username, Password, COALESCE(DisplayName, Username) AS DisplayName FROM userregister WHERE Username = :username";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() === 1) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (password_verify($password, $user['Password'])) {
                        // Regenerate session ID for security
                        session_regenerate_id(true);

                        // Set session variables
                        $_SESSION['username'] = $user['Username'];
                        $_SESSION['displayName'] = $user['DisplayName'];
                        $_SESSION['registerId'] = $user['RegisterId'];
                        // Return session data in the response
                        $response['success'] = true;
                        $response['redirect'] = "Homepage.php"; // Ensure this matches the frontend expectation
                        $response['username'] = $user['Username'];
                        $response['displayName'] = $user['DisplayName'];
                        $response['registerId'] = $user['RegisterId'];

                    } else {
                        $response['error'] = "Incorrect password.";
                    }
                } else {
                    $response['error'] = "Username not found.";
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $response['error'] = "A database error occurred. Please try again later.";
            }
        }
    }
} else {
    $response['error'] = "Invalid request method. Only POST requests are allowed.";
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>