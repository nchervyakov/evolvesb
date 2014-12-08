ALTER TABLE `tbl_news`
ADD `created_on` datetime NULL,
ADD `updated_on` timestamp NULL AFTER `created_on`,
COMMENT='';

ALTER TABLE `tbl_news` ADD UNIQUE `hurl` (`hurl`);