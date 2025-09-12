<?php
require_once dirname(__DIR__) . '/inc/db.php';
session_start();
// Check if OTP was verified
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true || !isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}
$email = $_SESSION['reset_email'];
$error = $success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($password) || empty($confirm_password)) {
        $error = "Please fill all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed_password, $email);
        
        if ($update->execute()) {
            // Clear session
            unset($_SESSION['otp_verified']);
            unset($_SESSION['reset_email']);
            
            // Delete OTP record
            $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete->bind_param("s", $email);
            $delete->execute();
            
            $success = "Password has been reset successfully. <a href='../login.php'>Login</a>";
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Custom styles for specific elements */
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
   <section>
     <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white dark:border-black border md:rounded-3xl 2xl:px-32 md:px-1 shadow-md overflow-hidden">
            <!-- Card Header -->
            <div class="py-4 shadow-sm border ">
                <h4 class="text-xl font-bold text-black text-center">Reset Password</h4>
            </div>
            
            <!-- Card Body -->
            <div class="p-6">
                <?php if($error): ?>
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                        <?php echo $success; ?>
                    </div>
                <?php else: ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 text-sm font-medium mb-2">New Password</label>
                            <input type="password" id="password" name="password" required
                                   class="w-full px-3 py-2 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                            <small class="text-gray-500 text-xs mt-1 block">Password must be at least 8 characters</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full px-3 py-2 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                            <small class="text-gray-500 text-xs mt-1 block">Re-enter your password</small>
                        </div>
                        
                        <div class="mb-4">
                            <button type="submit" class="text-base font-semibold 2xl:py-3 md:py-1 py-1 bg-white hover:bg-black text-black w-full hover:text-white border rounded-lg mt-5">
                                Reset Password
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="../login.php" class="text-gray-500 hover:text-black text-sm font-medium">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
   </section>
</body>
</html>