<?php
    session_start();
    define("moonstudio", true);
    include '../engine/config.php';


    $server = $mysqli->real_escape_string(stripslashes(htmlspecialchars(trim($_POST['server']))));
    $name = $mysqli->real_escape_string(stripslashes(htmlspecialchars(trim($_POST['name']))));
    $tovar = $mysqli->real_escape_string(stripslashes(htmlspecialchars(trim($_POST['tovar']))));
    $promo = $mysqli->real_escape_string(stripslashes(htmlspecialchars(trim($_POST['promo']))));


    $sql = "SELECT * FROM `servers` WHERE `name` = '{$server}'";
    $result = $mysqli->query($sql);
    $rows = $result->num_rows;

    if($rows != 1)
    {
        echo message('Ошибка', 'Выберите сервер', 'error');
        return false;
    }
    if($rows == 1)
    {
        $result->data_seek(0);
        $servers = $result->fetch_assoc();
        $server = $servers['code'];
    }
    if(empty($name))
    {
        echo message('Ошибка', 'Игровой ник не указан', 'error');
        return false;
    }


    if(!empty($promo))
    {
        $sql = "SELECT * FROM `promo` WHERE `name` = '{$promo}'";
        $result = $mysqli->query($sql);

        $rows = $result->num_rows;

        if($rows == 1)
        {
            $result->data_seek(0);
            $promo = $result->fetch_assoc();


            $sql = "SELECT * FROM `tovari` WHERE `id` = '{$tovar}'";
            $result = $mysqli->query($sql);

            $rows = $result->num_rows;

            if($rows == 1)
            {
                $result->data_seek(0);
                $tovar = $result->fetch_assoc();



                if($promo['for_tovar'] != 'all') {
                    $epromo = explode(':', $promo['for_tovar']);

                    if($epromo[0] == "cat") {
                        if($tovar['category'] != $epromo[1]) {
                            echo message('Ошибка', 'Введённый промокод не подходит для данного товара!', 'error');
                            return false;
                        }
                    }
                    else if($epromo[0] == "tovar") {
                        if($tovar['name'] != $epromo[1]) {
                            echo message('Ошибка', 'Введённый промокод не подходит для данного товара!', 'error');
                            return false;
                        }
                    }
                    else {
                        echo message('Ошибка', 'Введённый промокод не подходит для данного товара!', 'error');
                        return false;
                    }
                }




                $price = ($tovar['price']*$promo['percent'])/100;
				$price = $tovar['price'] - $price;

            }
            else
            {
                echo message('Ошибка', 'Выберите товар', 'error');
                return false;
            }
        }
        else
        {
            echo message('Ошибка', 'Промокод не найден!', 'error');
            return false;
        }
    }
    else
    {
        $sql = "SELECT * FROM `tovari` WHERE `id` = '{$tovar}'";
        $result = $mysqli->query($sql);

        $rows = $result->num_rows;

        if($rows == 1)
        {
            $result->data_seek(0);
            $tovar = $result->fetch_assoc();

            $price = $tovar['price'];
        }
        else
        {
            echo message('Ошибка', 'Выберите товар', 'error');
            return false;
        }
    }




    $sql = "SELECT * FROM `last_buys` WHERE `name` = '{$name}' AND `category` = 'Привилегии' AND `server` = '{$server}' ORDER BY `id` DESC LIMIT 1";
    $result = $mysqli->query($sql);

    $rows = $result->num_rows;

    if($rows == 1 && $tovar['category'] == 'Привилегии')
    {
        $result->data_seek(0);
        $last = $result->fetch_assoc();


        $sql = "SELECT * FROM `tovari` WHERE `code` = '{$last['tovar']}'";
        $result = $mysqli->query($sql);

        $rows = $result->num_rows;

        if($rows > 0)
        {
            $result->data_seek(0);
            $lasttovar = $result->fetch_assoc();

            if($lasttovar['price'] < $tovar['price'])
            {
                $price = $tovar['price'] - $last['price'];

                if(!empty($promo))
                {
                    $pricee = ($price*$promo['percent'])/100;

					$price = $price - $pricee;
                }

                $url = GotoPay($name, $server, $price, $tovar['category'], $tovar['code'], $tovar['name'], $config['MerchantID'], $config['SecretWord'], $config['pay_system']);
                $url = GotoPay($name, $server, $price, $tovar['category'], $tovar['code'], $tovar['name'], $config['ShopID'], $config['APIKey'], $config['pay_system']);

                echo json_encode(array(
                    'status' => 'info',
                    'url' => $url,
                    'title' => 'Информация',
                    'message' => 'Поскольку у Вас уже есть привилегия, сумма оплаты будет меньше',));
            }
            else
            {
                echo message('Ошибка', 'У вас высокий уровень привилегии', 'error');
            }
        }
        else
        {
            $url = GotoPay($name, $server, $price, $tovar['category'], $tovar['code'], $tovar['name'], $config['ShopID'], $config['APIKey'], $config['pay_system']);
            echo json_encode(array('status' => 'success', 'url' => $url));
        }
    }
    else
    {
        $url = GotoPay($name, $server, $price, $tovar['category'], $tovar['code'], $tovar['name'], $config['ShopID'], $config['APIKey'], $config['pay_system']);
        echo json_encode(array('status' => 'success', 'url' => $url));
    }


function GotoPay($nick, $serv, $sum, $cat, $tcode, $tname, $publickey, $secretword, $pay_system) {
    if($pay_system == 'yukassa') {
        // Создаем платеж через ЮKassa API
        $payment_data = array(
            'amount' => array(
                'value' => number_format($sum, 2, '.', ''),
                'currency' => 'RUB'
            ),
            'confirmation' => array(
                'type' => 'redirect',
                'return_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/payment/success'
            ),
            'capture' => true,
            'description' => 'Покупка ' . $tname . ' для игрока ' . $nick,
            'metadata' => array(
                'player' => $nick,
                'server' => $serv,
                'category' => $cat,
                'item_code' => $tcode,
                'item_name' => $tname
            )
        );
        
        $url = createYuKassaPayment($payment_data, $publickey, $secretword);
    }
    else if($pay_system == 'freekassa') {
        $currency = 'RUB';
        $order_id = $tcode;
        $sign = md5($publickey.':'.$sum.':'.$secretword.':'.$currency.':'.$order_id);
        $url = 'https://pay.freekassa.ru/?m='.$publickey.'&oa='.$sum.'&currency='.$currency.'&o='.$order_id.'&s='.$sign.'&us_category='.$cat.'&us_name='.$nick.'&us_server='.$serv.'';
    }          
    else {
        $url = '/';
    }
    return $url;
}

function createYuKassaPayment($payment_data, $shop_id, $secret_key) {
    $url = 'https://api.yookassa.ru/v3/payments';
    
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($shop_id . ':' . $secret_key),
        'Idempotence-Key: ' . uniqid()
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $payment = json_decode($response, true);
        return $payment['confirmation']['confirmation_url'];
    } else {
        return '/';
    }
}
?>
