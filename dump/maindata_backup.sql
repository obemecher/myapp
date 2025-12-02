-- phpMyAdmin SQL Dump
-- version 4.0.10deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 11 2025 г., 22:42
-- Версия сервера: 5.5.62-0ubuntu0.14.04.1
-- Версия PHP: 5.5.9-1ubuntu4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `MainData`
--

-- --------------------------------------------------------

--
-- Структура таблицы `catalog`
--

CREATE TABLE IF NOT EXISTS `catalog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `id_shop` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `id_shop` (`id_shop`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=40 ;

--
-- Дамп данных таблицы `catalog`
--

INSERT INTO `catalog` (`ID`, `name`, `price`, `id_shop`) VALUES
(1, 'Хот-дог с курицей 350г', 244.00, 2),
(5, 'Шаурма Классическая 340г', 325.00, 2),
(6, 'Шаурма мини 200г', 270.00, 2),
(7, 'Круассан', 67.00, 1),
(8, 'Медовик', 124.00, 1),
(9, 'Цезарь', 240.00, 1),
(10, 'Пирожное Картошка', 52.00, 3),
(11, 'Торт яблочный', 64.00, 3),
(12, 'Сосиска в тесте', 56.00, 3),
(13, 'Эспрессо', 69.00, 3),
(14, 'Латте 0.4', 125.00, 3),
(15, 'Круасан с ветчиной и сыром', 173.00, 4),
(16, 'Сэндвич с цыплёнком', 186.00, 4),
(17, 'Галета с клубникой', 93.00, 4),
(18, 'Булочка с маком', 95.00, 4),
(19, 'Морс облепиховый', 140.00, 4),
(20, 'Компот с чёрной смородиной', 145.00, 4),
(21, 'Капучино', 169.00, 5),
(22, 'Раф', 249.00, 5),
(23, 'Блинчики с цыплёнком в сливках', 249.00, 5),
(24, 'Салат Цезарь', 249.00, 5),
(25, 'Круасан с красной рыбой и сливо', 269.00, 5),
(26, 'Сэндвич с ветчиной и сыром', 219.00, 5),
(33, 'Латте 0.35', 185.00, 2),
(34, 'Капучино 0.35', 175.00, 2),
(35, 'Сырники с топингом', 105.00, 1),
(36, 'Лазанья с цыплёнком', 195.00, 1),
(37, 'Эклер с шоколадным кремом', 57.00, 1),
(38, 'Слойка с варёной сгущенкой', 54.00, 1),
(39, 'Слойка ржаная с цыплёнком и грибами', 79.00, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  `amount` int(11) DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statusor` varchar(255) DEFAULT NULL,
  `curer` varchar(255) DEFAULT NULL,
  `conflict` varchar(255) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `id_shop` (`id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `order_details`
--

CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_per_unit` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `Shop`
--

CREATE TABLE IF NOT EXISTS `Shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nameshop` varchar(255) NOT NULL,
  `adres` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `Shop`
--

INSERT INTO `Shop` (`id`, `nameshop`, `adres`, `contact`) VALUES
(1, 'ЛюдиЛюбят', 'Грибоедова 28/1', '+7(963)344-33-01'),
(2, 'Шаверма Кингдом', 'Грибоедова 26', '+7(921)904-83-53'),
(3, 'Булочная Ф. Вольчека', 'Садовая 19', '+7 (812) 407-26-31'),
(4, 'Пироговый дворик', 'Грибоедова 22', '+7 (812) 313-10-61 (доб. 209)'),
(5, 'Цех 85', 'Садовая 25', '8 (800) 500-89-85'),
(7, 'Burgerberg', 'Sad Edem', '12341');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imya` varchar(255) DEFAULT NULL,
  `familia` varchar(255) DEFAULT NULL,
  `otch` varchar(255) DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `contacts` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `login` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=20 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `imya`, `familia`, `otch`, `group`, `contacts`, `password`, `status`, `login`) VALUES
(8, 'i', 'i', 'i', NULL, 'i', 'i', NULL, 'i'),
(9, 'Иван', 'Иванов', 'Иванович', NULL, '89223450219', '1', NULL, 'ivan2002'),
(10, 'Дмитрий', 'Андреев', ' Вячеславович', NULL, '@andreevdv', '1', NULL, 'an'),
(12, 'Сильвестр', 'Сергеев', 'Андреевич', NULL, '@Silya', '1', '1', 'silya'),
(14, 'Евгений', 'Карпец', 'Сергеевич', NULL, 'karpets', '1', NULL, 'k'),
(17, 'Владимир', 'Птутин', '', NULL, '88005553535', '12345678', NULL, 'pty123'),
(18, '1', 'a1', '1', NULL, '88005553535', 'qq123456', NULL, '1'),
(19, '1', '1', '1', NULL, '+79912124312', 'qq123456', NULL, '11');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `catalog`
--
ALTER TABLE `catalog`
  ADD CONSTRAINT `catalog_ibfk_1` FOREIGN KEY (`id_shop`) REFERENCES `Shop` (`id`);

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id_shop`) REFERENCES `Shop` (`id`);

--
-- Ограничения внешнего ключа таблицы `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `catalog` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
