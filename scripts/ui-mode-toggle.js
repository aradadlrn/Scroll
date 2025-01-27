const toggleBtn = document.getElementById('themeToggle');
const iconImg = document.querySelector('.theme-icon');

// Load the saved theme from localStorage
if (localStorage.getItem('theme') === 'light') {
  document.body.classList.add('light');
  iconImg.src = "./assets/Vector.png";
}

toggleBtn.addEventListener('click', () => {
  document.body.classList.toggle('light');
  if (document.body.classList.contains('light')) {
    iconImg.src = "./assets/Vector.png";
    localStorage.setItem('theme', 'light');
  } else {
    iconImg.src = "./assets/moon0.svg";
    localStorage.setItem('theme', 'dark');
  }
});
