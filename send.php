<?php
header('Content-Type: application/json');

function createTelegramMessage(array $data): string
{
    return "<i>#новазаявка</i>\n"
        . "<b>Нова заявка на реєстрацію</b>\n\n"
        . "<b>Ім'я:</b> " . htmlspecialchars($data['firstName']) . "\n"
        . "<b>Прізвище:</b> " . htmlspecialchars($data['lastName']) . "\n"
        . "<b>Клас:</b> " . htmlspecialchars($data['class']) . "\n"
        . "<b>Рік навчання:</b> " . htmlspecialchars($data['year']) . "\n"
        . "<b>Телефон:</b> " . htmlspecialchars($data['phone']) . "\n"
        . "<b>Email:</b> " . htmlspecialchars($data['email']) . "\n"
        . "<b>Курс:</b> " . htmlspecialchars($data['course']) . "\n"
        . "<b>Формат занять:</b> " . htmlspecialchars($data['format']);
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!$body) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

$token  = "8580129535:AAGT2aed8qWrzw37PHIuSRlFtr_QcVjRTTQ";
$chatId = "-5159975255";
$url = "https://api.telegram.org/bot{$token}/sendMessage";

$payload = [
    'chat_id'    => $chatId,
    'parse_mode' => 'html',
    'text'       => createTelegramMessage($body),
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Telegram API Error', 'details' => json_decode($response)]);
}
?>


