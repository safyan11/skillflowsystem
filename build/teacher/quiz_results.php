<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;

// Fetch all quizzes created by this teacher
$quizzes_query = "
    SELECT q.*, c.title as course_title, 
           (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as question_count,
           (SELECT COUNT(DISTINCT student_id) FROM quiz_attempts WHERE quiz_id = q.id) as total_students,
           (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id) as total_attempts
    FROM quizzes q
    LEFT JOIN courses c ON q.course_id = c.id
    WHERE q.teacher_id = $teacher_id
    ORDER BY q.created_at DESC
";
$quizzes = $conn->query($quizzes_query);
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="md:p-10 p-6 max-w-7xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Quiz Performance Reports</h1>
            <p class="text-gray-500">Analyze how your students are performing across all assessments</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($quizzes && $quizzes->num_rows > 0): ?>
                <?php while($q = $quizzes->fetch_assoc()): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest"><?= htmlspecialchars($q['course_title'] ?? 'N/A') ?></span>
                                <span class="text-gray-400 text-xs"><i class="fas fa-calendar-alt"></i> <?= date('d M', strtotime($q['created_at'])) ?></span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($q['title']) ?></h3>
                            
                            <div class="grid grid-cols-2 gap-4 mt-6 py-4 border-t border-b border-gray-50">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Participation</p>
                                    <p class="text-lg font-bold text-gray-800"><?= $q['total_students'] ?> Students</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Avg. Score</p>
                                    <?php
                                        $avg_res = $conn->query("SELECT AVG((score / total_questions) * 100) as avg FROM quiz_attempts WHERE quiz_id = {$q['id']}");
                                        $avg = $avg_res->fetch_assoc()['avg'] ?? 0;
                                    ?>
                                    <p class="text-lg font-bold text-blue-600"><?= round($avg) ?>%</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4">
                            <a href="view_quiz_attempts.php?quiz_id=<?= $q['id'] ?>" class="block w-full text-center bg-gray-900 text-white font-bold py-2 rounded-xl hover:bg-black transition">
                                View Detailed Results
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center bg-white rounded-2xl border-2 border-dashed border-gray-100">
                    <p class="text-gray-400">No reports generated yet. Quizzes with attempts will show up here.</p>
                </div>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
