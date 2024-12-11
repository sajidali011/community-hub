

// Function to show the correct content
function showContent(sectionId) {
  const contents = document.querySelectorAll('.content-item');
  contents.forEach(content => content.classList.remove('active'));

  const activeContent = document.getElementById(sectionId);
  activeContent.classList.add('active');
}

// JavaScript to toggle dropdown visibility on click
document.querySelector('.menubar img').addEventListener('click', function() {
  const dropdownContent = document.querySelector('.dropdown-content');
  dropdownContent.style.display = (dropdownContent.style.display === 'block') ? 'none' : 'block';
});
