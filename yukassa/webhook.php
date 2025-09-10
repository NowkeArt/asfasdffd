<?php
session_start();
define("moonstudio", true);
include '../engine/config.php';

// Проверка IP адресов ЮKassa
function GetIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// IP адреса ЮKassa для webhook'ов
$allowed_ips = array(
    '185.71.76.0/27',
    '185.71.77.0/27',
    '77.75.153.0/25',
    '77.75.156.11',
    '77.75.156.35',
    '2a02:5180::/32'
);

// Функция проверки IP в подсети
function ip_in_range($ip, $range) {
    if (strpos($range, '/') == false) {
        return $ip == $range;
    }
    
    list($range, $netmask) = explode('/', $range, 2);
    $range_decimal = ip2long($range);
    $ip_decimal = ip2long($ip);
    $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
    $netmask_decimal = ~ $wildcard_decimal;
    
    return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
}

$client_ip = GetIP();
$ip_allowed = false;

foreach ($allowed_ips as $allowed_ip) {
    if (ip_in_range($client_ip, $allowed_ip)) {
        $ip_allowed = true;
        break;
    }
}

if (!$ip_allowed) {
    http_response_code(403);
    die('Access denied');
}

// Получаем данные webhook'а
$input = file_get_contents('php://input');
$notification = json_decode($input, true);

if (!$notification) {
    http_response_code(400);
    die('Invalid JSON');
}

// Проверяем тип события
if ($notification['event'] !== 'payment.succeeded') {
    http_response_code(200);
    die('OK');
}

$payment = $notification['object'];

// Извлекаем данные из метаданных
$metadata = $payment['metadata'];
$player = $metadata['player'];
$server = $metadata['server'];
$category = $metadata['category'];
$item_code = $metadata['item_code'];
$item_name = $metadata['item_name'];
$amount = $payment['amount']['value'];

// Сохраняем покупку в базу данных
$sql = "INSERT INTO `last_buys`(`name`,`tovarname`,`tovar`,`price`,`server`,`category`,`Email`,`status`) 
        VALUES ('{$player}', '{$item_name}', '{$item_code}', '{$amount}', '{$server}', '{$category}', '', '1')";
$mysqli->query($sql);

// Отправляем уведомление в VK (если настроено)
if($config['vksecret'] != 0) {
    SendVkMessage($config['vkid'], 'Игрок '.$player.' купил товар '.$item_name.', за: '.$amount.' руб.');
}

http_response_code(200);
echo 'OK';
?>