<?php
include("./php-scripts/connect.php");

$error = '';
$maxFileSize = 10 * 1024 * 1024; //MB NEED

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
            throw new Exception("Image must be smaller than 10MB!");
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
        $stmt->bindValue(':picture', $pictureContent, PDO::PARAM_STR);
        $stmt->bindValue(':titleBook', $_POST['title_book'], PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to save to database!");
        }

        header("Location: homepage.php?success=1");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link id="theme-style" rel="stylesheet" href="./styles/Homepage.css">
  <script src="./scripts/ACTION.js" defer></script>
  <script src="./scripts/comments.js" defer></script>
  <script src="./scripts/modal-homepage.js" defer></script>
  <script src="./scripts/Homepage.js" defer></script>
  <script src="./scripts/post.js" defer></script>
  
  <title>SCROLL</title>
</head>
<body>
  <div class="main-container">
    
    <!-- ===== TOP BAR (HEADER) ===== -->
    <header class="header-col search-col">
        <!-- The search box itself -->
        <div class="search-box">
          <img class="icon-search" src="./assets/icon-search0.svg" />
          <input type="text" id="search-query" placeholder="Search" />
        </div>
    </header>
    
    <!-- ===== LEFT SIDEBAR ===== -->
    <aside class="sidebar">
      <!-- LOGO at the top -->
        <div class="logo">
          <img src="./assets/logo0.png" alt="Scroll Logo">
        </div>
      <!-- Nav links -->
      <nav class="nav-links">
        <div class="nav-item">
          <img class="nav-icon" src="./assets/home-icon0.svg" />
          <span>For you</span>
        </div>
        <a href="/Scroll/profilev3.html" style="text-decoration: none;" class="nav-item">
          <img class="nav-icon" src="./assets/icon-user0.svg" />
          <span>Profile</span>
        </a>
        <a href="/Scroll/bookmarks.php" style="text-decoration: none;" class="nav-item">
          <img class="nav-icon" src="./assets/icon-bookmark0.svg" />
          <span>Bookmarks</span>
        </a>
        <a href="/Scroll/settings.html" style="text-decoration: none;" class="nav-item">
          <img class="nav-icon" src="./assets/icon-settings0.svg" />
          <span>Settings</span>
        </a>
      </nav>
      <!-- Theme toggle container -->
        <div class="dark-mode-toggle" id="themeToggle">
          <!-- The same <img> changes src on click -->
          <img src="./assets/moon0.svg" class="theme-icon" alt="Toggle theme" />
        </div>

      <!-- PROFILE CARD at the very bottom -->
      <div class="profile-card">
        <div class="profile-avatar">
          <img id="profileAvatarImg" src="./assets/profilepic.png" alt="Profile Avatar" />
        </div>
        <div class="profile-text">
          <div class="display-name">Display Name</div>
          <div class="username">@username</div>
        </div>
        <div class="profile-options">
          <img src="./assets/more-horizontal.png" alt="options" id="profileOptions">
          <div class="dropdown-content" id="dropdownContent">
            <div class="dropdown-item" id="logoutButton">Log out</div>
          </div>
        </div>
      </div>
  </aside>

  <!-- Logout Confirmation Modal -->
  <div id="logoutModal" class="lModal">
    <div class="lModal-content">
      <h2>Logout Confirmation</h2>
      <p>Are you sure you want to log out?</p>
      <div class="lModal-buttons">
        <button id="confirmLogout">Yes, Logout</button>
        <button id="cancelLogout">Cancel</button>
      </div>
    </div>
  </div>

    <!-- ===== MAIN CONTENT (POST DISPLAY) AREA ===== -->

    <div id="postContainer" class="postContainer">
    <div id="realPostContainer" class="realPostContainer">
        <!-- Dynamically Insert Posts -->
        <?php
        // Updated query to include idUpload and like_count
        $posts = $db->query("SELECT 
            r.DisplayName, 
            r.Username, 
            p.idUpload, 
            p.TitleBook, 
            p.Caption, 
            p.Picture, 
            (SELECT COUNT(*) FROM likes WHERE idUpload = p.idUpload) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE idUpload = p.idUpload) AS comment_count
        FROM userpost p
        JOIN userregister r ON p.RegisterId = r.RegisterId
        ORDER BY idUpload DESC")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as $post): ?>
            <div class="actualPost">
                <div class="postLeftContainer">
                    <div class="postCreator"><?= htmlspecialchars($post['DisplayName']) ?>
                        <text class="postUsername">@<?= htmlspecialchars($post['Username']) ?></text>
                    </div>
                    <text class="postTitle"><?= htmlspecialchars($post['TitleBook']) ?></text>
                    <text class="postDesc"><?= htmlspecialchars($post['Caption']) ?></text>
                    <div class="postIcons">
                        <img class="likeBtn" src="./assets/heart-nolike.png" onclick="toggleLike(<?= $post['idUpload'] ?>)">
                        <span id="like-count-<?= $post['idUpload'] ?>" class="like-count"><?= htmlspecialchars($post['like_count']) ?></span>
                        <button class="commentBtn" onclick="toggleComments(<?= $post['idUpload'] ?>)">
                            <img class="commentBtnImg" src="./assets/icon-message0.svg">
                        </button>
                        <span id="comment-count-<?= $post['idUpload'] ?>" class="comment-count">
                          <?= htmlspecialchars($post['comment_count']) ?>
                        </span>
                        <img class="bookmarkBtn" src="./assets/icon-bookmark0.svg" onclick="toggleBookmark()">
                    </div>
                </div>
                <div class="postRightContainer">
                    <div class="postImage">
                        <img src="data:image/jpeg;base64,<?= $post['Picture'] ?>" alt="Post Image">
                    </div>
                </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Upload Button na magoopen ng Upload Popup -->
      <button id = "openModal" class = "icon-plus">
        <img src="./assets/icon-plus0.svg" alt="plus"/>
      </button>

          <!-- Modal structure for Upload -->
      <div id="modal" class="modal">
        <div class="modal-content">
            <!-- Modal header -->
            <div class="modal-header">Create Post
              <button id="closeModal" class="btn btn-close">&times;</button>
            </div>

            <!-- Form -->
          <form method="POST" enctype="multipart/form-data" class="modal-content2">
            
                <!-- Left Container (Keep your existing icons) -->
                <div class="left-container">
                <!-- Feature Icons -->
                <div class="icon-wrapper">
                  <img class="req-icon" src="./assets/gg_size.png">
                  <span class="tooltipText">Maximum File Size of 10 MB per Images</span>
                </div>
                <div class="icon-wrapper">
                  <img class="req-icon" src="./assets/jpg.png" data-tooltip="JPEG, PNG, and GIF">
                  <span class="tooltipText">JPEG, PNG, and GIF</span>
                </div>
                <div class="icon-wrapper">
                  <img class="req-icon" src="./assets/Resolution.png">
                  <span class="tooltipText">Maximum Resolution of 1920x1080 Pixels</span>
                </div>
                <div class="icon-wrapper">
                  <img class="req-icon" src="./assets/material-symbols_aspect-ratio.png">
                  <span class="tooltipText">Landscape & Portrait</span>
                </div>
                <div class="icon-wrapper">
                  <img class="req-icon" src="./assets/cute.png">
                  <span class="tooltipText">Avoid Offensive & Copyrighted Material.</span>
                </div>
                <div class="icon-wrapper">
                  <img class="req-icon" src="./assets/accuracy.png">
                  <span class="tooltipText">Stop Spreading Misinformation</span>
                </div>
              </div>
              <!-- Right Container -->
            <div class="right-container">
            <input type="hidden" name="registered" id="registeredId">

                <!-- Title Input -->
                <textarea id="titleInput" class="titleInput" name="title_book" placeholder="What book is on your mind?" required></textarea>

                <div class="contentInput">
                  <!-- Description Input -->
                  <textarea id="descriptionInput" class="descInput" name="caption" placeholder="Every Scroll has a story. Share yours!" required></textarea>

                  <!-- Image Upload -->
                  <div class="modal-image-uploader">
                      <label for="file-input" class="upload-label">
                          <img id="preview-image" src="./assets/upload.png" alt="Upload Image">
                          <span>Click to upload an image</span>
                      </label>
                      <input type="file" id="file-input" name="picture" accept="image/*" required style="display: none;">
                  </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                  <button type="submit" id="postBtn" class="btn btn-secondary">Post</button>
                </div>
            </div>
          </form>
        </div>
      </div>

      <?php
        // Updated query to include idUpload, like_count, and comment_count
        $posts = $db->query("SELECT 
            r.DisplayName, 
            r.Username, 
            p.idUpload, 
            p.TitleBook, 
            p.Caption, 
            p.Picture, 
            (SELECT COUNT(*) FROM likes WHERE idUpload = p.idUpload) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE idUpload = p.idUpload) AS comment_count
        FROM userpost p
        JOIN userregister r ON p.RegisterId = r.RegisterId
        ORDER BY idUpload DESC")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as $post): ?>
      <!-- COMMENTS -->
      <div class="comments-container" id="comments-container-<?= $post['idUpload'] ?>" data-post-id="<?= $post['idUpload'] ?>">
            <!-- Close Button -->
            <button class="close-btn" onclick="closeComments(<?= $post['idUpload'] ?>)">×</button>
    
            <!-- Comments Header -->
            <div class="comments-header">
                <h2>Comments</h2>
            </div>
    
            <!-- Comments List -->
            <div class="comments-list" id="comments-list-<?= $post['idUpload'] ?>">
                <!-- Where comments show -->
                <?php
                        // Fetch comments for the current post
                        $comments = $db->prepare("SELECT 
                            c.Comment, 
                            c.dateCreated, 
                            r.DisplayName AS username
                        FROM comments c
                        JOIN userregister r ON c.RegisterId = r.RegisterId
                        WHERE c.idUpload = :postId
                        ORDER BY c.dateCreated ASC");
                        $comments->execute(['postId' => $post['idUpload']]);
                        foreach ($comments->fetchAll(PDO::FETCH_ASSOC) as $comment): ?>
                <div class="comment-item">
                    <div class="comment-avatar"></div>
                      <div class="comment-content">
                        <p class="comment-username"><?= htmlspecialchars($comment['username']) ?></p>
                        <p class="comment-text"><?= htmlspecialchars($comment['Comment']) ?></p>
                      </div>
                    </div>
                <?php endforeach; ?>
            </div>
    
            <!-- Add Comment Section -->
            <div class="comment-input-container">
              <input type="text" id="comment-input-<?= $post['idUpload'] ?>" placeholder="Add a comment..." />
              <button onclick="addComment(<?= $post['idUpload'] ?>)">Post</button>
            </div>
        </div> <!-- End comments container -->
        <?php endforeach; ?>
        </div>
    </div>
  </body>

</html>