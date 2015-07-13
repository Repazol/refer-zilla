<?php

function ReferZillaInitdb1()
{  global $ReferZillaTable,$wpdb;
  $ReferZillaTableEx=$ReferZillaTable.'Ex';
  $ReferZillaTableStat=$ReferZillaTable.'stat';
  if($wpdb->get_var("SHOW TABLES LIKE '$ReferZillaTable'") != $ReferZillaTable) {
  	$sql='CREATE TABLE `'.$ReferZillaTable.'` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`link` VARCHAR(200) NULL DEFAULT NULL,
	`redirect` TEXT NULL,
	`redirecttype` INT(11) NOT NULL DEFAULT "0",
	`taget` VARCHAR(50) NOT NULL DEFAULT "",
	PRIMARY KEY (`id`),
	UNIQUE INDEX `Ind2` (`link`)
)';

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   }

  if($wpdb->get_var("SHOW TABLES LIKE '$ReferZillaTableEx'") != $ReferZillaTableEx) {
  	$sql='CREATE TABLE `'.$ReferZillaTableEx.'` (
	`id` BIGINT NULL AUTO_INCREMENT,
	`id_link` BIGINT NOT NULL DEFAULT "-1",
	`cn` CHAR(2) NOT NULL DEFAULT "--",
	`redirect` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `idl` (`id_link`))';
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   }

  if($wpdb->get_var("SHOW TABLES LIKE '$ReferZillaTableStat'") != $ReferZillaTableStat) {
  	$sql='CREATE TABLE `'.$ReferZillaTableStat.'` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`dt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`id_link` BIGINT(20) NOT NULL DEFAULT "-1",
	`cn` CHAR(2) NOT NULL DEFAULT "--",
	`ip` VARCHAR(50) NOT NULL DEFAULT "0.0.0.0",
	`ref` VARCHAR(150) NOT NULL DEFAULT "Direct",
	`agent` VARCHAR(100) NULL DEFAULT NULL,
	`page` VARCHAR(250) NOT NULL DEFAULT "",
	PRIMARY KEY (`id`),
	INDEX `dat` (`dt`),
	INDEX `idl` (`id_link`)) AUTO_INCREMENT=100';
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   }

   add_option( "RefZilla_db_version", "1.0" );
   return "1.0"	;
}
function ReferZillaInitTables ()

{  $ver=get_option("RefZilla_db_version","");
  if ($ver==""||$ver=="1.0") {$ver=ReferZillaInitdb1();}



}

?>