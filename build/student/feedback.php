  <script>
    function toggleDropdown() {
      document.getElementById('dropdown').classList.toggle('hidden');
    }
    function selectCourse(id, title) {
      document.getElementById('selectedCourseBtn').textContent = title + " ▼";
      document.getElementById('course_id').value = id;
      toggleDropdown();
    }
  </script>
<?php require_once "inc/header.php"; 
require_once "../inc/db.php";
// Redirect if user not logged in


$userId = $_SESSION['user_id'];
$feedbackSaved = false;
$error = '';

// Fetch courses for dropdown
$courses = [];
$result = $conn->query("SELECT id, title, short_description FROM courses ORDER BY title");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Handle feedback form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = intval($_POST['course_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $comments = trim($_POST['comments'] ?? '');

    if ($course_id > 0 && $rating >= 1 && $rating <= 5 && !empty($comments)) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_id, rating, comments) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $userId, $course_id, $rating, $comments);
        if ($stmt->execute()) {
            $feedbackSaved = true;
        } else {
            $error = "Failed to save feedback: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields correctly.";
    }
}

?>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
  <?php require_once "inc/sidebar.php"; ?>

    <!-- Overlay for mobile when sidebar open -->
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <!-- top bar -->
       <?php require_once "inc/topbar.php"; ?>


      <div class="lg:p-20 py-8">
 




<h1 class="text-3xl font-bold mb-6">Your Feedback</h1>

  <?php if ($feedbackSaved): ?>
    <p class="text-green-600 mb-4">Thank you! Your feedback has been saved.</p>
  <?php elseif ($error): ?>
    <p class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="">
    <!-- Select Course -->
    <div class="flex gap-4 mb-6">
      <div class="relative inline-block">
        <button type="button" id="selectedCourseBtn" onclick="toggleDropdown()" class="bg-gray-200 px-4 py-2 rounded-md shadow text-sm font-medium focus:outline-none">
          Select Course ▼
        </button>
        <ul id="dropdown" class="absolute mt-2 w-48 bg-white border rounded shadow-lg hidden z-10 max-h-48 overflow-auto">
          <?php foreach ($courses as $course): ?>
            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer" onclick="selectCourse(<?= $course['id'] ?>, '<?= addslashes($course['title']) ?>')">
              <?= htmlspecialchars($course['title']) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <button type="button" class="text-gray-400 font-semibold" onclick="alert('Please select a course first')">Review Feedback</button>
    </div>

    <input type="hidden" name="course_id" id="course_id" value="">

    <!-- Rating -->
    <label class="block mb-2 font-semibold">Your Rating (1 to 5 stars):</label>
    <select name="rating" required class="mb-4 px-3 py-2 border rounded">
      <option value="">Select rating</option>
      <option value="5">★★★★★ (5 stars)</option>
      <option value="4">★★★★☆ (4 stars)</option>
      <option value="3">★★★☆☆ (3 stars)</option>
      <option value="2">★★☆☆☆ (2 stars)</option>
      <option value="1">★☆☆☆☆ (1 star)</option>
    </select>

    <!-- Comments -->
    <label class="block mb-2 font-semibold">Your Feedback:</label>
    <textarea name="comments" rows="5" required class="w-full px-3 py-2 border rounded mb-4" placeholder="Write your feedback here..."></textarea>

    <button type="submit" class="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-900">Upload feedback</button>
  </form>

 

  </div>
    </div>
  </div>

  <!-- sidebar menu  -->
  


 <!-- Script for dropdown -->
  <script>
    function toggleDropdown() {
      const dropdown = document.getElementById('dropdown');
      dropdown.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
      const dropdown = document.getElementById('dropdown');
      if (!e.target.matches('button')) {
        dropdown.classList.add('hidden');
      }
    });
  </script>

</body>
</html>
