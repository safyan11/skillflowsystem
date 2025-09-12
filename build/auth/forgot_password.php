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
            
            // For testing, show the OTP even if email fails
            if (!$mail_sent) {
                $error .= "<br><strong>For testing purposes, your OTP is: $otp</strong>";
            }
            
            // Redirect to OTP verification page only if email was sent
            if ($mail_sent) {
                header("Location: verify_otp.php?email=" . urlencode($email));
                exit();
            }
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
   <section>
     <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white dark:border-black border md:rounded-3xl 2xl:px-32 md:px-1 shadow-md overflow-hidden">
            <!-- Card Header -->
            <div class="py-4 shadow-sm border ">
                <h4 class="text-xl font-bold text-black text-center">Forgot Password</h4>
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
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required
                               class="w-full px-3 py-2 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                        <small class="text-gray-500 text-xs mt-1 block">We'll send an OTP to this email</small>
                    </div>
                    <div class="mb-4">
                        <button type="submit" class="text-base font-semibold 2xl:py-3 md:py-1 py-1 bg-white hover:bg-black text-black w-full hover:text-white border rounded-lg mt-5">
                            Send OTP
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <a href="../login.php" class="text-gray-500 hover:text-black text-sm font-medium">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
   </section>
</body>
</html>