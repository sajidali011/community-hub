<?php
function sendOtp($email, $otp) {
    // Use PHPMailer or any email service to send the OTP
    $subject = "Your OTP for Password Change";
    $message = "Your OTP is: " . $otp;
    $headers = "From: no-reply@example.com";
    mail($email, $subject, $message, $headers); // Simple mail function, consider PHPMailer for better handling
}
?>
