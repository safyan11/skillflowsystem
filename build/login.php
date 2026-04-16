<?php
error_reporting(0);
session_start();

require_once "inc/db.php";

require_once "inc/header.php";
require_once "inc/nav.php";

$loginError = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]);
    if (empty($email) || empty($password) || empty($role)) {
        $loginError = "Please fill all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Check if account is approved
            if ($user['verify_status'] !== 'approved') {
                $loginError = "Your account is not approved yet.";
            }
            // Check password
            elseif (!password_verify($password, $user['password'])) {
                $loginError = "Invalid password.";
            }
            // Success
            else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                // Redirect based on role
                if ($role === 'admin') {
                    header("Location: admin/admindashboard.php");
                } elseif ($role === 'teacher') {
                    header("Location: teacher/teacherdashboard.php");
                } else {
                    header("Location: student/studentdashboard.php");
                }
                exit();
            }
        } else {
            $loginError = "No user found with provided credentials.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<body class="bg-black relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Immersive Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-600/40 rounded-full mix-blend-screen filter blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-emerald-500/40 rounded-full mix-blend-screen filter blur-[100px]"></div>
        <img src="./assets/img/modern-bg.jpg" class="w-full h-full object-cover opacity-30" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80';">
    </div>

    <!-- Blur Overlay -->
    <div class="absolute inset-0 z-0 backdrop-blur-xl bg-black/20"></div>

    <style>
        @keyframes modalPop {
            0% { opacity: 0; transform: scale(0.9) translateY(30px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-modal {
            animation: modalPop 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    <section class="relative z-10 w-full px-4 sm:px-6 lg:px-8 py-12 flex justify-center items-center">
        <!-- Centered Glass Modal -->
        <div class="glass-card w-full max-w-lg rounded-[2rem] shadow-[0_0_50px_rgba(0,0,0,0.5)] overflow-hidden border border-white/20 bg-black/40 backdrop-blur-[40px] animate-modal p-8 sm:p-12">
            
            <form action="" method="POST">
                <!-- Logo Header -->
                <div class="text-center mb-10 flex flex-col items-center">
                    <img src="./assets/img/teachmate_logo.png" alt="TeachMate" class="h-16 w-16 mb-4 rounded-xl shadow-lg border border-white/10">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Welcome Back</h2>
                    <p class="text-sm font-medium text-gray-300 mt-2">Don't have an account? <a href="signup.php" class="text-emerald-400 font-bold hover:text-emerald-300 transition underline-offset-2 hover:underline">Create one</a></p>
                </div>
                
                <?php if (!empty($loginError)): ?>
                    <div class="bg-red-500/20 border border-red-500/50 text-red-100 p-4 rounded-xl text-sm font-bold flex items-center gap-3 backdrop-blur-md mb-6 shadow-inner">
                        <i class="fa-solid fa-circle-exclamation text-lg text-red-400"></i>
                        <?= $loginError ?>
                    </div>
                <?php endif; ?>
                
                <div class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-200">Email Address</label>
                        <input name="email" class="w-full px-5 py-4 rounded-xl border border-white/10 bg-white/10 text-white focus:bg-white/20 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all outline-none placeholder-gray-400 backdrop-blur-md shadow-inner" placeholder="user@example.com" type="email" required>
                    </div>
                    
                    <div x-data="{ show: false }">
                        <label class="block mb-2 text-sm font-bold text-gray-200">Password</label>
                        <div class="relative">
                            <input name="password" :type="show ? 'text' : 'password'" class="w-full px-5 py-4 rounded-xl border border-white/10 bg-white/10 text-white focus:bg-white/20 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all outline-none placeholder-gray-400 pr-12 backdrop-blur-md shadow-inner" placeholder="••••••••" required>
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center px-5 text-gray-300 hover:text-white transition-colors">
                                <i :class="show ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-200">User Role</label>
                        <select name="role" class="w-full px-5 py-4 rounded-xl border border-white/10 bg-white/10 text-white focus:bg-white/20 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all outline-none cursor-pointer backdrop-blur-md shadow-inner [&>option]:bg-gray-900" required>
                            <option value="">Select your role</option>
                            <option value="admin">Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="auth/forgot_password.php" class="text-sm font-semibold text-emerald-400 hover:text-emerald-300 transition underline-offset-2 hover:underline drop-shadow-md">Forgot Password?</a>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-blue-600 text-white font-bold text-lg shadow-[0_0_20px_rgba(52,211,153,0.3)] hover:shadow-[0_0_30px_rgba(52,211,153,0.5)] hover:-translate-y-1 transition-all duration-300 flex justify-center items-center gap-2">
                            <span>Sign In</span>
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>
