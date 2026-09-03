<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/mail.php';

// Get JSON data sent from JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Make sure the request contains an email
if (empty($data['customerEmail'])) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'error' => 'Customer email is missing.'
    ]);

    exit;
}

$orderId = $data['orderId'] ?? 'N/A';
$customerEmail = $data['customerEmail'];
$status = $data['status'] ?? 'updated';
$message = $data['message'] ?? 'Your order status has been updated.';

// Convert status into a nicer display name
$statusDisplay = ucwords(str_replace('-', ' ', $status));

// Email subject
$subject = "KingPin Order #{$orderId} Status Update";

// Email body
$body = "
<html>
<body>
    <h2>KingPin Order Update</h2>

    <p>Hello,</p>

    <p>Your order <strong>#{$orderId}</strong> has been updated.</p>

    <p>
        <strong>New Status:</strong>
        {$statusDisplay}
    </p>

    <p>{$message}</p>

    <p>Thank you for choosing KingPin!</p>

    <p>
        <strong>MNL KingPin Custom Jersey Shop</strong>
    </p>
</body>
</html>
";

// Send email using your existing mail function
$result = sendEmail($customerEmail, $subject, $body);

if ($result) {

    echo json_encode([
        'success' => true,
        'message' => 'Order status email sent successfully.'
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => 'Failed to send order status email.'
    ]);
}