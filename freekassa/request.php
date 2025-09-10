<?
	session_start();
	define("moonstudio", true);
	include '../engine/config.php';
	//Проверка ip
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
    if (!in_array(GetIP(), array('168.119.157.136', '168.119.60.227', '138.201.88.124', '178.154.197.79'))) {
        die('Bad IP :D');
    }

	$merchant_id = '';
    $merchant_secret = '';
	$currency = 'RUB';

    $sign = md5($merchant_id.':'.$_REQUEST['AMOUNT'].':'.$merchant_secret.':'.$currency.':'.$_REQUEST['MERCHANT_ORDER_ID']);

    if($sign != $_REQUEST['SIGN']) {
		die('wrong sign');
    }

    $nametovar = $_REQUEST['MERCHANT_ORDER_ID'];

	$amount = $_REQUEST['AMOUNT'];
	$name = $_REQUEST["us_name"];
	$server = $_REQUEST['us_server'];
	$email = $_REQUEST['P_EMAIL'];

	$category = $_REQUEST['us_category'];

	$sql = "INSERT INTO `last_buys`(`name`,`tovarname`,`price`,`server`,`category`,`Email`,`status`) VALUES ('{$name}', '{$nametovar}', '{$amount}', '{$server}', '{$category}', '{$email}', '0')";
	$mysqli->query($sql);

	if($config['vksecret'] != 0) {
		SendVkMessage($config['vkid'], 'Игрок '.$name.' купил товар '.$nametovar.', за: '.$amount.' руб.');
	}

    die($server);
?>
