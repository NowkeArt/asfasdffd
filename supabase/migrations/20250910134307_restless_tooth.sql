@@ .. @@
   `VkGroup` varchar(200) NOT NULL,
   `Youtube` varchar(300) NOT NULL,
   `pay_system` varchar(200) NOT NULL,
-  `MerchantID` varchar(200) NOT NULL,
-  `SecretWord` varchar(200) NOT NULL,
+  `ShopID` varchar(200) NOT NULL,
+  `APIKey` varchar(200) NOT NULL,
   `vktoken` varchar(200) NOT NULL,
   `vksecret` varchar(200) NOT NULL,
   `vkid` varchar(200) NOT NULL,
@@ .. @@
 -- Дамп данных таблицы `settings`
 --
 
-INSERT INTO `settings` (`id`, `ServerName`, `Notify`, `Discount`, `VkGroup`, `Youtube`, `pay_system`, `MerchantID`, `SecretWord`, `vktoken`, `vksecret`, `vkid`, `api_ver`) VALUES
-(1, 'RedMoon', 'Внимание! Сейчас действуют скидки 50% на весь донат!', 50, 'https://vk.com/moonstudio_mc', 'https://www.youtube.com/', 'unitpay', '', '', '', '0', '', '5.103');
+INSERT INTO `settings` (`id`, `ServerName`, `Notify`, `Discount`, `VkGroup`, `Youtube`, `pay_system`, `ShopID`, `APIKey`, `vktoken`, `vksecret`, `vkid`, `api_ver`) VALUES
+(1, 'RedMoon', 'Внимание! Сейчас действуют скидки 50% на весь донат!', 50, 'https://vk.com/moonstudio_mc', 'https://www.youtube.com/', 'yukassa', '', '', '', '0', '', '5.103');