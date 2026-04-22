<<<<<<< HEAD
<?php
// Fetch user info for topbar
$topbar_user_name = 'Guest';
$topbar_profile_img = 'https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff';

if (isset($_SESSION['user_id'])) {
    $tb_uid = intval($_SESSION['user_id']);
    $tb_stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
    $tb_stmt->bind_param("i", $tb_uid);
    $tb_stmt->execute();
    $tb_stmt->bind_result($tb_name, $tb_img);
    if ($tb_stmt->fetch()) {
        $topbar_user_name = htmlspecialchars($tb_name);
        if (!empty($tb_img)) $topbar_profile_img = "../uploads/profile/" . $tb_img;
=======
      <header class="px-4 py-4 border-b border-gray-200 bg-white">
         <div class="flex justify-between items-center space-x-4">
           <img src="../assets/img/logodashboard.png" class="w-20 md:hidden block" alt="">
           <button id="menu-btn" class="md:hidden p-2 rounded-md border border-gray-300">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
             </svg>
           </button>
         
         </div>

         <div class="flex md:flex-row flex-col-reverse justify-between items-center space-x-4 pt-10 md:pt-0">
           <div class="relative w-full lg:w-1/2">
             <input type="text" placeholder="Search" class="pl-3 pr-10 py-2 border rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-indigo-400" />
             <div class="absolute right-2 top-1/2 -translate-y-1/2">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
               </svg>
             </div>
           </div>


          <div class="relative" x-data="{ open: false }">
   <button id="user-menu-btn" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-2 px-3 py-2" onclick="toggleUserMenu()">

    <?php
require '../inc/db.php';
$user_name = 'Guest';
$profile_img = 'https://i.pravatar.cc/32'; // Default

if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name, $db_image);
    if ($stmt->fetch()) {
        $user_name = htmlspecialchars($name);
        if (!empty($db_image)) {
            $profile_img = "../uploads/profile/" . $db_image;
        }
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9
    }
    $tb_stmt->close();
}
?>
<<<<<<< HEAD
<header class="px-6 py-4 bg-white border-b border-gray-100 sticky top-0 z-10">
    <div class="flex items-center justify-between gap-4">
        <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-500 hover:text-blue-600 focus:outline-none transition">
            <i class="fas fa-bars text-xl"></i>
        </button>
=======
    <img src="<?= $profile_img ?>" alt="avatar" class="w-8 h-8 rounded-full object-cover" />
<span class="text-base font-medium"><?= $user_name ?></span>
  
    <!-- <svg id="chevron" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
    </svg> -->
  </button>

  <!-- dropdown -->
  <!-- <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-1 hidden z-30" role="menu" aria-label="User menu">
    <div class="px-4 py-2 text-xs text-gray-500">Signed in as</div>
    <div class="px-4 pb-2">
      <div class="font-semibold text-sm">Roh_ul_Hussnain</div>
      <div class="text-gray-400 text-xs">Finance</div>
    </div>
    <div class="border-t border-gray-100 my-1"></div>
    <button onclick="handleLogout()" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2" role="menuitem">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
      </svg>
      <span class="text-sm text-red-600">Logout</span>
    </button>
  </div> -->
</div>
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9


        <!-- Search -->
        <div class="relative w-full max-w-md">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-sm"></i>
            </div>
            <input type="text" placeholder="Search..."
                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
        </div>

        <!-- Profile Dropdown -->
        <div class="relative group flex-shrink-0">
            <a href="./profile.php" class="flex items-center gap-3 p-1.5 pr-4 bg-gray-50 hover:bg-gray-100 rounded-2xl border border-gray-200 transition cursor-pointer">
                <div class="w-9 h-9 rounded-xl overflow-hidden border-2 border-white shadow-sm">
                    <img src="<?= $topbar_profile_img ?>" class="w-full h-full object-cover" alt="avatar">
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-xs font-bold text-gray-800 leading-none mb-0.5"><?= $topbar_user_name ?></p>
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest">LMS Admin</p>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-gray-400 ml-1"></i>
            </a>
        </div>

    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    if(btn && sidebar) {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('-translate-x-full');
        });
        document.addEventListener('click', (e) => {
            if(!sidebar.contains(e.target) && !btn.contains(e.target) && !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    }
});
</script>
