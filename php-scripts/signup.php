<?php
require_once 'connect.php';

// For error handling
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle form submission
    $displayName = trim($_POST['displayName']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['ConfirmPassword'];

    // Validate user inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($displayName)) {
        $response['error'] = "All fields are required.";
        echo json_encode($response);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = "Invalid email format.";
        echo json_encode($response);
        exit();
    }

    if ($password !== $confirmPassword) {
        $response['error'] = "Passwords do not match.";
        echo json_encode($response);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if username already exists
        $checkQuery = "SELECT * FROM userregister WHERE Username = :username";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([
            ':username' => $username
        ]);

        if ($stmt->rowCount() > 0) {
            $response['error'] = "Username already exists.";
            echo json_encode($response);
            exit();
        }

        // Check if email already exists
        $checkQuery = "SELECT * FROM userregister WHERE EmailAddress = :email";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([
            ':email' => $email,
        ]);

        if ($stmt->rowCount() > 0) {
            $response['error'] = "Email already exists.";
            echo json_encode($response);
            exit();
        }

        // Insert user into the database
        $insertQuery = "INSERT INTO userregister (Username, EmailAddress, Password, DisplayName) VALUES (:username, :email, :password, :displayName)";
        $stmt = $db->prepare($insertQuery);
        $result = $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':displayName' => $displayName,
        ]);

        if ($result) {
            $response['success'] = true;
            $response['redirect'] = "http://localhost/Scroll/Login.html";
            echo json_encode($response);
            exit();
        } else {
            $response['error'] = "Error: Unable to register the user.";
            echo json_encode($response);
            exit();
        }
    } catch (PDOException $e) {
        // Log the error and show a generic error message
        error_log("Database error: " . $e->getMessage());
        $response['error'] = "An error occurred during registration. Please try again later.";
        echo json_encode($response);
        exit();
    }
} else {
    $response['error'] = "Invalid request method. Only POST requests are allowed.";
    echo json_encode($response);
    exit();
}
?>