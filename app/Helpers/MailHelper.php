<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendPasswordResetEmail($toEmail, $resetLink) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(SMTP_USER, SITENAME . ' Support');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - ' . SITENAME;
        
        $htmlBody = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                    background-color: #f3f4f6;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-w-md: 600px;
                    margin: 40px auto;
                    background-color: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    padding: 40px;
                }
                .logo {
                    font-size: 28px;
                    font-weight: bold;
                    color: #10B981;
                    text-decoration: none;
                    margin-bottom: 20px;
                    display: inline-block;
                }
                h2 {
                    color: #1f2937;
                    margin-bottom: 10px;
                }
                p {
                    color: #4b5563;
                    line-height: 1.6;
                    margin-bottom: 25px;
                }
                .btn {
                    display: inline-block;
                    background-color: #10B981;
                    color: #ffffff;
                    padding: 12px 24px;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-bottom: 25px;
                }
                .btn:hover {
                    background-color: #059669;
                }
                .footer {
                    font-size: 12px;
                    color: #9ca3af;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <a href='#' class='logo'>🌿 EcoPath</a>
                <h2>Password Reset Request</h2>
                <p>Hello,</p>
                <p>We received a request to reset the password for your EcoPath account. Click the button below to choose a new password. <strong>This link will expire in 5 minutes.</strong></p>
                <a href='{$resetLink}' class='btn'>Reset Password</a>
                <p>If you did not request a password reset, please ignore this email. Your password will remain unchanged.</p>
                <div class='footer'>
                    &copy; " . date('Y') . " EcoPath. All rights reserved.
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->Body = $htmlBody;
        $mail->AltBody = "Hello,\n\nWe received a request to reset your EcoPath password. Please click the following link to reset your password (expires in 5 minutes):\n\n{$resetLink}\n\nIf you did not request this, please ignore this email.";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
