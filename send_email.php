<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form data
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = filter_var(trim($_POST["subject"]), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);

    // Validate data
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please fill in all fields correctly.";
        exit;
    }

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings for Gmail SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'omethhettiarachchi@gmail.com'; // Your Gmail address
        $mail->Password = '123@Ometh'; // Google App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('omethhettiarachchi@gmail.com'); // Tecroot's email
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission: $subject";
        
        // HTML email body
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #32CD32; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; }
                .footer { text-align: center; padding: 10px; font-size: 0.8em; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Tecroot Contact Form Submission</h2>
                </div>
                <div class='content'>
                    <p><strong>From:</strong> $name &lt;$email&gt;</p>
                    <p><strong>Subject:</strong> $subject</p>
                    <hr>
                    <p>$message</p>
                </div>
                <div class='footer'>
                    <p>This email was sent from the Tecroot website contact form</p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Plain text version for non-HTML mail clients
        $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";

        // Send email
        $mail->send();
        
        // Success response
        echo "Thank you! Your message has been sent successfully.";
    } catch (Exception $e) {
        // Error response
        http_response_code(500);
        echo "Message could not be sent. Error: {$mail->ErrorInfo}";
    }
} else {
    // Not a POST request
    http_response_code(403);
    echo "There was a problem with your submission. Please try again.";
}
?>