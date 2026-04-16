<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    $course_id = intval($_POST['course_id']);
    $title = $conn->real_escape_string(trim($_POST['title']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    $time_limit = intval($_POST['time_limit']);
    $bulk_questions = trim($_POST['bulk_questions'] ?? '');

    if (!empty($title) && !empty($course_id)) {
        $sql = "INSERT INTO quizzes (course_id, teacher_id, title, description, time_limit) VALUES ($course_id, $teacher_id, '$title', '$description', $time_limit)";
        if ($conn->query($sql)) {
            $quiz_id = $conn->insert_id;
            $q_count = 0;

            if (!empty($bulk_questions)) {
                $raw_questions = preg_split('/(?=Q:)/i', $bulk_questions, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($raw_questions as $raw_q) {
                    $lines = explode("\n", trim($raw_q));
                    $q_text = ''; $a = ''; $b = ''; $c = ''; $d = ''; $ans = '';
                    
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (stripos($line, 'Q:') === 0) $q_text = trim(substr($line, 2));
                        elseif (stripos($line, 'A:') === 0) $a = trim(substr($line, 2));
                        elseif (stripos($line, 'B:') === 0) $b = trim(substr($line, 2));
                        elseif (stripos($line, 'C:') === 0) $c = trim(substr($line, 2));
                        elseif (stripos($line, 'D:') === 0) $d = trim(substr($line, 2));
                        elseif (stripos($line, 'ANS:') === 0) $ans = strtoupper(trim(substr($line, 4)));
                    }
                    
                    if (!empty($q_text) && !empty($a) && !empty($ans)) {
                        $q_text = $conn->real_escape_string($q_text);
                        $a = $conn->real_escape_string($a);
                        $b = $conn->real_escape_string($b);
                        $c = $conn->real_escape_string($c);
                        $d = $conn->real_escape_string($d);
                        $ans = $conn->real_escape_string($ans);
                        
                        $q_sql = "INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) 
                                 VALUES ($quiz_id, '$q_text', '$a', '$b', '$c', '$d', '$ans')";
                        if ($conn->query($q_sql)) $q_count++;
                    }
                }
            }
            $message = "Diagnostic Assessment '$title' successfully deployed. $q_count items synchronized to pool.";
        } else {
            $error = "Registry failure: " . $conn->error;
        }
    } else {
        $error = "Provisioning error: Course and Title identifiers required.";
    }
}

// Fetch data
$courses_result = $conn->query("SELECT * FROM courses ORDER BY title ASC");
$quizzes_result = $conn->query("SELECT q.*, c.title as course_title, (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as question_count FROM quizzes q LEFT JOIN courses c ON q.course_id = c.id WHERE q.teacher_id = $teacher_id ORDER BY q.created_at DESC");
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Quiz Orchestration</h1>
                <p class="text-slate-500 font-medium">Design diagnostic assessments and manage question repositories.</p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-2xl mb-8 font-bold border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-clipboard-check"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-rose-50 text-rose-700 p-4 rounded-2xl mb-8 font-bold border border-rose-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Creation Form -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 lg:p-12 mb-12">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <h2 class="text-xl font-black uppercase tracking-widest text-slate-900 text-sm">Issue New Diagnostic</h2>
            </div>

            <form method="POST" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Subject Module</label>
                        <select name="course_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600 font-bold">
                            <option value="">Choose Course...</option>
                            <?php while($c = $courses_result->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Assessment Title</label>
                        <input type="text" name="title" placeholder="e.g. Mid-Term Proficiency Exam" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600 font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Duration (Minutes)</label>
                        <input type="number" name="time_limit" value="0" min="0" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-blue-600 font-bold">
                        <p class="text-[9px] text-slate-300 font-bold mt-1 italic">Enter '0' for unlimited duration.</p>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Designation Metadata</label>
                    <textarea name="description" placeholder="Brief overview of assessment objectives..." rows="2" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-blue-600 font-bold"></textarea>
                </div>
                <div class="pt-6 border-t border-slate-50">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fa-solid fa-bolt-lightning text-amber-500 text-xs"></i>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Rapid Item Injection (Bulk Parser)</h3>
                    </div>
                    <textarea name="bulk_questions" rows="6" class="w-full bg-slate-900 text-emerald-400 rounded-2xl px-6 py-5 text-xs font-mono focus:ring-2 focus:ring-blue-600" placeholder="Q: Question text?&#10;A: Option 1&#10;B: Option 2&#10;ANS: A"></textarea>
                    <p class="text-[9px] text-slate-400 font-bold mt-3 uppercase tracking-widest">Follow Syntax: Q: [Question] A: [Opt] B: [Opt] C: [Opt] D: [Opt] ANS: [Letter]</p>
                </div>
                <button type="submit" name="create_quiz" class="w-full bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[10px] py-5 rounded-2xl hover:bg-blue-600 transition shadow-2xl shadow-slate-200">
                    Authorize Diagnostic Deployment
                </button>
            </form>
        </div>

        <!-- Inventory -->
        <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Registry of Assessments</h3>
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Diagnostic Meta</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Course Origin</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Item Pool</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Duration</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if ($quizzes_result && $quizzes_result->num_rows > 0): ?>
                            <?php while ($row = $quizzes_result->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-8 py-5">
                                        <p class="font-bold text-slate-800"><?= htmlspecialchars($row['title']) ?></p>
                                        <p class="text-[9px] font-bold text-slate-400 italic"><?= substr(htmlspecialchars($row['description']), 0, 45) ?>...</p>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-lg">
                                            <?= htmlspecialchars($row['course_title'] ?? 'Generic') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="font-black text-slate-900 text-lg"><?= $row['question_count'] ?></span>
                                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Locked Items</p>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="font-black text-slate-900"><?= $row['time_limit'] > 0 ? $row['time_limit'] . 'm' : '∞' ?></span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <a href="edit_quiz.php?id=<?= $row['id'] ?>&course_id=<?= $row['course_id'] ?>" class="inline-flex items-center gap-3 bg-slate-900 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-lg shadow-slate-200">
                                            <i class="fa-solid fa-list-check"></i> Manage Pool
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="py-20 text-center font-bold italic text-slate-300 uppercase tracking-tight">Digital repository is currently vacant.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
