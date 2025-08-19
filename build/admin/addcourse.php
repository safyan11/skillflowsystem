<?php
// Include DB connection
require '../inc/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $title = trim($_POST['title']);
    $short_description = trim($_POST['short_description']);
    $video_hours = trim($_POST['video_hours']);
    $articles = intval($_POST['articles']);
    $resources = intval($_POST['resources']);
    $assignments = intval($_POST['assignments']);
    $certificate = $_POST['certificate'] === 'Yes' ? 'Yes' : 'No';
    $full_description = trim($_POST['full_description']);
    $instructor_name = trim($_POST['instructor_name']);
    $instructor_designation = trim($_POST['instructor_designation']);
    $overview = trim($_POST['overview']);
    $what_you_will_learn = trim($_POST['what_you_will_learn']);

    // Handle thumbnail upload
    $thumbnail_path = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/courses/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['thumbnail']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_path)) {
            $thumbnail_path = 'uploads/courses/' . $file_name;
        } else {
            die("Error uploading file.");
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO courses 
        (title, short_description, video_hours, articles, resources, assignments, certificate, full_description, instructor_name, instructor_designation, overview, what_you_will_learn, thumbnail)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssiiisssssss",
        $title,
        $short_description,
        $video_hours,
        $articles,
        $resources,
        $assignments,
        $certificate,
        $full_description,
        $instructor_name,
        $instructor_designation,
        $overview,
        $what_you_will_learn,
        $thumbnail_path
    );

    if ($stmt->execute()) {
        echo "<script>alert('Course added successfully!'); window.location.href='addcourse.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$courses = $conn->query("SELECT * FROM courses ORDER BY created_at DESC");
?>


<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50">


  <?php require_once "inc/sidebar.php"; ?>

  <!-- Dashboard Header -->
  <!-- <header class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Admin Dashboard</h1>
    <a href="logout.php" class="text-black font-bold text-lg hover:underline">Logout</a>
  </header> -->

  <!-- Main Container -->
  <main class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden bg-white rounded-lg shadow p-6">
    <?php require_once "inc/topbar.php"; ?>
    <h2 class="text-2xl font-bold mb-6 mt-10">Add New Course</h2>
    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-4">

      <!-- Course Title -->
      <div>
        <label class="block font-semibold mb-1">Course Title</label>
        <input type="text" name="title" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
      </div>

      <!-- Short Description -->
      <div>
        <label class="block font-semibold mb-1">Short Description</label>
        <textarea name="short_description" rows="2" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
      </div>

      <!-- Course Includes -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">Hours of Videos</label>
          <input type="text" name="video_hours" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block font-semibold mb-1">Number of Articles</label>
          <input type="number" name="articles" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block font-semibold mb-1">Downloadable Resources</label>
          <input type="number" name="resources" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block font-semibold mb-1">Assignments</label>
          <input type="number" name="assignments" required class="w-full border rounded px-3 py-2">
        </div>
      </div>

      <!-- Certificate -->
      <div>
        <label class="block font-semibold mb-1">Certificate Provided?</label>
        <select name="certificate" class="w-full border rounded px-3 py-2">
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>
      </div>

      <!-- Full Description -->
      <div>
        <label class="block font-semibold mb-1">Full Course Description</label>
        <textarea name="full_description" rows="5" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
      </div>

      <!-- Instructor Details -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-semibold mb-1">Instructor Name</label>
          <input type="text" name="instructor_name" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
          <label class="block font-semibold mb-1">Instructor Designation</label>
          <input type="text" name="instructor_designation" required class="w-full border rounded px-3 py-2">
        </div>
      </div>

      <!-- Course Overview -->
      <div>
        <label class="block font-semibold mb-1">Course Overview</label>
        <textarea name="overview" rows="4" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
      </div>

      <!-- What You Will Learn -->
      <div>
        <label class="block font-semibold mb-1">What You Will Learn (One per line)</label>
        <textarea name="what_you_will_learn" rows="4" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"></textarea>
      </div>

      <!-- Thumbnail Upload -->
      <div>
        <label class="block font-semibold mb-1">Course Thumbnail</label>
        <input type="file" name="thumbnail" accept="image/*" required class="w-full border rounded px-3 py-2">
      </div>

      <!-- Submit Button -->
      <div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
          Add Course
        </button>
      </div>

    </form>
  </main>
<!-- Course List Table -->
    <div class="bg-white p-6 rounded-xl shadow ml-64 mt-20">
        <h2 class="text-xl font-bold mb-4">Uploaded Courses</h2>
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-2">Thumbnail</th>
                    <th class="border border-gray-300 p-2">Title</th>
                    <th class="border border-gray-300 p-2">Instructor</th>
                    <th class="border border-gray-300 p-2">Video Hours</th>
                    <th class="border border-gray-300 p-2">Certificate</th>
                    <th class="border border-gray-300 p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $courses->fetch_assoc()): ?>
                <tr>
                    <td class="border border-gray-300 p-2">
                        <img src="<?= htmlspecialchars($row['thumbnail']) ?>" class="w-20 h-12 object-cover rounded">
                    </td>
                    <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['title']) ?></td>
                    <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['instructor_name']) ?></td>
                    <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['video_hours']) ?></td>
                    <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['certificate']) ?></td>
                    <td class="border border-gray-300 p-2">
                        <a href="edit_course.php?id=<?= $row['id'] ?>" class="text-blue-600">Edit</a> | 
                        <a href="delete_course.php?id=<?= $row['id'] ?>" class="text-red-600" onclick="return confirm('Delete this course?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

      <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>

</body>
</html>
