<?php
$db_user = "root";
$db_pass = "";
$db_name = "scroll";

$db = new PDO("mysql:host=localhost;dbname=" . $db_name . ";charset=utf8", $db_user, $db_pass);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = '';
$maxFileSize = 10 * 1024 * 1024; // 10MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        $requiredFields = ['registered', 'caption', 'title_book'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required!");
            }
        }

        // Validate image
        if (empty($_FILES['picture']['tmp_name'])) {
            throw new Exception("Please select an image!");
        }

        $picture = $_FILES['picture'];
        
        // Check file size
        if ($picture['size'] > $maxFileSize) {
            throw new Exception("Image must be smaller than 2MB!");
        }

        // Validate MIME type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($picture['tmp_name']);
        
        if (!in_array($mime, $allowedTypes)) {
            throw new Exception("Invalid image format. Only JPG, PNG, and GIF are allowed.");
        }

        // Convert to Base64
        $pictureContent = base64_encode(file_get_contents($picture['tmp_name']));

        // Database insertion
        $stmt = $db->prepare("INSERT INTO userpost
            (RegisterId, Caption, Picture, TitleBook) 
            VALUES (:registered, :caption, :picture, :titleBook)");
        
        $stmt->bindValue(':registered', $_POST['registered'], PDO::PARAM_INT);
        $stmt->bindValue(':caption', $_POST['caption'], PDO::PARAM_STR);
        $stmt->bindValue(':picture', $pictureContent, PDO::PARAM_STR); // Store as string
        $stmt->bindValue(':titleBook', $_POST['title_book'], PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to save to database!");
        }

        header("Location: insert.php?success=1");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!-- HTML remains the same as previous version -->
<!DOCTYPE html>
<html>
<head>
    <title>Book Upload</title>
    <style>
        .container { max-width: 500px; margin: 50px auto; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], input[type="file"] { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;
        }
        .error { color: #dc3545; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; }
        .success { color: #28a745; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; }
        button { 
            background: #007bff; color: white; padding: 10px 20px; 
            border: none; border-radius: 4px; cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload Book Details</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error">Error: <?= htmlspecialchars($error) ?></div>
        <?php elseif(isset($_GET['success'])): ?>
            <div class="success">Record inserted successfully!</div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Registered ID:</label>
                <input type="number" name="registered" required>
            </div>
            
            <div class="form-group">
                <label>Caption (max 255 chars):</label>
                <input type="text" name="caption" maxlength="255" required>
            </div>
            
            <div class="form-group">
                <label>Book Title (max 45 chars):</label>
                <input type="text" name="title_book" maxlength="45" required>
            </div>
            
            <div class="form-group">
                <label>Book Cover Image (max 2MB):</label>
                <input type="file" name="picture" accept="image/*" required>
            </div>
            
            <div class="form-group">
                <button type="submit">Upload Book</button>
            </div>
        </form>
    </div>
</body>
</html>