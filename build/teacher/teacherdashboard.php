<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

// Fetch stats
$total_students = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='student'")->fetch_assoc()['total'] ?? 0;
$active_courses = $conn->query("SELECT COUNT(*) AS total FROM courses")->fetch_assoc()['total'] ?? 0;
$materials_shared = $conn->query("SELECT COUNT(*) AS total FROM materials")->fetch_assoc()['total'] ?? 0;
$pending_grading = $conn->query("SELECT COUNT(*) AS total FROM grading WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
// $live_chat_requests = $conn->query("SELECT COUNT(*) AS total FROM chat_requests WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
$assignments_submitted = $conn->query("SELECT COUNT(*) AS total FROM assignments")->fetch_assoc()['total'] ?? 0;

// Monthly Progress % (Dummy calculation for now)
$monthly_progress = 78;

// Active user today (from visit_statistics)
$today = date("Y-m-d");
$active_users_today = $conn->query("SELECT visit_count FROM visit_statistics WHERE visit_date='$today'")->fetch_assoc()['visit_count'] ?? 0;
?>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
  
<?php require_once "inc/sidebar.php"; ?>
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
       <?php require_once "inc/topbar.php"; ?>

      <div class=" p-6">
    <h1 class="text-3xl font-bold mb-6">Teacher Dashboard</h1>

    <!-- Top stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Total Students</div>
          <div class="text-3xl font-semibold"><?= $total_students ?></div>
        </div>
        <div class="text-gray-400 text-3xl">👤</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Active Courses</div>
          <div class="text-3xl font-semibold"><?= $active_courses ?></div>
        </div>
        <div class="text-gray-400 text-3xl">📚</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Materials Shared</div>
          <div class="text-3xl font-semibold"><?= $materials_shared ?></div>
        </div>
        <div class="text-gray-400 text-3xl">🔗</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Pending Grading</div>
          <div class="text-3xl font-semibold"><?= $pending_grading ?></div>
        </div>
        <div class="text-gray-400 text-3xl">📝</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Live Chat Requests</div>
          <div class="text-3xl font-semibold"><p>10</p></div>
        </div>
        <div class="text-gray-400 text-3xl">💬</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Monthly Progress %</div>
          <div class="text-3xl font-semibold"><?= $monthly_progress ?>%</div>
        </div>
        <div class="text-gray-400 text-3xl">📈</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center col-span-full sm:col-span-2 lg:col-span-1">
        <div>
          <div class="text-xs text-gray-500 uppercase">Assignments Submitted</div>
          <div class="text-3xl font-semibold"><?= $assignments_submitted ?></div>
        </div>
        <div class="text-gray-400 text-3xl">📥</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Active User Today</div>
          <div class="flex items-baseline gap-1">
            <div class="text-3xl font-semibold"><?= $active_users_today ?></div>
            <div class="text-xs text-green-600">Excellent</div>
          </div>
        </div>
        <div class="relative w-16 h-16">
          <svg class="w-full h-full text-gray-200" viewBox="0 0 36 36">
            <path stroke="currentColor" stroke-width="4" fill="none"
              d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831" />
          </svg>
          <svg class="absolute top-0 left-0 w-full h-full text-indigo-600" viewBox="0 0 36 36">
            <path stroke="currentColor" stroke-width="4" fill="none" stroke-dasharray="70,100"
              d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Keep rest of your page same -->
  </div>
</div>
</div>
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
