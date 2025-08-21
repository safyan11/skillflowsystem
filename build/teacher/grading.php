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
