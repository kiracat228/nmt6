<?php
// Return JSON for all responses
header('Content-Type: application/json');

function createTelegramMessage(array $data): string
{
    // Format message with all registration fields
    return "<i>#новазаявка</i>\n"
        . "<b>Нова заявка на реєстрацію</b>\n\n"
        . "<b>Ім'я:</b> {$data['firstName']}\n"
        . "<b>Прізвище:</b> {$data['lastName']}\n"
        . "<b>Клас:</b> {$data['class']}\n"
        . "<b>Рік навчання:</b> {$data['year']}\n"
        . "<b>Телефон:</b> {$data['phone']}\n"
        . "<b>Email:</b> {$data['email']}\n"
        . "<b>Курс:</b> {$data['course']}\n"
        . "<b>Формат занять:</b> {$data['format']}";
}

function sendTelegramMessage(array $data): array
{
    $token  = "8580129535:AAGT2aed8qWrzw37PHIuSRlFtr_QcVjRTTQ";
    $chatId = "-5159975255";

    if (!$token || !$chatId) {
        return ['success' => false, 'error' => 'Missing TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID'];
    }

    $url = "https://api.telegram.org/bot{$token}/sendMessage";

    $payload = [
        'chat_id'    => $chatId,
        'parse_mode' => 'html',
        'text'       => createTelegramMessage($data),
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $responseBody = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($responseBody === false) {
        return ['success' => false, 'error' => $curlError ?: 'Unknown cURL error'];
    }

    $responseJson = json_decode($responseBody, true);

    if ($httpCode >= 200 && $httpCode < 300) {
        return ['success' => true, 'response' => $responseJson];
    }

    return [
        'success' => false,
        'error' => $responseJson ?: $responseBody,
        'http_code' => $httpCode
    ];
}

// Read JSON body
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!is_array($body)) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON body']);
    exit;
}

// Basic server-side validation
$required = ['firstName', 'lastName', 'class', 'year', 'phone', 'email', 'course', 'format'];
foreach ($required as $field) {
    if (empty($body[$field])) {
        echo json_encode(['success' => false, 'error' => "Missing field: {$field}"]);
        exit;
    }
}

$result = sendTelegramMessage([
    'firstName' => $body['firstName'],
    'lastName' => $body['lastName'],
    'class' => $body['class'],
    'year' => $body['year'],
    'phone' => $body['phone'],
    'email' => $body['email'],
    'course' => $body['course'],
    'format' => $body['format'],
]);

http_response_code($result['success'] ? 200 : 500);
echo json_encode($result);