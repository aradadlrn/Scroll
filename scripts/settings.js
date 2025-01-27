document.getElementById('changePasswordForm').addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent the default form submission

    const form = event.target;
    const formData = new FormData(form);
    const message = document.getElementById('message');

    // Validate new password and confirm password
    const newPassword = formData.get('new-password');
    const confirmPassword = formData.get('confirm-password');

    if (newPassword !== confirmPassword) {
        message.textContent = 'New password and confirm password do not match.';
        message.style.color = 'red';
        return;
    }

    try {
        // Send the form data to the PHP backend
        const response = await fetch(form.action, {
            method: 'POST', // Ensure this is POST
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            message.textContent = result.message;
            message.style.color = 'green';
        } else {
            message.textContent = result.message;
            message.style.color = 'red';
        }
    } catch (error) {
        message.textContent = 'An error occurred. Please try again.';
        message.style.color = 'red';
        console.error(error);
    }
});

// Session Handling
document.addEventListener('DOMContentLoaded', function () {
    // Check if the user is logged in
    const loginData = localStorage.getItem('loginData');
  
    if (loginData) {
      try {
        const data = JSON.parse(loginData);
  
        // Update settings card with user data
        const usernameElement = document.querySelector('.settingsUsername');
        const displayNameElement = document.querySelector('.full-name');
  
        if (displayNameElement) {
          displayNameElement.textContent = data.displayName || data.username;
        }
        if (usernameElement) {
          usernameElement.textContent = data.username;
        }
      } catch (error) {
        console.error('Error parsing loginData:', error);
        localStorage.removeItem('loginData'); // Clear invalid login data
        window.location.href = 'Login.html'; // Redirect to login page
      }
    } else {
      // Redirect to login if no session data is found
      window.location.href = 'Login.html';
    }});

// For Modal stuff
// Modal and form elements
const openEditNameModal = document.getElementById('openEditNameModal');
const cancelNew = document.getElementById('cancelNew');
const modal = document.getElementById('formHolder');
const displayNameForm = document.getElementById('displayNameModal');
const newDisplayNameInput = document.getElementById('newDisplayName');
const confirmNew = document.getElementById('confirmNew');

// Show the modal
openEditNameModal.addEventListener('click', () => {
    modal.style.display = 'flex'; // Display the modal as a flexbox
});

// Hide the modal when the cancel button is clicked
cancelNew.addEventListener('click', () => {
    modal.style.display = 'none'; // Hide the modal
});

// Hide the modal when clicking outside of it
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none'; // Hide the modal
    }
});

// Handle form submission
displayNameForm.addEventListener('submit', async (event) => {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(displayNameForm); // Collect form data

    try {
        // Send the form data to the PHP backend
        const response = await fetch(displayNameForm.action, {
            method: 'POST',
            body: formData,
        });

        const result = await response.json();

        if (result.status === 'success') {
            alert(result.message); // Show success message

            // Update the display name on the page
            const displayNameElement = document.querySelector('.full-name');
            const usernameElement = document.querySelector('.settingsUsername');

            if (displayNameElement) {
                displayNameElement.textContent = formData.get('newDisplayName');
            }

            // Optional: Also update the username if needed
            if (usernameElement) {
                usernameElement.textContent = formData.get('newDisplayName');
            }

            // Close the modal and clear the input
            modal.style.display = 'none';
            newDisplayNameInput.value = '';
        } else {
            alert(result.message); // Show error message
        }
    } catch (error) {
        alert('An error occurred. Please try again.'); // Show error message
        console.error(error);
    }
});
