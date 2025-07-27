// Mobile menu toggle and subnav toggle
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.querySelector(".menu-toggle");
  const navMenu = document.querySelector("nav ul");
  const subnavs = document.querySelectorAll('.subnav');

  // Hamburger shows/hides main menu
  if (menuToggle && navMenu) {
    menuToggle.addEventListener("click", function (e) {
      navMenu.classList.toggle("active");
      if (!navMenu.classList.contains("active")) {
        subnavs.forEach(function (el) {
          el.classList.remove("open");
        });
      }
    });
  }

  // Subnav toggle for mobile (tap to open)
  subnavs.forEach(function (subnav) {
    const link = subnav.querySelector('a');
    if (link) {
      link.addEventListener("click", function (e) {
        // Only on mobile
        if (window.innerWidth <= 900) {
          e.preventDefault();
          // Close other subnavs
          subnavs.forEach(function (el) {
            if (el !== subnav) {
              el.classList.remove("open");
            }
          });
          subnav.classList.toggle("open");
        }
      });
    }
  });

  // Close menu/subnav if clicking outside (mobile)
  document.addEventListener("click", function (e) {
    if (window.innerWidth <= 900 && navMenu.classList.contains("active")) {
      if (!navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
        navMenu.classList.remove("active");
        subnavs.forEach(function (el) {
          el.classList.remove("open");
        });
      }
    }
  });
});


fetch('footer.html')
  .then(response => response.text())
  .then(data => {
    document.getElementById('footer').innerHTML = data;
  });

// Optional: Automatically update year
document.getElementById('currentYear').textContent = new Date().getFullYear();


// var ddmenuOptions=
// {
//     menuId: "menu",
//     linkIdToMenuHtml: "menuLink",
//     open: "onmouseover",  //or "onclick"
//     delay: 90,
//     speed: 400,
//     keysNav: true,
//     singleOpen: false, // new since v2017.3.17
//     license: "mylicense"
// };

// var ddmenu = new Ddmenu(ddmenuOptions);


