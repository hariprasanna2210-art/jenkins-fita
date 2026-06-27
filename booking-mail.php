<?php
/* Karai Marina website enquiry mail handler.
   Upload this file on PHP hosting. It uses PHP mail(). If your hosting disables mail(), enable SMTP in cPanel or replace mail() with PHPMailer SMTP. */

mb_internal_encoding('UTF-8');

function km_clean($value) {
    $value = is_string($value) ? $value : '';
    $value = trim($value);
    $value = str_replace(["\r", "\n"], ' ', $value);
    return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
}

function km_long_clean($value) {
    $value = is_string($value) ? $value : '';
    $value = trim($value);
    return htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /contact');
    exit;
}

// Honeypot spam protection
if (!empty($_POST['website'] ?? '')) {
    header('Location: /thank-you');
    exit;
}

$to = 'thekaraimarina@gmail.com';
$site_name = 'Karai Marina';
$domain = $_SERVER['SERVER_NAME'] ?? 'karaimarina.com';

$name = km_clean($_POST['name'] ?? '');
$phone = km_clean($_POST['phone'] ?? '');
$email = km_clean($_POST['email'] ?? '');
$service = km_clean($_POST['service'] ?? ($_POST['booking_type'] ?? 'Booking Enquiry'));
$event_date = km_clean($_POST['event_date'] ?? '');
$guest_count = km_clean($_POST['guest_count'] ?? '');
$source_page = km_clean($_POST['source_page'] ?? 'Website');
$message = km_long_clean($_POST['message'] ?? '');

if ($name === '' || $phone === '') {
    http_response_code(400);
    echo 'Please fill required fields: Name and Phone.';
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Please enter a valid email address.';
    exit;
}

$subject = 'New Karai Marina Booking Enquiry - ' . $service;
$body = "New enquiry received from Karai Marina website\n\n" .
        "Name: {$name}\n" .
        "Phone / WhatsApp: {$phone}\n" .
        "Email: {$email}\n" .
        "Service / Booking Type: {$service}\n" .
        "Event Date: {$event_date}\n" .
        "Guest Count: {$guest_count}\n" .
        "Source Page: {$source_page}\n" .
        "Website: https://karaimarina.com\n\n" .
        "Message:\n{$message}\n";

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'From: Karai Marina Website <no-reply@' . $domain . '>';
if ($email !== '') {
    $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
}
$headers[] = 'X-Mailer: PHP/' . phpversion();

$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

// Save a local backup log if PHP has permission. Useful when hosting mail is blocked.
$log_line = date('Y-m-d H:i:s') . " | " . ($sent ? 'SENT' : 'FAILED') . " | {$name} | {$phone} | {$service}\n";
@file_put_contents(__DIR__ . '/enquiry-log.txt', $log_line, FILE_APPEND | LOCK_EX);

if ($sent) {
    header('Location: /thank-you');
    exit;
}

http_response_code(500);
echo 'Mail sending failed. Please enable PHP mail or SMTP on hosting. Enquiry was logged in enquiry-log.txt if file permission is available.';
?>
