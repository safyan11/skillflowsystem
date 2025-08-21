<?php
require_once "inc/header.php";
require_once "../inc/db.php";



$teacher_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
  header('Location: grading.php');
  exit;
}

$grading_id = intval($_GET['id']);

// Fetch grading info with permission check
$sql = "SELECT g.*, a.title AS assignment_title, u.name AS student_name, a.uploaded_by
        FROM grading g
        JOIN assignments a ON g.assignment_id = a.id
        JOIN users u ON g.student_id = u.id
        WHERE g.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $grading_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row || $row['uploaded_by'] != $teacher_id) {
  header('Location: grading.php');
  exit;
}
?>

<body class="bg-gray-50 font-sans antialiased">


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
