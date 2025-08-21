<?php
require_once "inc/header.php";


// Assume student logged in
$student_id = $_SESSION['user_id'] ?? 2;

require_once "../inc/db.php";

$sql = "SELECT a.*, u.name AS teacher_name 
        FROM assignments a 
        JOIN users u ON a.uploaded_by = u.id
        ORDER BY a.uploaded_at DESC";
$assignments = $conn->query($sql);

$sql_subs = "SELECT * FROM assignment_submissions WHERE student_id = $student_id";
$student_submissions = $conn->query($sql_subs);

$subs_map = [];
if ($student_submissions && $student_submissions->num_rows > 0) {
    while ($row = $student_submissions->fetch_assoc()) {
        $subs_map[$row['assignment_id']] = $row;
    }
}

$message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $message = '<p class="text-green-600 mb-4">Assignment submitted successfully!</p>';
    } elseif ($_GET['status'] === 'error') {
        $message = '<p class="text-red-600 mb-4">There was an error submitting your assignment. Please try again.</p>';
    }
}
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="flex gap-10 justify-between overflow-y-auto px-6 py-6">
        <div class="w-full p-6 bg-white rounded-lg shadow">
          <h1 class="text-2xl font-bold mb-6">Assignments</h1>

          <?= $message ?>

          <div id="assignment-list" class="space-y-6">
            <?php if ($assignments && $assignments->num_rows > 0): ?>
              <?php while ($assignment = $assignments->fetch_assoc()): ?>
                <?php
                  $sub = $subs_map[$assignment['id']] ?? null;
                  $submitted = $sub !== null;
                ?>
                <div class="flex items-center justify-between border rounded-lg p-4">
                  <div class="flex items-center space-x-4">
                    <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f4c4.svg" class="w-8 h-8" />
                    <div>
                      <p class="font-semibold"><?= htmlspecialchars($assignment['title']) ?></p>
                      <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span>Uploaded by <?= htmlspecialchars($assignment['teacher_name']) ?></span>
                        <span>• <?= date('Y-m-d', strtotime($assignment['uploaded_at'])) ?></span>
                        <span class="<?= $submitted ? 'text-blue-500' : 'text-red-500' ?>">
                          • <?= $submitted ? 'Submitted' : 'Pending' ?>
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="flex space-x-2 items-center">
                    <a href="../teacher/uploads/assignments/<?= urlencode($assignment['filename']) ?>" 
                       class="px-4 py-2 border rounded-md text-blue-600 hover:underline" download>Download</a>

                    <?php if ($submitted): ?>
                      <a href="uploads/submissions/<?= urlencode($sub['filename']) ?>" 
                         class="px-4 py-2 border rounded-md text-green-600 hover:underline" download>
                         Your Submission
                      </a>
                    <?php else: ?>
                      <form action="submit_assignment.php" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                        <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>" />
                        <input type="file" name="submission_file" required
                          accept=".pdf,.doc,.docx,.zip,.rar,.txt,.jpg,.png"
                          class="block border border-gray-300 rounded-md p-1" />
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">Upload</button>
                      </form>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p class="text-gray-500">No assignments available.</p>
            <?php endif; ?>
          </div>


<?php
// Fetch student's grading records with assignment titles
$sql_grades = "SELECT g.*, a.title AS assignment_title 
               FROM grading g
               JOIN assignments a ON g.assignment_id = a.id
               WHERE g.student_id = ?
               ORDER BY g.graded_at DESC";
$stmt_grades = $conn->prepare($sql_grades);
$stmt_grades->bind_param('i', $student_id);
$stmt_grades->execute();
$result_grades = $stmt_grades->get_result();
?>

<div class="mt-12 bg-white p-6 rounded-lg shadow w-full">
  <h2 class="text-2xl font-bold mb-6">Your Assignment Results</h2>
  
  <?php if ($result_grades && $result_grades->num_rows > 0): ?>
    <div class="overflow-x-auto rounded-xl shadow">
      <table class="min-w-full text-left border-collapse">
        <thead class="bg-gray-100 text-sm font-semibold text-gray-700">
          <tr>
            <th class="p-4 border">Assignment</th>
            <th class="p-4 border">Total Marks</th>
            <th class="p-4 border">Obtained Marks</th>
            <th class="p-4 border">Percentage</th>
            <th class="p-4 border">Grade</th>
            <th class="p-4 border">Status</th>
            <th class="p-4 border">Graded At</th>
          </tr>
        </thead>
        <tbody class="text-sm text-gray-800">
          <?php while ($grade = $result_grades->fetch_assoc()): ?>
            <tr class="border-b">
              <td class="p-4 border"><?= htmlspecialchars($grade['assignment_title']) ?></td>
              <td class="p-4 border"><?= $grade['total_marks'] ?></td>
              <td class="p-4 border"><?= $grade['obtained_marks'] ?></td>
              <td class="p-4 border"><?= number_format($grade['percentage'], 2) ?>%</td>
              <td class="p-4 border"><?= htmlspecialchars($grade['grade']) ?></td>
              <td class="p-4 border <?= strtolower($grade['status']) === 'passed' ? 'text-green-600' : 'text-red-600' ?> font-medium"><?= htmlspecialchars($grade['status']) ?></td>
              <td class="p-4 border"><?= $grade['graded_at'] ? date('Y-m-d', strtotime($grade['graded_at'])) : '-' ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="text-gray-500">No grading results available yet.</p>
  <?php endif; ?>
</div>

<?php
$stmt_grades->close();
?>

        </div>
      </div>
    </div>
  </div>
</body>
</html>
