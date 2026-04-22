<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

if ($quiz_id <= 0) {
    header("Location: quiz_results.php");
    exit();
}

// Fetch quiz info
$quiz_res = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id AND teacher_id = $teacher_id");
$quiz = $quiz_res->fetch_assoc();
if (!$quiz) {
    header("Location: quiz_results.php");
    exit();
}

// Fetch all attempts for this quiz
$attempts_query = "
    SELECT a.*, u.name, u.email, u.roll_number, u.profile_image
    FROM quiz_attempts a
    JOIN users u ON a.student_id = u.id
    WHERE a.quiz_id = $quiz_id
    ORDER BY a.attempted_at DESC
";
$attempts = $conn->query($attempts_query);
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="md:p-10 p-6 max-w-7xl">
        <div class="flex items-center gap-4 mb-8">
            <a href="quiz_results.php" class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($quiz['title']) ?> - Submissions</h1>
                <p class="text-gray-500">Student response list and grading overview</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Student Info</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Roll Number</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Score</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Percentage</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Date Submitted</th>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if ($attempts && $attempts->num_rows > 0): ?>
                        <?php while($a = $attempts->fetch_assoc()): 
                            $pct = ($a['total_questions'] > 0) ? ($a['score'] / $a['total_questions']) * 100 : 0;
                            $color_class = $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-blue-600' : 'text-red-600');
                        ?>
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= !empty($a['profile_image']) ? '../uploads/profile/'.$a['profile_image'] : 'https://i.pravatar.cc/100?u='.$a['student_id'] ?>" 
                                             class="w-10 h-10 rounded-full object-cover border border-gray-100">
                                        <div>
                                            <p class="font-bold text-gray-800"><?= htmlspecialchars($a['provided_name'] ?? $a['name']) ?></p>
                                            <p class="text-[10px] text-gray-400 font-medium lowercase italic"><?= htmlspecialchars($a['email']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase">
                                        <?= !empty($a['provided_roll']) ? htmlspecialchars($a['provided_roll']) : (!empty($a['roll_number']) ? htmlspecialchars($a['roll_number']) : 'No ID') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-black text-gray-800"><?= $a['score'] ?></span> / <?= $a['total_questions'] ?>
                                </td>
                                <td class="px-6 py-4 text-center font-black <?= $color_class ?>">
                                    <?= round($pct) ?>%
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    <?= date('d M Y, h:i A', strtotime($a['attempted_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="student_quiz_detail.php?attempt_id=<?= $a['id'] ?>" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-900 hover:text-white transition inline-block shadow-sm">
                                        View Responses
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                No submissions found for this quiz yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
