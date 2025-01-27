document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('signupForm');
  const errorMessage = document.getElementById('error-message');

  form.addEventListener('submit', function (event) {
      event.preventDefault(); // Prevent the default form submission

      // Get the form inputs
      const displayName = document.getElementById('displayName').value.trim();
      const name = document.getElementById('name').value.trim();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm-password').value;
      const terms = document.getElementById('terms').checked;

      // Clear any previous error message
      errorMessage.textContent = '';

      let error = '';

      // Validate the form inputs
      if (!displayName){
          error = 'Please enter your preferred name';
      } else if (!name) {
          error = 'Please enter your username.';
      } else if (!email) {
          error = 'Please enter your email.';
      } else if (!password) {
          error = 'Please enter your password.';
      } else if (!confirmPassword) {
          error = 'Please confirm your password.';
      } else if (password !== confirmPassword) {
          error = 'Passwords do not match.';
      } else if (!terms) {
          error = 'You must accept the terms and conditions.';
      }

      if (error) {
          errorMessage.textContent = error;
          return;
      }

      // If no errors, proceed with form submission
      const formData = new FormData(form);

      fetch('php-scripts/signup.php', {
          method: 'POST',
          body: formData,
      })
      .then(response => {
          if (!response.ok) {
              throw new Error('Network response was not ok');
          }
          return response.json();
      })
      .then(data => {
          if (data.success) {
              // Redirect to the homepage on successful signup
              window.location.href = data.redirect;
          } else {
              // Display the error message
              errorMessage.textContent = data.error;
          }
      })
      .catch(error => {
          console.error('Error:', error);
          errorMessage.textContent = 'An error occurred. Please try again.';
      });
  });
});
