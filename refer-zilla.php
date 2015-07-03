<?php
/*
Plugin Name: Refer Zilla
Plugin URI: http://www.yourpluginurlhere.com/
Version: 1.0
Author: Repa
Description: Refer Zilla
*/

 function refer_zilla_strip_data($text)
{
    $quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "?", "!" );
    $goodquotes = array ("-", "+", "#" );
    $repquotes = array ("\-", "\+", "\#" );
    $text = trim( strip_tags( $text ) );
    $text = str_replace( $quotes, '', $text );
    $text = str_replace( $goodquotes, $repquotes, $text );
    $text = ereg_replace(" +", " ", $text);

    return $text;
}

function refer_zilla_ref()
{  if (isset($_COOKIE["r"]))
  {
    $ref=$_COOKIE["r"];
  } else {$ref="Direct";}
  return addslashes($ref);

}
function refer_zilla ($text)
{
  $ref=refer_zilla_ref();
  include_once("tabgeo_country_v4.php");
  $ip = $_SERVER['REMOTE_ADDR'];
  $country_code = tabgeo_country_v4($ip);

  $text=str_ireplace("{REFER}",$ref,$text);
  $text=str_ireplace("{COUNTRY}",$country_code,$text);
  $text=str_ireplace("{IP}",$ip,$text);
  return $text;
}
function MyTest( $query ) {  global $ZillaName,$wpdb,$ReferZillaTable;
  $ip = $_SERVER['REMOTE_ADDR'];
  $agent = $_SERVER['HTTP_USER_AGENT'];
  $ref =refer_zilla_ref();
  include_once("tabgeo_country_v4.php");
  $country_code = tabgeo_country_v4($ip);
  $r='';//print_r($query,true);
  $sql='SELECT id, redirect FROM '.$ReferZillaTable.' where (link="'.$query->request.'")';
  $r.=$sql."\n\r";
  $d=$wpdb->get_results($sql);
  if (isset($d[0])) {
        $id=$d[0]->id;
        $wpdb->query('insert into '.$ReferZillaTable.'stat (id_link, cn, ip, ref, agent) values ('.$id.',"'.$country_code.'","'.$ip.'","'.$ref.'","'.$agent.'")');
        $r.=print_r($d,true)."\n\r";
        //file_put_contents("wp-content/plugins/refer-zilla/fileEx.txt", print_r($d,true));
        $l=refer_zilla ($d[0]->redirect);
        $sql='SELECT redirect FROM '.$ReferZillaTable.'Ex where (id_link='.$id.' and cn="'.$country_code.'")';
        $r.=$sql."\n\r";
        $d=$wpdb->get_results($sql);
        $r.="\n\r Result:\n\r".print_r($d,true);
        if (isset($d[0])) {
          $l=refer_zilla ($d[0]->redirect);
        }
        file_put_contents("wp-content/plugins/refer-zilla/file.txt", $r);
        foreach($_GET as $k =>$v)
        {
          $l.='&';
          $l.=$k.'='.$v;
        }

	    header('location: '.$l);
	    die ();
	  }    return $query;
}

function mt_options_page() {	global $ZillaName;
	include_once("refer-zilla-manager.php");
	$r=ReferZillaManager();
    echo $r;
}
function mt_manage_page() {	global $ZillaName;
    echo "<h2>$ZillaName Manage</h2>";
}
function mt_toplevel_page() {	global $ZillaName;
	include_once("refer-zilla-manager.php");
	$r=ReferZillaManager();
    echo $r;
}
function mt_manage_stat() {
	global $ZillaName;
	include_once("refer-zilla-statistic.php");
	$r=refer_zilla_stat();
    echo $r;
}

function mt_add_pages() {
    // Add a new submenu under Options:
    //add_options_page('Refer Zilla', 'Refer Zilla', 8, 'testoptions', 'mt_options_page');
    //add_management_page('Refer Zilla Manage', 'Refer Zilla Manage', 8, 'testmanage', 'mt_manage_page');
    add_menu_page('Refer Zilla', 'Refer Zilla', 8, __FILE__, 'mt_toplevel_page');
    add_submenu_page(__FILE__, 'Statistics', 'Statistics', 8, 'refer-zillla-statistic', 'mt_manage_stat');
    //add_submenu_page( 'Refer Zilla', 'Statistics', 'Statistics', 8, __FILE__, 'mt_manage_stat' );


    // Add a new submenu under Manage:
    //add_management_page('Test Manage', 'Test Manage', 8, 'testmanage', 'mt_manage_page');
}
function ReferZillaCountry ()
{include("tabgeo_country_v4.php");
$ip = $_SERVER['REMOTE_ADDR'];
$country_code = tabgeo_country_v4($ip);
return $country_code;
}

function ReferZilla_activate() {
  global $ReferZillaTable,$wpdb;
  $ReferZillaTableEx=$ReferZillaTable.'Ex';
  $ReferZillaTableStat=$ReferZillaTable.'stat';
  if($wpdb->get_var("SHOW TABLES LIKE '$ReferZillaTable'") != $ReferZillaTable) {  	$sql='CREATE TABLE `'.$ReferZillaTable.'` (
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
	PRIMARY KEY (`id`),
	INDEX `dat` (`dt`),
	INDEX `idl` (`id_link`)) AUTO_INCREMENT=100';
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   }

   add_option( "RefZilla_db_version", "1.0" );
}
global $ZillaName, $ReferZillaTable,$wpdb;
$ZillaName='Refer Zilla';
$ReferZillaTable=$wpdb->prefix . "referzilla";;
register_activation_hook( __FILE__, 'ReferZilla_activate' );
add_filter ('the_content','refer_zilla');
add_action( 'parse_request', 'MyTest' );
add_action('admin_menu', 'mt_add_pages');

 if (!isset($_COOKIE["r"]))
 {
   if (isset($_SERVER["HTTP_REFERER"])) {
     $r=$_SERVER["HTTP_REFERER"];
   } else   {$r="Direct";}
   $r=refer_zilla_strip_data(str_replace("http://","",$r));
   setcookie("r",$r);
 }

?>