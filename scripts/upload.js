document.addEventListener("DOMContentLoaded", function () {
  // Get the modal and its contents
  var modal = document.getElementById("myModal");
  var content1 = document.querySelector(".modal-content1"); // Only using content1
  var box1 = document.querySelector(".box1");
  var closeButtons = document.querySelectorAll(".close");
  var profilePicInput = document.getElementById("profilePic");
  var profilePicPreview = document.getElementById("profilePicPreview");
  var saveButton = document.getElementById("saveButton");

  // Get the profile images
  var profileAvatarImg = document.querySelector('.profile-avatar img'); // Profile image in the profile card
  var avatarImg = document.querySelector('.avatar img'); // Avatar image in the profile section
  var avatar2Img = document.querySelector('.avatar2 img'); // Avatar image in the modal
  
  // Default profile image path
  var defaultAvatarPath = "C:\Users\jezre\Downloads\Profile\assets\profilepic.png";

  // Set initial avatar image for the modal when the page loads
  function setModalAvatar() {
    var currentAvatarSrc = avatarImg ? avatarImg.src : profileAvatarImg.src; // Fallback to profile avatar image if available
    avatar2Img.src = currentAvatarSrc || defaultAvatarPath; // Set avatar2 image source (with default fallback)
  }

  // Show the modal when box1 is clicked
  box1.addEventListener("click", function () {
    content1.style.display = "block";
    modal.style.display = "flex"; // Show the modal
    
    // Set the avatar in the modal to match the profile avatar
    setModalAvatar();
  });

  // Close the modal when close buttons are clicked
  closeButtons.forEach(function (btn) {
    btn.addEventListener("click", function () {
      modal.style.display = "none";
    });
  });

  // Close the modal when clicking outside of the modal content
  window.addEventListener("click", function (event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  });

  // Image preview functionality
  var avatar2 = document.querySelector('.avatar2');
  avatar2.addEventListener("click", function () {
    profilePicInput.click(); // Trigger the file input
  });

  // Handle the file input change for image preview
  profilePicInput.addEventListener("change", function (event) {
    var reader = new FileReader();
    reader.onload = function () {
      profilePicPreview.src = reader.result;
      profilePicPreview.style.display = "block"; // Show image preview
    };
    reader.readAsDataURL(event.target.files[0]);
  });

  // Save the changes when the Save button is clicked
  saveButton.addEventListener("click", function () {
    // Apply the new profile picture to both .avatar and .profile-avatar
    var newAvatarSrc = profilePicPreview.src || profileAvatarImg.src; // Default to current avatar if no new picture
    document.querySelector('.avatar img').src = newAvatarSrc;
    document.querySelector('.profile-avatar img').src = newAvatarSrc; // Update profile card avatar as well

    // Save the bio changes
    var bioText = document.querySelector('.bio-entry').value;
    document.querySelector('.bio').textContent = bioText; // Update the displayed bio on profile tab

    // Close the modal after saving
    modal.style.display = "none";
  });

  function uploadImage() {
    // Trigger the file input dialog
    document.getElementById("file-input").click();
  }
  
  function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
      const fileName = file.name;
      alert(`Image "${fileName}" has been selected.`);
      // Handle image preview or upload logic here
    }
  }
  
});