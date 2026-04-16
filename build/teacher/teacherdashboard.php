<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 

$teacher_id = $_SESSION['user_id'] ?? 1;

// Metrics
$total_students = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='student'")->fetch_assoc()['total'] ?? 0;
$active_courses = $conn->query("SELECT COUNT(*) AS total FROM courses")->fetch_assoc()['total'] ?? 0;
$materials_shared = $conn->query("SELECT COUNT(*) AS total FROM materials")->fetch_assoc()['total'] ?? 0;
$pending_grading = $conn->query("SELECT COUNT(*) AS total FROM grading WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
?>

<body class="bg-slate-50 relative before:fixed before:inset-0 before:-z-10 before:w-full before:h-full before:bg-\[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))\] before:from-blue-100 before:via-white before:to-emerald-50">
    <div class="flex">
        <?php require_once "inc/sidebar.php"; ?>
        <div class="flex-1">
            <?php require_once "inc/topbar.php"; ?>
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8">Teacher Dashboard</h1>

                <!-- Stats Tiles -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100 text-center">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Students</p>
                        <h3 class="text-3xl font-bold"><?= $total_students ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100 text-center">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Active Courses</p>
                        <h3 class="text-3xl font-bold"><?= $active_courses ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100 text-center">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Materials Shared</p>
                        <h3 class="text-3xl font-bold"><?= $materials_shared ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100 text-center">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Pending Grading</p>
                        <h3 class="text-3xl font-bold"><?= $pending_grading ?></h3>
                    </div>
                </div>

                <!-- Quick Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Grading Card -->
                    <a href="grading.php" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fa-solid fa-graduation-cap text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Grading Center</h3>
                        <p class="text-gray-500 text-sm">Review and grade recently submitted student assignments.</p>
                    </a>

                    <!-- Quizzes Card -->
                    <a href="manage_quizzes.php" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fa-solid fa-list-check text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Quiz Management</h3>
                        <p class="text-gray-500 text-sm">Create and organize academic evaluations for your students.</p>
                    </a>

                    <!-- Share Material Card -->
                    <a href="sharematerial.php" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
                        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fa-solid fa-share-nodes text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Content Sharing</h3>
                        <p class="text-gray-500 text-sm">Upload and distribute course materials to your modules.</p>
                    </a>

                    <!-- Online Class Card -->
                    <a href="onlineclass.php" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
                        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fa-solid fa-video text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Live Instruction</h3>
                        <p class="text-gray-500 text-sm">Initiate and manage virtual classroom sessions.</p>
                    </a>

                    <!-- Attendance Card -->
                    <a href="attendance.php" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fa-solid fa-clipboard-user text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Attendance Sync</h3>
                        <p class="text-gray-500 text-sm">Log and verify student participation for daily sessions.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>

