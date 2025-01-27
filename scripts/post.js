document.getElementById('postButton').addEventListener('click', function () {
    const titleBook = document.getElementById('titleBook').value;
    const caption = document.getElementById('caption').value;
    const imageFile = document.getElementById('uploadImage').files[0];

    if (!titleBook || !caption || !imageFile) {
        alert('Please fill out all fields and upload an image.');
        return;
    }

    // Create FormData object for AJAX
    const formData = new FormData();
    formData.append('titleBook', titleBook);
    formData.append('caption', caption);
    formData.append('image', imageFile);

    // Send the data to the server
    fetch('save_post.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Add the new post to the top of the posts container
                const postsContainer = document.getElementById('posts-container');
                const newPost = document.createElement('div');
                newPost.className = 'post';
                newPost.innerHTML = `
                    <h3>${data.titleBook}</h3>
                    <img src="${data.imageUrl}" alt="Post Image" />
                    <p>${data.caption}</p>
                `;
                postsContainer.prepend(newPost);
            } else {
                alert('Error creating post: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
});
