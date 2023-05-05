-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 05 2023 г., 18:34
-- Версия сервера: 8.0.32
-- Версия PHP: 8.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `scandiweb`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Book`
--

CREATE TABLE `Book` (
  `id` int NOT NULL,
  `weight` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `Book`
--

INSERT INTO `Book` (`id`, `weight`) VALUES
(11, 1.20),
(13, 0.60);

-- --------------------------------------------------------

--
-- Структура таблицы `DVD`
--

CREATE TABLE `DVD` (
  `id` int NOT NULL,
  `size` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `DVD`
--

INSERT INTO `DVD` (`id`, `size`) VALUES
(1, 120),
(2, 150),
(3, 130),
(4, 110),
(5, 140),
(17, 2121),
(23, 3232),
(24, 5345),
(25, 111),
(26, 32322);

-- --------------------------------------------------------

--
-- Структура таблицы `Furniture`
--

CREATE TABLE `Furniture` (
  `id` int NOT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `length` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `Furniture`
--

INSERT INTO `Furniture` (`id`, `height`, `width`, `length`) VALUES
(6, 80.00, 200.00, 100.00),
(7, 75.00, 150.00, 90.00),
(8, 75.00, 120.00, 70.00),
(9, 60.00, 220.00, 150.00),
(10, 100.00, 180.00, 200.00),
(21, 4.34, 22.30, 30.10),
(22, 2121.10, 212.00, 5.60);

-- --------------------------------------------------------

--
-- Структура таблицы `Product`
--

CREATE TABLE `Product` (
  `id` int NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` enum('DVD','Book','Furniture') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `Product`
--

INSERT INTO `Product` (`id`, `sku`, `name`, `price`, `type`) VALUES
(1, 'DVD-001', 'The Dark Knight', 10.99, 'DVD'),
(2, 'DVD-002', 'Pulp Fiction', 9.99, 'DVD'),
(3, 'DVD-003', 'Inception', 12.99, 'DVD'),
(4, 'DVD-004', 'Forrest Gump', 8.99, 'DVD'),
(5, 'DVD-005', 'The Shawshank Redemption', 7.99, 'DVD'),
(6, 'FRN-001', 'Sofa', 499.99, 'Furniture'),
(7, 'FRN-002', 'Dining Table', 349.99, 'Furniture'),
(8, 'FRN-003', 'Office Desk', 299.99, 'Furniture'),
(9, 'FRN-004', 'Wardrobe', 599.99, 'Furniture'),
(10, 'FRN-005', 'Bed', 699.99, 'Furniture'),
(11, 'BK-001', 'The Lord of the Rings', 24.99, 'Book'),
(13, 'BK-003', 'The Great Gatsby', 12.99, 'Book'),
(17, 'уцуц', 'уццуцуц', 212.00, 'DVD'),
(21, 'куку', 'кукук', 332.00, 'Furniture'),
(22, 'вывы', 'ывыв', 2121.00, 'Furniture'),
(23, 'выв', 'ывывы', 2332.32, 'DVD'),
(24, 'gdfg', 'gfdgdf', 54353.00, 'DVD'),
(25, '1111', '11', 111.00, 'DVD'),
(26, 'erwe', 'rwer', 3232.30, 'DVD');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Book`
--
ALTER TABLE `Book`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `DVD`
--
ALTER TABLE `DVD`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Furniture`
--
ALTER TABLE `Furniture`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Product`
--
ALTER TABLE `Product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Product`
--
ALTER TABLE `Product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Book`
--
ALTER TABLE `Book`
  ADD CONSTRAINT `book_ibfk_1` FOREIGN KEY (`id`) REFERENCES `Product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `DVD`
--
ALTER TABLE `DVD`
  ADD CONSTRAINT `dvd_ibfk_1` FOREIGN KEY (`id`) REFERENCES `Product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Furniture`
--
ALTER TABLE `Furniture`
  ADD CONSTRAINT `furniture_ibfk_1` FOREIGN KEY (`id`) REFERENCES `Product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
