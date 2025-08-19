<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 
// ✅ Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_name'])) {
    $courseName = $_POST['course_name'];

    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] == 0) {
        $uploadDir = "uploads/certificates/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['certificate']['name']);
        $targetPath = $uploadDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES['certificate']['tmp_name'], $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO certificates (course_name, file_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $courseName, $targetPath);
            $stmt->execute();
            echo "<script>alert('Certificate uploaded successfully');</script>";
        } else {
            echo "<script>alert('File upload failed');</script>";
        }
    }
}

// ✅ Fetch courses from DB (replace with your real course table)
$courses = [
    "INTRODUCTION TO FINANCIAL MARKETS",
    "WEB DEVELOPMENT WITH HTML, CSS & JAVASCRIPT",
    "GRAPHIC DESIGN WITH ADOBE PHOTOSHOP",
    "SEO CRASH COURSE: RANK #1 ON GOOGLE",
    "PUBLIC SPEAKING & CONFIDENCE BUILDING",
    "INSTAGRAM REELS & SHORTS CREATION"
];
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

  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Provide Certification</h1>

    <!-- List of Courses -->
  <div class="space-y-4">
    <?php foreach ($courses as $course) { ?>
        <div class="bg-white rounded-2xl shadow flex items-center justify-between p-6">
            <p class="font-semibold text-sm sm:text-base"><?= htmlspecialchars($course) ?></p>
            <form method="POST" enctype="multipart/form-data" style="display:inline;">
                <input type="hidden" name="course_name" value="<?= htmlspecialchars($course) ?>">
                <input type="file" name="certificate" required class="hidden" id="file_<?= md5($course) ?>" onchange="this.form.submit()">
                <label for="file_<?= md5($course) ?>" class="bg-black text-white px-4 py-2 rounded-md text-sm hover:bg-gray-800 transition cursor-pointer">
                    + Upload Certificate
                </label>
            </form>
        </div>
    <?php } ?>
</div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-8 text-sm text-gray-600">
      <button class="hover:text-black">&larr;</button>
      <span>Page 1 of 10</span>
      <button class="hover:text-black">&rarr;</button>
    </div>
  </div>
    </div>
  </div>

  

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
