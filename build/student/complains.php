<?php
require_once "inc/header.php";
require_once "../inc/db.php"; // <-- Make sure you have DB connection here


// Handle complaint submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject'], $_POST['message'])) {
    $student_id = $_SESSION['user_id'];
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

   // Assuming user is logged in
// echo $student_id;
// Check if the student exists in users
$check = $conn->prepare("SELECT id FROM users WHERE id = ?");
$check->bind_param("i", $student_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("INSERT INTO complaints (student_id, subject, message, status, created_at) VALUES (?, ?, ?, ?, NOW())");
    $status = 'pending';
    $stmt->bind_param("isss", $student_id, $subject, $message, $status);
    $stmt->execute();
} else {
    echo "Error: Student ID not found in users table.";
}
}
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

      <div class="w-full max-w-6xl bg-white rounded-2xl shadow-lg p-8 grid grid-cols-1  gap-8">
    <!-- FAQ list -->
  

    <!-- Side info card (Complaint Form) -->
    <div class="lg:col-span-4 flex">
      <div class="w-full border rounded-xl border-gray-300 p-6 flex flex-col">
        <div class="flex justify-center mb-4">
          <!-- Placeholder icon -->
          <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded-lg">
            <svg class="w-10 h-10 text-gray-800" fill="currentColor" viewBox="0 0 24 24">
              <path d="M4 4h16v16H4V4zm8 2a3 3 0 100 6 3 3 0 000-6zm0 8c-2.7 0-5.2 1.3-6.8 3.4.1 1 1 1.6 2 1.6h9.6c1 0 1.9-.6 2-1.6A8.97 8.97 0 0012 14z"/>
            </svg>
          </div>
        </div>
        <h2 class="text-xl font-bold mb-2 text-center">Submit a Complaint</h2>

        <?php if (!empty($success)): ?>
          <p class="text-green-600 text-sm text-center mb-3"><?= htmlspecialchars($success) ?></p>
        <?php elseif (!empty($error)): ?>
          <p class="text-red-600 text-sm text-center mb-3"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
          <input type="text" name="subject" placeholder="Complaint Subject" class="w-full border rounded-lg px-3 py-2 text-sm" required>
          <textarea name="message" placeholder="Describe your issue" rows="4" class="w-full border rounded-lg px-3 py-2 text-sm" required></textarea>
          <button type="submit" class="w-full px-4 py-3 bg-black text-white rounded-lg font-medium hover:opacity-90 transition">
            Send Complaint
          </button>
        </form>
      </div>
    </div>
  </div>
    </div>
  </div>

<!-- faqs js  -->
  <script>
    document.querySelectorAll('[data-accordion-target]').forEach(btn => {
      btn.addEventListener('click', () => {
        const isOpen = btn.getAttribute('aria-expanded') === 'true';
        document.querySelectorAll('[data-accordion-target]').forEach(b => {
          b.setAttribute('aria-expanded', 'false');
          b.querySelector('[aria-hidden]').textContent = '+';
          b.nextElementSibling.classList.add('hidden');
        });
        if (!isOpen) {
          btn.setAttribute('aria-expanded', 'true');
          btn.querySelector('[aria-hidden]').textContent = '−';
          btn.nextElementSibling.classList.remove('hidden');
        }
      });
    });
  </script>
</body>
</html>
