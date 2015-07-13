<?php
/*
Plugin Name: Track Zilla Old
Plugin URI: http://trackzilla.link/
Version: 1.0
Author: ALVIK Solutions
Description: Track Zilla Old
*/

//error_reporting (E_ALL);
 function refer_zilla_strip_data($text)
{
    $quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "?", "!" );
    $goodquotes = array ("-", "+", "#" );
    $repquotes = array ("\-", "\+", "\#" );
    $text = trim( strip_tags( $text ) );
    $text = str_replace( $quotes, '', $text );
    //$text = str_replace( $goodquotes, $repquotes, $text );
    //$text = ereg_replace(" +", " ", $text);

    return $text;
}

function refer_zilla_ref()
{  if (isset($_SESSION['ref']))
  {
    $ref=$_SESSION['ref'];
  } else {$ref="Direct";}
  return addslashes($ref);
}
function refer_zilla_refpage()
{
  if (isset($_SESSION['inpage']))
  {
    $ref=$_SESSION['inpage'];
  } else {$ref="Unknown";}
  return addslashes($ref);
}

function refer_zilla ($text, $click_id=-1)
{
  $ref=refer_zilla_ref();
  include_once("tabgeo_country_v4.php");
  $ip = $_SERVER['REMOTE_ADDR'];
  $country_code = tabgeo_country_v4($ip);

  $text=str_ireplace("{REFER}",$ref,$text);
  $text=str_ireplace("{COUNTRY}",$country_code,$text);
  $text=str_ireplace("{IP}",$ip,$text);
  $text=str_ireplace("{INPAGE}",refer_zilla_refpage(),$text);
  $text=str_ireplace("{CLICK-ID}",$click_id,$text);

  return $text;
}
function ReferZilla_Redirect ($l, $rt)
{  switch ($rt) {
    case 0: {    	      header("HTTP/1.1 301 Moved Permanently");
    	      header("Location: ".$l);
    	      break;
    	    }
    case 1: {
    	      header("HTTP/1.1 302 Found");
    	      header("Location: ".$l);
    	      break;
    	    }
    case 2: {
    	      header("HTTP/1.1 307 Temporary Redirect");
    	      header("Location: ".$l);
    	      break;
    	    }
    default: header('location: '.$l);
  }

   die;
}
function MyTest( $query ) {  global $ZillaName,$wpdb,$ReferZillaTable;
  $ip = $_SERVER['REMOTE_ADDR'];
  $agent = $_SERVER['HTTP_USER_AGENT'];
  $ref =refer_zilla_ref();
  include_once("tabgeo_country_v4.php");
  $country_code = tabgeo_country_v4($ip);
  $r='';//print_r($query,true);
  $sql='SELECT id, redirect, redirecttype FROM '.$ReferZillaTable.' where (link="'.$query->request.'")';
  $r.=$sql."\n\r";
  $d=$wpdb->get_results($sql);
  if (isset($d[0])) {
        $id=$d[0]->id;
        $pg=refer_zilla_refpage();
        $wpdb->query('insert into '.$ReferZillaTable.'stat (id_link, cn, ip, ref, agent, page) values ('.$id.',"'.$country_code.'","'.$ip.'","'.$ref.'","'.$agent.'","'.$pg.'")');
        $id_click = intval($wpdb->get_var('SELECT LAST_INSERT_ID() FROM '.$ReferZillaTable.'stat'));
        $l=refer_zilla ($d[0]->redirect,$id_click);
        $sql='SELECT redirect FROM '.$ReferZillaTable.'Ex where (id_link='.$id.' and cn="'.$country_code.'")';
        $r.=$sql."\n\r";
        $d=$wpdb->get_results($sql);
        $r.="\n\r Result:\n\r".print_r($d,true);
        if (isset($d[0])) {
          $l=refer_zilla ($d[0]->redirect);
          $rt=$d[0]->redirecttype;
        }
        //file_put_contents("wp-content/plugins/refer-zilla/file.txt", $r);
        foreach($_GET as $k =>$v)
        {
          $l.='&';
          $l.=$k.'='.$v;
        }
        ReferZilla_Redirect ($l,$rt);
	    die ();
	  }  return $query;

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
    global $ZillaName;
    add_menu_page($ZillaName, $ZillaName, 8, __FILE__, 'mt_toplevel_page');
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
  include_once("refer-zilla-db.php");
  ReferZillaInitTables();
}
global $ZillaName, $ReferZillaTable,$wpdb,$reder_zilla_redirecttypes;
$ZillaName='Track Zilla';
$ReferZillaTable=$wpdb->prefix . "referzilla";;
register_activation_hook( __FILE__, 'ReferZilla_activate' );
add_filter ('the_content','refer_zilla');
add_action( 'parse_request', 'MyTest' );
add_action('admin_menu', 'mt_add_pages');

  $reder_zilla_redirecttypes=array ("301","302","307");

  session_start();
  if (!isset($_SESSION['inpage']))
  {   $_SESSION['inpage']=refer_zilla_strip_data($_SERVER["REQUEST_URI"]);
   //file_put_contents("wp-content/plugins/refer-zilla/in.txt", $_SESSION['inpage']."\n", FILE_APPEND);
  }
  if (!isset($_SESSION['ref']))
  {
   $r="Direct";
   if (isset($_SERVER["HTTP_REFERER"])) {$r=$_SERVER["HTTP_REFERER"];}
   $_SESSION['ref']=refer_zilla_strip_data($r);
  }


?>