<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
 <?php require_once "inc/sidebar.php"; ?>

    <!-- Overlay for mobile when sidebar open -->
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <!-- top bar -->
      <?php require_once "inc/topbar.php"; ?>

      <?php
include '../inc/db.php';

// If id is passed and valid, use it
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $courseId = intval($_GET['id']);
} else {
    // Fetch first available course id from table
    $result = $conn->query("SELECT id FROM courses ORDER BY id ASC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $courseId = intval($row['id']);
    } else {
        die("No courses found in database.");
    }
}

// Fetch course data
$sql = "SELECT * FROM courses WHERE id = $courseId";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("<p class='text-center text-red-500 mt-10'>Course not found.</p>");
}

$course = $result->fetch_assoc();
?>

<!-- scrollable content -->
<div class="flex gap-10 justify-between overflow-y-auto px-6 py-6">

    <div class="lg:w-2/3 w-full">
        <div class="card bg-white rounded-2xl overflow-hidden">
            <!-- Dynamic Thumbnail -->
            <div class="h-60 sm:h-72 bg-center bg-cover" style="background-image: url('../admin/<?php echo htmlspecialchars($course['thumbnail']); ?>');"></div>
            <div class="p-6">
                <div class="flex items-start gap-4 mb-2">
                    <h1 class="text-2xl font-bold flex-1"><?php echo htmlspecialchars($course['title']); ?></h1>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <!-- Instructor Image Placeholder -->
                        <img src="https://via.placeholder.com/40" alt="author" class="w-10 h-10 rounded-full object-cover" />
                        <div>
                            <div class="font-semibold"><?php echo htmlspecialchars($course['instructor_name']); ?></div>
                            <div class="text-gray-500 text-xs"><?php echo htmlspecialchars($course['instructor_designation']); ?></div>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed mb-6 text-sm">
                    <?php echo nl2br(htmlspecialchars($course['full_description'])); ?>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="font-semibold mb-2">🚀 What You’ll Learn:</div>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 text-sm">
                            <?php
                            $lessons = explode("\n", $course['what_you_will_learn']);
                            foreach ($lessons as $lesson) {
                                echo "<li>" . htmlspecialchars(trim($lesson)) . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Playlist section -->
    <aside class="hidden lg:flex flex-col inset-y-0 right-0 w-1/3 bg-white border-l border-gray-200 px-4 py-6 playlist-scroll">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Playlist</h2>
                <p class="text-gray-500 text-xs mt-1"><?php echo htmlspecialchars($course['short_description']); ?></p>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center bg-white rounded-xl overflow-hidden shadow">
                <div class="flex-1 p-3">
                    <p class="text-xs"><?php echo htmlspecialchars($course['overview']); ?></p>
                </div>
                <div class="w-24 h-16 flex-shrink-0">
                    <img src="<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="thumb" class="object-cover w-full h-full" />
                </div>
            </div>
        </div>
    </aside>
</div>
    </div>
  </div>

 
</body>
</html>
