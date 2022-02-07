-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 01 2022 г., 10:01
-- Версия сервера: 10.5.12-MariaDB-cll-lve
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `u176269397_hopcourse`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Class', 'Duis vel nibh at velit scelerisque suscipit. Duis lobortis massa imperdiet quam.', '2020-11-01 11:25:12'),
(2, 'Fusce commodo', 'Cras sagittis. Maecenas nec odio et ante tincidunt tempus.', '2020-11-02 11:25:28'),
(3, 'Ut Donec', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In dui magna, posuere eget, vestibulum et, tempor auctor, justo.', '2020-11-03 11:25:54'),
(4, 'Vivamus', 'Praesent porttitor, nulla vitae posuere iaculis, arcu nisl dignissim dolor, a pretium mi sem ut ipsum. Curabitur suscipit suscipit tellus.', '2020-11-04 11:26:16'),
(5, 'Suspendisse', 'Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.', '2020-11-05 11:27:02'),
(6, 'Praesent', 'Curabitur nisi. Ut id nisl quis enim dignissim sagittis.', '2020-11-06 11:45:09');

-- --------------------------------------------------------

--
-- Структура таблицы `likes`
--

CREATE TABLE `likes` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `liked` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `post_id`, `liked`, `created_at`) VALUES
(1, 1, 1, 0, '2020-11-09 11:59:07'),
(2, 1, 2, 0, '2020-11-09 13:05:46'),
(3, 2, 3, 0, '2020-11-09 13:42:10'),
(4, 2, 5, 0, '2020-11-09 16:10:44'),
(5, 1, 5, 0, '2020-11-09 16:49:31'),
(6, 1, 4, 0, '2020-11-09 16:49:52'),
(7, 2, 4, 0, '2020-11-09 16:50:45'),
(8, 3, 5, 1, '2020-11-09 17:14:04'),
(9, 3, 6, 1, '2020-11-09 17:21:49'),
(10, 3, 7, 1, '2020-11-09 17:50:57'),
(11, 3, 3, 0, '2020-11-09 17:59:51'),
(12, 1, 6, 1, '2020-11-09 18:21:07'),
(13, 4, 8, 0, '2020-11-10 05:12:53'),
(14, 4, 9, 0, '2020-11-10 07:09:55'),
(15, 1, 10, 1, '2020-11-10 07:30:40'),
(16, 1, 9, 1, '2021-12-16 06:22:21');

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `meta_desc` varchar(255) DEFAULT NULL,
  `format` enum('image','video','','') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `video_urls` text DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `category_id`, `title`, `meta_desc`, `format`, `image`, `video`, `body`, `video_urls`, `published`, `approved`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'In auctor', 'Maecenas vestibulum mollis diam. Etiam iaculis nunc ac metus', 'image', '/2020/11/09/16049225491821.jpg', NULL, '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n[img src=\"16049236948456.jpg\" alt=\"\"]\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.\r\n', NULL, 0, 0, '2020-11-01 09:44:21', '2020-11-09 11:49:10', '2020-11-10 05:02:35'),
