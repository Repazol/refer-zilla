<?php

function GetPostGetParam($v)
{
  if (isset($_GET[$v])) {$id=$_GET[$v];}
    elseif (isset($_POST[$v])) {$id=$_POST[$v];}
       else {$id='';}
 return $id;
}

function ReferZillaManagerList()
{  global $ZillaName, $wpdb,$ReferZillaTable;
  $paged=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
  $r='<a href="'.$paged.'&do=editlink&id=-1">Create link...</a>';
  $ReferZillaTableStat=$ReferZillaTable.'stat';
  $sql="SELECT ID, link, redirect FROM $ReferZillaTable";
  $links = $wpdb->get_results($sql);
  $r.="<h3>Links list...</h3>";
  $r.='<style>
   table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
   }
   th, td {
    padding: 15px;
   }
   </style>
  ';
  $r.='<table style="">';
  $r.='<tr><th>Link</th><th>Redirect to..</th><th>Href</th><th>Statistic</th><th></th></tr>';

  foreach($links as $link)
  {
    //$r.=print_r($link,true);
    $id='id='.$link->ID;
    $l=site_url().'/'.$link->link;
    $s='admin.php?page=refer-zillla-statistic&do=show-link&'.$id;
    $r.='<tr><td><a href="'.$paged.'&do=editlink&'.$id.'">'.$link->link.'</a></td><td>'.$link->redirect.'</td><td>'.$l.'</td><td><a href="'.$s.'">Statistic</a></td><td><a href="'.$paged.'&do=deletelink&'.$id.'">Delete</a></td></tr>';
  }
  $r.='</table>';

  return $r;
}
function ReferZillaCountryListCombo($cn)
{  $RefZilcns=array('AD', 'AE', 'AF', 'AG', 'AI', 'AL', 'AM', 'AO', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AX', 'AZ',
                 'BA', 'BB', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BN', 'BO', 'BQ', 'BR', 'BS',
                 'BT', 'BV', 'BW', 'BY', 'BZ', 'CA', 'CC', 'CD', 'CF', 'CG', 'CH', 'CI', 'CK', 'CL', 'CM', 'CN',
                 'CO', 'CR', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DE', 'DJ', 'DK', 'DM', 'DO', 'DZ', 'EC', 'EE',
                 'EG', 'EH', 'ER', 'ES', 'ET', 'FI', 'FJ', 'FK', 'FM', 'FO', 'FR', 'GA', 'GB', 'GD', 'GE', 'GF',
                 'GG', 'GH', 'GI', 'GL', 'GM', 'GN', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GW', 'GY', 'HK', 'HM',
                 'HN', 'HR', 'HT', 'HU', 'ID', 'IE', 'IL', 'IM', 'IN', 'IO', 'IQ', 'IR', 'IS', 'IT', 'JE', 'JM',
                 'JO', 'JP', 'KE', 'KG', 'KH', 'KI', 'KM', 'KN', 'KP', 'KR', 'KW', 'KY', 'KZ', 'LA', 'LB', 'LC',
                 'LI', 'LK', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY', 'MA', 'MC', 'MD', 'ME', 'MF', 'MG', 'MH', 'MK',
                 'ML', 'MM', 'MN', 'MO', 'MP', 'MQ', 'MR', 'MS', 'MT', 'MU', 'MV', 'MW', 'MX', 'MY', 'MZ', 'NA',
                 'NC', 'NE', 'NF', 'NG', 'NI', 'NL', 'NO', 'NP', 'NR', 'NU', 'NZ', 'OM', 'PA', 'PE', 'PF', 'PG',
                 'PH', 'PK', 'PL', 'PM', 'PN', 'PR', 'PS', 'PT', 'PW', 'PY', 'QA', 'RE', 'RO', 'RS', 'RU', 'RW',
                 'SA', 'SB', 'SC', 'SD', 'SE', 'SG', 'SH', 'SI', 'SJ', 'SK', 'SL', 'SM', 'SN', 'SO', 'SR', 'SS',
                 'ST', 'SV', 'SX', 'SY', 'SZ', 'TC', 'TD', 'TF', 'TG', 'TH', 'TJ', 'TK', 'TL', 'TM', 'TN', 'TO',
                 'TR', 'TT', 'TV', 'TW', 'TZ', 'UA', 'UG', 'UM', 'US', 'UY', 'UZ', 'VA', 'VC', 'VE', 'VG', 'VI',
                 'VN', 'VU', 'WF', 'WS', 'YE', 'YT', 'ZA', 'ZM', 'ZW', 'XA', 'YU', 'CS', 'AN', 'AA', 'EU', 'AP');
  sort($RefZilcns);
  $r.='<select size="1" name="country">';
  foreach($RefZilcns as $k => $c)
  {
    if ($cn==$c) {$s=" selected";} else {$s="";}
    $r.='<option value="'.$c.'"'.$s.'>'.$c.'</option>';
  }
  $r.='</select>';

  return $r;
}
function ReferZillaGenerateCountryForm($id,$id_c, $cn="", $rd="")
{  $paged=$_SERVER['PHP_SELF'].'?refer-zilla/refer-zilla.php';//.$_SERVER['QUERY_STRING'];
  $r='<form action="'.$paged.'" method="get" name="form1">';
  $r.='<input name="do" type="hidden" value="linkpostcountry">';
  $r.='<input name="page" type="hidden" value="refer-zilla/refer-zilla.php">';
  $r.='<input name="id" type="hidden" value="'.$id.'">';
  $r.='<input name="id_c" type="hidden" value="'.$id_c.'">';
  if ($id_c==-1) {$sub='Create';} else {$sub='Update';}
  $r.='<tr><td>'.ReferZillaCountryListCombo($cn).'</td><td><input style="width:100%;" name="redirect" type="text" value="'.$rd.'"></td><td><input type="submit" value="'.$sub.'"></td></form>';

  if ($id_c!=-1) {  	$paged=$_SERVER['PHP_SELF'].'?page=refer-zilla/refer-zilla.php';  	$r.='<td><a href="'.$paged.'&id='.$id.'&id_c='.$id_c.'&cn='.$cn.'&do=deletecnt">Delete</a></td>';
  	}
  $r.='</tr>';



 return $r;
}
function ReferZillaCountryList ($id)
{  global $wpdb,$ReferZillaTable;
  $r='';
  $r.='<h3>Redirect by country</h3>';
  $r.='<table style="width:80%;border: 1px solid #000000;">';
  $r.='<tr><th style="width:80px;">Country</th><th>Redirect</th></tr>';
  if ($id>0)
  {
   $ReferZillaTableEx=$ReferZillaTable.'Ex';
   $d=$wpdb->get_results("SELECT ID, cn, redirect FROM $ReferZillaTableEx where (id_link=$id)");
   foreach($d as $link)
   {
    $r.=ReferZillaGenerateCountryForm($id,$link->ID,$link->cn, $link->redirect);
  }

  	$r.='<tr><td colspan="3"><hr></td></tr>';
  	$r.=ReferZillaGenerateCountryForm($id,-1);


  }
  $r.='</table>';
  return $r;


}
function ReferZillaManagerEdit ($id)
{
  global $wpdb,$ReferZillaTable,$reder_zilla_redirecttypes;  $paged=$_SERVER['PHP_SELF'].'?refer-zilla/refer-zilla.php';//.$_SERVER['QUERY_STRING'];
  if ($id==-1)
    {
      $r='Create new link<br>';
    } else {$r='Edit link<br>';}
  $id=$id+0;
  //$r.='<pre>'.print_r($d,true).'</pre>';
  $dt=array();
  $dt['id']=-1;
  $dt['link']="";
  $dt['redirect']="";
  $dt['rt']=0;
  if ($id>0)
    {      $d=$wpdb->get_results("SELECT ID, link, redirect,redirecttype FROM $ReferZillaTable where (id=$id)");
      if (isset($d[0]))
       {         $dt['id']=$d[0]->ID;
         $dt['link']=$d[0]->link;
         $dt['redirect']=$d[0]->redirect;
         $dt['rt']=$d[0]->redirecttype;
      	}
    }
  //$r.='<pre>'.print_r($dt,true).'</pre>';
  $rt='<select size="1" name="rt">';
  foreach($reder_zilla_redirecttypes as $k =>$v)
  {  	$rt.='<option value="'.$k.'"';
  	if ($dt['rt']==$k) {$rt.=' selected';}
  	$rt.='>'.$v.'</option>';
  }
  $rt.='</select>';

  $r.='<form action="'.$paged.'" method="get" name="form1">';
  $r.='<input name="do" type="hidden" value="linkpost">';
  $r.='<input name="page" type="hidden" value="refer-zilla/refer-zilla.php">';
  $r.='<input name="id" type="hidden" value="'.$dt['id'].'">';
  $r.=wp_nonce_field('update-options');
  $r.='<input type="hidden" name="action" value="update" />';
  $r.='<table style="width:80%;">';
  $r.='<tr><td style="width:80px;">Link:</td><td><input style="width:80%;" name="link" type="text" value="'.$dt['link'].'"></td></tr>';
  $r.='<tr><td>Redirect to:</td><td><input style="width:80%;" name="redirect" type="text" value="'.$dt['redirect'].'"> *Default</td></tr>';
  $r.='<tr><td>Redirect type:</td><td>'.$rt.'</td></tr>';
  $r.='<tr><td><input type="submit" value="Save"></td></form><td><a href="'.$_SERVER['PHP_SELF'].'?page=refer-zilla%2Frefer-zilla.php">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; * Code {REFER} - From where the user came, {COUNTRY} - User country, {IP} - User ip, {INPAGE} - Incoming links, {CLICK-ID} - Unique click ID</td></tr>';
  $r.='</table>';
  $r.=ReferZillaCountryList ($id);
  return $r;
}
function ClearLink($s)
{
   return preg_replace('/(?:[^-a-z0-9]|(?<=-)-+)/i', '', $s);
}
function ReferZillaManagerPost()
{  global $ZillaName, $wpdb,$ReferZillaTable;
  $r='Link saved...<br>';
  $id=GetPostGetParam('id')+0;
  $link=ClearLink(GetPostGetParam('link'));
  $redirect=addslashes(GetPostGetParam('redirect'));
  $rt=GetPostGetParam('rt')+0;
  if ($id==-1)
    {
      $sql='insert into '.$ReferZillaTable.' (link, redirect,redirecttype) values ("'.$link.'","'.$redirect.'",'.$rt.')';
    }  else {        $sql='update '.$ReferZillaTable.' set link="'.$link.'", redirect="'.$redirect.'", redirecttype='.$rt.' where (id='.$id.')';
      }
  $d=$wpdb->query($sql);
  //$r.=$sql.'<br>';
  return $r;
}
function ReferZillaPostCountry($id)
{  global $ZillaName, $wpdb,$ReferZillaTable;
  $r='Country Link saved...<br>';
  $id=GetPostGetParam('id')+0;
  $id_c=GetPostGetParam('id_c')+0;
  $cn=addslashes(GetPostGetParam('country'));
  $redirect=addslashes(GetPostGetParam('redirect'));
  $ReferZillaTableEx=$ReferZillaTable.'Ex';
  if ($id_c==-1)
    {
      $sql='insert into '.$ReferZillaTableEx.' (id_link, cn, redirect) values ('.$id.', "'.$cn.'","'.$redirect.'")';
    }  else {
        $sql='update '.$ReferZillaTableEx.' set cn="'.$cn.'", redirect="'.$redirect.'" where (id='.$id_c.')';
      }
  $d=$wpdb->query($sql);
  //$r.=$sql.'<br>';
  return $r;
}
function ReferZillaManagerDelete ($id)
{  global $wpdb,$ReferZillaTable;
  $r='';
  $paged=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
  $d=$wpdb->get_results("SELECT ID, link, redirect FROM $ReferZillaTable where (id=$id)");
  if (isset($d[0]))
       {
         $dt['link']=$d[0]->link;
         $r.='Delete link "'.$d[0]->link.'"?<br>';
         $paged=str_ireplace('do=deletelink','do=deletelinkok&l='.$d[0]->link,$paged);
         $pagedno=str_ireplace('do=deletelinkok','',$paged);
         $r.='<a href="'.$paged.'"><button>Yes</button></a>&nbsp;&nbsp;<a href="'.$pagedno.'"><button>No</button></a>';
        }

  return $r;
}
function ReferZillaManagerDeleteok ($id)
{
  global $wpdb,$ReferZillaTable;
  $r='Link "'.GetPostGetParam('l').'" deleted.<br>';
  $ReferZillaTableex=$ReferZillaTable.'Ex';
  $wpdb->query("delete FROM $ReferZillaTableex where (id_link=$id)");
  $wpdb->query("delete FROM $ReferZillaTable where (id=$id)");

  return $r;
}

