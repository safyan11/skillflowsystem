<?php
require_once "inc/header.php";
require_once "../inc/db.php";

$user_id = $_SESSION['user_id'] ?? 1;
$message = '';
<<<<<<< HEAD
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name        = $conn->real_escape_string(trim($_POST['name']));
    $email       = $conn->real_escape_string(trim($_POST['email']));
    $roll_number = $conn->real_escape_string(trim($_POST['roll_number']));
    $password    = $_POST['password'];

    $image_update_sql = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed  = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = time() . "_" . $user_id . "." . $ext;
            $dir = "../uploads/profile/";
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $upload_path  = $dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $old_img_res = $conn->query("SELECT profile_image FROM users WHERE id=$user_id");
                $old_img     = $old_img_res->fetch_assoc()['profile_image'] ?? '';
=======

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $roll_number = $conn->real_escape_string(trim($_POST['roll_number']));
    $password = $_POST['password'];
    
    // Identity Image Orchestration
    $image_update_sql = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = time() . "_" . $user_id . "." . $ext;
            $upload_path = "../uploads/profile/" . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                $old_img_res = $conn->query("SELECT profile_image FROM users WHERE id=$user_id");
                $old_img = $old_img_res->fetch_assoc()['profile_image'];
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9
                if ($old_img && file_exists("../uploads/profile/" . $old_img)) {
                    unlink("../uploads/profile/" . $old_img);
                }
                $image_update_sql = ", profile_image='$new_filename'";
            }
        }
    }

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name='$name', email='$email', roll_number='$roll_number', password='$hashed' $image_update_sql WHERE id=$user_id";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email', roll_number='$roll_number' $image_update_sql WHERE id=$user_id";
    }

    if ($conn->query($sql)) {
<<<<<<< HEAD
        $message = "Profile updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

$user        = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$current_img = !empty($user['profile_image'])
    ? "../uploads/profile/" . $user['profile_image']
    : "https://ui-avatars.com/api/?name=" . urlencode($user['name']) . "&background=3b82f6&color=fff";
?>

<body class="bg-gray-50" style="font-family:'Outfit',sans-serif;">
<div class="min-h-screen flex">

    <!-- Sidebar -->
    <?php require_once "inc/sidebar.php"; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col md:ml-72">
        <?php require_once "inc/topbar.php"; ?>

        <div class="p-6 max-w-4xl mx-auto mt-6 w-full mb-12">

            <!-- Page Header -->
            <div class="flex items-center gap-4 mb-8">
                <div class="bg-blue-600 p-3 rounded-2xl shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-user-gear text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">My Profile</h1>
                    <p class="text-gray-500">Update your personal details and profile picture.</p>
                </div>
            </div>

            <!-- Success / Error Messages -->
            <?php if ($message): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> <?= $message ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6 font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Profile Picture Sidebar -->
                <div class="md:col-span-1">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                        <div class="relative inline-block mb-4">
                            <img src="<?= $current_img ?>" id="preview" class="w-32 h-32 rounded-full object-cover border-4 border-gray-50 shadow-sm mx-auto">
                            <label for="imageUpload" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition shadow-lg">
                                <i class="fa-solid fa-camera text-sm"></i>
                            </label>
                            <input type="file" name="profile_image" id="imageUpload" class="hidden" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <h3 class="font-bold text-gray-800 tracking-tight text-lg"><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="text-xs text-gray-400 mb-4"><?= htmlspecialchars($user['email']) ?></p>
                        <div class="px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-[10px] uppercase font-black tracking-widest inline-block">Student</div>
                        <?php if (!empty($user['roll_number'])): ?>
                            <p class="text-xs text-gray-400 mt-3 font-semibold">Roll: <?= htmlspecialchars($user['roll_number']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Details -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="grid grid-cols-1 gap-6">

                            <!-- Full Name -->
                            <div>
                                <label class="block mb-2 font-bold text-gray-700 text-sm">Full Name</label>
                                <div class="relative">
                                    <i class="fa-solid fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50">
                                </div>
                            </div>

                            <!-- Roll Number -->
                            <div>
                                <label class="block mb-2 font-bold text-gray-700 text-sm">Roll Number</label>
                                <div class="relative">
                                    <i class="fa-solid fa-id-badge absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="roll_number" value="<?= htmlspecialchars($user['roll_number'] ?? '') ?>" placeholder="E.g. SF-2024-001"
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block mb-2 font-bold text-gray-700 text-sm">Email Address</label>
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="pt-4 border-t">
                                <label class="block mb-2 font-bold text-gray-700 text-sm">New Password</label>
                                <div class="relative">
                                    <i class="fa-solid fa-shield-halved absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="password" name="password" placeholder="Leave blank to keep current password"
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Password will be encrypted before saving.</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" name="update_profile"
                                class="w-full bg-blue-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                                Upload
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
=======
        $message = '<div class="bg-emerald-50 text-emerald-700 p-6 rounded-[2rem] mb-10 border border-emerald-100 flex items-center gap-4 italic font-black shadow-lg">
                        <i class="fa-solid fa-circle-check text-xl"></i> Identity Synchronized Successfully.
                    </div>';
    } else {
        $message = '<div class="bg-rose-50 text-rose-700 p-6 rounded-[2rem] mb-10 border border-rose-100 flex items-center gap-4 italic font-black">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i> Protocol Failure: ' . $conn->error . '
                    </div>';
    }
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$current_img = !empty($user['profile_image']) ? "../uploads/profile/" . $user['profile_image'] : "https://i.pravatar.cc/150";
?>

<body class="bg-[#f8fafc] font-sans antialiased text-slate-900">
  <div class="min-h-screen flex">
    <?php require_once "inc/sidebar.php"; ?>
    <div class="flex-1 flex flex-col ml-0 md:ml-64 transition-all duration-300">
      <?php require_once "inc/topbar.php"; ?>

      <main class="p-6 lg:p-10">
        <div class="mb-12 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black tracking-tight italic">Identity Console</h1>
                <p class="text-slate-500 font-medium italic mt-2 uppercase tracking-widest text-[11px]">Personal Attribute Management</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-2 bg-white rounded-2xl border border-slate-100 shadow-sm text-[10px] font-black uppercase tracking-widest text-slate-400 italic">
                    Node ID: #<?= str_pad($user_id, 4, "0", STR_PAD_LEFT) ?>
                </div>
            </div>
        </div>

        <?= $message ?>

        <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            <!-- Identity Preview Sidebar -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white rounded-[3rem] p-10 border border-slate-100 shadow-sm text-center group">
                    <div class="relative inline-block mb-8">
                        <div class="w-40 h-40 rounded-[3rem] overflow-hidden border-4 border-white shadow-2xl relative">
                            <img src="<?= $current_img ?>" id="preview" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                                <i class="fa-solid fa-camera text-white text-2xl"></i>
                            </div>
                        </div>
                        <label for="imageUpload" class="absolute -bottom-2 -right-2 w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center cursor-pointer hover:bg-slate-900 transition shadow-xl border-4 border-white">
                            <i class="fa-solid fa-plus"></i>
                        </label>
                        <input type="file" name="profile_image" id="imageUpload" class="hidden" accept="image/*" onchange="previewIdentity(this)">
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 italic"><?= htmlspecialchars($user['name']) ?></h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 mb-8 italic"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <div class="pt-8 border-t border-slate-50">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic">
                            Role: <?= strtoupper($user['role']) ?>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:rotate-12 transition duration-500">
                        <i class="fa-solid fa-shield-halved text-8xl"></i>
                    </div>
                    <h4 class="text-lg font-black mb-4 italic">Security Guard</h4>
                    <p class="text-[10px] font-bold text-blue-100 italic leading-relaxed">Ensure your credentials remain localized. Multi-factor synchronization is recommended for administrative accounts.</p>
                </div>
            </div>

            <!-- Attribute Configuration -->
            <div class="lg:col-span-3 space-y-8">
                <div class="bg-white rounded-[3rem] p-10 lg:p-14 border border-slate-100 shadow-sm">
                    <h3 class="text-2xl font-black mb-10 italic flex items-center gap-4">
                        <span class="w-1.5 h-10 bg-blue-600 rounded-full"></span>
                        Attribute Synchronization
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4 italic">Legal Identifier (Name)</label>
                            <div class="relative group">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition">
                                    <i class="fa-solid fa-users-gear"></i>
                                </div>
                                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                                    class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4 italic">Operational ID (Roll Number)</label>
                            <div class="relative group">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition">
                                    <i class="fa-solid fa-id-badge"></i>
                                </div>
                                <input type="text" name="roll_number" value="<?= htmlspecialchars($user['roll_number'] ?? '') ?>" placeholder="SF-2024-XXX"
                                    class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm">
                            </div>
                        </div>

                        <div class="space-y-3 md:col-span-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4 italic">Digital Frequency (Email Address)</label>
                            <div class="relative group">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition">
                                    <i class="fa-solid fa-at"></i>
                                </div>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                                    class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm">
                            </div>
                        </div>

                        <div class="md:col-span-2 pt-8 border-t border-slate-100 mt-4">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4 italic">Credential Cipher (New Password)</label>
                                <div class="relative group">
                                    <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition">
                                        <i class="fa-solid fa-key"></i>
                                    </div>
                                    <input type="password" name="password" placeholder="Leave vacant to maintain existing cipher pool..."
                                        class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-600 focus:bg-white transition shadow-sm">
                                </div>
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest ml-4 mt-2 italic">* Protocol enforces advanced encryption on storage.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12">
                        <button type="submit" name="update_profile" 
                            class="w-full bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-[11px] py-6 rounded-3xl hover:bg-blue-600 transition-all duration-700 shadow-2xl shadow-slate-200 group">
                            Synchronize Account Profile <i class="fa-solid fa-sync ml-2 group-hover:rotate-180 transition duration-700"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
      </main>
    </div>
  </div>

<script>
function previewIdentity(input) {
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<<<<<<< HEAD
=======
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9
</body>
</html>
