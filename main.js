document.addEventListener('DOMContentLoaded', function() {
  const burgerIcon = document.querySelector('.burger-icon');
  const sideMenu = document.querySelector('.side-menu');
  const closeBtn = sideMenu.querySelector('.btn-close');
  const dropdownToggle = sideMenu.querySelector('.dropdown-toggle');
  const dropdownMenu = sideMenu.querySelector('.dropdown-menu');

  function toggleMenu() {
      burgerIcon.classList.toggle('open');
      sideMenu.classList.toggle('open');
  }

  burgerIcon.addEventListener('click', toggleMenu);
  closeBtn.addEventListener('click', toggleMenu);

  

  // Close the menu when a nav item is clicked (except for dropdown toggle and its items)
  const navItems = sideMenu.querySelectorAll('.nav-link:not(.dropdown-toggle):not(.dropdown-item)');
  navItems.forEach(item => {
      item.addEventListener('click', toggleMenu);
  });

  // Close the menu when clicking outside
  document.addEventListener('click', function(event) {
      if (!sideMenu.contains(event.target) && !burgerIcon.contains(event.target) && sideMenu.classList.contains('open')) {
          toggleMenu();
      }
  });
});





/* Ripple Effect */

document.getElementById('signUpBtn').addEventListener('click', function(e) {
    let ripple = document.createElement('span');
    this.appendChild(ripple);
    let x = e.clientX - e.target.offsetLeft;
    let y = e.clientY - e.target.offsetTop;
    ripple.style.left = `${x}px`;
    ripple.style.top = `${y}px`;
    setTimeout(() => {
        ripple.remove();
    }, 300);
});







 


  /* Signup Form */

  $(document).ready(function() {
    $('#signupFormElement').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'signup.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // Optionally, clear the form or close the modal
                    $('#signupFormElement')[0].reset();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});




