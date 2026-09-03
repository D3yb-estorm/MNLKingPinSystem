<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/mail.php';

// Get JSON data sent from JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Make sure the request contains required data
if (empty($data['customerEmail']) || empty($data['orderId'])) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'error' => 'Customer email or order ID is missing.'
    ]);

    exit;
}

$orderId = $data['orderId'] ?? 'N/A';
$customerEmail = $data['customerEmail'];
$customerName = $data['customerName'] ?? 'Customer';
$totalAmount = $data['totalAmount'] ?? 0;
$itemCount = $data['itemCount'] ?? 0;
$loginMethod = $data['loginMethod'] ?? 'unknown';

// Admin email
$adminEmail = 'davetormes25@gmail.com';

// Email subject
$subject = "New Purchase Order #$orderId from Gmail Customer";

// Email body
$body = "
<html>
<body style='font-family: Arial, sans-serif; color: #333;'>
    <h2 style='color: #d4af37;'>🎉 New Purchase Notification</h2>

    <p>Hi Admin,</p>

    <p>A new purchase has been made by a customer who logged in via Gmail!</p>

    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
        <tr style='background-color: #f5f5f5;'>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Order ID:</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>#$orderId</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Customer Name:</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>$customerName</td>
        </tr>
        <tr style='background-color: #f5f5f5;'>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Customer Email:</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>$customerEmail</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Login Method:</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>Gmail</td>
        </tr>
        <tr style='background-color: #f5f5f5;'>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Number of Items:</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>$itemCount</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Total Amount:</td>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #d4af37;'>₱" . number_format($totalAmount, 2) . "</td>
        </tr>
    </table>

    <p><strong>Action Required:</strong> Please log in to the KingPin Admin Panel to review and process this order.</p>

    <p>Thank you!</p>

    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
    <p style='font-size: 12px; color: #666;'>
        This is an automated email from KingPin System. Please do not reply to this email.
    </p>
</body>
</html>
";

// Send email
if (sendEmail($adminEmail, $subject, $body)) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Admin notification sent successfully'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to send email notification'
    ]);
}

?>