(2, 1, 2, 'Vestibulum purus', 'Morbi mattis ullamcorper velit. Maecenas egestas arcu quis ligula mattis placerat.', 'video', NULL, '{\"https:\\/\\/youtu.be\\/_VVwpdJYHNo\":\"https:\\/\\/www.youtube.com\\/embed\\/_VVwpdJYHNo\"}', 'Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam. Integer tincidunt. Vestibulum turpis sem, aliquet eget, lobortis pellentesque, rutrum eu, nisl. Phasellus magna. Curabitur vestibulum aliquam leo.\r\n[img src=\"16049267479165.jpg\" alt=\"\"]\r\nCurabitur ullamcorper ultricies nisi. Nullam sagittis. Integer ante arcu, accumsan a, consectetuer eget, posuere ut, mauris. Praesent ac sem eget est egestas volutpat. Donec sodales sagittis magna.\r\n[img src=\"16049267709307.jpg\" alt=\"\"]\r\n<em>Praesent egestas neque eu enim. Suspendisse pulvinar, augue ac venenatis condimentum, sem libero volutpat nibh, nec pellentesque velit pede quis nunc. Sed a libero. Duis leo. Curabitur nisi.</em>\r\n\r\nPellentesque dapibus hendrerit tortor. Suspendisse eu ligula. Quisque id odio. Phasellus nec sem in justo pellentesque facilisis. Phasellus gravida semper nisi.\r\n\r\nAenean tellus metus, bibendum sed, posuere ac, mattis non, nunc. Nulla facilisi. Vivamus aliquet elit ac nisl. Sed lectus. Phasellus volutpat, metus eget egestas mollis, lacus lacus blandit dui, id egestas quam mauris ut lacus.', NULL, 1, 1, '2020-11-02 09:44:26', '2020-11-09 13:05:40', '2020-11-10 05:01:02'),
(3, 2, 3, 'Fusce ac Cras', 'Maecenas malesuada. Aliquam eu nunc.', 'image', '/2020/11/09/16049293252250.jpg', NULL, '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n[img src=\"16049293982916.jpg\" alt=\"\"]\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.', NULL, 1, 1, '2020-11-03 09:43:57', '2020-11-09 13:42:05', '2020-11-10 07:38:05'),
(4, 2, 2, 'Donec vitae', 'Nullam sagittis. Etiam feugiat lorem non metus.', 'image', '/2020/11/09/16049406771462.jpg', NULL, '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n[img src=\"16049408066154.jpg\" alt=\"\"][img src=\"16049408198216.jpg\" alt=\"\"]\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.[img src=\"16049408508677.jpg\" alt=\"\"][img src=\"16049408638653.jpg\" alt=\"\"]', NULL, 1, 1, '2020-11-04 12:34:26', '2020-11-09 16:02:22', '2020-11-10 07:38:04'),
(5, 2, 4, 'Etiam sollicitudin', 'Nullam sagittis. Etiam feugiat lorem non metus.', 'video', '/2020/11/09/16049384358733.jpg', '{\"https:\\/\\/youtu.be\\/jOSsb2GgcoU\":\"https:\\/\\/www.youtube.com\\/embed\\/jOSsb2GgcoU\"}', '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.', NULL, 1, 1, '2020-11-05 12:34:28', '2020-11-09 16:04:58', '2020-11-10 07:38:06'),
(6, 3, 5, 'Aliquam erat', 'Curabitur nisi. Nam ipsum risus, rutrum vitae, vestibulum eu, molestie vel, lacus.', 'image', '/2020/11/09/16049425032439.jpg', NULL, '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n<div class=\"my-4\">https://www.youtube.com/watch?v=I8Hs1_1WYVg</div>\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n<div class=\"clearfix\"></div>\r\n<div class=\"d-flex flex-wrap\">\r\n<div class=\"w-50 p-2 p-md-3\">[img src=\"16049423821095.jpg\" alt=\"\"]</div>\r\n<div class=\"w-50 p-2 p-md-3\">[img src=\"16049424001090.jpg\" alt=\"\"]</div>\r\n<div class=\"w-50 p-2 p-md-3\">[img src=\"16049424217224.jpg\" alt=\"\"]</div>\r\n<div class=\"w-50 p-2 p-md-3\">[img src=\"16049429666856.jpeg\" alt=\"\"]</div>\r\n</div>\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.', '{\"https:\\/\\/www.youtube.com\\/watch?v=I8Hs1_1WYVg\":\"https:\\/\\/www.youtube.com\\/embed\\/I8Hs1_1WYVg\"}', 1, 1, '2020-11-06 13:54:59', '2020-11-09 17:21:43', '2020-11-10 17:58:28'),
(7, 3, 6, 'Vivamus aliquet', 'Nullam tincidunt adipiscing enim. Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam.', 'image', '/2020/11/09/16049442527301.jpg', NULL, '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n<div class=\"my-4\">https://vimeo.com/35244257</div>\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.', '{\"https:\\/\\/vimeo.com\\/35244257\":\"https:\\/\\/player.vimeo.com\\/video\\/35244257\"}', 1, 1, '2020-11-07 13:54:56', '2020-11-09 17:50:53', '2020-11-10 07:38:07'),
(8, 4, 1, 'Vestibulum rutrum mi', 'Cras ultricies mi eu turpis hendrerit fringilla. Vivamus quis mi.', 'image', '/2020/11/10/16049857977384.jpg', NULL, '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n<div class=\"d-flex flex-wrap\">\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049871995498.jpg\" alt=\"Image 1\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049872153575.jpg\" alt=\"Image 2\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049872321009.jpg\" alt=\"Image 3\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049872437009.jpg\" alt=\"Image 4\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049872606254.jpg\" alt=\"Image 5\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049872768519.jpg\" alt=\"Image 6\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049872906622.jpg\" alt=\"Image 7\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049872994382.jpg\" alt=\"Image 8\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049873089128.jpg\" alt=\"Image 9\"]</span>\r\n<span class=\"w-50 p-2 p-md-3\">[img src=\"16049878493943.jpg\" alt=\"Image 10\"]</span>\r\n</div>\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.', NULL, 1, 1, '2020-11-08 03:10:30', '2020-11-10 05:12:09', '2020-11-10 07:38:12'),
(9, 4, 3, 'Phasellus gravida semper', 'Sed augue ipsum, egestas nec, vestibulum et, malesuada adipiscing, dui. Mauris sollicitudin fermentum libero.', 'image', '/2020/11/10/16049921887048.jpg', NULL, '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n<div class=\"my-4\">https://vimeo.com/11663853</div>\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.', '{\"https:\\/\\/vimeo.com\\/11663853\":\"https:\\/\\/player.vimeo.com\\/video\\/11663853\"}', 1, 1, '2020-11-09 03:10:29', '2020-11-10 07:09:49', '2020-11-10 07:38:12'),
(10, 1, 3, 'Vestibulum eu odio', 'Fusce commodo aliquam arcu. Nullam tincidunt adipiscing enim.', 'video', NULL, '{\"https:\\/\\/www.youtube.com\\/watch?v=mTtIAQOodak\":\"https:\\/\\/www.youtube.com\\/embed\\/mTtIAQOodak\"}', '<h2>Lorem ipsum</h2>\r\nVestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Sed cursus turpis vitae tortor. In ut quam vitae odio lacinia tincidunt. Donec vitae sapien ut libero venenatis faucibus. Donec mi odio, faucibus at, scelerisque quis, convallis in, nisi.\r\n<div class=\"my-4\">[img src=\"16049926544431.jpg\" alt=\"Bear\"]</div>\r\n<ul>\r\n  <li>Suspendisse</li>\r\n  <li>Donec vitae</li>\r\n  <li>Fusce</li>\r\n  <li>Pellentesque</li>\r\n  <li>Etiam</li>\r\n</ul>\r\n<em>Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Vestibulum purus quam, scelerisque ut, mollis sed, nonummy id, metus. Suspendisse potenti. Maecenas vestibulum mollis diam. Curabitur at lacus ac velit ornare lobortis.</em>\r\n\r\n<h3>Cras proin</h3>\r\nPraesent venenatis metus at tortor pulvinar varius. Proin faucibus arcu quis ante. Etiam iaculis nunc ac metus. Praesent ac sem eget est egestas volutpat. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci.\r\n\r\n<blockquote><em>Donec vitae orci sed dolor rutrum auctor. Ut id nisl quis enim dignissim sagittis. Fusce pharetra convallis urna.<strong>Etiam feugiat</strong></em></blockquote>\r\n<div class=\"my-4\">https://www.youtube.com/watch?v=47_SOihuJ1c</div>\r\n<h4>Donec Vitae</h4>\r\nDonec sodales sagittis magna. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc, eu sollicitudin urna dolor sagittis lacus. Nulla facilisi. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Nulla neque dolor, sagittis eget, iaculis quis, molestie non, velit. Aliquam erat volutpat.\r\n', '{\"https:\\/\\/www.youtube.com\\/watch?v=47_SOihuJ1c\":\"https:\\/\\/www.youtube.com\\/embed\\/47_SOihuJ1c\"}', 1, 1, '2020-11-10 03:30:25', '2020-11-10 07:30:25', '2020-11-10 18:00:31');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_type` enum('simple','author','admin') NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_confirmed` tinyint(4) NOT NULL DEFAULT 0,
  `about_image` varchar(255) DEFAULT NULL,
  `about_text` varchar(255) DEFAULT NULL,
  `about_appear` tinyint(4) NOT NULL DEFAULT 0,
  `hashed_password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_reset_hash` varchar(64) DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `email_confirm_hash` varchar(64) DEFAULT NULL,
  `email_confirm_expires_at` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `user_type`, `username`, `email`, `email_confirmed`, `about_image`, `about_text`, `about_appear`, `hashed_password`, `created_at`, `password_reset_hash`, `password_reset_expires_at`, `email_confirm_hash`, `email_confirm_expires_at`) VALUES
