<?php
require '../inc/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = trim($_POST['site_name']);
    $contact_email = trim($_POST['contact_email']);
    
    // Handle logo upload
    $logo_path = $_POST['current_logo'];
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/settings/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES['logo']['name']);
        $target_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_path)) {
            $logo_path = 'uploads/settings/' . $file_name;
        }
    }

    $stmt = $conn->prepare("UPDATE system_settings SET site_name=?, contact_email=?, logo_path=? WHERE id=1");
    if ($stmt) {
        $stmt->bind_param("sss", $site_name, $contact_email, $logo_path);
        if ($stmt->execute()) {
            $msg = "<div class='text-green-600 font-bold mb-4'>Settings updated successfully.</div>";
        } else {
            $msg = "<div class='text-red-600 font-bold mb-4'>Error updating settings.</div>";
        }
        $stmt->close();
    }
}

// Fetch current
$settings_sql = $conn->query("SELECT * FROM system_settings WHERE id=1");
$settings = $settings_sql->fetch_assoc();
if (!$settings) {
    // defaults if empty
    $settings = ['site_name'=>'LMS', 'contact_email'=>'', 'logo_path'=>''];
}
?>

<?php require_once "inc/header.php"; ?>
<body class="bg-gray-50">

  <?php require_once "inc/sidebar.php"; ?>

  <main class="flex-1 flex flex-col ml-0 md:ml-64 overflow-hidden bg-white rounded-lg shadow p-6 min-h-screen">
    <?php require_once "inc/topbar.php"; ?>
    <h2 class="text-2xl font-bold mb-6 mt-10">System Settings</h2>
    
    <?= $msg ?>

    <form action="settings.php" method="POST" enctype="multipart/form-data" class="w-full max-w-lg space-y-4">
        <div>
            <label class="block font-semibold mb-1">Site Name</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
        </div>
        <div>
            <label class="block font-semibold mb-1">Contact Email</label>
            <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
        </div>
        <div>
            <label class="block font-semibold mb-1">Site Logo</label>
            <?php if (!empty($settings['logo_path'])): ?>
                <div class="mb-2">
                    <img src="<?= htmlspecialchars($settings['logo_path']) ?>" alt="Logo" class="h-16 w-auto object-contain bg-gray-100 p-2 rounded">
                </div>
            <?php endif; ?>
            <input type="file" name="logo" accept="image/*" class="w-full border rounded px-3 py-2">
            <input type="hidden" name="current_logo" value="<?= htmlspecialchars($settings['logo_path']) ?>">
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current logo.</p>
        </div>
        <div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Save Settings
            </button>
        </div>
    </form>
  </main>

  <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
