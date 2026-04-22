<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$teacher_id = $_SESSION['user_id'] ?? 1;
$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($quiz_id <= 0) {
    header("Location: manage_quizzes.php");
    exit();
}

// Verify teacher owns this quiz
$quiz_check = $conn->query("SELECT * FROM quizzes WHERE id = $quiz_id AND teacher_id = $teacher_id");
if (!$quiz_check || $quiz_check->num_rows === 0) {
    header("Location: manage_quizzes.php");
    exit();
}
$quiz_data = $quiz_check->fetch_assoc();

$message = '';

// Handle Add Question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question = $conn->real_escape_string(trim($_POST['question']));
    $opt_a = $conn->real_escape_string(trim($_POST['option_a']));
    $opt_b = $conn->real_escape_string(trim($_POST['option_b']));
    $opt_c = $conn->real_escape_string(trim($_POST['option_c']));
    $opt_d = $conn->real_escape_string(trim($_POST['option_d']));
    $correct = $conn->real_escape_string($_POST['correct_option']);

    if (!empty($question) && !empty($opt_a) && !empty($opt_b) && !empty($correct)) {
        $sql = "INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) 
                VALUES ($quiz_id, '$question', '$opt_a', '$opt_b', '$opt_c', '$opt_d', '$correct')";
        if ($conn->query($sql)) {
            $message = '<p class="bg-green-100 text-green-700 p-3 rounded mb-4 font-bold">Question Added Successfully!</p>';
        } else {
            $message = '<p class="bg-red-100 text-red-700 p-3 rounded mb-4 font-bold">Error: ' . $conn->error . '</p>';
        }
    } else {
        $message = '<p class="bg-red-100 text-red-700 p-3 rounded mb-4 font-bold">Please fill all required fields.</p>';
    }
}

// Handle Delete Question
if (isset($_GET['delete_question'])) {
    $q_id = intval($_GET['delete_question']);
    if ($conn->query("DELETE FROM quiz_questions WHERE id = $q_id AND quiz_id = $quiz_id")) {
        $message = '<p class="bg-blue-100 text-blue-700 p-3 rounded mb-4 font-bold">Question Removed Successfully!</p>';
    }
}

// Handle Bulk Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_upload'])) {
    $bulk_text = trim($_POST['bulk_text']);
    if (!empty($bulk_text)) {
        // Simple Parser: Splits by double newlines or Q:
        $raw_questions = preg_split('/(?=Q:)/', $bulk_text, -1, PREG_SPLIT_NO_EMPTY);
        $count = 0;
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
                
                $sql = "INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) 
                        VALUES ($quiz_id, '$q_text', '$a', '$b', '$c', '$d', '$ans')";
                if ($conn->query($sql)) $count++;
            }
        }
        $message = "<p class='bg-green-100 text-green-700 p-3 rounded mb-4 font-bold'>Bulk Upload Complete! $count questions added.</p>";
    }
}

// Fetch all questions
$questions_result = $conn->query("SELECT * FROM quiz_questions WHERE quiz_id = $quiz_id");
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="md:p-10 p-6 max-w-5xl">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="manage_quizzes.php" class="bg-gray-200 p-2 rounded-full hover:bg-gray-300">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold">Manage Quiz: <?= htmlspecialchars($quiz_data['title']) ?></h1>
            </div>
            <div class="text-sm font-bold text-blue-600 bg-blue-50 px-4 py-2 rounded-full">
                <?= $questions_result->num_rows ?> Questions Total
            </div>
        </div>

        <?= $message ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Add Question Form -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-blue-500"></i> Add Single Question
                </h2>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block mb-1 text-sm font-bold text-gray-700">Question Text</label>
                        <textarea name="question" required rows="2" class="w-full border border-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="What is..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="option_a" required placeholder="Option A" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <input type="text" name="option_b" required placeholder="Option B" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <input type="text" name="option_c" placeholder="Option C (Optional)" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                        <input type="text" name="option_d" placeholder="Option D (Optional)" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-bold text-gray-700">Correct Answer</label>
                        <select name="correct_option" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                            <option value="A">Option A</option>
                            <option value="B">Option B</option>
                            <option value="C">Option C</option>
                            <option value="D">Option D</option>
                        </select>
                    </div>
                    <button type="submit" name="add_question" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">Save Question</button>
                </form>
            </div>

            <!-- Bulk Upload Form -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-purple-600">
                    <i class="fas fa-bolt"></i> Fast Bulk Upload
                </h2>
                <form method="POST" class="space-y-4">
                    <textarea name="bulk_text" rows="8" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-mono focus:ring-2 focus:ring-purple-500 outline-none" 
                              placeholder="Format:&#10;Q: Question text?&#10;A: Opt1&#10;B: Opt2&#10;ANS: A&#10;&#10;Q: Next question..."></textarea>
                    <button type="submit" name="bulk_upload" class="w-full bg-purple-600 text-white font-bold py-3 rounded-xl hover:bg-purple-700 transition">Analyze & Upload All</button>
                </form>
            </div>
        </div>

        <!-- Question List -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Questions in Quiz (<?= $questions_result->num_rows ?>)</h2>
            <?php if ($questions_result && $questions_result->num_rows > 0): ?>
                <div class="space-y-6">
                    <?php $i = 1; while ($row = $questions_result->fetch_assoc()): ?>
                        <div class="p-4 border border-gray-200 rounded-lg relative">
                            <a href="edit_quiz.php?id=<?= $quiz_id ?>&delete_question=<?= $row['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this question?')"
                               class="absolute top-4 right-4 text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </a>
                            <p class="font-bold mb-3"><?= $i ?>. <?= htmlspecialchars($row['question']) ?></p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                <p class="<?= $row['correct_option'] == 'A' ? 'text-green-700 font-bold' : 'text-gray-600' ?>">A) <?= htmlspecialchars($row['option_a']) ?></p>
                                <p class="<?= $row['correct_option'] == 'B' ? 'text-green-700 font-bold' : 'text-gray-600' ?>">B) <?= htmlspecialchars($row['option_b']) ?></p>
                                <p class="<?= $row['correct_option'] == 'C' ? 'text-green-700 font-bold' : 'text-gray-600' ?>">C) <?= htmlspecialchars($row['option_c']) ?></p>
                                <p class="<?= $row['correct_option'] == 'D' ? 'text-green-700 font-bold' : 'text-gray-600' ?>">D) <?= htmlspecialchars($row['option_d']) ?></p>
                            </div>
                        </div>
                    <?php $i++; endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">No questions added yet. Add your first question above!</p>
            <?php endif; ?>
        </div>

      </div>
    </div>
  </div>
  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
