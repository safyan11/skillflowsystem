
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

      <!-- scrollable content -->
      <div class="flex-1 overflow-y-auto">
        <!-- hero/banner -->
        <section class="px-4 py-6">
          <h1 class="text-4xl pb-10 font-bold">Student Dashboard</h1>
          <div class="rounded-xl bg-neutral-900 text-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex-1">
              <div class="text-sm uppercase tracking-wide mb-1">Online Course</div>
              <h2 class="text-3xl font-bold leading-tight">Every expert was once a beginner — start your journey now.</h2>
            </div>
            <div class="mt-4 md:mt-0">
              <button class="inline-flex items-center gap-2 bg-white text-black px-6 py-3 rounded-full font-medium shadow">
                Join Now
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
              </button>
            </div>
          </div>
        </section>

        <!-- cards grid -->
        <main class="px-4 pb-8">
        <?php
include '../inc/db.php'; // Your database connection

$sql = "SELECT * FROM courses ORDER BY id DESC";
$result = $conn->query($sql);

echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
 echo '
<a href="playlist.php?id=' . $row['id'] . '" class="block">
    <div class="card bg-white rounded-2xl overflow-hidden shadow hover:shadow-lg transition">
        <!-- Thumbnail -->
        <div class="h-40 bg-cover bg-center" style="background-image: url(\'../admin/' . htmlspecialchars($row['thumbnail']) . '\');"></div>
        
        <!-- Content -->
        <div class="p-4">
            <h3 class="font-semibold text-lg mb-1">' . htmlspecialchars($row['title']) . '</h3>
            <p class="text-sm text-gray-600 mb-3">' . htmlspecialchars($row['short_description']) . '</p>
            
            <ul class="text-xs text-gray-500 space-y-1">
                <li><strong>Video Hours:</strong> ' . htmlspecialchars($row['video_hours']) . '</li>
                <li><strong>Articles:</strong> ' . htmlspecialchars($row['articles']) . '</li>
                <li><strong>Resources:</strong> ' . htmlspecialchars($row['resources']) . '</li>
                <li><strong>Assignments:</strong> ' . htmlspecialchars($row['assignments']) . '</li>
                <li><strong>Certificate:</strong> ' . htmlspecialchars($row['certificate']) . '</li>
            </ul>

            <div class="mt-4">
                <p class="text-xs text-gray-500"><strong>Instructor:</strong> ' . htmlspecialchars($row['instructor_name']) . ' (' . htmlspecialchars($row['instructor_designation']) . ')</p>
            </div>
        </div>
    </div>
</a>';
    }
} else {
    echo '<p class="col-span-4 text-center text-gray-500">No courses found.</p>';
}
echo '</div>';

$conn->close();
?>

        </main>
      </div>
    </div>
  </div>

</body>
</html>
