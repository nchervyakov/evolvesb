CREATE TABLE `tbl_pages` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `alias` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `tag` varchar(32) NOT NULL DEFAULT '',
  `meta_keywords` varchar(255) NOT NULL DEFAULT '',
  `meta_title` varchar(255) NOT NULL DEFAULT '',
  `meta_description` varchar(255) NOT NULL DEFAULT '',
  `created_on` datetime NULL,
  `modified_on` timestamp NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_pages` ADD UNIQUE `alias` (`alias`);