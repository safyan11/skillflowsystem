<?php
require_once "../inc/db.php"; // your DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);

    // ✅ Toggle Status Logic
    if (isset($_POST['toggle_status'])) {
        $currentStatus = $_POST['current_status'];
        $newStatus = $currentStatus === 'approved' ? 'pending' : 'approved';

        $stmt = $conn->prepare("UPDATE users SET verify_status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Status updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update status.";
        }

        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Assign Role
    if (isset($_POST['assign_role']) && isset($_POST['user_id']) && isset($_POST['role'])) {
        $userId = intval($_POST['user_id']);
        $role = $_POST['role']; // 'student' or 'teacher'

        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $userId);
       if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Role updated successfully');</script>";
    } else {
        echo "<script>alert('Query ran but no rows were updated. User ID may not exist or role was already the same.');</script>";
    }
} else {
    echo "<script>alert('Failed to update role: " . $stmt->error . "');</script>";
}
    }
}


// 📥 Fetch all users
$users = $conn->query("SELECT * FROM users");
?>


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

<div class="p-6">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold">Add User</h2>
    <button class="bg-black text-white px-4 py-2 rounded">+ Add User</button>
  </div>

  <div class="overflow-x-auto rounded-lg shadow pb-44">
<table class="min-w-full bg-white">
  <thead>
    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
      <th class="py-3 px-6 text-left">User ID</th>
      <th class="py-3 px-6 text-left">Student Name</th>
      <th class="py-3 px-6 text-left">Email</th>
      <th class="py-3 px-6 text-left">Role</th>
      <th class="py-3 px-6 text-left">Signup Date</th>
      <th class="py-3 px-6 text-left">Status</th>
      <th class="py-3 px-6 text-center">Actions</th>
    </tr>
  </thead>
 <tbody class="text-gray-700 text-sm">
  <?php while($row = $users->fetch_assoc()): ?>
  <tr class="border-b border-gray-200 hover:bg-gray-50">
    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['id']) ?></td>
    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['name']) ?></td>
    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['email']) ?></td>
    <td class="py-3 px-6 text-left"><?= htmlspecialchars($row['role']) ?></td>
    <td class="py-3 px-6 text-left"><?= date("d M Y", strtotime($row['created_at'])) ?></td>
    <td class="py-3 px-6 text-left"><?= ucfirst($row['verify_status']) ?></td>
    <td class="py-3 px-6 text-center relative">
      <div class="relative inline-block text-left group">
        <button class="p-1 rounded hover:bg-gray-200">
          <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
          </svg>
        </button>
        <!-- Dropdown -->
        <div class="absolute right-0 z-10 w-36 origin-top-right bg-white border border-gray-200 rounded-md shadow-lg hidden group-hover:block">
          <div class="py-1">

            <!-- Toggle Status Form -->
            <form method="POST" action="#">
              <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
              <input type="hidden" name="current_status" value="<?= $row['verify_status'] ?>">
              <button type="submit" name="toggle_status" class="px-2 py-2 hover:bg-black hover:text-white text-center text-base font-semibold text-black rounded w-full">
                <?= $row['verify_status'] === 'approved' ? '❌ Set Pending' : '✅ Approve' ?>
              </button>
            </form>

<!-- Assign Role Buttons -->
  <!-- Assign Student Role -->
    <form method="POST" style="display:inline;">
      <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
      <input type="hidden" name="role" value="student">
      <button type="submit" name="assign_role" class="px-2 py-2 hover:bg-black hover:text-white text-center text-base font-semibold text-black rounded w-full">Make Student</button>
    </form>

    <!-- Assign Teacher Role -->
    <form method="POST" style="display:inline;">
      <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
      <input type="hidden" name="role" value="teacher">
      <button type="submit" name="assign_role" class="px-2 py-2 hover:bg-black hover:text-white text-center text-base font-semibold text-black rounded w-full">Make Teacher</button>
    </form>

          </div>
        </div>
      </div>
    </td>
  </tr>
  <?php endwhile; ?>
</tbody>

</table>
  </div>

  <!-- Pagination -->
  <div class="flex justify-between items-center mt-4 text-sm text-gray-500">
    <button class="p-2 rounded-full hover:bg-gray-100">
      <span>&larr;</span>
    </button>
    <span>Page 1 of 10</span>
    <button class="p-2 rounded-full hover:bg-gray-100">
      <span>&rarr;</span>
    </button>
  </div>
</div>

    </div>
  </div>

  



  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
