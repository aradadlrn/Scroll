// Dynamically load comments (simulated for now)
const commentsList = document.getElementById('comments-list');

// Add Comment and Refresh List
function addComment(postId) {
    const commentInput = document.getElementById(`comment-input-${postId}`); // Dynamically select the input based on postId
    const commentText = commentInput.value.trim();
  
    if (commentText) {
      fetch('./php-scripts/add_comments.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          postId: postId,
          Comment: commentText,
        }),
      })
        .then((response) => {
          if (!response.ok) throw new Error('Failed to add comment');
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            commentInput.value = ''; // Clear the input
            loadComments(postId); // Reload comments
          } else {
            console.error(data.message);
            alert('Error adding comment: ' + data.message);
          }
        })
        .catch((error) => {
          console.error('Error adding comment:', error);
          alert('An error occurred. Please try again.');
        });
    }
  }
  

// Utility function to append a comment
// Utility function to append a comment
function addCommentToList(postId, username, text) {
    const commentsList = document.getElementById(`comments-list-${postId}`);
    const commentItem = document.createElement('div');

    commentItem.innerHTML = `
        <div class="comment-avatar"></div>
        <div class="comment-content">
            <p class="comment-username">${username}</p>
            <p class="comment-text">${text}</p>
        </div>
    `;

    commentsList.appendChild(commentItem);
    commentsList.scrollTop = commentsList.scrollHeight; // Scroll to the latest comment
}


// Close the comments section
function closeComments(postId) {
    const commentsSection = document.getElementById(`comments-container-${postId}`);
    const realPostContainer = document.querySelector('.realPostContainer');
    
    commentsSection.classList.remove('open');
    realPostContainer.classList.remove('moveLeft'); // Remove the moveLeft class
}

// For the comments to slide over
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-container-${postId}`);
    const realPostContainer = document.querySelector('.realPostContainer');

    if (commentsSection) {
        commentsSection.classList.toggle('open');
        realPostContainer.classList.toggle('moveLeft');
    }
}

// Load initial comments on page load
function loadComments(postId) {
    const commentsList = document.getElementById(`comments-list-${postId}`);

    fetch(`./php-scripts/fetch_comments.php?postId=${postId}`)
      .then((response) => {
        if (!response.ok) throw new Error('Failed to fetch comments');
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          const comments = data.comments;
          commentsList.innerHTML = ''; // Clear existing comments (You might want to keep this to avoid duplicates)

          // Append each comment to the list
          comments.forEach((comment) => {
            addCommentToList(postId, comment.username, comment.Comment);
          });
        } else {
          console.error(data.message);
        }
      })
      .catch((error) => {
        console.error('Error loading comments:', error);
      });
}

  
