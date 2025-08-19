<nav class="bg-black py-6 xl:px-20 lg:px-10 px-5   ">
  <div class="flex justify-between items-center text-white ">
    <div class="flex items-center space-x-4">
      <img src="" alt="">
      <h1 class="md:text-2xl text-xl font-bold">Skill Flow</h1>
    </div>

    <!-- Menu -->
    <div class="hidden lg:flex">
      <ul class="flex space-x-8 items-center">

        <li><a href="./index.php" class="font-medium text-base">Home</a></li>

        <!-- Courses Dropdown (Desktop Hover) -->
        <li class="relative group">
          <a href="./web.php" class="font-medium text-base flex items-center space-x-1">
            <span>Courses</span>
            <i class="fas fa-chevron-down transition-transform duration-300 group-hover:rotate-180"></i>
          </a>

          <ul class="absolute bg-white text-black rounded shadow-lg w-44 hidden group-hover:block">
            <li><a href="./web.php" class="block px-4 py-2 hover:bg-black hover:text-white">Web Development</a></li>
            <li><a href="./game.php" class="block px-4 py-2 hover:bg-black hover:text-white">Game Development</a></li>
            <li><a href="./graphics.php" class="block px-4 py-2 hover:bg-black hover:text-white">Graphic Design</a></li>
            <li><a href="./digital.php" class="block px-4 py-2 hover:bg-black hover:text-white">Digital Marketing</a></li>
          </ul>
        </li>

        <li><a href="./about.php" class="font-medium text-base">About Us</a></li>
        <li><a href="contact.php" class="font-medium text-base">Contact Us</a></li>

        <!-- Login Dropdown (Desktop Hover) -->
        <li class="relative group">
          <a href="./login.php" class="font-medium text-base flex items-center space-x-1">
            <span>Log In</span>
           
          </a>

        
        </li>

      </ul>
    </div>

    <!-- Mobile Menu Button -->
    <div class="lg:hidden">
      <button id="menu-btn">
        <i class="fas fa-bars text-2xl"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="lg:hidden mt-5 text-white hidden text-center items-center">
    <ul class="space-y-4">
      <li><a href="#" class="block font-medium text-base">Home</a></li>

      <!-- Courses Dropdown (Mobile Click) -->
      <li>
        <button onclick="toggleDropdown('courses-mobile', 'courses-icon-mobile')" class="flex justify-center items-center space-x-2 font-medium text-base w-full">
          <span>Courses</span>
          <i id="courses-icon-mobile" class="fas fa-chevron-down transition-transform duration-300"></i>
        </button>
        <ul id="courses-mobile" class="ml-4 mt-2 space-y-2 hidden">
          <li><a href="#" class="block text-base">Web Development</a></li>
          <li><a href="#" class="block text-base">Graphic Design</a></li>
          <li><a href="#" class="block text-base">Digital Marketing</a></li>
          <li><a href="#" class="block text-base">AI & ML Basics</a></li>
        </ul>
      </li>

      <li><a href="#" class="block font-medium text-base">About Us</a></li>
      <li><a href="#" class="block font-medium text-base">Contact Us</a></li>

      <!-- Login Dropdown (Mobile Click) -->
      <li>
        <button onclick="toggleDropdown('login-mobile', 'login-icon-mobile')" class="flex justify-center items-center space-x-2 font-medium text-base w-full">
          <span>Log In</span>
 
        </button>
     
      </li>
    </ul>
  </div>
</nav>