function ReferZillaManagerDelcnt($id)
{  global $ZillaName, $wpdb,$ReferZillaTable;
  $paged=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
  $id_c=GetPostGetParam('id_c')+0;
  $cn=addslashes(GetPostGetParam('cn'));
  $r='Delete country link "'.$cn.'"?<br>';
  $paged=str_ireplace('do=deletecnt','do=deletecntok&cn='.$cn,$paged);
  $pagedno=str_ireplace('do=deletecntok','do=editlink',$paged);
  $r.='<a href="'.$paged.'"><button>Yes</button></a>&nbsp;&nbsp;<a href="'.$pagedno.'"><button>No</button></a>';

  return $r;
}
function ReferZillaManagerDelcntok($id)
{  global $ZillaName, $wpdb,$ReferZillaTable;
  $id_c=GetPostGetParam('id_c')+0;
  $cn=addslashes(GetPostGetParam('cn'));
  $r='Country link "'.$cn.'" deleted<br>';
  $paged=str_ireplace('do=deletecnt','do=deletecntok&cn='.$cn,$paged);
  $pagedno=str_ireplace('do=deletecntok','do=editlink',$paged);

  $ReferZillaTableex=$ReferZillaTable.'Ex';
  $wpdb->query("delete FROM $ReferZillaTableex where (id=$id_c)");

  return $r;
}
function ReferZillaManager ()
{
  global $ZillaName, $wpdb,$ReferZillaTable;
  $r='';
  if (get_option('permalink_structure') == '')
  {
   	$r.='<div id="message" class="error"><font size=+2>To use a plugin, you must enable permalink</font></div>';
  }
  $r.='<h2>'.$ZillaName.' Manager</h2>';
  $do=GetPostGetParam('do');
  $id=GetPostGetParam('id')+0;
  switch ($do) {    case 'linkpostcountry': {$r.=ReferZillaPostCountry($id);$r.=ReferZillaManagerEdit ($id);break;}
    case 'editlink': {$r.=ReferZillaManagerEdit ($id);break;}
    case 'deletelink':	{$r.=ReferZillaManagerDelete ($id);break;}
    case 'deletelinkok':	{$r.=ReferZillaManagerDeleteok ($id).ReferZillaManagerList();break;}
    case 'linkpost':	{$r.=ReferZillaManagerPost().ReferZillaManagerList();break;}
    case 'deletecnt':	{$r.=ReferZillaManagerDelcnt($id);break;}
    case 'deletecntok':	{$r.=ReferZillaManagerDelcntok($id).ReferZillaManagerEdit ($id);;break;}
    default: $r.=ReferZillaManagerList();
  }

  //$r.='Do:'.$do.' id='.$id;
  return $r;
}

?>