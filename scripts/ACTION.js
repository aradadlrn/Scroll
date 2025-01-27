document.addEventListener("DOMContentLoaded", () => {
  // Grab the toggle button <div id="themeToggle">
  const toggleBtn = document.getElementById('themeToggle');

  // Grab the <img> inside it
  const iconImg = document.querySelector('.theme-icon');

  // Apply saved theme on page load (check if theme is in localStorage)
  const savedMode = localStorage.getItem('theme') || 'dark'; // default to dark if no theme found
  if (savedMode === 'light') {
    document.body.classList.add('light');
    iconImg.src = '/scroll-site2/assets/Vector.png'; // Light mode icon
  } else {
    document.body.classList.remove('light');
    iconImg.src = '/scroll-site2/assets/moon0.svg'; // Dark mode icon
  }

  // Remove the no-transition class to enable transitions after theme is applied
  document.body.classList.remove('no-transition');

  // Add event listener for the theme toggle button
  toggleBtn.addEventListener('click', () => {
    // Check if the body has the 'light' class
    const isLightMode = document.body.classList.contains('light');

    // Toggle the 'light' class and update the icon
    if (isLightMode) {
      document.body.classList.remove('light');
      iconImg.src = './assets/moon0.svg'; // Dark mode icon
      localStorage.setItem('theme', 'dark'); // Save dark mode to localStorage
    } else {
      document.body.classList.add('light');
      iconImg.src = './assets/Vector.png'; // Light mode icon
      localStorage.setItem('theme', 'light'); // Save light mode to localStorage
    }
  });
});