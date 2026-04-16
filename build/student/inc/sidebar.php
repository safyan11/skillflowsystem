<?php
// ─── State Orchestration for Student Participant ───
$student_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Real-time metric synchronization
$r = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE is_read=0");
$unread_notifications_count = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM chat_messages WHERE receiver_id=$student_id AND is_read=0");
$unread_chat_count = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM complaints WHERE student_id=$student_id AND status='Pending'");
$pending_complaints_count = $r ? $r->fetch_assoc()['c'] : 0;

function student_badge($count) {
    if ($count > 0) {
        return "<span class=\"ml-auto bg-blue-600 text-white text-[9px] font-black rounded-lg px-2 py-0.5 shadow-sm shadow-blue-200\">$count</span>";
    }
    return "";
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside id="sidebar" class="fixed inset-y-0 left-0 z-20 w-72 bg-white border-r border-slate-100 transform -translate-x-full transition-all duration-300 ease-in-out md:translate-x-0 overflow-y-auto">
    <div class="px-8 py-6 flex items-center gap-3">
        <img src="../assets/img/teachmate_logo.png" alt="TeachMate Logo" class="w-12 h-12 rounded-[10px] shadow-md shadow-blue-200">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500">TeachMate</h2>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">Student Hub</p>
        </div>
    </div>

    <nav class="mt-6 px-4 space-y-2">
        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mb-2">Academic Hub</p>
        
        <a href="./studentdashboard.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-th-large"></i>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        <a href="./playlist.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-play"></i>
            <span class="text-sm font-medium">Course Playlist</span>
        </a>

        <a href="./material.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-file-alt"></i>
            <span class="text-sm font-medium">Materials</span>
        </a>

        <a href="./assignment.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-tasks"></i>
            <span class="text-sm font-medium">Assignments</span>
        </a>

        <a href="./onlineclass.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-video"></i>
            <span class="text-sm font-medium">Online Classes</span>
        </a>

        <p class="text-xs text-gray-400 uppercase font-semibold px-2 mt-6 mb-2">Account</p>

        <a href="./feedback.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-comment"></i>
            <span class="text-sm font-medium">Feedback</span>
        </a>

        <a href="./certificate.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-certificate"></i>
            <span class="text-sm font-medium">Certification</span>
        </a>

        <a href="./complains.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-exclamation-circle"></i>
            <span class="text-sm font-medium">Complaints</span>
            <?= student_badge($pending_complaints_count) ?>
        </a>

        <a href="./profile.php" class="sidebar-link flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
            <i class="fas fa-user-circle"></i>
            <span class="text-sm font-medium">Profile</span>
        </a>

    <div class="pt-10">
      <a href="../index.php" class="flex items-center space-x-3 px-6 py-4 rounded-[1.5rem] text-rose-500 bg-rose-50/50 hover:bg-rose-500 hover:text-white transition duration-300 font-bold italic">
        <i class="fa-solid fa-door-open"></i>
        <span class="text-xs uppercase tracking-widest">Logout</span>
      </a>
    </div>
  </nav>
</aside>
