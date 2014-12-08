CREATE TABLE `tbl_newsletter_signups` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NOT NULL,
  `unsubscribe_token` varchar(64) NOT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` timestamp NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_newsletter_signups` ADD UNIQUE `email` (`email`);

ALTER TABLE `tbl_newsletter_signups` ADD INDEX `unsubscribe_token` (`unsubscribe_token`);