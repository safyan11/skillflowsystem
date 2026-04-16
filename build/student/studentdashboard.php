<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;

// Metrics
$res = $conn->query("SELECT COUNT(*) as c FROM course_enrollments WHERE student_id = $student_id");
$enrolled_count = $res ? $res->fetch_assoc()['c'] : 0;

$res = $conn->query("
    SELECT COUNT(*) as c 
    FROM assignments a 
    JOIN course_enrollments ce ON a.course_id = ce.course_id 
    WHERE ce.student_id = $student_id 
    AND NOT EXISTS (SELECT 1 FROM submissions s WHERE s.assignment_id = a.id AND s.student_id = $student_id)
");
$pending_tasks = $res ? $res->fetch_assoc()['c'] : 0;

$res = $conn->query("SELECT COUNT(DISTINCT quiz_id) as c FROM quiz_attempts WHERE student_id = $student_id");
$quizzes_taken = $res ? $res->fetch_assoc()['c'] : 0;

$res = $conn->query("SELECT COUNT(*) as c FROM certificates WHERE student_id = $student_id");
$certificates_earned = $res ? $res->fetch_assoc()['c'] : 0;

// Courses
$search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
$sql = "SELECT * FROM courses";
if (!empty($search)) {
    $sql .= " WHERE title LIKE '%$search%' OR short_description LIKE '%$search%'";
}
$sql .= " ORDER BY id DESC";
$courses_result = $conn->query($sql);
?>

<body class="bg-slate-50 relative before:fixed before:inset-0 before:-z-10 before:w-full before:h-full before:bg-\[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))\] before:from-blue-100 before:via-white before:to-emerald-50">
    <div class="flex">
        <?php require_once "inc/sidebar.php"; ?>
        <div class="flex-1">
            <?php require_once "inc/topbar.php"; ?>
            <div class="p-8">
                <!-- Hero Section -->
                <div class="bg-black text-white p-10 rounded-3xl mb-12 shadow-sm">
                    <h1 class="text-4xl font-bold mb-4">Master New Skills Anytime, Anywhere</h1>
                    <p class="text-gray-400 text-lg">Learn from the best instructors and boost your career with practical knowledge.</p>
                </div>

                <!-- Stats Tiles -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Enrolled Courses</p>
                        <h3 class="text-3xl font-bold text-black"><?= $enrolled_count ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Assignments Pending</p>
                        <h3 class="text-3xl font-bold text-black"><?= $pending_tasks ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Quizzes Taken</p>
                        <h3 class="text-3xl font-bold text-black"><?= $quizzes_taken ?></h3>
                    </div>
                    <div class="glass-card stat-card p-8 rounded-[1.5rem] border border-blue-100">
                        <p class="text-sm text-gray-500 font-semibold mb-1">Certificates</p>
                        <h3 class="text-3xl font-bold text-black"><?= $certificates_earned ?></h3>
                    </div>
                </div>

                <!-- Course Grid -->
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold">Recommended Courses</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <?php if ($courses_result && $courses_result->num_rows > 0): ?>
                        <?php while ($row = $courses_result->fetch_assoc()): ?>
                            <a href="playlist.php?id=<?= $row['id'] ?>" class="group">
                                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                                    <div class="h-44 relative overflow-hidden">
                                        <img src="../admin/<?= $row['thumbnail'] ?>" class="w-full h-full object-cover">
                                        <div class="absolute top-4 right-4 bg-white/90 px-3 py-1 rounded-lg text-xs font-bold text-black">
                                            <?= $row['video_hours'] ?> hrs
                                        </div>
                                    </div>
                                    <div class="p-6">
                                        <h4 class="font-bold text-lg mb-2 text-black line-clamp-1"><?= htmlspecialchars($row['title']) ?></h4>
                                        <p class="text-gray-500 text-xs mb-6 line-clamp-2"><?= htmlspecialchars($row['short_description']) ?></p>
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                    <i class="fa-solid fa-user-tie text-xs"></i>
                                                </div>
                                                <p class="text-[11px] font-bold text-gray-700"><?= htmlspecialchars($row['instructor_name']) ?></p>
                                            </div>
                                            <span class="text-xs font-bold text-blue-600 hover:underline">View Course</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-span-full py-20 text-center">
                            <p class="text-gray-400 italic font-medium">No courses available matching your search.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>

