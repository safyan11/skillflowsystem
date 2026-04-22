<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'safimughal.com@gmail.com'; // Original sender email
        $mail->Password   = 'mwsu drsf dowt sxgr'; // Keep existing for now, user may need to update
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
<<<<<<< HEAD
        $mail->setFrom('safimughal.com@gmail.com', 'TeachMate LMS');
=======
        $mail->setFrom('your_email@gmail.com', 'TeachMate System');
>>>>>>> b9fc0b0caa5737cb92934e15d7778649bf2a89a9
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
