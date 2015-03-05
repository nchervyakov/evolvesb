ALTER TABLE `tbl_products`
ADD `max_items_per_order` int unsigned NOT NULL DEFAULT '100',
ADD `status` varchar(32) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'available' AFTER `max_items_per_order`,
COMMENT='';

UPDATE `tbl_products` SET `in_stock` = '1', `enabled` = '1';


# Remove test orders
DELETE `po` FROM `tbl_payment_operations` `po`
  INNER JOIN `tbl_payments` `p` ON `po`.`payment_id` = `p`.`id`
  INNER JOIN `tbl_orders` `o` ON `p`.`order_id` = `o`.`id`
WHERE `o`.`customer_email` = 'nick.chervyakov@gmail.com';

DELETE `p` FROM `tbl_payments` `p`
  INNER JOIN `tbl_orders` `o` ON `p`.`order_id` = `o`.`id`
WHERE `o`.`customer_email` = 'nick.chervyakov@gmail.com';

DELETE FROM `tbl_orders` WHERE `customer_email` = 'nick.chervyakov@gmail.com';
