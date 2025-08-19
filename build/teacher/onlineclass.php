<?php
require_once "inc/header.php";


$teacher_id = $_SESSION['user_id'] ?? 1; // Replace with actual logged-in teacher id
require_once "../inc/db.php";


$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_title'], $_POST['meet_link'], $_POST['class_date'], $_POST['class_time'])) {
    $class_title = $conn->real_escape_string(trim($_POST['class_title']));
    $class_description = $conn->real_escape_string(trim($_POST['class_description']));
    $meet_link = $conn->real_escape_string(trim($_POST['meet_link']));
    $class_date = $_POST['class_date'];
    $class_time = $_POST['class_time'];

    // Basic validation
    if (empty($class_title) || empty($meet_link) || empty($class_date) || empty($class_time)) {
        $message = '<p class="text-red-600 mb-4">Please fill in all required fields.</p>';
    } else {
        $sql = "INSERT INTO online_classes (teacher_id, class_title, class_description, meet_link, class_date, class_time)
                VALUES ($teacher_id, '$class_title', '$class_description', '$meet_link', '$class_date', '$class_time')";
        if ($conn->query($sql)) {
            $message = '<p class="text-green-600 mb-4">Online class added successfully.</p>';
        } else {
            $message = '<p class="text-red-600 mb-4">Failed to add class: ' . $conn->error . '</p>';
        }
    }
}

// Fetch all classes for this teacher
$sql_fetch = "SELECT * FROM online_classes WHERE teacher_id = $teacher_id ORDER BY class_date DESC, class_time DESC";
$result = $conn->query($sql_fetch);
?>

<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>

    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <?php require_once "inc/topbar.php"; ?>

      <div class="md:p-20 p-6 w-full max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Add Upcoming Online Class</h1>

        <?= $message ?>

        <form method="POST" action="" class="bg-white p-6 rounded-lg shadow mb-12">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block font-semibold mb-2" for="class_title">Class Title <span class="text-red-600">*</span></label>
              <input type="text" name="class_title" id="class_title" required
                class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>

            <div>
              <label class="block font-semibold mb-2" for="meet_link">Google Meet Link <span class="text-red-600">*</span></label>
              <input type="url" name="meet_link" id="meet_link" placeholder="https://meet.google.com/xxx-xxxx-xxx" required
                class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>

            <div>
              <label class="block font-semibold mb-2" for="class_date">Date <span class="text-red-600">*</span></label>
              <input type="date" name="class_date" id="class_date" required
                class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>

            <div>
              <label class="block font-semibold mb-2" for="class_time">Time <span class="text-red-600">*</span></label>
              <input type="time" name="class_time" id="class_time" required
                class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>

            <div class="md:col-span-2">
              <label class="block font-semibold mb-2" for="class_description">Class Description (optional)</label>
              <textarea name="class_description" id="class_description" rows="3"
                class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
            </div>
          </div>

          <button type="submit" class="mt-6 px-6 py-3 bg-black text-white rounded-md hover:bg-gray-800 transition">Add Class</button>
        </form>

        <h2 class="text-2xl font-bold mb-6">Your Upcoming Online Classes</h2>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
          <table class="min-w-full table-auto">
            <thead>
              <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Title</th>
                <th class="py-3 px-6 text-left">Description</th>
                <th class="py-3 px-6 text-left">Google Meet Link</th>
                <th class="py-3 px-6 text-center">Date</th>
                <th class="py-3 px-6 text-center">Time</th>
                <th class="py-3 px-6 text-center">Actions</th>
              </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
              <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-6"><?= htmlspecialchars($row['class_title']) ?></td>
                    <td class="py-3 px-6 max-w-xs truncate" title="<?= htmlspecialchars($row['class_description']) ?>"><?= htmlspecialchars($row['class_description']) ?></td>
                    <td class="py-3 px-6">
                      <a href="<?= htmlspecialchars($row['meet_link']) ?>" target="_blank" class="text-blue-600 hover:underline">
                        Join Meet
                      </a>
                    </td>
                    <td class="py-3 px-6 text-center"><?= htmlspecialchars($row['class_date']) ?></td>
                    <td class="py-3 px-6 text-center"><?= htmlspecialchars(substr($row['class_time'], 0, 5)) ?></td>
                    <td class="py-3 px-6 text-center">
                      <!-- Optionally add edit/delete buttons here -->
                      <form method="POST" action="delete_online_class.php" onsubmit="return confirm('Delete this class?');" style="display:inline;">
                        <input type="hidden" name="class_id" value="<?= $row['id'] ?>" />
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="6" class="py-4 text-center text-gray-500">No classes found.</td></tr>
              <?php endif; ?>
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