(1, 'admin', 'dale', 'dale@mail.io', 1, NULL, NULL, 0, '$2y$10$cz3tpa/XQ70H8Xz4Om.fE.gYxFDVNbGg/6FcT8xBDeT6A41tiiOqm', '2020-11-09 11:19:06', '0473ba4f910e9f9b2d56c6ac4b412c10cf7d058afea0e1cc439d9bcd38a124c0', '2021-12-28 05:18:20', NULL, NULL),
(2, 'author', 'kate', 'kate@mail.io', 1, NULL, NULL, 0, '$2y$10$yTFKXvKdVOHng0NBqXbZlenmllDCywHPKAAWwobjn/J2qakd/hxUa', '2020-11-09 13:35:01', NULL, NULL, NULL, NULL),
(3, 'author', 'rony', 'rony@mail.io', 1, NULL, NULL, 0, '$2y$10$MZrGPW0fVoAoH9QYnbVFQeBnFsKBa8xNPDnmDOV703xYunkILGyGG', '2020-11-09 17:13:36', NULL, NULL, NULL, NULL),
(4, 'author', 'anna', 'anna@mail.io', 1, NULL, NULL, 0, '$2y$10$2kNKFarPI9qhM7x2YB7/9uaY2V26aLhgajqWrpHwe1/l50a1MyXbO', '2020-11-10 05:04:22', NULL, NULL, NULL, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_name` (`name`);

--
-- Индексы таблицы `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `likes_ibfk_3` (`post_id`),
  ADD KEY `user_id` (`user_id`,`post_id`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_topic_id` (`category_id`),
  ADD KEY `index_user_id` (`user_id`) USING BTREE,
  ADD KEY `updated_at` (`updated_at`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `published_at` (`published_at`),
  ADD KEY `title` (`title`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `password_reset_hash` (`password_reset_hash`),
  ADD UNIQUE KEY `email_confirm_hash` (`email_confirm_hash`),
  ADD KEY `created_at` (`created_at`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `likes_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
