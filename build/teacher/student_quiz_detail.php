<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;
$attempt_id = isset($_GET['attempt_id']) ? intval($_GET['attempt_id']) : 0;

if ($attempt_id <= 0) {
    header("Location: quiz_results.php");
    exit();
}

// Fetch attempt info along with student and quiz details
$attempt_query = "
    SELECT a.*, u.name as student_name, u.roll_number, q.title as quiz_title, q.description as quiz_desc
    FROM quiz_attempts a
    JOIN users u ON a.student_id = u.id
    JOIN quizzes q ON a.quiz_id = q.id
    WHERE a.id = $attempt_id AND q.teacher_id = $teacher_id
";
$attempt_res = $conn->query($attempt_query);
$attempt = $attempt_res->fetch_assoc();

if (!$attempt) {
    header("Location: quiz_results.php");
    exit();
}

// Fetch detailed responses for this attempt
$responses_query = "
    SELECT r.*, q.question, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option
    FROM quiz_responses r
    JOIN quiz_questions q ON r.question_id = q.id
    WHERE r.attempt_id = $attempt_id
";
$responses = $conn->query($responses_query);
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="md:p-10 p-6 max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="view_quiz_attempts.php?quiz_id=<?= $attempt['quiz_id'] ?>" class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">Attempt Analysis</h1>
                <p class="text-gray-500 font-medium">Detailed response report for <?= htmlspecialchars($attempt['student_name']) ?></p>
            </div>
        </div>

        <!-- Student Summary Header -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-100">
                        <i class="fas fa-user-check text-2xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 leading-tight"><?= htmlspecialchars($attempt['provided_name'] ?? $attempt['student_name']) ?></h2>
                        <div class="flex gap-2 mt-1">
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest">ID: <?= !empty($attempt['provided_roll']) ? htmlspecialchars($attempt['provided_roll']) : (!empty($attempt['roll_number']) ? htmlspecialchars($attempt['roll_number']) : 'N/A') ?></p>
                            <span class="text-[10px] font-black text-gray-300">|</span>
                            <p class="text-[10px] font-black text-purple-600 uppercase tracking-widest">SUB: <?= htmlspecialchars($attempt['provided_subject'] ?? 'General') ?></p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="bg-gray-50 px-6 py-3 rounded-2xl text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Final Score</p>
                        <p class="text-2xl font-black text-gray-900"><?= $attempt['score'] ?> / <?= $attempt['total_questions'] ?></p>
                    </div>
                    <?php $pct = ($attempt['total_questions'] > 0) ? ($attempt['score'] / $attempt['total_questions']) * 100 : 0; ?>
                    <div class="<?= $pct >= 80 ? 'bg-green-50' : ($pct >= 50 ? 'bg-blue-50' : 'bg-red-50') ?> px-6 py-3 rounded-2xl text-center min-w-[100px]">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Accuracy</p>
                        <p class="text-2xl font-black <?= $pct >= 80 ? 'text-green-600' : ($pct >= 50 ? 'text-blue-600' : 'text-red-600') ?>"><?= round($pct) ?>%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question List -->
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6 pl-2">Individual Question Analysis</h3>
        <div class="space-y-6 pb-20">
            <?php if ($responses && $responses->num_rows > 0): ?>
                <?php $i = 1; while($r = $responses->fetch_assoc()): ?>
                    <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm transition hover:shadow-md">
                        <div class="flex items-start gap-4 mb-6">
                            <span class="w-8 h-8 rounded-xl bg-gray-900 text-white flex items-center justify-center font-black text-xs shrink-0"><?= $i ?></span>
                            <div class="flex-1">
                                <p class="text-lg font-bold text-gray-900"><?= nl2br(htmlspecialchars($r['question'])) ?></p>
                            </div>
                            <?php if ($r['is_correct']): ?>
                                <span class="bg-green-100 text-green-700 w-10 h-10 rounded-full flex items-center justify-center shadow-sm shadow-green-100">
                                    <i class="fas fa-check"></i>
                                </span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 w-10 h-10 rounded-full flex items-center justify-center shadow-sm shadow-red-100">
                                    <i class="fas fa-times"></i>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-12">
                            <?php 
                            $options = ['A' => $r['option_a'], 'B' => $r['option_b'], 'C' => $r['option_c'], 'D' => $r['option_d']];
                            foreach($options as $key => $val):
                                if (empty($val)) continue;
                                $is_correct_opt = ($key === $r['correct_option']);
                                $is_selected_opt = ($key === $r['selected_option']);
                                
                                $card_style = 'border-gray-50 bg-gray-50';
                                $icon = '';
                                
                                if ($is_correct_opt) {
                                    $card_style = 'border-green-200 bg-green-50/50 text-green-800';
                                    $icon = '<i class="fas fa-check-circle ml-auto"></i>';
                                }
                                if ($is_selected_opt && !$is_correct_opt) {
                                    $card_style = 'border-red-200 bg-red-50/50 text-red-800';
                                    $icon = '<i class="fas fa-exclamation-circle ml-auto"></i>';
                                }
                                if ($is_selected_opt && $is_correct_opt) {
                                    $icon = '<i class="fas fa-check-circle ml-auto text-green-600"></i>';
                                }
                            ?>
                                <div class="px-5 py-3 rounded-2xl border flex items-center gap-3 <?= $card_style ?>">
                                    <span class="text-[10px] font-black uppercase"><?= $key ?>.</span>
                                    <span class="text-sm font-bold"><?= htmlspecialchars($val) ?></span>
                                    <?php if ($is_selected_opt): ?>
                                        <span class="text-[8px] font-black bg-white px-2 py-0.5 rounded-full shadow-sm ml-2">YOU CHOSE</span>
                                    <?php endif; ?>
                                    <?= $icon ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php $i++; endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-12 rounded-3xl text-center text-gray-400">
                    No detailed responses found for this attempt.
                </div>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
