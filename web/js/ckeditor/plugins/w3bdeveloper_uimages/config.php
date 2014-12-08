<?php
ini_set('display_errors', '0');
$dbConfig = include __DIR__ . '/../../../../../assets/config/db.php';
$dbConfig = $dbConfig['default'] ?: [];
$uploadDir = '/upload/'; # the link for the plugin,add slash after
$dbHost = $dbConfig['host'];
$dbName = $dbConfig['db'];
$dbUser = $dbConfig['user'];
$dbPass = $dbConfig['password'];

$db = new PDO('mysql:host='.$dbHost.';dbname='.$dbName.';charset=utf8',$dbUser, $dbPass);
$db->query("
			CREATE TABLE IF NOT EXISTS `tbl_w3bdeveloper_media` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type` varchar(255) NOT NULL,
			  `path` text NOT NULL,
			  `thumbnailPath` text NOT NULL,
			  `fileName` text NOT NULL,
			  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

?>