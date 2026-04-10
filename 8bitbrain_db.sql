-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2026 at 07:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `8bitbrain_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `is_correct`) VALUES
(1, 1, 'Internet Protocol', 1),
(2, 1, 'Internal Program', 0),
(3, 1, 'Internet Process', 0),
(4, 1, 'Input Protocol', 0),
(5, 2, 'Switch', 0),
(6, 2, 'Router', 1),
(7, 2, 'Hub', 0),
(8, 2, 'Modem', 0),
(9, 3, '21', 0),
(10, 3, '25', 0),
(11, 3, '80', 1),
(12, 3, '443', 0),
(13, 4, 'Bus', 0),
(14, 4, 'Ring', 0),
(15, 4, 'Star', 1),
(16, 4, 'Mesh', 0),
(17, 5, 'Encrypts data', 0),
(18, 5, 'Translates domain names to IP addresses', 1),
(19, 5, 'Sends emails', 0),
(20, 5, 'Stores files', 0),
(21, 6, 'Structured Query Language', 1),
(22, 6, 'Simple Query Logic', 0),
(23, 6, 'System Query Language', 0),
(24, 6, 'Sequential Query Language', 0),
(25, 7, 'INSERT', 0),
(26, 7, 'UPDATE', 0),
(27, 7, 'SELECT', 1),
(28, 7, 'DELETE', 0),
(29, 8, 'A duplicate field', 0),
(30, 8, 'A unique identifier', 1),
(31, 8, 'A foreign field', 0),
(32, 8, 'A temporary value', 0),
(33, 9, 'Hierarchical', 0),
(34, 9, 'Relational', 1),
(35, 9, 'Network', 0),
(36, 9, 'Object-based', 0),
(37, 10, 'Create, Read, Update, Delete', 1),
(38, 10, 'Copy, Run, Update, Delete', 0),
(39, 10, 'Create, Remove, Use, Delete', 0),
(40, 10, 'Code, Run, Upload, Debug', 0),
(41, 11, 'A fixed value', 0),
(42, 11, 'A storage for data', 1),
(43, 11, 'A loop', 0),
(44, 11, 'A function', 0),
(45, 12, '//', 0),
(46, 12, '<!-- -->', 0),
(47, 12, '#', 1),
(48, 12, '**', 0),
(49, 13, 'Loops code', 0),
(50, 13, 'Declares variables', 0),
(51, 13, 'Makes decisions', 1),
(52, 13, 'Ends program', 0),
(53, 14, 'HTML', 0),
(54, 14, 'Phyton', 1),
(55, 14, 'HTTP', 0),
(56, 14, 'DNS', 0),
(57, 15, 'A condition', 0),
(58, 15, 'A repeated execution of code', 1),
(59, 15, 'A variable', 0),
(60, 15, 'A function', 0),
(61, 16, 'A hacking tool', 0),
(62, 16, 'A fake attempt to steal information', 1),
(63, 16, 'A firewall', 0),
(64, 16, 'A virus scanner', 0),
(65, 17, 'Deletes files', 0),
(66, 17, 'Protects against malware', 1),
(67, 17, 'Speeds up internet', 0),
(68, 17, 'Stores passwords', 0),
(69, 18, '123456', 0),
(70, 18, 'password', 0),
(71, 18, 'Mix of letters, numbers, symbols', 1),
(72, 18, 'Your name', 0),
(73, 19, 'Hardware tool', 0),
(74, 19, 'Malicious software', 1),
(75, 19, 'Network device', 0),
(76, 19, 'Programming language', 0),
(77, 20, 'Virtual Private Network', 1),
(78, 20, 'Verified Public Network', 0),
(79, 20, 'Virtual Protected Node', 0),
(80, 20, 'Visual Private Network', 0),
(81, 21, 'Hyper Trainer Marking Language', 0),
(82, 21, 'HyperText Markup Language', 1),
(83, 21, 'HyperText Machine Language', 0),
(84, 21, 'HighText Markup Language', 0),
(85, 22, 'HTML', 0),
(86, 22, 'CSS', 1),
(87, 22, 'Python', 0),
(88, 22, 'SQL', 0),
(89, 23, '<p>', 0),
(90, 23, '<a>', 1),
(91, 23, '<h1>', 0),
(92, 23, '<div>', 0),
(93, 24, 'Structure pages', 0),
(94, 24, 'Style pages', 0),
(95, 24, 'Add interactivity', 1),
(96, 24, 'Store data', 0),
(97, 25, 'SQL', 0),
(98, 25, 'Java', 0),
(99, 25, 'CSS', 1),
(100, 25, 'PHP', 0),
(101, 26, 'Internet Protocol', 1),
(102, 26, 'Internal Program', 0),
(103, 26, 'Internet Process', 0),
(104, 26, 'Input Protocol', 0),
(105, 27, 'Switch', 0),
(106, 27, 'Router', 1),
(107, 27, 'Hub', 0),
(108, 27, 'Modem', 0),
(109, 28, '21', 0),
(110, 28, '25', 0),
(111, 28, '80', 1),
(112, 28, '443', 0),
(113, 29, 'Bus', 0),
(114, 29, 'Ring', 0),
(115, 29, 'Star', 1),
(116, 29, 'Mesh', 0),
(117, 30, 'Encrypts data', 0),
(118, 30, 'Translates domain names to IP addresses', 1),
(119, 30, 'Sends emails', 0),
(120, 30, 'Stores files', 0),
(121, 31, 'Internet Protocol', 1),
(122, 31, 'Internal Program', 0),
(123, 31, 'Internet Process', 0),
(124, 31, 'Input Protocol', 0),
(125, 32, 'Switch', 0),
(126, 32, 'Router', 1),
(127, 32, 'Hub', 0),
(128, 32, 'Modem', 0),
(129, 33, '21', 0),
(130, 33, '25', 0),
(131, 33, '80', 1),
(132, 33, '443', 0),
(133, 34, 'Bus', 0),
(134, 34, 'Ring', 0),
(135, 34, 'Star', 1),
(136, 34, 'Mesh', 0),
(137, 35, 'Encrypts data', 0),
(138, 35, 'Translates domain names to IP addresses', 1),
(139, 35, 'Sends emails', 0),
(140, 35, 'Stores files', 0),
(141, 36, 'Internet Protocol', 1),
(142, 36, 'Internal Program', 0),
(143, 36, 'Internet Process', 0),
(144, 36, 'Input Protocol', 0),
(145, 37, 'Switch', 0),
(146, 37, 'Router', 1),
(147, 37, 'Hub', 0),
(148, 37, 'Modem', 0),
(149, 38, '21', 0),
(150, 38, '25', 0),
(151, 38, '80', 1),
(152, 38, '443', 0),
(153, 39, 'Bus', 0),
(154, 39, 'Ring', 0),
(155, 39, 'Star', 1),
(156, 39, 'Mesh', 0),
(157, 40, 'Encrypts data', 0),
(158, 40, 'Translates domain names to IP addresses', 1),
(159, 40, 'Sends emails', 0),
(160, 40, 'Stores files', 0),
(161, 41, 'Internet Protocol', 1),
(162, 41, 'Internal Program', 0),
(163, 41, 'Internet Process', 0),
(164, 41, 'Input Protocol', 0),
(165, 42, 'Switch', 0),
(166, 42, 'Router', 1),
(167, 42, 'Hub', 0),
(168, 42, 'Modem', 0),
(169, 43, '21', 0),
(170, 43, '25', 0),
(171, 43, '80', 1),
(172, 43, '443', 0),
(173, 44, 'Bus', 0),
(174, 44, 'Ring', 0),
(175, 44, 'Star', 1),
(176, 44, 'Mesh', 0),
(177, 45, 'Encrypts data', 0),
(178, 45, 'Translates domain names to IP addresses', 1),
(179, 45, 'Sends emails', 0),
(180, 45, 'Stores files', 0),
(181, 46, 'Structured Query Language', 1),
(182, 46, 'Simple Query Logic', 0),
(183, 46, 'System Query Language', 0),
(184, 46, 'Sequential Query Language', 0),
(185, 47, 'INSERT', 0),
(186, 47, 'UPDATE', 0),
(187, 47, 'SELECT', 1),
(188, 47, 'DELETE', 0),
(189, 48, 'A duplicate field', 0),
(190, 48, 'A unique identifier', 1),
(191, 48, 'A foreign field', 0),
(192, 48, 'c', 0),
(193, 49, 'Hierarchical', 0),
(194, 49, 'Relational', 1),
(195, 49, 'Network', 0),
(196, 49, 'Object-based', 0),
(197, 50, 'Create, Read, Update, Delete', 1),
(198, 50, 'Copy, Run, Update, Delete', 0),
(199, 50, 'Create, Remove, Use, Delete', 0),
(200, 50, 'Code, Run, Upload, Debug', 0),
(201, 51, 'Structured Query Language', 1),
(202, 51, 'Simple Query Logic', 0),
(203, 51, 'System Query Language', 0),
(204, 51, 'Sequential Query Language', 0),
(205, 52, 'INSERT', 0),
(206, 52, 'UPDATE', 0),
(207, 52, 'SELECT', 1),
(208, 52, 'DELETE', 0),
(209, 53, 'A duplicate field', 0),
(210, 53, 'A unique identifier', 1),
(211, 53, 'A foreign field', 0),
(212, 53, 'A foreign field', 0),
(213, 54, 'Hierarchical', 0),
(214, 54, 'Relational', 1),
(215, 54, 'Network', 0),
(216, 54, 'Object-based', 0),
(217, 55, 'Create, Read, Update, Delete', 1),
(218, 55, 'Copy, Run, Update, Delete', 0),
(219, 55, 'Create, Remove, Use, Delete', 0),
(220, 55, 'Code, Run, Upload, Debug', 0),
(221, 56, 'Structured Query Language', 1),
(222, 56, 'Simple Query Logic', 0),
(223, 56, 'System Query Language', 0),
(224, 56, 'Sequential Query Language', 0),
(225, 57, 'INSERT', 0),
(226, 57, 'UPDATE', 0),
(227, 57, 'SELECT', 1),
(228, 57, 'DELETE', 0),
(229, 58, 'A duplicate field', 0),
(230, 58, 'A unique identifier', 1),
(231, 58, 'A foreign field', 0),
(232, 58, 'A foreign field', 0),
(233, 59, 'Hierarchical', 0),
(234, 59, 'Relational', 1),
(235, 59, 'Network', 0),
(236, 59, 'Object-based', 0),
(237, 60, 'Create, Read, Update, Delete', 1),
(238, 60, 'Copy, Run, Update, Delete', 0),
(239, 60, 'Create, Remove, Use, Delete', 0),
(240, 60, 'Code, Run, Upload, Debug', 0),
(241, 61, 'Structured Query Language', 1),
(242, 61, 'Simple Query Logic', 0),
(243, 61, 'System Query Language', 0),
(244, 61, 'Sequential Query Language', 0),
(245, 62, 'INSERT', 0),
(246, 62, 'UPDATE', 0),
(247, 62, 'SELECT', 1),
(248, 62, 'DELETE', 0),
(249, 63, 'A duplicate field', 0),
(250, 63, 'A unique identifier', 1),
(251, 63, 'A foreign field', 0),
(252, 63, 'A foreign field', 0),
(253, 64, 'Hierarchical', 0),
(254, 64, 'Relational', 1),
(255, 64, 'Network', 0),
(256, 64, 'Object-based', 0),
(257, 65, 'Create, Read, Update, Delete', 1),
(258, 65, 'Copy, Run, Update, Delete', 0),
(259, 65, 'Create, Remove, Use, Delete', 0),
(260, 65, 'Code, Run, Upload, Debug', 0),
(261, 66, 'A fixed value', 0),
(262, 66, 'A storage for data', 1),
(263, 66, 'A loop', 0),
(264, 66, 'A function', 0),
(265, 67, '//', 0),
(266, 67, '<!-- -->', 0),
(267, 67, '#', 1),
(268, 67, '**', 0),
(269, 68, 'Loops code', 0),
(270, 68, 'Declares variables', 0),
(271, 68, 'Makes decisions', 1),
(272, 68, 'Ends program', 0),
(273, 69, 'HTML', 0),
(274, 69, 'Python', 1),
(275, 69, 'HTTP', 0),
(276, 69, 'DNS', 0),
(277, 70, 'A condition', 0),
(278, 70, 'A repeated execution of code', 1),
(279, 70, 'A variable', 0),
(280, 70, 'A function', 0),
(281, 71, 'A fixed value', 0),
(282, 71, 'A storage for data', 1),
(283, 71, 'A loop', 0),
(284, 71, 'A function', 0),
(285, 72, '//', 0),
(286, 72, '<!-- -->', 0),
(287, 72, '#', 1),
(288, 72, '**', 0),
(289, 73, 'Loops code', 0),
(290, 73, 'Declares variables', 0),
(291, 73, 'Makes decisions', 1),
(292, 73, 'Ends program', 0),
(293, 74, 'HTML', 0),
(294, 74, 'Python', 1),
(295, 74, 'HTTP', 0),
(296, 74, 'DNS', 0),
(297, 75, 'A condition', 0),
(298, 75, 'A repeated execution of code', 1),
(299, 75, 'A variable', 0),
(300, 75, 'A function', 0),
(301, 76, 'A fixed value', 0),
(302, 76, 'A storage for data', 1),
(303, 76, 'A loop', 0),
(304, 76, 'A function', 0),
(305, 77, '//', 0),
(306, 77, '<!-- -->', 0),
(307, 77, '#', 1),
(308, 77, '**', 0),
(309, 78, 'Loops code', 0),
(310, 78, 'Declares variables', 0),
(311, 78, 'Makes decisions', 1),
(312, 78, 'Ends program', 0),
(313, 79, 'HTML', 0),
(314, 79, 'Python', 1),
(315, 79, 'HTTP', 0),
(316, 79, 'DNS', 0),
(317, 80, 'A condition', 0),
(318, 80, 'A repeated execution of code', 1),
(319, 80, 'A variable', 0),
(320, 80, 'A function', 0),
(321, 81, 'A fixed value', 0),
(322, 81, 'A storage for data', 1),
(323, 81, 'A loop', 0),
(324, 81, 'A function', 0),
(325, 82, '//', 0),
(326, 82, '<!-- -->', 0),
(327, 82, '#', 1),
(328, 82, '**', 0),
(329, 83, 'Loops code', 0),
(330, 83, 'Declares variables', 0),
(331, 83, 'Makes decisions', 1),
(332, 83, 'Ends program', 0),
(333, 84, 'HTML', 0),
(334, 84, 'Python', 1),
(335, 84, 'HTTP', 0),
(336, 84, 'DNS', 0),
(337, 85, 'A condition', 0),
(338, 85, 'A repeated execution of code', 1),
(339, 85, 'A variable', 0),
(340, 85, 'A function', 0),
(341, 86, 'A hacking tool', 0),
(342, 86, 'A fake attempt to steal information', 1),
(343, 86, 'A firewall', 0),
(344, 86, 'A virus scanner', 0),
(345, 87, 'Deletes files', 0),
(346, 87, 'Protects against malware', 1),
(347, 87, 'Speeds up internet', 0),
(348, 87, 'Stores passwords', 0),
(349, 88, '123456', 0),
(350, 88, 'password', 0),
(351, 88, 'Mix of letters, numbers, symbols', 1),
(352, 88, 'Your name', 0),
(353, 89, 'Hardware tool', 0),
(354, 89, 'Malicious software', 1),
(355, 89, 'Network device', 0),
(356, 89, 'Programming language', 0),
(357, 90, 'Virtual Private Network', 1),
(358, 90, 'Verified Public Network', 0),
(359, 90, 'Virtual Protected Node', 0),
(360, 90, 'Visual Private Network', 0),
(361, 91, 'A hacking tool', 0),
(362, 91, 'A fake attempt to steal information', 1),
(363, 91, 'A firewall', 0),
(364, 91, 'A virus scanner', 0),
(365, 92, 'Deletes files', 0),
(366, 92, 'Protects against malware', 1),
(367, 92, 'Speeds up internet', 0),
(368, 92, 'Stores passwords', 0),
(369, 93, '123456', 0),
(370, 93, 'password', 0),
(371, 93, 'Mix of letters, numbers, symbols', 1),
(372, 93, 'Your name', 0),
(373, 94, 'Hardware tool', 0),
(374, 94, 'Malicious software', 1),
(375, 94, 'Network device', 0),
(376, 94, 'Programming language', 0),
(377, 95, 'Virtual Private Network', 1),
(378, 95, 'Verified Public Network', 0),
(379, 95, 'Virtual Protected Node', 0),
(380, 95, 'Visual Private Network', 0),
(381, 96, 'A hacking tool', 0),
(382, 96, 'A fake attempt to steal information', 1),
(383, 96, 'A firewall', 0),
(384, 96, 'A virus scanner', 0),
(385, 97, 'Deletes files', 0),
(386, 97, 'Protects against malware', 1),
(387, 97, 'Speeds up internet', 0),
(388, 97, 'Stores passwords', 0),
(389, 98, '123456', 0),
(390, 98, 'password', 0),
(391, 98, 'Mix of letters, numbers, symbols', 1),
(392, 98, 'Your name', 0),
(393, 99, 'Hardware tool', 0),
(394, 99, 'Malicious software', 1),
(395, 99, 'Network device', 0),
(396, 99, 'Programming language', 0),
(397, 100, 'Virtual Private Network', 1),
(398, 100, 'Verified Public Network', 0),
(399, 100, 'Virtual Protected Node', 0),
(400, 100, 'Visual Private Network', 0),
(401, 101, 'A hacking tool', 0),
(402, 101, 'A fake attempt to steal information', 1),
(403, 101, 'A firewall', 0),
(404, 101, 'A virus scanner', 0),
(405, 102, 'Deletes files', 0),
(406, 102, 'Protects against malware', 1),
(407, 102, 'Speeds up internet', 0),
(408, 102, 'Stores passwords', 0),
(409, 103, '123456', 0),
(410, 103, 'password', 0),
(411, 103, 'Mix of letters, numbers, symbols', 1),
(412, 103, 'Your name', 0),
(413, 104, 'Hardware tool', 0),
(414, 104, 'Malicious software', 1),
(415, 104, 'Network device', 0),
(416, 104, 'Programming language', 0),
(417, 105, 'Virtual Private Network', 1),
(418, 105, 'Verified Public Network', 0),
(419, 105, 'Virtual Protected Node', 0),
(420, 105, 'Visual Private Network', 0),
(421, 106, 'Hyper Trainer Marking Language', 0),
(422, 106, 'HyperText Markup Language', 1),
(423, 106, 'HyperText Machine Language', 0),
(424, 106, 'HighText Markup Language', 0),
(425, 107, 'HTML', 0),
(426, 107, 'CSS', 1),
(427, 107, 'Python', 0),
(428, 107, 'SQL', 0),
(429, 108, '<p>', 0),
(430, 108, '<a>', 1),
(431, 108, '<h1>', 0),
(432, 108, '<div>', 0),
(433, 109, 'Structure pages', 0),
(434, 109, 'Style pages', 0),
(435, 109, 'Add interactivity', 1),
(436, 109, 'Store data', 0),
(437, 110, 'SQL', 0),
(438, 110, 'Java', 0),
(439, 110, 'CSS', 1),
(440, 110, 'PHP', 0),
(441, 111, 'Hyper Trainer Marking Language', 0),
(442, 111, 'HyperText Markup Language', 1),
(443, 111, 'HyperText Machine Language', 0),
(444, 111, 'HighText Markup Language', 0),
(445, 112, 'HTML', 0),
(446, 112, 'CSS', 1),
(447, 112, 'Python', 0),
(448, 112, 'SQL', 0),
(449, 113, '<p>', 0),
(450, 113, '<a>', 1),
(451, 113, '<h1>', 0),
(452, 113, '<div>', 0),
(453, 114, 'Structure pages', 0),
(454, 114, 'Style pages', 0),
(455, 114, 'Add interactivity', 1),
(456, 114, 'Store data', 0),
(457, 115, 'SQL', 0),
(458, 115, 'Java', 0),
(459, 115, 'CSS', 1),
(460, 115, 'PHP', 0),
(461, 116, 'Hyper Trainer Marking Language', 0),
(462, 116, 'HyperText Markup Language', 1),
(463, 116, 'HyperText Machine Language', 0),
(464, 116, 'HighText Markup Language', 0),
(465, 117, 'HTML', 0),
(466, 117, 'CSS', 1),
(467, 117, 'Python', 0),
(468, 117, 'SQL', 0),
(469, 118, '<p>', 0),
(470, 118, '<a>', 1),
(471, 118, '<h1>', 0),
(472, 118, '<div>', 0),
(473, 119, 'Structure pages', 0),
(474, 119, 'Style pages', 0),
(475, 119, 'Add interactivity', 1),
(476, 119, 'Store data', 0),
(477, 120, 'SQL', 0),
(478, 120, 'Java', 0),
(479, 120, 'CSS', 1),
(480, 120, 'PHP', 0),
(481, 121, 'Hyper Trainer Marking Language', 0),
(482, 121, 'HyperText Markup Language', 1),
(483, 121, 'HyperText Machine Language', 0),
(484, 121, 'HighText Markup Language', 0),
(485, 122, 'HTML', 0),
(486, 122, 'CSS', 1),
(487, 122, 'Python', 0),
(488, 122, 'SQL', 0),
(489, 123, '<p>', 0),
(490, 123, '<a>', 1),
(491, 123, '<h1>', 0),
(492, 123, '<div>', 0),
(493, 124, 'Structure pages', 0),
(494, 124, 'Style pages', 0),
(495, 124, 'Add interactivity', 0),
(496, 124, 'Store data', 1),
(497, 125, 'SQL', 0),
(498, 125, 'Java', 0),
(499, 125, 'CSS', 1),
(500, 125, 'PHP', 0),
(501, 126, 'Emilio Aguinaldo', 1),
(502, 126, 'Manuel Quezon', 0),
(503, 126, 'Jose Rizal', 0),
(504, 126, 'Andres Bonifacio', 0),
(505, 127, '1898', 0),
(506, 127, '1946', 1),
(507, 127, '1935', 0),
(508, 127, '1972', 0),
(509, 128, 'Jose Rizal', 1),
(510, 128, 'Andres Bonifacio', 0),
(511, 128, 'Apolinario Mabini', 0),
(512, 128, 'Lapu-Lapu', 0),
(513, 129, 'Spain', 1),
(514, 129, 'United States', 0),
(515, 129, 'Japan', 0),
(516, 129, 'Portugal', 0),
(517, 130, 'HyperText Markup Language', 1),
(518, 130, 'High Transfer Markup Language', 0),
(519, 130, 'HyperText Machine Language', 0),
(520, 130, 'Home Tool Markup Language', 0),
(521, 131, '//', 0),
(522, 131, '#', 1),
(523, 131, '--', 0),
(524, 131, '**', 0),
(525, 132, 'A fixed constant value', 0),
(526, 132, 'A container that stores data', 1),
(527, 132, 'A type of loop', 0),
(528, 132, 'A function', 0),
(529, 133, 'Stops the program', 0),
(530, 133, 'Runs code once', 0),
(531, 133, 'Repeats code multiple times', 1),
(532, 133, 'Defines a variable', 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `feedback_text` text NOT NULL,
  `feedback_type` enum('general','suggestion','bug','complaint') NOT NULL DEFAULT 'general',
  `rating` tinyint(1) DEFAULT NULL,
  `status` enum('pending','resolved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `quiz_id`, `feedback_text`, `feedback_type`, `rating`, `status`, `created_at`) VALUES
(1, 2, 3, 'why not?', 'suggestion', 4, 'resolved', '2026-04-03 15:41:36');

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `total_points` int(11) NOT NULL DEFAULT 0,
  `total_correct` int(11) NOT NULL DEFAULT 0,
  `total_questions` int(11) NOT NULL DEFAULT 0,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard`
