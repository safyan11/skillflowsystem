<?php include("inc/header.php"); ?>
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
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Create Account</h2>
                <p class="text-sm font-medium text-gray-300 mt-2">Already have an account? <a href="./login.php" class="text-emerald-400 font-bold hover:text-emerald-300 transition underline-offset-2 hover:underline drop-shadow-md">Login here</a></p>
            </div>
                    
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = trim($_POST["name"]);
                $email = trim($_POST["email"]);
                $password = trim($_POST["password"]);
                $confirm_password = trim($_POST["confirm_password"]);
                
                if ($password !== $confirm_password) {
                    echo "<div class='bg-red-500/20 border border-red-500/50 text-red-100 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 backdrop-blur-md mb-6 shadow-inner'>Passwords do not match!</div>";
                } else {
                    // Check if email already exists
                    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $check_email->bind_param("s", $email);
                    $check_email->execute();
                    $result = $check_email->get_result();
                    
                    if ($result->num_rows > 0) {
                        echo "<div class='bg-red-500/20 border border-red-500/50 text-red-100 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 backdrop-blur-md mb-6 shadow-inner'>Email already registered.</div>";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO users (name, email, password, role, verify_status) VALUES (?, ?, ?, 'student', 'approved')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $name, $email, $hashed_password);
                        
                        if ($stmt->execute()) {
                            echo "<div class='bg-emerald-500/20 border border-emerald-500/50 text-emerald-100 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 backdrop-blur-md mb-6 shadow-inner'>Account created successfully! <a href='./login.php' class='underline hover:text-white'>Log in</a></div>";
                        } else {
                            echo "<div class='bg-red-500/20 border border-red-500/50 text-red-100 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 backdrop-blur-md mb-6 shadow-inner'>Error creating account. Please try again.</div>";
                        }
                        $stmt->close();
                    }
                    $check_email->close();
                }
            }
            ?>
                    
            <div class="space-y-6">
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-200">Full Name</label>
                    <input name="name" required class="w-full px-5 py-4 rounded-xl border border-white/10 bg-white/10 text-white focus:bg-white/20 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all outline-none placeholder-gray-400 backdrop-blur-md shadow-inner" placeholder="John Doe" type="text">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-200">Email Address</label>
                    <input name="email" required type="email" class="w-full px-5 py-4 rounded-xl border border-white/10 bg-white/10 text-white focus:bg-white/20 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all outline-none placeholder-gray-400 backdrop-blur-md shadow-inner" placeholder="john@example.com">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-200">Password</label>
                        <input name="password" required type="password" class="w-full px-5 py-4 rounded-xl border border-white/10 bg-white/10 text-white focus:bg-white/20 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all outline-none placeholder-gray-400 backdrop-blur-md shadow-inner" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-200">Confirm</label>
                        <input name="confirm_password" required type="password" class="w-full px-5 py-4 rounded-xl border border-white/10 bg-white/10 text-white focus:bg-white/20 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all outline-none placeholder-gray-400 backdrop-blur-md shadow-inner" placeholder="••••••••">
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-blue-600 text-white font-bold text-lg shadow-[0_0_20px_rgba(52,211,153,0.3)] hover:shadow-[0_0_30px_rgba(52,211,153,0.5)] hover:-translate-y-1 transition-all duration-300 flex justify-center items-center gap-2">
                        <span>Create Account</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
<script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</body>
</html>