<?php
require_once dirname(__DIR__) . '/inc/db.php';
// Set timezone to match your location
date_default_timezone_set('Asia/Karachi'); // Change this to your timezone
// Set MySQL timezone to match PHP timezone
$conn->query("SET time_zone = '+05:00'"); // Change this to your timezone offset
$email = isset($_GET['email']) ? $_GET['email'] : '';
$local_otp = isset($_GET['local_otp']) ? $_GET['local_otp'] : '';
$error = $success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = trim($_POST['otp']);
    $email = trim($_POST['email']);
    
    if (empty($otp)) {
        $error = "Please enter the OTP";
    } else {
        // Get current time in the same format as stored in database
        $current_time = date('Y-m-d H:i:s');
        
        // Check if OTP exists and is not expired
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE email = ? AND token = ? AND expires_at > ?");
        $stmt->bind_param("sss", $email, $otp, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // OTP is valid, set session and redirect to reset password
            session_start();
            $_SESSION['otp_verified'] = true;
            $_SESSION['reset_email'] = $email;
            header("Location: reset_password.php");
            exit();
        } else {
            $error = "Invalid or expired OTP";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Custom styles for specific elements */
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<body>
   <section class="bg-slate-50 relative before:fixed before:inset-0 before:-z-10 before:w-full before:h-full before:bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] before:from-blue-100 before:via-white before:to-emerald-50">
     <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white/70 backdrop-blur-xl border border-white/50 rounded-3xl shadow-xl overflow-hidden">
            <!-- Card Header -->
            <div class="py-6 border-b border-gray-100 bg-white/40">
                <h4 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500 text-center">Verify Identity</h4>
            </div>
            
            <!-- Card Body -->
            <div class="p-8">
                <?php if($error): ?>
                    <div class="p-4 mb-5 text-sm text-red-700 bg-red-50 border border-red-100 rounded-xl">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($local_otp)): ?>
                    <div class="p-4 mb-5 text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded-xl font-medium">
                        <i class="fa-solid fa-flask mr-2"></i><strong>Local Dev Mode:</strong> Your OTP is <code><?php echo htmlspecialchars($local_otp); ?></code>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly
                               class="w-full px-4 py-3 bg-gray-100/50 border border-gray-200 rounded-xl focus:outline-none text-gray-500 font-medium">
                    </div>
                    
                    <div class="mb-6">
                        <label for="otp" class="block text-gray-700 text-sm font-bold mb-2">Enter 6-Digit Code</label>
                        <input type="text" id="otp" name="otp" placeholder="XXXXXX" required
                               class="w-full px-4 py-3 bg-white/80 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-mono text-center tracking-widest text-xl placeholder-gray-300">
                    </div>
                    
                    <div class="mt-8">
                        <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 text-white font-bold text-lg hover:shadow-lg hover:scale-[1.02] transition-all flex justify-center items-center gap-2">
                            <span>Verify Code</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-6">
                    <a href="forgot_password.php" class="text-blue-600 hover:text-blue-800 text-sm font-bold transition-all"><span class="mr-1">&larr;</span> Change Email</a>
                </div>
            </div>
        </div>
    </div>
   </section>
</body>
</html>