--

INSERT INTO `leaderboard` (`id`, `user_id`, `username`, `fullname`, `total_points`, `total_correct`, `total_questions`, `attempts`, `updated_at`) VALUES
(1, 2, 'Luis Garcia', 'Luis Garcia', 399, 18, 24, 5, '2026-04-05 06:25:33'),
(3, 5, 'LuisMagluyan', 'LuisMagluyan', 30, 0, 5, 1, '2026-04-07 12:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`) VALUES
(1, 1, 'What does IP stand for?'),
(2, 1, 'Which device connects multiple networks together?'),
(3, 1, 'What is the default port for HTTP?'),
(4, 1, 'Which topology uses a central connection point?'),
(5, 1, 'What does DNS do?'),
(6, 2, 'What does SQL stand for?'),
(7, 2, 'Which command is used to retrieve data?'),
(8, 2, 'What is a primary key?'),
(9, 2, 'Which database model uses tables?'),
(10, 2, 'What does CRUD stand for?'),
(11, 3, 'What is a variable?'),
(12, 3, 'Which symbol is used for comments in Python?'),
(13, 3, 'What does “if” statement do?'),
(14, 3, 'Which is a programming language?'),
(15, 3, 'What is a loop?'),
(16, 4, 'What is phishing?'),
(17, 4, 'What does antivirus software do?'),
(18, 4, 'What is a strong password?'),
(19, 4, 'What is malware?'),
(20, 4, 'What does VPN stand for?'),
(21, 5, 'What does HTML stand for?'),
(22, 5, 'Which is used for styling web pages?'),
(23, 5, 'Which tag is used for a hyperlink?'),
(24, 5, 'What does JavaScript do?'),
(25, 5, 'Which is a frontend language?'),
(26, 6, 'What does IP stand for?'),
(27, 6, 'Which device connects multiple networks together?'),
(28, 6, 'What is the default port for HTTP?'),
(29, 6, 'Which topology uses a central connection point?'),
(30, 6, 'What does DNS do?'),
(31, 7, 'What does IP stand for?'),
(32, 7, 'Which device connects multiple networks together?'),
(33, 7, 'What is the default port for HTTP?'),
(34, 7, 'Which topology uses a central connection point?'),
(35, 7, 'What does DNS do?'),
(36, 8, 'What does IP stand for?'),
(37, 8, 'Which device connects multiple networks together?'),
(38, 8, 'What is the default port for HTTP?'),
(39, 8, 'Which topology uses a central connection point?'),
(40, 8, 'What does DNS do?'),
(41, 9, 'What does IP stand for?'),
(42, 9, 'Which device connects multiple networks together?'),
(43, 9, 'What is the default port for HTTP?'),
(44, 9, 'Which topology uses a central connection point?'),
(45, 9, 'What does DNS do?'),
(46, 10, 'What does SQL stand for?'),
(47, 10, 'Which command is used to retrieve data?'),
(48, 10, 'What is a primary key?'),
(49, 10, 'Which database model uses tables?'),
(50, 10, 'What does CRUD stand for?'),
(51, 11, 'What does SQL stand for?'),
(52, 11, 'Which command is used to retrieve data?'),
(53, 11, 'What is a primary key?'),
(54, 11, 'Which database model uses tables?'),
(55, 11, 'What does CRUD stand for?'),
(56, 12, 'What does SQL stand for?'),
(57, 12, 'Which command is used to retrieve data?'),
(58, 12, 'What is a primary key?'),
(59, 12, 'Which database model uses tables?'),
(60, 12, 'What does CRUD stand for?'),
(61, 13, 'What does SQL stand for?'),
(62, 13, 'Which command is used to retrieve data?'),
(63, 13, 'What is a primary key?'),
(64, 13, 'Which database model uses tables?'),
(65, 13, 'What does CRUD stand for?'),
(66, 14, 'What is a variable?'),
(67, 14, 'Which symbol is used for comments in Python?'),
(68, 14, 'What does “if” statement do?'),
(69, 14, 'Which is a programming language?'),
(70, 14, 'What is a loop?'),
(71, 15, 'What is a variable?'),
(72, 15, 'Which symbol is used for comments in Python?'),
(73, 15, 'What does “if” statement do?'),
(74, 15, 'Which is a programming language?'),
(75, 15, 'What is a loop?'),
(76, 16, 'What is a variable?'),
(77, 16, 'Which symbol is used for comments in Python?'),
(78, 16, 'What does “if” statement do?'),
(79, 16, 'Which is a programming language?'),
(80, 16, 'What is a loop?'),
(81, 17, 'What is a variable?'),
(82, 17, 'Which symbol is used for comments in Python?'),
(83, 17, 'What does “if” statement do?'),
(84, 17, 'Which is a programming language?'),
(85, 17, 'What is a loop?'),
(86, 18, 'What is phishing?'),
(87, 18, 'What does antivirus software do?'),
(88, 18, 'What is a strong password?'),
(89, 18, 'What is malware?'),
(90, 18, 'What does VPN stand for?'),
(91, 19, 'What is phishing?'),
(92, 19, 'What does antivirus software do?'),
(93, 19, 'What is a strong password?'),
(94, 19, 'What is malware?'),
(95, 19, 'What does VPN stand for?'),
(96, 20, 'What is phishing?'),
(97, 20, 'What does antivirus software do?'),
(98, 20, 'What is a strong password?'),
(99, 20, 'What is malware?'),
(100, 20, 'What does VPN stand for?'),
(101, 21, 'What is phishing?'),
(102, 21, 'What does antivirus software do?'),
(103, 21, 'What is a strong password?'),
(104, 21, 'What is malware?'),
(105, 21, 'What does VPN stand for?'),
(106, 22, 'What does HTML stand for?'),
(107, 22, 'Which is used for styling web pages?'),
(108, 22, 'Which tag is used for a hyperlink?'),
(109, 22, 'What does JavaScript do?'),
(110, 22, 'Which is a frontend language?'),
(111, 23, 'What does HTML stand for?'),
(112, 23, 'Which is used for styling web pages?'),
(113, 23, 'Which tag is used for a hyperlink?'),
(114, 23, 'What does JavaScript do?'),
(115, 23, 'Which is a frontend language?'),
(116, 24, 'What does HTML stand for?'),
(117, 24, 'Which is used for styling web pages?'),
(118, 24, 'Which tag is used for a hyperlink?'),
(119, 24, 'What does JavaScript do?'),
(120, 24, 'Which is a frontend language?'),
(121, 25, 'What does HTML stand for?'),
(122, 25, 'Which is used for styling web pages?'),
(123, 25, 'Which tag is used for a hyperlink?'),
(124, 25, 'What does JavaScript do?'),
(125, 25, 'Which is a frontend language?'),
(126, 26, 'Who was the first President of the Philippines?'),
(127, 26, 'When did the Philippines gain independence from the US?'),
(128, 26, 'Who is the national hero of the Philippines?'),
(129, 26, 'Which country colonized the Philippines for 333 years?'),
(130, 27, 'What does HTML stand for?'),
(131, 27, 'Which symbol is used for single-line comments in Python?'),
(132, 27, 'What is a variable?'),
(133, 27, 'What does a loop do?');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `difficulty` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `mode` enum('single_player','timed_quiz','ranked_quiz','memory_match','endless_quiz') NOT NULL DEFAULT 'single_player',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `category`, `difficulty`, `mode`, `created_at`) VALUES
(1, 'Networking Basics', 'Networking', 'easy', 'single_player', '2026-04-03 13:34:55'),
(2, 'Database Fundamentals', 'Database', 'medium', 'single_player', '2026-04-03 13:41:05'),
(3, 'Programming Basics', 'Programming Basics', 'easy', 'single_player', '2026-04-03 13:45:16'),
(4, 'Cybersecurity Basics', 'Cybersecurity', 'hard', 'single_player', '2026-04-03 13:51:48'),
(5, 'Web Development Basics', 'Web Development', 'medium', 'single_player', '2026-04-03 13:56:36'),
(6, 'Networking Basics', 'Networking', 'easy', 'timed_quiz', '2026-04-03 14:15:21'),
(7, 'Networking Basics', 'Networking', 'medium', 'ranked_quiz', '2026-04-03 14:15:40'),
(8, 'Networking Basics', 'Networking', 'medium', 'memory_match', '2026-04-03 14:16:05'),
(9, 'Networking Basics', 'Networking', 'hard', 'endless_quiz', '2026-04-03 14:16:15'),
(10, 'Database Fundamentals', 'Database', 'hard', 'endless_quiz', '2026-04-03 14:28:57'),
(11, 'Database Fundamentals', 'Database', 'easy', 'memory_match', '2026-04-03 14:29:07'),
(12, 'Database Fundamentals', 'Database', 'hard', 'ranked_quiz', '2026-04-03 14:29:15'),
(13, 'Database Fundamentals', 'Database', 'medium', 'timed_quiz', '2026-04-03 14:29:27'),
(14, 'Programming Basics', 'Programming', 'hard', 'endless_quiz', '2026-04-03 14:45:48'),
(15, 'Programming Basics', 'Programming', 'medium', 'memory_match', '2026-04-03 14:45:58'),
(16, 'Programming Basics', 'Programming', 'hard', 'ranked_quiz', '2026-04-03 14:46:10'),
(17, 'Programming Basics', 'Programming', 'easy', 'timed_quiz', '2026-04-03 14:46:18'),
(18, 'Cybersecurity Basics', 'Cybersecurity', 'hard', 'endless_quiz', '2026-04-03 14:59:50'),
(19, 'Cybersecurity Basics', 'Cybersecurity', 'easy', 'timed_quiz', '2026-04-03 14:59:57'),
(20, 'Cybersecurity Basics', 'Cybersecurity', 'medium', 'ranked_quiz', '2026-04-03 15:00:03'),
(21, 'Cybersecurity Basics', 'Cybersecurity', 'easy', 'memory_match', '2026-04-03 15:00:09'),
(22, 'Web Development Basics', 'Web Development', 'hard', 'endless_quiz', '2026-04-03 15:14:02'),
(23, 'Web Development Basics', 'Web Development', 'hard', 'memory_match', '2026-04-03 15:14:08'),
(24, 'Web Development Basics', 'Web Development', 'medium', 'ranked_quiz', '2026-04-03 15:14:20'),
(25, 'Web Development Basics', 'Web Development', 'medium', 'timed_quiz', '2026-04-03 15:14:26'),
(26, 'Philippine History', 'History', 'easy', 'single_player', '2026-04-05 06:24:48'),
(27, 'Programming Basics', 'Computer Science', 'medium', 'timed_quiz', '2026-04-05 06:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `mode` enum('single_player','timed_quiz','ranked_quiz','memory_match','endless_quiz') NOT NULL DEFAULT 'single_player',
  `difficulty` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `correct` int(11) NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL DEFAULT 0,
  `time_taken` int(11) NOT NULL DEFAULT 0,
  `points_earned` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `user_id`, `quiz_id`, `mode`, `difficulty`, `correct`, `total`, `time_taken`, `points_earned`, `created_at`) VALUES
