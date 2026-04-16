<?php
require_once dirname(__DIR__) . '/inc/db.php';

// Set timezone to match your location
date_default_timezone_set('Asia/Karachi'); // Change this to your timezone

$email = $error = $success = '';

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
            
            // Set expiration time (15 minutes from now)
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            // Delete any existing OTPs for this email
            $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete->bind_param("s", $email);
            $delete->execute();
            
            // Store OTP in database
            $insert = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $email, $otp, $expiry);
            $insert->execute();
            
            // Try to send email
            $to = $email;
            $subject = "Your OTP for Password Reset";
            $message = "Your OTP is: $otp\n\nThis OTP will expire in 15 minutes.\n\nIf you did not request this, please ignore this email.";
            $headers = "From: safimughal.com@gmail.com" . "\r\n" .
                       "Reply-To: safimughal.com@gmail.com" . "\r\n" .
                       "X-Mailer: PHP/" . phpversion();
            
            // Try to send email
            if (mail($to, $subject, $message, $headers)) {
                $success = "OTP has been sent to your email address.";
                $mail_sent = true;
            } else {
                $error = "Failed to send OTP to your email. Please try again.";
                $mail_sent = false;
            }
            
            // Redirect to OTP verification page regardless of mail success for Localhost testing.
            // On localhost without sendmail configured, this ensures the developer can still proceed.
            $redirect_url = "Location: verify_otp.php?email=" . urlencode($email);
            if (!$mail_sent) {
                // Pass OTP purely for local testing if mail fails
                $redirect_url .= "&local_otp=" . $otp;
            }
            
            header($redirect_url);
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
    
    <style>
        /* Custom styles for specific elements */
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
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
                </div>
            </div>
        </div>
    </div>
   </section>
</body>
</html>