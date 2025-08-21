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
 





  <!-- sidebar menu  -->
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
