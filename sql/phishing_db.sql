-- Import this file in phpMyAdmin to set up the database.
-- Replaces the legacy php_project_db schema (no user accounts).

CREATE DATABASE IF NOT EXISTS `phishing_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `phishing_db`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

CREATE TABLE `urls` (
  `uid` int(11) NOT NULL,
  `url` varchar(200) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `urls` (`uid`, `url`, `type`) VALUES
(1, 'https://www.google.com', -1),
(2, 'https://www.facebook.com', -1),
(3, 'https://www.youtube.com', -1),
(4, 'https://www.instagram.com', -1),
(5, 'https://www.twitter.com', -1),
(6, 'https://www.linkedin.com', -1),
(7, 'https://www.github.com', -1),
(8, 'https://www.stackoverflow.com', -1),
(9, 'https://www.reddit.com', -1),
(10, 'https://www.pinterest.com', -1),
(11, 'https://www.tumblr.com', -1),
(12, 'https://www.reddit.com', -1),
(13, 'https://www.pinterest.com', -1),
(14, 'https://www.tumblr.com', -1),
(15, 'https://www.reddit.com', -1),
(16, 'https://www.pinterest.com', -1),
(17, 'https://www.tumblr.com', -1),
(18, 'https://www.reddit.com', -1),
(19, 'https://www.pinterest.com', -1),
(20, 'https://www.tumblr.com', -1),
(21, 'https://www.reddit.com', -1),
(22, 'https://www.pinterest.com', -1),
(23, 'https://www.tumblr.com', -1),
(24, 'https://www.reddit.com', -1),
(25, 'https://www.pinterest.com', -1),
(26, 'https://www.tumblr.com', -1),
(27, 'https://www.reddit.com', -1),
(28, 'https://www.pinterest.com', -1),
(29, 'https://www.tumblr.com', -1),
(30, 'https://www.reddit.com', -1),
(31, 'https://www.pinterest.com', -1),
(32, 'https://www.tumblr.com', -1),
(33, 'https://www.reddit.com', -1),
(34, 'https://www.pinterest.com', -1),
(35, 'https://www.tumblr.com', -1),
(36, 'https://www.reddit.com', -1),
(37, 'https://www.pinterest.com', -1),
(38, 'https://www.tumblr.com', -1),
(39, 'https://www.reddit.com', -1),
(40, 'https://www.pinterest.com', -1),
(41, 'https://www.tumblr.com', -1),
(42, 'https://www.reddit.com', -1),
(43, 'https://www.pinterest.com', -1),
(44, 'https://www.tumblr.com', -1),
(45, 'https://www.reddit.com', -1),
(46, 'https://www.pinterest.com', -1),
(47, 'https://www.tumblr.com', -1),
(48, 'https://www.reddit.com', -1),
(49, 'https://www.pinterest.com', -1),
(50, 'https://www.tumblr.com', -1);

-- --------------------------------------------------------

CREATE TABLE `user_feedback` (
  `fid` int(11) NOT NULL,
  `rate` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user_feedback` (`fid`, `rate`, `name`, `email`, `comment`) VALUES
(1, 'average', 'sandeep sharma', 'sandip@gmail.com', 'its good.'),
(2, 'good', 'krishna kc', 'krishna.kc@outlook.com', 'I like this system. it helped me a lot.'),
(3, 'good', 'Shilpa Kushwaha', 'shilpaKUsh@gmail.com', 'Does this algorthim work ??????');

-- --------------------------------------------------------

ALTER TABLE `urls`
  ADD PRIMARY KEY (`uid`);

ALTER TABLE `user_feedback`
  ADD PRIMARY KEY (`fid`);

ALTER TABLE `urls`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

ALTER TABLE `user_feedback`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

COMMIT;
