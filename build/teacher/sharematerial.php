<?php
require_once "inc/header.php";
require_once "inc/sidebar.php";

require_once "../inc/db.php";

// Handle form submission for file upload
$upload_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate title and file
    if (!empty($_POST['title']) && isset($_FILES['material_file']) && $_FILES['material_file']['error'] === 0) {
        $title = $conn->real_escape_string($_POST['title']);
        $file = $_FILES['material_file'];

        // Sanitize filename and prepare upload directory
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = basename($file['name']);
        $target_file = $upload_dir . $filename;

        // Move uploaded file to uploads directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $filesize = $file['size'];
            // Assuming teacher's user id = 1 for example, replace as needed
            $uploaded_by = 1;

            // Insert into DB
            $sql = "INSERT INTO materials (title, filename, filesize, uploaded_by) VALUES ('$title', '$filename', $filesize, $uploaded_by)";
            if (!$conn->query($sql)) {
                $upload_error = "Database error: " . $conn->error;
            }
        } else {
            $upload_error = "Failed to move uploaded file.";
        }
    } else {
        $upload_error = "Please provide a title and select a file.";
    }
}

// Fetch all materials to display
$materials = [];
$sql = "SELECT * FROM materials ORDER BY uploaded_at DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
}
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">   
    <!-- Sidebar -->
    <?php // sidebar included above ?>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow">
      <!-- Topbar -->

      <?php require_once "inc/topbar.php"; ?>
      <?php // topbar included above ?>

      <div class="p-6 max-w-7xl">
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-3xl font-bold">Upload Material</h1>
        </div>

        <!-- Upload Form -->
        <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl shadow-md max-w-7xl mb-8">
          <?php if ($upload_error): ?>
            <div class="mb-4 text-red-600 font-semibold"><?= htmlspecialchars($upload_error) ?></div>
          <?php endif; ?>

          <div class="mb-4">
            <label for="title" class="block text-gray-700 font-semibold mb-2">Title</label>
            <input type="text" id="title" name="title" required
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black" />
          </div>

          <div class="mb-4">
            <label for="material_file" class="block text-gray-700 font-semibold mb-2">Select File</label>
            <input type="file" id="material_file" name="material_file" required
              class="w-full" />
          </div>

          <button type="submit" 
            class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">Upload</button>
        </form>

        <!-- Materials Table -->
        <div class="overflow-x-auto bg-white rounded-2xl shadow p-4">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase whitespace-nowrap">Title</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase whitespace-nowrap">Filename</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase whitespace-nowrap">Size</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase whitespace-nowrap">Uploaded At</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase whitespace-nowrap">Download</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php if (count($materials) > 0): ?>
                <?php foreach ($materials as $material): ?>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($material['title']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($material['filename']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= number_format($material['filesize'] / 1024, 2) ?> KB</td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= date('Y-m-d H:i', strtotime($material['uploaded_at'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <a href="<?= 'uploads/' . urlencode($material['filename']) ?>" 
                         class="text-blue-600 hover:underline" 
                         download>Download</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="px-6 py-4 text-center text-gray-500">No materials uploaded yet.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Sidebar & overlay JS (from your code) -->
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

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
