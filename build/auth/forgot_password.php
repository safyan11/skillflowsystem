<?php
require_once dirname(__DIR__) . '/inc/db.php';
require_once dirname(__DIR__) . '/inc/mailer.php';

// Set timezone to match your location
date_default_timezone_set('Asia/Karachi'); // Change this to your timezone

$email = '';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            
            // Set expiration time (2 minutes from now)
            $expiry = date('Y-m-d H:i:s', strtotime('+2 minutes'));
            
            // Delete any existing OTPs for this email
            $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete->bind_param("s", $email);
            $delete->execute();
            
            // Store OTP in database
            $insert = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $email, $otp, $expiry);
            $insert->execute();
            
            // Try to send email
            $subject = "Your OTP for Password Reset";
            $message = "Your OTP is: <b>$otp</b><br><br>This OTP will expire in 2 minutes.<br><br>If you did not request this, please ignore this email.";
            
            if (sendEmail($email, $subject, $message)) {
                $success = "OTP has been sent to your email address.";
            } else {
                // In production, we typically don't want to show errors, 
                // but for now, we'll notify that system mailing failed.
                $error = "Failed to send OTP. Please check your SMTP settings.";
            }
            
<<<<<<< HEAD
            // Redirect to OTP verification page
            header("Location: verify_otp.php?email=" . urlencode($email));
=======
            // Redirect to OTP verification page regardless of mail success for Localhost testing.
            // On localhost without sendmail configured, this ensures the developer can still proceed.
            $redirect_url = "Location: verify_otp.php?email=" . urlencode($email);
            if (!$mail_sent) {
                // Pass OTP purely for local testing if mail fails
                $redirect_url .= "&local_otp=" . $otp;
            }
            
            header($redirect_url);
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9
            exit();
        } else {
            $error = "Email address not found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2ada4947c.js" crossorigin="anonymous"></script>
</head>
<<<<<<< HEAD
<body class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 font-sans">
    <!-- Immersive Background with Soft Gradients -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-blue-600/30 rounded-full mix-blend-screen filter blur-[120px] animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-indigo-600/30 rounded-full mix-blend-screen filter blur-[120px]" style="animation-duration: 10s; animation-direction: reverse;"></div>
        <img src="../assets/img/modern-bg.jpg" class="w-full h-full object-cover opacity-20" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80';">
    </div>

    <!-- Blur Overlay -->
    <div class="absolute inset-0 z-0 backdrop-blur-[2px] bg-slate-950/60"></div>

    <style>
        @keyframes slideInFade {
            0% { opacity: 0; transform: scale(0.95) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-card { animation: slideInFade 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .input-glow:focus-within {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
            border-color: rgba(59, 130, 246, 0.5);
        }
        @keyframes shimmer { 100% { transform: translateX(100%); } }
    </style>

    <section class="relative z-10 w-full min-h-screen px-4 py-10 flex items-center justify-center">
        <!-- Glassmorphism Card -->
        <div class="w-full max-w-md mx-auto rounded-3xl shadow-2xl border border-white/10 bg-slate-900/60 backdrop-blur-2xl animate-card p-8 sm:p-10 opacity-0 relative z-10 overflow-hidden">
            
            <div class="text-center mb-8 flex flex-col items-center">
                <div class="h-16 w-16 mb-4 rounded-2xl shadow-lg border border-white/10 bg-slate-800/80 p-3 flex items-center justify-center">
                    <i class="fa-solid fa-key text-2xl text-blue-400"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Password Recovery</h2>
            </div>
            
            <?php if($error): ?>
                <div class='bg-red-500/10 border border-red-500/40 text-red-400 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 mb-6 animate-pulse'>
                    <i class='fa-solid fa-triangle-exclamation'></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class='bg-emerald-500/10 border border-emerald-500/40 text-emerald-400 p-4 rounded-xl text-sm font-bold flex items-center justify-center gap-3 mb-6'>
                    <i class='fa-solid fa-circle-check'></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="space-y-5">
                    <!-- Email Field -->
                    <div class="input-glow rounded-xl border border-white/10 bg-slate-800/50 transition-all duration-300 relative group overflow-hidden">
                        <label class="block mb-1 text-xs font-bold text-slate-300 px-4 pt-2">Registered Email Address</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-4 text-slate-400 group-focus-within:text-blue-400 transition-colors">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full pl-11 pr-5 pb-3 pt-1 bg-transparent text-white focus:outline-none placeholder-slate-500 font-medium" placeholder="your@email.com">
                        </div>
                    </div>
                    <small class="text-slate-400 text-xs mt-1 block font-medium px-1">We'll dispatch a 6-digit verification code to this address.</small>
                    
                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent rounded-xl text-lg font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none shadow-[0_0_15px_rgba(37,99,235,0.4)] hover:shadow-[0_0_25px_rgba(37,99,235,0.6)] hover:-translate-y-[2px] active:translate-y-0 transition-all duration-300 overflow-hidden">
                            <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                            <div class="flex items-center gap-3 relative z-10">
                                <span>Send Recovery Code</span>
                                <i class="fa-solid fa-paper-plane group-hover:translate-x-1.5 group-hover:-translate-y-1.5 transition-transform duration-300"></i>
                            </div>
                        </button>
                    </div>
=======
<body>
   <section class="bg-slate-50 relative before:fixed before:inset-0 before:-z-10 before:w-full before:h-full before:bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] before:from-blue-100 before:via-white before:to-emerald-50">
     <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white/70 backdrop-blur-xl border border-white/50 rounded-3xl shadow-xl overflow-hidden">
            <!-- Card Header -->
            <div class="py-6 border-b border-gray-100 bg-white/40">
                <h4 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500 text-center">Password Recovery</h4>
            </div>
            
            <!-- Card Body -->
            <div class="p-8">
                <?php if($error): ?>
                    <div class="p-4 mb-5 text-sm text-red-700 bg-red-50 border border-red-100 rounded-xl">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="p-4 mb-5 text-sm text-green-700 bg-emerald-50 border border-emerald-100 rounded-xl">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Registered Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required
                               class="w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all placeholder-gray-400" placeholder="your@email.com">
                        <small class="text-gray-500 text-xs mt-2 block font-medium">We'll dispatch a 6-digit verification code to this address.</small>
                    </div>
                    <div class="mt-8">
                        <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 text-white font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex justify-center items-center gap-2">
                            <span>Send Recovery Code</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-6">
                    <a href="../login.php" class="text-blue-600 hover:text-blue-800 text-sm font-bold transition-all"><span class="mr-1">&larr;</span> Return to securely login</a>
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9
                </div>
            </form>
            
            <div class="text-center mt-6">
                <a href="../login.php" class="text-blue-400 hover:text-blue-300 text-sm font-bold transition-all underline-offset-4 hover:underline">
                    <span class="mr-1">&larr;</span> Return to securely login
                </a>
            </div>
        </div>
    </section>
</body>
</html>