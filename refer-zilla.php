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

function refer_zilla ($text)
{
  if (isset($_COOKIE["r"]))
  {
    $ref=$_COOKIE["r"];
  } else {$ref="Direct";}  $text=str_replace("%REFER%",$ref,$text);
  return $text;
}
function MyTest( $query ) {  global $ZillaName,$wpdb,$ReferZillaTable;
  $r=print_r($query,true);
  $sql='SELECT redirect FROM '.$ReferZillaTable.' where (link="'.$query->request.'")';
  $r.="\n\r".$sql;
  file_put_contents("file.txt", $r);
  $d=$wpdb->get_results($sql);
  if (isset($d[0])) {
        file_put_contents("fileEx.txt", print_r($d,true));
        $l=refer_zilla ($d[0]->redirect);
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
function mt_add_pages() {
    // Add a new submenu under Options:
    add_options_page('Refer Zilla', 'Refer Zilla', 8, 'testoptions', 'mt_options_page');
    add_management_page('Refer Zilla Manage', 'Refer Zilla Manage', 8, 'testmanage', 'mt_manage_page');
    add_menu_page('Refer Zilla', 'Refer Zilla', 8, __FILE__, 'mt_toplevel_page');

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
	INDEX `idl` (`id_link`)
)';

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