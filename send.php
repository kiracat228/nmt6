<?php
$to = "kudenkokira2006@gmail.com";
$subject = "Нова реєстрація на курси NMT";

$message =
"Ім'я: " . $_POST['firstName'] . "\n" .
"Прізвище: " . $_POST['lastName'] . "\n" .
"Клас: " . $_POST['class'] . "\n" .
"Рік навчання: " . $_POST['year'] . "\n" .
"Телефон: " . $_POST['phone'] . "\n" .
"Email: " . $_POST['email'] . "\n" .
"Курс: " . $_POST['course'] . "\n" .
"Формат: " . $_POST['format'];

$headers = "From: site@nmt.com";

mail($to, $subject, $message, $headers);

header("Location: thankyou.html");
?>
