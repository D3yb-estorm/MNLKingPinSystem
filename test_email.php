<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {

    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    $mail->Username   = getenv('SMTP_USER');
    $mail->Password   = getenv('SMTP_PASS');

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int) (getenv('SMTP_PORT') ?: 587);

    // Sender
    $mail->setFrom(
        getenv('SMTP_FROM') ?: getenv('SMTP_USER'),
        'KingPin System'
    );

    // Recipient
    $mail->addAddress(getenv('SMTP_USER'));

    // Email
    $mail->isHTML(true);
    $mail->Subject = 'KingPin Test Email';

    $mail->Body = '
        <h2>KingPin System</h2>
        <p>This is a test email.</p>
        <p>PHP and PHPMailer are working!</p>
    ';

    // Send
    $mail->send();

    echo 'EMAIL SENT SUCCESSFULLY!';

} catch (Exception $e) {

    echo 'EMAIL FAILED: ' . $mail->ErrorInfo;

}