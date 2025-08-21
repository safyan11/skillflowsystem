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



<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
