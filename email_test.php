<?php

require_once __DIR__ . '/includes/mail.php';

$success = sendEmail(
    'davetormes25@gmail.com',
    'KingPin System Test',
    '
    <h2>KingPin System</h2>
    <p>This is a test of the reusable email function.</p>
    <p>If you received this message, the email system is working.</p>
    '
);

if ($success) {
    echo 'EMAIL SENT SUCCESSFULLY!';
} else {
    echo 'EMAIL FAILED!';
}