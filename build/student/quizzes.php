<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$student_id = $_SESSION['user_id'] ?? 1;
$message = '';
$error = '';

// Handle Quiz Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $quiz_id = intval($_POST['quiz_id']);
    $score = 0;
    $total = 0;
    
    $questions = $conn->query("SELECT id, correct_option FROM quiz_questions WHERE quiz_id = $quiz_id");
    if ($questions && $questions->num_rows > 0) {
        $total = $questions->num_rows;
        $p_name = $conn->real_escape_string(trim($_POST['p_name'] ?? ''));
        $p_roll = $conn->real_escape_string(trim($_POST['p_roll'] ?? ''));
        $p_subject = $conn->real_escape_string(trim($_POST['p_subject'] ?? ''));

        $conn->query("INSERT INTO quiz_attempts (quiz_id, student_id, score, total_questions, provided_name, provided_roll, provided_subject) 
                      VALUES ($quiz_id, $student_id, 0, $total, '$p_name', '$p_roll', '$p_subject')");
        $attempt_id = $conn->insert_id;
        
        while ($q = $questions->fetch_assoc()) {
            $qid = $q['id'];
            $selected = $_POST["q_{$qid}"] ?? '';
            $is_correct = ($selected === $q['correct_option']) ? 1 : 0;
            if ($is_correct) $score++;
            
            $stmt = $conn->prepare("INSERT INTO quiz_responses (attempt_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisi", $attempt_id, $qid, $selected, $is_correct);
            $stmt->execute();
        }
        $conn->query("UPDATE quiz_attempts SET score = $score WHERE id = $attempt_id");
        
        $message = "Diagnostic complete. You achieved a synchronization rate of $score / $total accurately.";
    }
}