(1, 2, 3, 'memory_match', 'easy', 5, 5, 101, 113, '2026-04-03 15:35:45'),
(2, 2, 3, 'memory_match', 'easy', 5, 5, 133, 113, '2026-04-03 15:41:21'),
(5, 2, 3, 'memory_match', 'easy', 5, 5, 53, 113, '2026-04-03 21:39:33'),
(7, 2, 24, 'ranked_quiz', 'medium', 0, 5, 10, 30, '2026-04-04 12:15:22'),
(8, 2, 26, 'single_player', 'easy', 3, 4, 18, 30, '2026-04-05 06:25:33'),
(9, 5, 13, 'timed_quiz', 'medium', 0, 5, 6, 30, '2026-04-07 12:17:26');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_references`
--

CREATE TABLE `quiz_references` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `reference_text` text DEFAULT NULL,
  `reference_url` varchar(500) DEFAULT NULL,
  `reference_type` varchar(50) NOT NULL DEFAULT 'url',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_references`
--

INSERT INTO `quiz_references` (`id`, `quiz_id`, `question_id`, `reference_text`, `reference_url`, `reference_type`, `created_at`) VALUES
(1, 1, NULL, 'Cisco Networking Academy provides beginner-friendly lessons on networking concepts such as IP addressing, DNS, routers, and network topologies.', 'https://www.netacad.com/courses/networking-basics', 'url', '2026-04-03 13:34:55'),
(2, 2, NULL, 'GeeksforGeeks explains core database concepts like SQL, relational databases, primary keys, and CRUD operations with simple examples and practice problems.', 'https://www.geeksforgeeks.org/', 'url', '2026-04-03 13:41:05'),
(3, 3, NULL, 'W3Schools offers easy-to-understand tutorials on programming fundamentals such as variables, loops, conditions, and basic syntax across different languages like Python and JavaScript.', 'https://www.w3schools.com/programming/', 'url', '2026-04-03 13:45:16'),
(4, 4, NULL, 'IBM’s cybersecurity guide introduces essential concepts including malware, phishing, VPNs, and online safety practices, making it ideal for beginners.', 'https://www.ibm.com/think/topics/cybersecurity', 'url', '2026-04-03 13:51:48'),
(5, 5, NULL, 'MDN Web Docs is a trusted resource for learning HTML, CSS, and JavaScript, covering how websites are structured, styled, and made interactive.', 'https://developer.mozilla.org/en-US/docs/Learn_web_development', 'url', '2026-04-03 13:56:36'),
(6, 6, NULL, 'Cisco Networking Academy provides beginner-friendly lessons on networking concepts such as IP addressing, DNS, routers, and network topologies. It is widely used in IT education and certification training.', 'https://www.netacad.com/courses/networking-basics?courseLang=en-US', 'url', '2026-04-03 14:15:21'),
(7, 7, NULL, 'Cisco Networking Academy provides beginner-friendly lessons on networking concepts such as IP addressing, DNS, routers, and network topologies. It is widely used in IT education and certification training.', 'https://www.netacad.com/courses/networking-basics?courseLang=en-US', 'url', '2026-04-03 14:15:40'),
(8, 8, NULL, 'Cisco Networking Academy provides beginner-friendly lessons on networking concepts such as IP addressing, DNS, routers, and network topologies. It is widely used in IT education and certification training.', 'https://www.netacad.com/courses/networking-basics?courseLang=en-US', 'url', '2026-04-03 14:16:05'),
(9, 9, NULL, 'Cisco Networking Academy provides beginner-friendly lessons on networking concepts such as IP addressing, DNS, routers, and network topologies. It is widely used in IT education and certification training.', 'https://www.netacad.com/courses/networking-basics?courseLang=en-US', 'url', '2026-04-03 14:16:15'),
(10, 10, NULL, 'GeeksforGeeks explains core database concepts like SQL, relational databases, primary keys, and CRUD operations with simple examples and practice problems.', 'https://www.geeksforgeeks.org/dbms/dbms/', 'url', '2026-04-03 14:28:57'),
(11, 11, NULL, 'GeeksforGeeks explains core database concepts like SQL, relational databases, primary keys, and CRUD operations with simple examples and practice problems.', 'https://www.geeksforgeeks.org/dbms/dbms/', 'url', '2026-04-03 14:29:07'),
(12, 12, NULL, 'GeeksforGeeks explains core database concepts like SQL, relational databases, primary keys, and CRUD operations with simple examples and practice problems.', 'https://www.geeksforgeeks.org/dbms/dbms/', 'url', '2026-04-03 14:29:15'),
(13, 13, NULL, 'GeeksforGeeks explains core database concepts like SQL, relational databases, primary keys, and CRUD operations with simple examples and practice problems.', 'https://www.geeksforgeeks.org/dbms/dbms/', 'url', '2026-04-03 14:29:27'),
(14, 14, NULL, 'W3Schools offers easy-to-understand tutorials on programming fundamentals such as variables, loops, conditions, and basic syntax across different languages like Python and JavaScript.', 'https://www.w3schools.com/programming/', 'url', '2026-04-03 14:45:48'),
(15, 15, NULL, 'W3Schools offers easy-to-understand tutorials on programming fundamentals such as variables, loops, conditions, and basic syntax across different languages like Python and JavaScript.', 'https://www.w3schools.com/programming/', 'url', '2026-04-03 14:45:58'),
(16, 16, NULL, 'W3Schools offers easy-to-understand tutorials on programming fundamentals such as variables, loops, conditions, and basic syntax across different languages like Python and JavaScript.', 'https://www.w3schools.com/programming/', 'url', '2026-04-03 14:46:10'),
(17, 17, NULL, 'W3Schools offers easy-to-understand tutorials on programming fundamentals such as variables, loops, conditions, and basic syntax across different languages like Python and JavaScript.', 'https://www.w3schools.com/programming/', 'url', '2026-04-03 14:46:18'),
(18, 18, NULL, 'IBM’s cybersecurity guide introduces essential concepts including malware, phishing, VPNs, and online safety practices, making it ideal for beginners.', 'https://www.ibm.com/think/topics/cybersecurity', 'url', '2026-04-03 14:59:50'),
(19, 19, NULL, 'IBM’s cybersecurity guide introduces essential concepts including malware, phishing, VPNs, and online safety practices, making it ideal for beginners.', 'https://www.ibm.com/think/topics/cybersecurity', 'url', '2026-04-03 14:59:57'),
(20, 20, NULL, 'IBM’s cybersecurity guide introduces essential concepts including malware, phishing, VPNs, and online safety practices, making it ideal for beginners.', 'https://www.ibm.com/think/topics/cybersecurity', 'url', '2026-04-03 15:00:03'),
(21, 21, NULL, 'IBM’s cybersecurity guide introduces essential concepts including malware, phishing, VPNs, and online safety practices, making it ideal for beginners.', 'https://www.ibm.com/think/topics/cybersecurity', 'url', '2026-04-03 15:00:09'),
(22, 22, NULL, 'MDN Web Docs is a trusted resource for learning HTML, CSS, and JavaScript, covering how websites are structured, styled, and made interactive.', 'https://developer.mozilla.org/en-US/docs/Learn_web_development', 'url', '2026-04-03 15:14:02'),
(23, 23, NULL, 'MDN Web Docs is a trusted resource for learning HTML, CSS, and JavaScript, covering how websites are structured, styled, and made interactive.', 'https://developer.mozilla.org/en-US/docs/Learn_web_development', 'url', '2026-04-03 15:14:08'),
(24, 24, NULL, 'MDN Web Docs is a trusted resource for learning HTML, CSS, and JavaScript, covering how websites are structured, styled, and made interactive.', 'https://developer.mozilla.org/en-US/docs/Learn_web_development', 'url', '2026-04-03 15:14:20'),
(25, 25, NULL, 'MDN Web Docs is a trusted resource for learning HTML, CSS, and JavaScript, covering how websites are structured, styled, and made interactive.', 'https://developer.mozilla.org/en-US/docs/Learn_web_development', 'url', '2026-04-03 15:14:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `account_type` enum('user','admin') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `username`, `age`, `avatar`, `password`, `account_type`, `status`, `created_at`) VALUES
(1, 'Admin', 'admin@8bitbrain.com', 'admin', 25, NULL, 'admin123', 'admin', 'active', '2026-03-13 04:51:10'),
(2, 'Luis Garcia', 'lg5330359@gmail.com', 'Luis Garcia', 23, 'uploads/avatars/avatar_2_1775485440.png', 'luis123', 'user', 'active', '2026-04-03 15:33:41'),
(5, 'LuisMagluyan', 'luismagluyan@gmail.com', 'LuisMagluyan', 18, NULL, 'luis123', 'user', 'active', '2026-04-06 02:33:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_references`
--
ALTER TABLE `quiz_references`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=533;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `quiz_references`
--
ALTER TABLE `quiz_references`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fb_quiz_fk` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fb_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD CONSTRAINT `leaderboard_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `qa_quiz_fk` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `qa_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_references`
--
ALTER TABLE `quiz_references`
  ADD CONSTRAINT `qr_question_fk` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `qr_quiz_fk` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
