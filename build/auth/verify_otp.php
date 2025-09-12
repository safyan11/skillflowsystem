<?php
require_once dirname(__DIR__) . '/inc/db.php';
// Set timezone to match your location
date_default_timezone_set('Asia/Karachi'); // Change this to your timezone
// Set MySQL timezone to match PHP timezone
$conn->query("SET time_zone = '+05:00'"); // Change this to your timezone offset
$email = isset($_GET['email']) ? $_GET['email'] : '';
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
   <section>
     <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white dark:border-black border md:rounded-3xl 2xl:px-32 md:px-1 shadow-md overflow-hidden">
            <!-- Card Header -->
            <div class="py-4 shadow-sm border ">
                <h4 class="text-xl font-bold text-black text-center">Verify OTP</h4>
            </div>
            
            <!-- Card Body -->
            <div class="p-6">
                <?php if($error): ?>
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly
                               class="w-full px-3 py-2 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent bg-gray-100">
                        <small class="text-gray-500 text-xs mt-1 block">This email will receive the OTP</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="otp" class="block text-gray-700 text-sm font-medium mb-2">Enter OTP</label>
                        <input type="text" id="otp" name="otp" placeholder="Enter 6-digit OTP" required
                               class="w-full px-3 py-2 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                        <small class="text-gray-500 text-xs mt-1 block">Enter the OTP sent to your email</small>
                    </div>
                    
                    <div class="mb-4">
                        <button type="submit" class="text-base font-semibold 2xl:py-3 md:py-1 py-1 bg-white hover:bg-black text-black w-full hover:text-white border rounded-lg mt-5">
                            Verify OTP
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <a href="forgot_password.php" class="text-gray-500 hover:text-black text-sm font-medium">Back to Forgot Password</a>
                </div>
            </div>
        </div>
    </div>
   </section>
</body>
</html>