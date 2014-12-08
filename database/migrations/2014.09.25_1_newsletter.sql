CREATE TABLE `tbl_newsletter_templates` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `type` varchar(16) NOT NULL DEFAULT 'html',
  `created_on` datetime NULL,
  `updated_on` timestamp NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

CREATE TABLE `tbl_newsletters` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `template_id` int(11) NOT NULL,
  `subject` varchar(255) NULL,
  `status` varchar(16) NOT NULL DEFAULT 'new',
  `subscriber_ids` text NOT NULL DEFAULT '',
  `send_to_all` tinyint NOT NULL DEFAULT '1',
  `content` text NOT NULL DEFAULT '',
  `recipient_count` int NOT NULL DEFAULT '0',
  `completed` tinyint NOT NULL DEFAULT '0',
  `completed_count` tinyint NOT NULL,
  `created_on` datetime NULL,
  `updated_on` timestamp NULL,
  `completed_on` datetime NULL,
  FOREIGN KEY (`template_id`) REFERENCES `tbl_newsletter_templates` (`id`) ON DELETE CASCADE
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

CREATE TABLE `tbl_newsletter_instances` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `newsletter_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `completed` tinyint NOT NULL DEFAULT '0',
  FOREIGN KEY (`newsletter_id`) REFERENCES `tbl_newsletters` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subscriber_id`) REFERENCES `tbl_newsletter_signups` (`id`) ON DELETE CASCADE
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';