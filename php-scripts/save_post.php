<?php
include("connect.php");

// Check if data is sent via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titleBook = $_POST['titleBook'];
    $caption = $_POST['caption'];
    $image = $_FILES['image'];

    // Handle image upload
    $targetDir = "uploads/";
    $fileName = uniqid() . "-" . basename($picture['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($picture['tmp_name'], $targetFile)) {
        // Save the file path to the database
        $stmt->bindValue(':picture', $targetFile, PDO::PARAM_STR);
    } else {
        throw new Exception("Failed to upload the image!");
    }


    $targetFile = $targetDir . basename($image["name"]);
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        // Save post data to the database
        $stmt = $pdo->prepare("INSERT INTO userpost (RegisterId, TitleBook, Caption, Picture) VALUES (1, :titleBook, :caption, :picture)");
        $stmt->execute([
            ':titleBook' => $titleBook,
            ':caption' => $caption,
            ':picture' => $targetFile,
        ]);

        // Return the response
        echo json_encode([
            'success' => true,
            'titleBook' => $titleBook,
            'caption' => $caption,
            'imageUrl' => $targetFile,
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Image upload failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
