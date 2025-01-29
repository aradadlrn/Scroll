document.addEventListener("DOMContentLoaded", function () {
  // DOM Elements
  const modal = document.getElementById("myModal");
  const content1 = document.querySelector(".modal-content-edit-profile");
  const editProfile = document.querySelector(".edit-profile");
  const closeButtons = document.querySelectorAll(".close");
  const profilePicInput = document.getElementById("profilePic");
  const profilePicPreview = document.getElementById("profilePicPreview");
  const saveButton = document.getElementById("saveButton");
  //const imagePathInput = document.getElementById("imagePath");

  // Profile Images
  const profileAvatarImg = document.querySelector('.profile-avatar img');
  const avatarImg = document.querySelector('.avatar img');
  const avatar2Img = document.querySelector('.avatar2 img');
  const defaultAvatarPath = "./assets/profilepic.png";

  // Initialize modal avatar
  function setModalAvatar() {
    const currentAvatarSrc = avatarImg?.src || profileAvatarImg?.src || defaultAvatarPath;
    avatar2Img.src = currentAvatarSrc;
    imagePathInput.value = currentAvatarSrc; // Sync with hidden input
  }

  // Open modal
  editProfile.addEventListener("click", () => {
    content1.style.display = "block";
    modal.style.display = "flex";
    setModalAvatar();
    // Check for login data and update user info
    const loginData = localStorage.getItem('loginData');
    
    if (loginData) {
        try {
            const data = JSON.parse(loginData);
            
            if (data?.username) {
                const displayNameElement = document.querySelector('.display-name3');
                const usernameElement = document.querySelector('.username3');

                if (displayNameElement) {
                    displayNameElement.textContent = data.displayName || data.username;
                }
                if (usernameElement) {
                    usernameElement.textContent = `@${data.username}`;
                }
            }
        } catch (error) {
            console.error('Error parsing loginData:', error);
            localStorage.removeItem('loginData');
        }
    }
  });

  // Close modal
  closeButtons.forEach(btn => btn.addEventListener("click", () => modal.style.display = "none"));
  window.addEventListener("click", e => e.target === modal && (modal.style.display = "none"));

  // Upload image to server
  async function uploadImage(file) {
    const formData = new FormData();
    formData.append("profilePic", file);

    try {
      const response = await fetch("/upload", {
        method: "POST",
        body: formData,
      });
      const data = await response.json();
      return data.filePath; // Expects { filePath: "/uploads/filename.jpg" }
    } catch (error) {
      console.error("Upload failed:", error);
      return null;
    }
  }

  // Handle file input
  profilePicInput.addEventListener("change", async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Preview locally
    const reader = new FileReader();
    reader.onload = () => {
      profilePicPreview.src = reader.result;
      profilePicPreview.style.display = "block";
    };
    reader.readAsDataURL(file);

    // Upload to server
    const filePath = await uploadImage(file);
    if (filePath) imagePathInput.value = filePath;
  });

  // Save to database
  saveButton.addEventListener("click", async () => {
    const newAvatarPath = imagePathInput.value || defaultAvatarPath;
    const bioText = document.querySelector(".bio-entry").value;

    // Update UI
    avatarImg.src = newAvatarPath;
    profileAvatarImg.src = newAvatarPath;

    // Save to backend
    try {
      const response = await fetch("php-scripts/profile-pic.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          avatarPath: newAvatarPath,
          bio: bioText,
        }),
      });

      if (!response.ok) throw new Error("Save failed");
      console.log("Saved successfully!");
    } catch (error) {
      console.error("Error:", error);
    }

    modal.style.display = "none";
  });
});
