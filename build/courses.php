<?php require_once "inc/header.php"; ?>

<?php require_once "inc/nav.php"; ?>

<body>


<script>
  // Mobile Menu Toggle with icon change
  const menuBtn = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const menuIcon = menuBtn.querySelector('i');

  menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    
    if (mobileMenu.classList.contains('hidden')) {
      menuIcon.classList.remove('fa-times');
      menuIcon.classList.add('fa-bars');
    } else {
      menuIcon.classList.remove('fa-bars');
      menuIcon.classList.add('fa-times');
    }
  });

  // Dropdown Toggle for Mobile only
  function toggleDropdown(menuId, iconId) {
    const dropdowns = [
      {menu: 'courses-mobile', icon: 'courses-icon-mobile'},
      {menu: 'login-mobile', icon: 'login-icon-mobile'},
    ];

    dropdowns.forEach(item => {
      const menu = document.getElementById(item.menu);
      const icon = document.getElementById(item.icon);

      if (item.menu === menuId) {
        menu.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
      } else {
        menu.classList.add('hidden');
        icon.classList.remove('rotate-180');
      }
    });
  }
</script>



<section class="h-96 bg-fixed bg-center bg-no-repeat bg-cover" style="background-image: url('./assets/img/about.png');">
  <div class="h-96 flex items-center justify-center bg-black bg-opacity-80">
    <h1 class="md:text-4xl text-2xl font-bold text-white">Courses</h1>
  </div>
</section>

<section>
    <div>
        <div>
            <h1 class="">Achievement</h1>
        </div>
        <div>

        </div>
        <div>
            
        </div>
    </div>
</section>

<!-- footer  -->
 
<footer class="bg-black lg:py-20 py-10 xl:px-20 lg:px-10 px-5">
  <div class="flex md:justify-between justify-center items-center md:flex-row flex-col">
    <div class="flex flex-col justify-center">
      <img src="./assets/img/" alt="">
      <h1 class="text-2xl font-bold text-white">Learning Hub</h1>
    </div>
    <div class="pt-5 md:pt-0">
       <h1 class="md:text-2xl text-xl font-bold text-white">Quick Links</h1>
      <div class="flex flex-col justify-center items-center pt-6 space-y-4">
        <a href="index.html" class="text-white font-medium text-base hover:underline">Home</a>
        <a href="courses.html" class="text-white font-medium text-base hover:underline">Course</a>
        <a href="about.html" class="text-white font-medium text-base hover:underline">About</a>
        <a href="contact.html" class="text-white font-medium text-base hover:underline">Contact</a>
        </div>
    </div>
    
    <div class="space-y-4 flex flex-col justify-center items-center pt-5 md:pt-0">
      <h1 class="md:text-2xl text-xl font-bold text-white">Contact Info</h1>
      <p class="text-white text-base "><i class="fa-solid fa-location-dot"></i>&nbsp; Lahore, Pakistan</p>
      <p class="text-white text-base "><i class="fa-solid fa-envelope"></i>&nbsp; learninghub@gmail.com</p>
      <p class="text-white text-base "><i class="fa-solid fa-phone"></i>&nbsp; 0562929978</p>
    </div>
  </div>
</footer>


  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>


</body>
</html>