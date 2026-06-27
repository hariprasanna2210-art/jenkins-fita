<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /contact");
    exit;
}

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$email   = trim($_POST['email'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

/* Required fields */
if (empty($name) || empty($phone)) {
    die("Name and Phone are required.");
}

/* Email validation */
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}

/* Clean input */
$name    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$phone   = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
$email   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$service = htmlspecialchars($service, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

$mail = new PHPMailer(true);

try {

    /* SMTP Settings */
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    /* Gmail Login */
    $mail->Username   = 'thekaraimarina@gmail.com';
    $mail->Password   = 'uukw qhlp hisa zavl';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    /* Sender and Receiver */
    $mail->setFrom('thekaraimarina@gmail.com', 'Karai Marina Website');
    $mail->addAddress('thekaraimarina@gmail.com');

    /* Reply directly to customer */
    if (!empty($email)) {
        $mail->addReplyTo($email, $name);
    }

    /* Email Content */
    $mail->isHTML(false);
    $mail->CharSet = 'UTF-8';

    $mail->Subject = 'New Enquiry - Karai Marina';

    $mail->Body = "
New enquiry received from Karai Marina Website

Name: $name
Phone: $phone
Email: $email
Service: $service

Message:
$message

--------------------------------
Sent From: https://karaimarina.com
Date: " . date('d-m-Y h:i:s A');

    $mail->send();

    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Success</title>
        <meta http-equiv='refresh' content='3;url=/contact'>
    </head>
    <body style='font-family:Arial;text-align:center;padding:50px'>
        <h2>✅ Enquiry Sent Successfully</h2>
        <p>Thank you for contacting Karai Marina.</p>
        <p>We will contact you soon.</p>
    </body>
    </html>";

} catch (Exception $e) {

    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
    </head>
    <body style='font-family:Arial;text-align:center;padding:50px'>
        <h2>❌ Failed to Send Email</h2>
        <p>Please try again later.</p>
        <p style='color:red;font-size:14px'>Mailer Error: " . $mail->ErrorInfo . "</p>
    </body>
    </html>";
}
?>