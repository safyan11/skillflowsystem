<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

// Registered Users
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];

// Pending Complaints
$pendingComplaints = $conn->query("SELECT COUNT(*) AS total FROM complaints WHERE status = 'pending'")->fetch_assoc()['total'];

// Certificates
$totalCertificates = $conn->query("SELECT COUNT(*) AS total FROM certificates")->fetch_assoc()['total'];

// Feedback Received (Assuming you have a 'feedback' table)
$feedbackCount = $conn->query("SELECT COUNT(*) AS total FROM feedback")->fetch_assoc()['total'] ?? 0;

// Remove Users (banned or deleted - assuming verify_status='ban')
$bannedUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE verify_status = 'ban'")->fetch_assoc()['total'];

// Complaints Resolved
$resolvedComplaints = $conn->query("SELECT COUNT(*) AS total FROM complaints WHERE status = 'resolved'")->fetch_assoc()['total'];
$complaintResolveRate = ($resolvedComplaints + $pendingComplaints) > 0 ? round(($resolvedComplaints / ($resolvedComplaints + $pendingComplaints)) * 100, 2) : 0;

// Active Teachers
$activeTeachers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'teacher' AND verify_status = 'approved'")->fetch_assoc()['total'];

// Active Users Today (visit_statistics table)
$today = date("Y-m-d");
$activeToday = $conn->query("SELECT visit_count FROM visit_statistics WHERE visit_date = '$today'")->fetch_assoc()['visit_count'] ?? 0;

// Most Active Student (based on submissions)
$mostActiveStudent = $conn->query("
    SELECT u.name, COUNT(s.id) AS submissions 
    FROM assignment_submissions s 
    JOIN users u ON s.student_id = u.id 
    GROUP BY s.student_id 
    ORDER BY submissions DESC 
    LIMIT 1
")->fetch_assoc();
$mostActiveStudentName = $mostActiveStudent['name'] ?? 'N/A';

// Top Scorer (based on grading)
$topScorer = $conn->query("
    SELECT u.name, MAX(g.obtained_marks) as marks
    FROM grading g
    JOIN users u ON g.student_id = u.id
    GROUP BY g.student_id
    ORDER BY marks DESC
    LIMIT 1
")->fetch_assoc();
$topScorerName = $topScorer['name'] ?? 'N/A';
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">

  <?php require_once "inc/sidebar.php"; ?>

    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

    <!-- Top stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Registered Users</div>
          <div class="text-3xl font-semibold"><?= $totalUsers ?></div>
        </div>
        <div class="text-gray-400 text-3xl">👤</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Pending Complaints</div>
          <div class="text-3xl font-semibold"><?= $pendingComplaints ?></div>
        </div>
        <div class="text-gray-400 text-3xl">📚</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Certificates</div>
          <div class="text-3xl font-semibold"><?= $totalCertificates ?></div>
        </div>
        <div class="text-gray-400 text-3xl">🔗</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Feedback Received</div>
          <div class="text-3xl font-semibold"><?= $feedbackCount ?></div>
        </div>
        <div class="text-gray-400 text-3xl">📝</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Remove Users</div>
          <div class="text-3xl font-semibold"><?= $bannedUsers ?></div>
        </div>
        <div class="text-gray-400 text-3xl">💬</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Complaints Resolved</div>
          <div class="text-3xl font-semibold"><?= $complaintResolveRate ?>%</div>
        </div>
        <div class="text-gray-400 text-3xl">📈</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center col-span-full sm:col-span-2 lg:col-span-1">
        <div>
          <div class="text-xs text-gray-500 uppercase">Active Teacher</div>
          <div class="text-3xl font-semibold"><?= $activeTeachers ?></div>
        </div>
        <div class="text-gray-400 text-3xl">📥</div>
      </div>
      <div class="bg-white rounded-2xl shadow p-5 flex justify-between items-center">
        <div>
          <div class="text-xs text-gray-500 uppercase">Active User Today</div>
          <div class="flex items-baseline gap-1">
            <div class="text-3xl font-semibold"><?= $activeToday ?></div>
            <div class="text-xs text-green-600">Excellent</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="col-span-2 bg-white rounded-2xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">Recent Activity</h2>
          <div class="text-sm text-gray-500">Live Users & Submissions</div>
        </div>
        <div class="min-h-[260px]">
          <canvas id="combinedChart"></canvas>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Student Activity</h2>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between">
            <div>Most Active Student</div>
            <div class="text-gray-500"><?= $mostActiveStudentName ?></div>
          </div>
          <div class="flex justify-between bg-gray-50 rounded p-2">
            <div>Top Scorer</div>
            <div class="text-gray-500"><?= $topScorerName ?></div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</body>
</html>