// Fetch all available quizzes
$quizzes_query = "
    SELECT q.*, c.title as course_title, t.name as teacher_name, 
           (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as question_count,
           (SELECT score FROM quiz_attempts WHERE quiz_id = q.id AND student_id = $student_id ORDER BY attempted_at DESC LIMIT 1) as last_score,
           (SELECT total_questions FROM quiz_attempts WHERE quiz_id = q.id AND student_id = $student_id ORDER BY attempted_at DESC LIMIT 1) as last_total
    FROM quizzes q 
    LEFT JOIN courses c ON q.course_id = c.id
    LEFT JOIN users t ON q.teacher_id = t.id
    ORDER BY q.created_at DESC
";
$quizzes_result = $conn->query($quizzes_query);

// Pre-fill info
$std_info = $conn->query("SELECT name, roll_number FROM users WHERE id = $student_id")->fetch_assoc();
$attempt_quiz_id = isset($_GET['attempt']) ? intval($_GET['attempt']) : 0;
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <?php if ($attempt_quiz_id > 0): 
            $quiz = $conn->query("SELECT * FROM quizzes WHERE id = $attempt_quiz_id")->fetch_assoc();
            $questions = $conn->query("SELECT * FROM quiz_questions WHERE quiz_id = $attempt_quiz_id");
        ?>
            <!-- Evaluation Interface -->
            <?php if ($quiz && $questions && $questions->num_rows > 0): ?>
                <div class="max-w-4xl mx-auto mb-20">
                    <div class="mb-12 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest mb-6 border border-blue-100 italic">
                            <i class="fa-solid fa-microscope"></i> Active Diagnostic Session
                        </div>
                        <h2 class="text-4xl font-black text-slate-900 mb-4"><?= htmlspecialchars($quiz['title']) ?></h2>
                        <p class="text-slate-500 font-medium max-w-2xl mx-auto"><?= htmlspecialchars($quiz['description']) ?></p>
                        
                        <div class="mt-8 flex justify-center gap-6">
                            <div class="bg-white px-6 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
                                <i class="fa-solid fa-layer-group text-slate-400"></i>
                                <span class="text-xs font-black uppercase tracking-widest text-slate-600"><?= $questions->num_rows ?> Items Pool</span>
                            </div>
                            <?php if ($quiz['time_limit'] > 0): ?>
                                <div class="bg-amber-50 px-6 py-3 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-3">
                                    <i class="fa-solid fa-stopwatch text-amber-500"></i>
                                    <span class="text-xs font-black uppercase tracking-widest text-amber-700"><?= $quiz['time_limit'] ?>m Limit</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form method="POST" action="quizzes.php" class="space-y-10">
                        <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                        <?php $i = 1; while ($q = $questions->fetch_assoc()): ?>
                            <div class="bg-white rounded-[2.5rem] p-8 lg:p-12 border border-slate-100 shadow-sm hover:shadow-xl transition duration-500">
                                <div class="flex gap-6">
                                    <div class="shrink-0 flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-900 text-white font-black italic shadow-lg shadow-slate-200"><?= $i ?></div>
                                    <div class="flex-1">
                                        <p class="text-xl font-black text-slate-800 mb-8 leading-tight"><?= nl2br(htmlspecialchars($q['question'])) ?></p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <?php foreach(['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']] as $key => $val): if(!empty($val)): ?>
                                                <label class="relative flex items-center gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-100 cursor-pointer hover:bg-white hover:border-blue-600 hover:shadow-lg transition group">
                                                    <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $key ?>" required class="w-4 h-4 text-blue-600 focus:ring-blue-600 border-slate-200">
                                                    <span class="text-xs font-black text-slate-400 group-hover:text-blue-600 transition uppercase"><?= $key ?>.</span>
                                                    <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition"><?= htmlspecialchars($val) ?></span>
                                                </label>
                                            <?php endif; endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $i++; endwhile; ?>

                        <!-- Identity Block -->
                        <div class="bg-slate-900 rounded-[3rem] p-10 lg:p-14 text-white relative overflow-hidden shadow-2xl">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 blur-3xl -mr-32 -mt-32"></div>
                            <h3 class="text-2xl font-black mb-8 italic">Final Verification</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div>
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Participant Name</label>
                                    <input type="text" name="p_name" value="<?= htmlspecialchars($std_info['name']) ?>" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm font-bold focus:bg-white/10 focus:ring-2 focus:ring-blue-600">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Matriculation ID</label>
                                    <input type="text" name="p_roll" value="<?= htmlspecialchars($std_info['roll_number'] ?? '') ?>" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm font-bold focus:bg-white/10 focus:ring-2 focus:ring-blue-600">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Subject Field</label>
                                    <input type="text" name="p_subject" value="Advanced Curriculum" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm font-bold focus:bg-white/10 focus:ring-2 focus:ring-blue-600">
                                </div>
                            </div>
                            <div class="mt-12 pt-8 border-t border-white/5 flex flex-col md:flex-row gap-6">
                                <button type="submit" name="submit_quiz" class="flex-1 bg-blue-600 text-white font-black uppercase tracking-[0.2em] text-[11px] py-6 rounded-3xl hover:bg-white hover:text-slate-900 transition-all duration-500 shadow-xl shadow-blue-600/20">
                                    Authorize Submission
                                </button>
                                <a href="quizzes.php" class="md:w-1/3 flex items-center justify-center px-8 py-6 bg-white/5 text-white rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 transition duration-500 border border-white/10">Abort Session</a>
                            </div>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="py-32 text-center bg-white rounded-[3rem] border border-slate-100 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-6xl mb-6"></i>
                    <h3 class="text-2xl font-black text-slate-400 italic uppercase tracking-widest">Diagnostic Vacant</h3>
                    <p class="text-slate-300 font-bold mt-2">Check back shortly for updated curriculum assessments.</p>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Registry View -->
            <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black tracking-tight">Diagnostic Hub</h1>
                    <p class="text-slate-500 font-medium">Evaluate your field knowledge across all active modules.</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="bg-emerald-50 text-emerald-700 p-6 rounded-[2rem] mb-12 font-black border border-emerald-100 flex items-center gap-4 italic shadow-lg shadow-emerald-50">
                    <i class="fa-solid fa-circle-check text-xl"></i> <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if ($quizzes_result && $quizzes_result->num_rows > 0): ?>
                    <?php while ($q = $quizzes_result->fetch_assoc()): ?>
                        <div class="bg-white rounded-[2.5rem] p-3 border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition duration-500 group flex flex-col">
                            <div class="bg-slate-50/50 rounded-[2.2rem] p-8 flex-1">
                                <div class="flex justify-between items-start mb-6">
                                    <span class="px-3 py-1 bg-white border border-slate-100 rounded-lg text-[9px] font-black uppercase tracking-widest text-blue-600 italic">
                                        <?= htmlspecialchars($q['course_title'] ?? 'Generic') ?>
                                    </span>
                                    <?php if ($q['last_score'] !== null): ?>
                                        <span class="flex items-center gap-2 text-[8px] font-black text-emerald-600 bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-100">
                                            <i class="fa-solid fa-check-double"></i> EVALUATED
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-xl font-black text-slate-800 mb-3 leading-tight group-hover:text-blue-600 transition"><?= htmlspecialchars($q['title']) ?></h3>
                                <p class="text-xs text-slate-400 font-bold mb-8 line-clamp-2"><?= htmlspecialchars($q['description']) ?></p>
                                
                                <div class="flex items-center gap-3">
                                    <div class="bg-white px-3 py-2 rounded-xl flex items-center gap-2 border border-slate-50">
                                        <i class="fa-solid fa-layer-group text-[10px] text-slate-300"></i>
                                        <span class="text-[10px] font-black text-slate-600"><?= $q['question_count'] ?> ITM</span>
                                    </div>
                                    <div class="bg-white px-3 py-2 rounded-xl flex items-center gap-2 border border-slate-50">
                                        <i class="fa-solid fa-clock-rotate-left text-[10px] text-slate-300"></i>
                                        <span class="text-[10px] font-black text-slate-600"><?= $q['time_limit'] > 0 ? $q['time_limit'].'m' : '∞' ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8 pt-4">
                                <?php if ($q['last_score'] !== null): ?>
                                    <div class="flex justify-between items-center mb-6 px-2">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Previous Audit</span>
                                        <span class="text-sm font-black text-slate-900 italic"><?= $q['last_score'] ?> <span class="text-slate-300 text-[10px]">/ <?= $q['last_total'] ?></span></span>
                                    </div>
                                    <a href="quizzes.php?attempt=<?= $q['id'] ?>" class="block w-full text-center bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-blue-600 transition shadow-xl shadow-slate-200">Retake Diagnostic</a>
                                <?php else: ?>
                                    <a href="quizzes.php?attempt=<?= $q['id'] ?>" class="block w-full text-center bg-blue-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-slate-900 transition shadow-xl shadow-blue-50">Initialize Evaluation</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full py-20 text-center italic text-slate-300 font-bold uppercase tracking-widest">Digital evaluation library is currently vacant.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
