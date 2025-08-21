<?php 
require_once "inc/header.php"; 

// Example logged-in teacher user id (replace with your session logic)
$teacher_id = $_SESSION['user_id'] ?? 1; 

// DB connection
require_once "../inc/db.php"; 
$upload_error = '';

// Handle assignment upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_assignment'])) {
    if (!empty($_POST['title']) && isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === 0) {
        $title = $conn->real_escape_string($_POST['title']);
        $file = $_FILES['assignment_file'];

        $upload_dir = __DIR__ . '/uploads/assignments/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $filename = basename($file['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $filesize = $file['size'];

            $sql = "INSERT INTO assignments (title, filename, filesize, uploaded_by) VALUES ('$title', '$filename', $filesize, $teacher_id)";
            if (!$conn->query($sql)) {
                $upload_error = "DB error: " . $conn->error;
            }
        } else {
            $upload_error = "Failed to move uploaded file.";
        }
    } else {
        $upload_error = "Please provide a title and select a file.";
    }
}

// Fetch assignments uploaded by this teacher
$sql = "SELECT * FROM assignments WHERE uploaded_by = $teacher_id ORDER BY uploaded_at DESC";
$assignments = $conn->query($sql);

?>

<body class="bg-gray-50 font-sans antialiased">
<div class="min-h-screen flex">
  <?php require_once "inc/sidebar.php"; ?>

  <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

  <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
    <?php require_once "inc/topbar.php"; ?>

    <div class="p-6 max-w-7xl  mt-10">
      <h1 class="text-3xl font-bold mb-6">Upload New Assignment</h1>

      <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md max-w-7xl mb-10">
        <?php if ($upload_error): ?>
          <div class="mb-4 text-red-600 font-semibold"><?= htmlspecialchars($upload_error) ?></div>
        <?php endif; ?>

        <label class="block mb-2 font-semibold text-gray-700" for="title">Assignment Title</label>
        <input type="text" name="title" id="title" required
          class="w-full border border-gray-300 rounded px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-black">

        <label class="block mb-2 font-semibold text-gray-700" for="assignment_file">Select Assignment File</label>
        <input type="file" name="assignment_file" id="assignment_file" required class="mb-4 w-full" />

        <button type="submit" name="upload_assignment" 
          class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition">Upload Assignment</button>
      </form>

      <h2 class="text-2xl font-bold mb-4">Your Assignments</h2>

      <?php if ($assignments && $assignments->num_rows > 0): ?>
        <div class="space-y-6">
          <?php while ($assignment = $assignments->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow p-5 border border-gray-300">
              <div class="flex justify-between items-center">
                <div>
                  <p class="font-semibold text-lg"><?= htmlspecialchars($assignment['title']) ?></p>
                  <p class="text-sm text-gray-500">Uploaded at: <?= date('Y-m-d H:i', strtotime($assignment['uploaded_at'])) ?></p>
                </div>
                <a href="uploads/assignments/<?= urlencode($assignment['filename']) ?>" 
                   class="text-blue-600 hover:underline" download>Download</a>
              </div>

              <!-- Fetch student submissions for this assignment -->
              <?php
              $aid = (int)$assignment['id'];
              $sql_sub = "SELECT s.id, s.filename, s.filesize, s.submitted_at, u.name AS student_name 
                          FROM assignment_submissions s 
                          JOIN users u ON s.student_id = u.id 
                          WHERE s.assignment_id = $aid
                          ORDER BY s.submitted_at DESC";
              $submissions = $conn->query($sql_sub);
              ?>

              <button 
                class="mt-4 text-sm text-gray-700 underline focus:outline-none" 
                onclick="const el = document.getElementById('submissions-<?= $aid ?>'); el.classList.toggle('hidden');">
                <?= $submissions && $submissions->num_rows > 0 ? 'Show/Hide' : 'No' ?> Student Submissions (<?= $submissions ? $submissions->num_rows : 0 ?>)
              </button>

              <div id="submissions-<?= $aid ?>" class="hidden mt-3">
                <?php if ($submissions && $submissions->num_rows > 0): ?>
                  <table class="min-w-full text-sm divide-y divide-gray-200 border rounded overflow-hidden">
                    <thead class="bg-gray-100">
                      <tr>
                        <th class="px-4 py-2 text-left font-semibold">Student Name</th>
                        <th class="px-4 py-2 text-left font-semibold">File</th>
                        <th class="px-4 py-2 text-left font-semibold">Submitted At</th>
                        <th class="px-4 py-2 text-left font-semibold">Download</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                      <?php while ($sub = $submissions->fetch_assoc()): ?>
                        <tr>
                          <td class="px-4 py-2"><?= htmlspecialchars($sub['student_name']) ?></td>
                          <td class="px-4 py-2"><?= htmlspecialchars($sub['filename']) ?></td>
                          <td class="px-4 py-2"><?= date('Y-m-d H:i', strtotime($sub['submitted_at'])) ?></td>
                          <td class="px-4 py-2">
                            <a href="uploads/submissions/<?= urlencode($sub['filename']) ?>" class="text-blue-600 hover:underline" download>Download</a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                <?php else: ?>
                  <p class="text-gray-500 italic">No submissions yet.</p>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p class="text-gray-500">No assignments uploaded yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>



<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
