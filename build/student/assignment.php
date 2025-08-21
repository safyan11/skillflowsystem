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
