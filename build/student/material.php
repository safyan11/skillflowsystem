<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50 font-sans antialiased">
  <div class="min-h-screen flex">
 <?php require_once "inc/sidebar.php"; ?>
<?php
require_once "../inc/db.php";
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
    <!-- Overlay for mobile when sidebar open -->
    <div id="overlay" class="fixed inset-0 bg-black/30 z-10 hidden md:hidden"></div>

    <!-- Main content area -->
    <div class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden">
      <!-- top bar -->
      <?php require_once "inc/topbar.php"; ?>

  <div class="overflow-x-auto bg-white rounded-2xl shadow p-4 mt-10 xl:mx-10 mx-5">
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
 
</body>
</html>
