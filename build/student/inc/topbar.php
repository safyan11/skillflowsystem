<<<<<<< HEAD
<?php
// Fetch student info for topbar
$topbar_user_name = 'Guest';
$topbar_profile_img = 'https://ui-avatars.com/api/?name=Student&background=3b82f6&color=fff';

if (isset($_SESSION['user_id'])) {
    $tb_uid = intval($_SESSION['user_id']);
    $tb_stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
    $tb_stmt->bind_param("i", $tb_uid);
    $tb_stmt->execute();
    $tb_stmt->bind_result($tb_name, $tb_img);
    if ($tb_stmt->fetch()) {
        $topbar_user_name = htmlspecialchars($tb_name);
        if (!empty($tb_img)) $topbar_profile_img = "../uploads/profile/" . $tb_img;
    }
    $tb_stmt->close();
}
?>
<header class="px-6 py-4 bg-white border-b border-gray-100 sticky top-0 z-10">
    <div class="flex items-center justify-between gap-4">
        <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-500 hover:text-blue-600 focus:outline-none transition">
            <i class="fas fa-bars text-xl"></i>
        </button>


        <!-- Search -->
        <form method="GET" action="studentdashboard.php" class="relative w-full max-w-md">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
            </div>
            <input type="text" name="search" placeholder="Search courses..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
        </form>

        <!-- Profile Link -->
        <div class="relative flex-shrink-0">
            <a href="./profile.php" class="flex items-center gap-3 p-1.5 pr-4 bg-gray-50 hover:bg-gray-100 rounded-2xl border border-gray-200 transition cursor-pointer">
                <div class="w-9 h-9 rounded-xl overflow-hidden border-2 border-white shadow-sm">
                    <img src="<?= $topbar_profile_img ?>" class="w-full h-full object-cover" alt="avatar">
                </div>
                <div class="hidden sm:block text-left">
                    <p class="text-xs font-bold text-gray-800 leading-none mb-0.5"><?= $topbar_user_name ?></p>
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest">Active Student</p>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-gray-400 ml-1"></i>
            </a>
=======
      <header class="px-6 py-4 bg-white/80 backdrop-blur-md sticky top-0 z-10 border-b border-slate-100">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-4 lg:gap-8">
          
          <!-- Command Search -->
          <form method="GET" action="studentdashboard.php" class="relative w-full lg:max-w-xl group">
            <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 group-focus-within:text-blue-600 transition"></i>
            </div>
            <input type="text" name="search" placeholder="Search for courses, modules, or labs..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                   class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-3.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm group-hover:shadow-md">
          </form>

          <!-- Interaction Suite -->
          <div class="flex items-center gap-6 w-full lg:w-auto justify-between lg:justify-end">
            <!-- Shortcuts -->
            <div class="hidden md:flex items-center gap-4">
                <button class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition shadow-sm">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <div class="h-6 w-[1px] bg-slate-100"></div>
            </div>

            <!-- Identity Core -->
            <div class="relative group">
              <?php
              $user_name = 'Guest Participant';
              $profile_img = 'https://ui-avatars.com/api/?name=Guest&background=0D8ABC&color=fff';
              
              if (isset($_SESSION['user_id'])) {
                  $user_id = intval($_SESSION['user_id']);
                  $stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id = ?");
                  $stmt->bind_param("i", $user_id);
                  $stmt->execute();
                  $stmt->bind_result($name, $db_image);
                  if ($stmt->fetch()) {
                      $user_name = htmlspecialchars($name);
                      if (!empty($db_image)) $profile_img = "../uploads/profile/" . $db_image;
                  }
                  $stmt->close();
              }
              ?>
              <button class="flex items-center gap-3 p-1.5 pr-4 bg-slate-50 hover:bg-slate-100 rounded-2xl transition border border-slate-100 group">
                <div class="w-10 h-10 rounded-xl overflow-hidden border-2 border-white shadow-sm ring-2 ring-transparent group-hover:ring-blue-600/20 transition">
                    <img src="<?= $profile_img ?>" class="w-full h-full object-cover">
                </div>
                <div class="text-left hidden sm:block">
                    <p class="text-[11px] font-black text-slate-900 leading-none mb-0.5"><?= $user_name ?></p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Active Student</p>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:text-blue-600 transition ml-2"></i>
              </button>

              <!-- Premium Dropdown (Hover) -->
              <div class="absolute right-0 top-full mt-2 w-56 opacity-0 translate-y-2 pointer-events-none group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto transition-all duration-300 z-50">
                  <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 p-3">
                      <a href="profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 transition text-slate-600 hover:text-blue-600">
                          <i class="fa-solid fa-id-card-clip text-xs"></i>
                          <span class="text-xs font-black uppercase tracking-widest">Profile Matrix</span>
                      </a>
                      <a href="certificate.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 transition text-slate-600 hover:text-emerald-600">
                          <i class="fa-solid fa-award text-xs"></i>
                          <span class="text-xs font-black uppercase tracking-widest">Achievements</span>
                      </a>
                      <div class="my-2 border-t border-slate-50"></div>
                      <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition group/out">
                          <i class="fa-solid fa-power-off text-xs group-hover/out:rotate-90 transition duration-500"></i>
                          <span class="text-xs font-black uppercase tracking-widest">Disconnect</span>
                      </a>
                  </div>
              </div>
            </div>
          </div>
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9
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
