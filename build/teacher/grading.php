<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php";



$teacher_id = $_SESSION['user_id'];

// Fetch all grading entries for this teacher
$sql = "SELECT g.*, a.title AS assignment_title, u.name AS student_name
        FROM grading g
        JOIN assignments a ON g.assignment_id = a.id
        JOIN users u ON g.student_id = u.id
        WHERE a.uploaded_by = ?
        ORDER BY g.graded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="max-w-full px-4 py-6">
        <div class="text-2xl font-bold mb-4">Course Grading Overview</div>

        <div class="overflow-x-auto rounded-xl shadow bg-white">
          <table class="min-w-[900px] w-full text-left border-collapse">
            <thead class="bg-gray-100 text-sm font-semibold text-gray-700">
              <tr>
                <th class="p-4 border">Assignment</th>
                <th class="p-4 border">Student</th>
                <th class="p-4 border">Total Marks</th>
                <th class="p-4 border">Obtained Marks</th>
                <th class="p-4 border">Percentage</th>
                <th class="p-4 border">Grade</th>
                <th class="p-4 border">Status</th>
                <th class="p-4 border">Action</th>
              </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="border-b">
                  <td class="p-4 border"><?= htmlspecialchars($row['assignment_title']) ?></td>
                 <td class="p-4 border">
  <?= htmlspecialchars($row['student_name']) ?>
</td>
                  <td class="p-4 border"><?= $row['total_marks'] ?></td>
                  <td class="p-4 border"><?= $row['obtained_marks'] !== null ? $row['obtained_marks'] : '-' ?></td>
                  <td class="p-4 border"><?= $row['percentage'] !== null ? number_format($row['percentage'], 2) . '%' : '-' ?></td>
                  <td class="p-4 border"><?= $row['grade'] ?? '-' ?></td>
                  <td class="p-4 border <?= ($row['status'] === 'Passed') ? 'text-green-600' : 'text-red-600' ?> font-medium"><?= $row['status'] ?? '-' ?></td>
                  <td class="p-4 border">
                    <a href="grade_edit.php?id=<?= $row['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">Edit</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menu-btn');
    const overlay = document.getElementById('overlay');
    function openSidebar() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    }
    function closeSidebar() {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    }
    menuBtn.addEventListener('click', () => {
      if (sidebar.classList.contains('-translate-x-full')) {
        openSidebar();
      } else {
        closeSidebar();
      }
    });
    overlay.addEventListener('click', closeSidebar);
  </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
