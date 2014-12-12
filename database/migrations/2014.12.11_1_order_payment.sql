ALTER TABLE `tbl_orders` ADD `amount` decimal NOT NULL DEFAULT '0', COMMENT='';
ALTER TABLE `tbl_orders` ADD `uid` varchar(20) NOT NULL, COMMENT='';
ALTER TABLE `tbl_orders` ADD `success_notified` tinyint(1) unsigned NOT NULL DEFAULT '0', COMMENT='';

CREATE TABLE `tbl_payments` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_id` int(11) unsigned NULL,
  `order_number` int unsigned NOT NULL,
  `amount` decimal(10, 2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `tbl_orders` (`id`) ON DELETE RESTRICT
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_orders`
ADD `payment_id` int(11) NULL,
ADD FOREIGN KEY (`payment_id`) REFERENCES `tbl_payments` (`id`) ON DELETE SET NULL,
COMMENT='';

ALTER TABLE `tbl_payments`
ADD `type` varchar(16) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'immediate',
ADD `status` varchar(16) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'new' AFTER `type`,
COMMENT='';

CREATE TABLE `tbl_payment_operations` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `payment_id` int(11) NOT NULL,
  `transaction_type` int unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `order` varchar(20) NOT NULL,
  `description` varchar(80) NOT NULL,
  `rrn` varchar(12) NULL,
  `merchant_name` varchar(80) NOT NULL,
  `merchant_url` varchar(250) NOT NULL,
  `merchant_gmt` varchar(5) NOT NULL,
  `country` varchar(2) NOT NULL,
  `merchant` varchar(16) NOT NULL,
  `terminal` varchar(8) NOT NULL,
  `email` varchar(80) NOT NULL,
  `back_reference` varchar(250) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'new',
  `timestamp` varchar(14) NOT NULL,
  `nonce` varchar(64) NOT NULL,
  FOREIGN KEY (`payment_id`) REFERENCES `tbl_payment` (`payID`) ON DELETE RESTRICT
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

ALTER TABLE `tbl_payments`
ADD `payment_operation_id` int(11) NULL,
ADD `auth_operation_id` int(11) NULL AFTER `payment_operation_id`,
ADD `confirm_operation_id` int(11) NULL AFTER `auth_operation_id`,
ADD `cancel_operation_id` int(11) NULL AFTER `confirm_operation_id`,
ADD `refund_operation_id` int(11) NULL AFTER `cancel_operation_id`,
ADD FOREIGN KEY (`payment_operation_id`) REFERENCES `tbl_payment_operations` (`id`) ON DELETE SET NULL,
ADD FOREIGN KEY (`auth_operation_id`) REFERENCES `tbl_payment_operations` (`id`) ON DELETE SET NULL,
ADD FOREIGN KEY (`confirm_operation_id`) REFERENCES `tbl_payment_operations` (`id`) ON DELETE SET NULL,
ADD FOREIGN KEY (`cancel_operation_id`) REFERENCES `tbl_payment_operations` (`id`) ON DELETE SET NULL,
ADD FOREIGN KEY (`refund_operation_id`) REFERENCES `tbl_payment_operations` (`id`) ON DELETE SET NULL,
COMMENT='';

ALTER TABLE `tbl_payment_operations`
DROP FOREIGN KEY `tbl_payment_operations_ibfk_1`,
ADD FOREIGN KEY (`payment_id`) REFERENCES `tbl_payments` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `tbl_payment_operations`
ADD `rc` int NULL AFTER `rrn`,
ADD `action` int NULL AFTER `rc`,
COMMENT='';