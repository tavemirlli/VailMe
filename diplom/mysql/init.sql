-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.0:3306
-- Время создания: Апр 26 2026 г., 21:14
-- Версия сервера: 8.0.44
-- Версия PHP: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vailme_store`
--

-- --------------------------------------------------------

--
-- Структура таблицы `carts`
--

CREATE TABLE `carts` (
  `id` int NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `carts`
--

INSERT INTO `carts` (`id`, `session_id`, `user_id`, `created_at`, `updated_at`) VALUES
(10, 'efaeem93m0f7f8srtk3omonjjpbfa5b3', 3, '2026-04-09 07:05:06', '2026-04-09 12:53:41'),
(12, 'f508vc18gc9rp5sr6oqs6l7mhoovblcf', 2, '2026-04-09 17:50:25', '2026-04-09 17:50:25'),
(14, 'm5um4gcg6j6vdcdmklv25nqhre0b4epe', NULL, '2026-04-23 11:50:25', '2026-04-23 11:50:25'),
(17, 'h8b5cj02em36uled3otjv8vu5m5r4n3u', 4, '2026-04-23 13:39:38', '2026-04-23 13:39:38'),
(19, 'npc08022afqmob5tfngkcrib2n8kdr90', 5, '2026-04-23 13:54:50', '2026-04-23 13:54:50'),
(21, 'pk59ie966d0vdlmum4736vo4en71i81n', NULL, '2026-04-26 17:20:18', '2026-04-26 17:20:18');

-- --------------------------------------------------------

--
-- Структура таблицы `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `variant_id`, `quantity`, `price`, `created_at`) VALUES
(27, 14, 7, 9, 1, 13560.00, '2026-04-23 11:50:25'),
(42, 17, 3, 3, 1, 5990.00, '2026-04-23 13:51:44'),
(44, 19, 4, 5, 1, 14490.00, '2026-04-23 13:55:06'),
(51, 12, 4, 5, 1, 14490.00, '2026-04-26 17:17:15'),
(52, 21, 14, 25, 5, 2990.00, '2026-04-26 17:20:18');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `image_url`) VALUES
(1, 'Верхняя одежда', '/uploads/categories/cat_1_69eb592bd0a0b.jpg'),
(4, 'Спортивный стиль', '/uploads/categories/cat_4_69eb593a5b127.jpg'),
(6, 'Обувь', '/uploads/categories/cat_6_69eb5a3f6803c.jpg'),
(7, 'Мужская одежда', '/uploads/categories/cat_7_69eb5b6c30b47.jpg'),
(8, 'Женская одежда', '/uploads/categories/cat_8_69eb5c7bdaf26.jpg'),
(9, 'Аксессуары', '/uploads/categories/cat_9_69ee51606bf91.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `contacts`
--

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `new_arrivals`
--

CREATE TABLE `new_arrivals` (
  `id` int NOT NULL,
  `product_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` enum('new','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `invoice_sent` tinyint(1) DEFAULT '0',
  `invoice_sent_at` timestamp NULL DEFAULT NULL,
  `admin_comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `promocode_id` int DEFAULT NULL,
  `promocode_discount` decimal(10,2) DEFAULT '0.00',
  `original_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `customer_phone`, `customer_email`, `total_amount`, `order_status`, `invoice_sent`, `invoice_sent_at`, `admin_comment`, `created_at`, `updated_at`, `promocode_id`, `promocode_discount`, `original_total`) VALUES
(1, 3, 'ORD-20260408-6821', 'Bdfy', '89281844117', 'talemirli@gmail.com', 14490.00, 'cancelled', 1, '2026-04-08 12:52:03', NULL, '2026-04-08 12:44:37', '2026-04-08 12:52:03', NULL, 0.00, NULL),
(2, 3, 'ORD-20260408-7783', 'Bdfy', '89281844117', 'talemirli@gmail.com', 14490.00, 'cancelled', 1, '2026-04-08 12:46:06', NULL, '2026-04-08 12:46:04', '2026-04-08 12:51:04', NULL, 0.00, NULL),
(3, 3, 'ORD-20260408-5733', 'ff', '89281844117', 'talemirli@gmail.com', 14490.00, 'cancelled', 1, '2026-04-08 12:48:44', NULL, '2026-04-08 12:48:42', '2026-04-08 12:51:06', NULL, 0.00, NULL),
(4, 2, 'ORD-20260408-3796', 'аа', '89281844117', 'talemirli@gmail.com', 28980.00, 'cancelled', 0, NULL, NULL, '2026-04-08 12:59:40', '2026-04-08 13:02:19', NULL, 0.00, NULL),
(5, 2, 'ORD-20260408-4119', 'Елизавета Мирончик', '89281844117', 'talemirli@gmail.com', 43470.00, 'cancelled', 0, NULL, NULL, '2026-04-08 13:04:08', '2026-04-08 13:04:44', NULL, 0.00, NULL),
(6, 2, 'ORD-20260408-9528', 'аа', '89281844117', 'talemirli@gmail.com', 14490.00, 'new', 0, NULL, NULL, '2026-04-08 15:10:38', '2026-04-08 15:10:38', NULL, 0.00, NULL),
(7, NULL, 'ORD-20260408-4063', 'ПРП', '89281844117', 'talemirli@gmail.com', 13560.00, 'new', 1, '2026-04-08 19:07:41', NULL, '2026-04-08 19:02:02', '2026-04-08 19:07:41', NULL, 0.00, NULL),
(8, 2, 'ORD-20260409-6108', 'fff', '89281844117', 'talemirli@gmail.com', 14490.00, 'cancelled', 0, NULL, NULL, '2026-04-09 07:05:16', '2026-04-09 07:05:55', NULL, 0.00, NULL),
(9, 2, 'ORD-20260409-2007', 'Елизавета Мирончик', '89281844117', 'talemirli@gmail.com', 14490.00, 'cancelled', 0, NULL, NULL, '2026-04-09 07:25:09', '2026-04-09 07:25:37', NULL, 0.00, NULL),
(10, 2, 'ORD-20260409-1483', 'Елизавета Мирончик', '89281844117', 'talemirli@gmail.com', 28980.00, 'shipped', 0, NULL, NULL, '2026-04-09 12:52:29', '2026-04-09 12:52:54', NULL, 0.00, NULL),
(11, 2, 'ORD-20260423-5724', 'Елизавета Мирончик', '89885178420', 'admin1@vailme.ru', 64900.00, 'shipped', 0, NULL, NULL, '2026-04-23 12:51:59', '2026-04-23 12:52:13', NULL, 0.00, NULL),
(12, 4, 'ORD-20260423-6227', 'Елизавета Мирончик', '89999999999', 'mirelis2006@gmail.com', 14490.00, 'new', 0, NULL, NULL, '2026-04-23 13:39:51', '2026-04-23 13:39:51', NULL, 0.00, NULL),
(13, 4, 'ORD-20260423-3079', 'Елизавета Мирончик', '89999999999', 'mirelis2006@gmail.com', 19550.00, 'new', 0, NULL, NULL, '2026-04-23 13:47:27', '2026-04-23 13:47:27', NULL, 0.00, NULL),
(14, 5, 'ORD-20260423-8851', 'fff ffff', '89281844117', 'fff@hhh.jj', 5391.00, 'cancelled', 0, NULL, NULL, '2026-04-23 13:54:57', '2026-04-26 16:47:40', 3, 599.00, 5990.00),
(15, 2, 'ORD-20260426-4254', 'Елизавета Мирончик', '89885178420', 'admin1@vailme.ru', 28980.00, 'new', 0, NULL, NULL, '2026-04-26 17:03:38', '2026-04-26 17:03:38', NULL, 0.00, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variant_id`, `product_name`, `product_price`, `quantity`, `total_price`) VALUES
(1, 2, 4, 5, 'Куртка Calvin Klein', 14490.00, 1, 14490.00),
(2, 3, 4, 5, 'Куртка Calvin Klein', 14490.00, 1, 14490.00),
(3, 4, 4, 5, 'Куртка Calvin Klein', 14490.00, 2, 28980.00),
(4, 5, 4, 5, 'Куртка Calvin Klein', 14490.00, 3, 43470.00),
(5, 6, 4, 5, 'Куртка Calvin Klein', 14490.00, 1, 14490.00),
(6, 7, 7, 9, 'Штаны Adidas Avavav', 13560.00, 1, 13560.00),
(7, 8, 4, 5, 'Куртка Calvin Klein', 14490.00, 1, 14490.00),
(8, 9, 4, 6, 'Куртка Calvin Klein', 14490.00, 1, 14490.00),
(9, 10, 4, 6, 'Куртка Calvin Klein', 14490.00, 2, 28980.00),
(10, 11, 3, 4, 'Кофта Adidas ', 5590.00, 4, 22360.00),
(11, 11, 4, 5, 'Куртка Calvin Klein', 14490.00, 2, 28980.00),
(12, 11, 7, 9, 'Штаны Adidas Avavav', 13560.00, 1, 13560.00),
(13, 12, 4, 5, 'Куртка Calvin Klein', 14490.00, 1, 14490.00),
(14, 13, 3, 3, 'Кофта Adidas ', 5990.00, 1, 5990.00),
(15, 13, 7, 9, 'Штаны Adidas Avavav', 13560.00, 1, 13560.00),
(16, 14, 3, 3, 'Кофта Adidas ', 5990.00, 1, 5990.00),
(17, 15, 4, 5, 'Куртка Calvin Klein', 14490.00, 2, 28980.00);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `subcategory_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `is_new` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `subcategory_id`, `category_id`, `name`, `description`, `price`, `old_price`, `is_new`) VALUES
(3, 4, 1, 'Кофта Adidas ', 'Стильная кофта Adidas в трендовом персиковом цвете — идеальный выбор для тех, кто ценит комфорт и следит за модой.\r\n\r\nЭта модель выполнена в популярном спортивно-повседневном стиле (athleisure), который уже несколько сезонов остается на пике популярности. Нежный персиковый оттенок добавляет образу свежести и оригинальности, выделяя вас из толпы.\r\n\r\nУниверсальность: Кофта разработана как унисекс — она одинаково хорошо сидит как на мужчинах, так и на женщинах благодаря свободному крою Oversized Fit. Можно носить с джоггерами, спортивными штанами или даже с джинсами — создавайте свой уникальный образ!\r\n\r\nОсобенности модели:\r\n\r\nСвободный крой Oversized Fit — главный тренд сезона\r\n\r\nМягкий капюшон на регулируемых шнурках\r\n\r\nУдобный кенгуру-карман спереди\r\n\r\nЭластичные манжеты и низ рукавов\r\n\r\nФирменный логотип Adidas в виде трилистника (оригинальная вышивка)\r\n\r\nКачественная фурнитура\r\n\r\nМатериал: Высококачественный флис (хлопок 80%, полиэстер 20%) — мягкий, теплый, но при этом дышащий. Материал не теряет форму после стирки и не скатывается.\r\n\r\n', 5990.00, 6990.00, 1),
(4, 1, 2, 'Куртка Calvin Klein', 'Куртка демисезонная, отлично подходит под осень и весну, а так же под теплую зиму (если такова имеется). Качественный материалы - другие быть не могут, т.к. весь товар оригинальный. Подойдет как для девушек, так и для мужчин.', 14490.00, 16990.00, 1),
(7, 5, 1, 'Штаны Adidas Avavav', 'Штаны Adidas — это не просто спортивная одежда, а настоящая икона стиля, которая давно вышла за пределы спортзала. Они славятся своим комфортом, качеством и узнаваемым дизайном с тремя полосками.', 13560.00, 14490.00, 1),
(8, 14, 1, 'Nike Court Slam Платье ', 'Nike Court Slam — это облегающее женское теннисное платье без рукавов, выполненное из эластичного материала. Модель оснащена технологией Nike Dri-FIT для отвода влаги и поддержания сухости и комфорта во время игры. Ключевыми особенностями являются высокая линия горловины для дополнительного покрытия и сетчатые вставки на верхней части спины для улучшенной вентиляции. Также платье отличается удлиненной задней частью подола, обеспечивающей надежную фиксацию при динамичных движениях.', 10590.00, 12449.00, 1),
(11, 14, 1, 'Nike Court Slam Dri-FIT', 'Nike Court Slam Dri-FIT — это женское теннисное платье облегающего кроя (tight fit) из эластичного полиэстера с добавением эластана, как правило, в соотношении 89% на 11% . Главная технологическая особенность — фирменная система Dri-FIT, которая эффективно отводит влагу от тела, помогая оставаться сухой и комфортной даже во время самых интенсивных нагрузок . Ключевые детали дизайна включают высокую линию горловины для надежного покрытия и дополнительной уверенности в движении, а также продуманные вентиляционные зоны: это могут быть сетчатые вставки на спине или поясе, открытая область на нижней части спины или специальные вырезы, обеспечивающие отличную циркуляцию воздуха . Задняя часть подола часто удлинена или имеет плиссированную структуру, которая помогает избежать задирания ткани во время активного перемещения по корту . Некоторые версии модели также дополнены сетчатыми вставками на юбке или внутренними шортами с карманом для мячей или телефона . Платье может иметь как классическую цельнокроеную форму, так и застежку-молнию спереди .', 7449.00, 8449.00, 1),
(12, 14, 1, 'Adidas Originals ', 'Платье из коллаборации Adidas Originals и Monkey Kingdom — это женская модель с коротким рукавом и круглым вырезом, прямого или слегка приталенного силуэта, длиной чуть выше или до колена. Ключевая особенность — дизайн в пиксельной эстетике, отсылающий к ретро-играм и хип-хоп культуре 70-х. Ткань часто украшена монограммой с «глитчевым» (искажённым) логотипом Adidas Originals и/или принтами с пиксельными персонажами. В некоторых версиях присутствуют объёмные рукава-фонарики или элементы уличного кроя. Платье выполнено в свободном, расслабленном стиле, характерном для уличной моды.\r\n\r\n', 4990.00, 5989.99, 1),
(13, 15, 1, 'Nike SS24 T', 'Топ Nike SS24 T — это короткая модель для тренировок из коллекции 2024 года. Ключевая особенность — плоские швы, которые снижают риск натирания при активных движениях . Он выполнен из ткани с технологией Dri-FIT, которая отводит влагу для быстрого испарения и помогает оставаться сухим', 3690.00, 4289.99, 1),
(14, 15, 1, 'Nike AEROSWIFT', 'Розовая майка Nike AeroSwift — это женская беговая модель с укороченным кроем (cropped) и облегающим силуэтом (slim fit) . Изделие изготовлено из легкого, дышащего полиэстера с использованием передовой технологии Nike Dri-FIT ADV, которая эффективно отводит влагу, помогая оставаться сухой и комфортной во время интенсивных тренировок . Дизайн включает боковые разрезы (side slits) для дополнительной вентиляции и свободы движений, а также плиссированную (мягко гофрированную) спинку . Конкретный оттенок розового цвета, представленный в ряде моделей, называется Hyper Pink и часто сочетается с черными акцентами .', 2990.00, 3289.99, 1),
(15, 12, 2, 'SKARO Поло', 'Поло SKARO — это модель преимущественно мужского кроя, представленная в двух ключевых вариантах: классическое короткое поло из хлопка с добавлением эластана  и утепленный джемпер с длинным рукавом и V-образным воротником из чистой овечьей шерсти . В зависимости от коллекции, поло может быть как строгим повседневным вариантом в стиле «бизнес-кэжуал» , так и более свободным свитером на холодное время года .', 4590.00, 4990.00, 0),
(16, 12, 2, 'COCA COLA Поло', 'Поло COCA-COLA — это собирательное название множества моделей, объединенных культовой брендированной символикой, которые выпускаются в рамках коллабораций и как самостоятельная продукция', 4990.00, 5290.00, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `product_images`
--

CREATE TABLE `product_images` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_main` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_main`) VALUES
(1, 3, '/uploads/products/3/69bbc89c94916.jpg', 1),
(2, 4, '/uploads/products/4/69c9056090d77.jpg', 1),
(3, 4, '/uploads/products/4/69c90560922ef.jpg', 0),
(11, 7, '/uploads/products/7/69d659385e05c.jpg', 1),
(12, 7, '/uploads/products/7/69d659385eb7d.jpg', 0),
(13, 8, '/uploads/products/8/69ede6e825278.jpg', 1),
(14, 8, '/uploads/products/8/69ede6e825c61.jpg', 0),
(19, 11, '/uploads/products/11/69ede94379cd3.jpg', 1),
(20, 11, '/uploads/products/11/69ede9437a7bc.jpg', 0),
(21, 12, '/uploads/products/12/69edea8e5db78.jpg', 0),
(22, 12, '/uploads/products/12/69edea8e5e738.jpg', 1),
(23, 12, '/uploads/products/12/69edea8e5f0e2.jpg', 0),
(24, 13, '/uploads/products/13/69edeb9e452d3.jpg', 1),
(25, 13, '/uploads/products/13/69edeb9e45ea5.jpg', 0),
(26, 14, '/uploads/products/14/69edeca25054d.jpg', 1),
(27, 14, '/uploads/products/14/69edeca25107a.jpg', 0),
(28, 15, '/uploads/products/15/69eded8044bf8.jpg', 1),
(29, 15, '/uploads/products/15/69eded8045706.jpg', 0),
(30, 16, '/uploads/products/16/69edee435629c.jpg', 1),
(31, 16, '/uploads/products/16/69edee4356c89.jpg', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `color`, `size`, `quantity`, `price`) VALUES
(2, 3, 'Персиковый', 'M', 12, 5990.00),
(3, 3, 'Персиковый ', 'L', 5, 5990.00),
(4, 3, 'Персиковый', 'S', 16, 5590.00),
(5, 4, 'Черный', 'M', 5, 14490.00),
(6, 4, 'Черный', 'S', 0, 14490.00),
(9, 7, 'Хаки', 'M', 11, 13560.00),
(10, 8, 'Лавандовый', 'M', 12, 10590.00),
(11, 8, 'Лавандовый', 'S', 4, 10590.00),
(12, 8, 'Лавандовый', 'XS', 1, 10590.00),
(19, 11, 'Минеральный синий ', 'M', 12, 7449.00),
(20, 12, 'Белый', 'L', 2, 4990.00),
(21, 12, 'Белый', 'S', 4, 4990.00),
(22, 13, 'Розовый', 'S', 13, 3690.00),
(23, 13, 'Розовый', 'XS', 3, 3690.00),
(24, 14, 'Розовый', 'L', 2, 2990.00),
(25, 14, 'Розовый', 'XS', 12, 2990.00),
(26, 15, 'Абрикос', 'M', 16, 4590.00),
(27, 15, 'Абрикос', 'L', 4, 4590.00),
(28, 15, 'Абрикос', 'XL', 2, 4590.00),
(29, 16, 'Бежевый', 'M', 12, 4990.00);

-- --------------------------------------------------------

--
-- Структура таблицы `promocodes`
--

CREATE TABLE `promocodes` (
  `id` int NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci DEFAULT 'percent',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT '0.00',
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int DEFAULT '1',
  `used_count` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `user_id` int DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `promocodes`
--

INSERT INTO `promocodes` (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount`, `usage_limit`, `used_count`, `is_active`, `user_id`, `expires_at`, `created_at`) VALUES
(1, 'WELCOMEVL10', 'percent', 10.00, 1000.00, NULL, 1, 0, 1, NULL, '2026-05-23 13:28:28', '2026-04-23 13:28:28'),
(2, 'WELCOME4232', 'percent', 10.00, 0.00, NULL, 1, 0, 1, 4, '2026-05-23 13:35:47', '2026-04-23 13:35:47'),
(3, 'WELCOME5762', 'percent', 10.00, 0.00, NULL, 1, 1, 0, 5, '2026-05-23 13:53:47', '2026-04-23 13:53:47'),
(4, 'WELCOME6185', 'percent', 10.00, 0.00, NULL, 1, 0, 1, 6, '2026-05-26 17:09:39', '2026-04-26 17:09:39');

-- --------------------------------------------------------

--
-- Структура таблицы `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `name`) VALUES
(1, 1, 'Куртки'),
(4, 4, 'Кофты'),
(5, 4, 'Штаны'),
(6, 1, 'Пуховики'),
(7, 1, 'Ветровки'),
(8, 4, 'Футболки'),
(9, 6, 'Кроссовки'),
(10, 6, 'Сланцы'),
(12, 7, 'Поло'),
(13, 7, 'Штаны и шорты'),
(14, 8, 'Платья'),
(15, 8, 'Топы');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart_data` text COLLATE utf8mb4_unicode_ci,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `privacy_consent` tinyint(1) DEFAULT '0',
  `data_consent` tinyint(1) DEFAULT '0',
  `consent_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `cart_data`, `is_admin`, `created_at`, `privacy_consent`, `data_consent`, `consent_date`) VALUES
(2, 'admin1@vailme.ru', '$2y$10$QXlj7Ijh0fBYtT8qXDzM5.FEuP146xa6tPeYR3bkaR9EOTmdoOg5e', 'Елизавета', 'Мирончик', '89885178420', NULL, 1, '2026-04-08 11:36:19', 0, 0, NULL),
(3, 'talemirli@gmail.com', '$2y$10$kI1sn.cAUud2vwbhrfswHOlkoA.fSZdVa45TS2JYQWpucaBYp0kiy', 'Иван', 'Иванов', '89281844117', '{\"3_u041fu0435u0440u0441u0438u043au043eu0432u044bu0439 _L\":{\"id\":\"3\",\"name\":\"u041au043eu0444u0442u0430 Adidas \",\"price\":\"5990.00\",\"quantity\":1,\"color\":\"u041fu0435u0440u0441u0438u043au043eu0432u044bu0439 \",\"size\":\"L\",\"variant_id\":\"3\",\"max_quantity\":\"6\"}}', 0, '2026-04-08 12:10:18', 0, 0, NULL),
(4, 'mirelis2006@gmail.com', '$2y$10$8/PLu/r/kaLaYGgow9wuA.6j6hQGY15SoyE.z2XLQFltErbGJdRRa', 'Елизавета', 'Мирончик', '89999999999', NULL, 0, '2026-04-23 13:35:47', 0, 0, NULL),
(5, 'fff@hhh.jj', '$2y$10$v6pYwDa8c8u38hFN5lJq/uxzfzyMRU48.zNWemb51DJb.FI3TEJHS', 'fff', 'ffff', '89281844117', NULL, 0, '2026-04-23 13:53:47', 0, 0, NULL),
(6, 'ff@ff.com', '$2y$10$OSJZdzoGlWqiJf3oyoVIOOv9cnQ.KxhiGRxZy1vhTqVdJ8TvHGgs.', 'ff', 'ff', '89281844117', NULL, 0, '2026-04-26 17:09:39', 0, 0, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_cart` (`user_id`);

--
-- Индексы таблицы `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_product` (`cart_id`,`product_id`,`variant_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `new_arrivals`
--
ALTER TABLE `new_arrivals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Индексы таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_variant` (`product_id`,`color`,`size`);

--
-- Индексы таблицы `promocodes`
--
ALTER TABLE `promocodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT для таблицы `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `new_arrivals`
--
ALTER TABLE `new_arrivals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT для таблицы `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT для таблицы `promocodes`
--
ALTER TABLE `promocodes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `new_arrivals`
--
ALTER TABLE `new_arrivals`
  ADD CONSTRAINT `new_arrivals_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
