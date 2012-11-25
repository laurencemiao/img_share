-- --------------------------------------------------------

--
-- 表的结构 `gallery`
--

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `desc` text,
  `filename` varchar(255) NOT NULL,
  `filesize` int(11) DEFAULT NULL,
  `store_id` varchar(32) CHARACTER SET ascii NOT NULL,
  `store_thumb_id` varchar(32) DEFAULT NULL,
  `change_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `upload`
--

DROP TABLE IF EXISTS `upload`;
CREATE TABLE IF NOT EXISTS `upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` varchar(32) CHARACTER SET ascii NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
