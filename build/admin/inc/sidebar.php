<?php
// ─── Badge Logic ───
// Check pending users
$r = $conn->query("SELECT COUNT(*) as c FROM users WHERE verify_status='pending'");
$pending_users_count = $r ? $r->fetch_assoc()['c'] : 0;

// Check pending complaints
$r = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE status='Pending'");
$pending_complaints_count = $r ? $r->fetch_assoc()['c'] : 0;

// Check unread notifications
$r = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE is_read=0");
$unread_notifications_count = $r ? $r->fetch_assoc()['c'] : 0;

function show_badge($count) {
    if ($count > 0) {
        return "<span class=\"ml-auto bg-red-600 text-white text-[10px] font-bold rounded-full px-2 py-0.5\">$count</span>";
    }
    return "";
}
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-20 w-64 bg-white border-r border-gray-200 transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0">
      <div class="px-8 py-6 flex items-center gap-3">
          <img src="../assets/img/teachmate_logo.png" alt="TeachMate Logo" class="w-12 h-12 rounded-[10px] shadow-md shadow-blue-200">
          <div>
              <h2 class="text-xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500">TeachMate</h2>
              <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">LMS Admin</p>
          </div>
      </div>

      <nav class="mt-6 px-4 space-y-2">
        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mb-2">Main Menu</p>
        
        <a href="./admindashboard.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
          <i class="fas fa-th-large"></i>
          <span class="text-sm font-medium">Dashboard</span>
        </a>

        <a href="./adduser.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
          <i class="fas fa-user-plus"></i>
          <span class="text-sm font-medium">Manage User</span>
          <?= show_badge($pending_users_count) ?>
        </a>

        <a href="./addcourse.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
          <i class="fas fa-book"></i>
          <span class="text-sm font-medium">Add Course</span>
        </a>



        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mt-6 mb-2">Other</p>

        <a href="./complaints.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
          <i class="fas fa-comment-dots"></i>
          <span class="text-sm font-medium">Student Complaints</span>
          <?= show_badge($pending_complaints_count) ?>
        </a>

        <a href="./certification.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
          <i class="fas fa-certificate"></i>
          <span class="text-sm font-medium">Issue Certification</span>
        </a>

        <a href="./profile.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
          <i class="fas fa-user-circle"></i>
          <span class="text-sm font-medium">Profile Settings</span>
        </a>

        <div class="pt-10">
          <a href="../index.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-red-500 hover:bg-red-50 transition">
            <i class="fas fa-sign-out-alt"></i>
            <span class="text-sm font-medium">Logout</span>
          </a>
        </div>
      </nav>
    </aside>
