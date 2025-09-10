<?php
session_start();
define("moonstudio", true);
include '../engine/config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?=$realdir?>/assets/img/logo1.png">
    <title>Успешная оплата - <?=$config['ServerName']?></title>
    <!-- CSS -->
    <link rel="stylesheet" href="<?=$realdir?>/assets/css/bootstrap-grid.css">
    <link rel="stylesheet" href="<?=$realdir?>/assets/css/fontawesome-all.css">
    <link rel="stylesheet" href="<?=$realdir?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?=$realdir?>/assets/css/style.css">
    <link rel="stylesheet" href="<?=$realdir?>/assets/css/responsive.css">
    <!-- CSS -->
</head>
<body class="main-page">

<div class="container">
    <div class="header">
        <div class="logo">
            <a href="/"> <img class="logo-img" src="<?=$realdir?>/assets/img/logo.png" alt=""> </a>
            <p class="slogan">играй в свое наслаждение</p>
        </div>
        <ul class="main-menu">
            <li><a href="/">Главная</a></li>
            <li><a href="/">Что можно купить?</a></li>
            <li><a href="/">Мы Вконтакте</a></li>
            <li><a href="/">Информация о сервере</a></li>
            <li><a href="<?=$config['VkGroup']?>"><i class="icon-social fab fa-vk"></i></a></li>
            <li><a href="<?=$config['Youtube']?>"><i class="icon-social fab fa-youtube"></i></a></li>
        </ul>
    </div>

    <div class="row mobile-row">
        <div class="main-form" style="text-align: center; padding: 50px;">
            <h2 style="color: #4CAF50; margin-bottom: 20px;">
                <i class="fas fa-check-circle" style="font-size: 48px; display: block; margin-bottom: 20px;"></i>
                Оплата прошла успешно!
            </h2>
            <p style="color: #fff; font-size: 18px; margin-bottom: 30px;">
                Спасибо за покупку! Ваш товар будет выдан в течение нескольких минут.
            </p>
            <a href="/" class="form-btn" style="display: inline-block; text-decoration: none; margin-top: 20px;">
                Вернуться на главную
            </a>
        </div>
    </div>

    <div class="footer">
        <img class="logo-footer" src="<?=$realdir?>/assets/img/logo.png" alt="">
        <p class="copyright"><?=$config['ServerName'];?> 2022 © <br> Сайт автодоната</p>
    </div>
</div>

<!-- JS -->
<script type="text/javascript" src="<?=$realdir?>/assets/js/jquery.min.js"></script>
<!-- JS -->
</body>
</html>