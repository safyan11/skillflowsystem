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

      <!-- certificate content  -->
    <div class="md:p-10 p-5 w-full grid grid-cols-1 md:grid-cols-3 gap-6">
  

<?php
require_once "../inc/db.php"; // Adjust path if needed

// Fetch upcoming classes (assuming class_date stores date + time)
$today = date('Y-m-d H:i:s');
$sql = "SELECT oc.*, u.name AS teacher_name 
        FROM online_classes oc
        JOIN users u ON oc.teacher_id = u.id
        WHERE oc.class_date >= ?
        ORDER BY oc.class_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $today);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="col-span-1 md:col-span-3 bg-white p-6 rounded-lg shadow">
  <h2 class="text-2xl font-semibold mb-4">Upcoming Online Classes</h2>
  <?php if ($result->num_rows > 0): ?>
  <div class="overflow-x-auto">
    <table class="min-w-full border border-gray-300 rounded-lg">
      <thead class="bg-gray-100">
        <tr>
          <th class="text-left p-3 border-b">Class Title</th>
          <th class="text-left p-3 border-b">Date & Time</th>
          <th class="text-left p-3 border-b">Teacher</th>
          <th class="text-left p-3 border-b">Google Meet Link</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr class="hover:bg-gray-50">
          <td class="p-3 border-b"><?= htmlspecialchars($row['class_title']) ?></td>
          <td class="p-3 border-b"><?= date('d M Y, H:i', strtotime($row['class_date'])) ?></td>
          <td class="p-3 border-b"><?= htmlspecialchars($row['teacher_name']) ?></td>
          <td class="p-3 border-b">
            <a href="<?= htmlspecialchars($row['meet_link']) ?>" target="_blank" class="text-blue-600 hover:underline">
              Join Class
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p class="text-gray-600">No upcoming classes scheduled.</p>
  <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
?>



  
  </div>
    </div>
  </div>


<!-- side bar menu  -->
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

   <script>
    function downloadCertificate() {
      const cert = document.getElementById('certificate-panel');
      if (!cert) return;
      // Use html2canvas to snapshot the certificate panel
      html2canvas(cert, { scale: 2 }).then(canvas => {
        canvas.toBlob(blob => {
          if (!blob) return;
          const link = document.createElement('a');
          link.download = 'certificate.png';
          link.href = URL.createObjectURL(blob);
          document.body.appendChild(link);
          link.click();
          URL.revokeObjectURL(link.href);
          link.remove();
        }, 'image/png');
      }).catch(err => {
        console.error('Capture failed:', err);
        alert('Failed to capture certificate. Please try again.');
      });
    }
  </script>


</body>
</html>
