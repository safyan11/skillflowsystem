<?php 
require_once "inc/header.php"; 
require_once "../inc/db.php"; 
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
        <h2 class="text-2xl font-semibold mb-4">Certificates</h2>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white shadow rounded-lg">
            <thead>
              <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">ID</th>
                <th class="py-3 px-6 text-left">Course Name</th>
                <th class="py-3 px-6 text-left">File</th>
                <th class="py-3 px-6 text-left">Uploaded At</th>
                <th class="py-3 px-6 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
              <?php
                $sql = "SELECT * FROM certificates ORDER BY uploaded_at DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr class='border-b border-gray-200 hover:bg-gray-100'>
                              <td class='py-3 px-6 text-left'>{$row['id']}</td>
                              <td class='py-3 px-6 text-left'>{$row['course_name']}</td>
                              <td class='py-3 px-6 text-left'>
                                <a href='../admin/{$row['file_path']}' target='_blank' class='text-blue-500 underline'>View</a>
                              </td>
                              <td class='py-3 px-6 text-left'>{$row['uploaded_at']}</td>
                              <td class='py-3 px-6 text-center'>
                                <a href='../{$row['file_path']}' download class='bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition'>Download</a>
                              </td>
                            </tr>";
                  }
                } else {
                  echo "<tr><td colspan='5' class='py-3 px-6 text-center text-gray-500'>No certificates found</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
