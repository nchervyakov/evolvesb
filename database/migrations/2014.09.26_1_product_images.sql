CREATE TABLE `tbl_product_images` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_name_big` varchar(255) NOT NULL,
  `title` varchar(255) NULL,
  `created_on` datetime NULL,
  `updated_on` timestamp NOT NULL,
  FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`productID`) ON DELETE CASCADE
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_products`
ADD `show_in_root` tinyint NOT NULL DEFAULT '0',
COMMENT='';

ALTER TABLE `tbl_categories`
ADD `sort_order` int(11) NOT NULL DEFAULT '0' AFTER `hidden`,
COMMENT='';