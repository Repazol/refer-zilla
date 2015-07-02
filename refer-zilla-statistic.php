<?php

function GetPostGetParam($v)
{
  if (isset($_GET[$v])) {$id=$_GET[$v];}
    elseif (isset($_POST[$v])) {$id=$_POST[$v];}
       else {$id='';}
 return $id;
}

function refer_zilla_stat_main()
{  global $ZillaName, $wpdb,$ReferZillaTable;
  $r='';
  $ReferZillaTableStat=$ReferZillaTable.'stat';
  $sql="SELECT $ReferZillaTable.ID, $ReferZillaTable.link, $ReferZillaTable.redirect, count($ReferZillaTableStat.id) as cnt
FROM $ReferZillaTable
left outer join $ReferZillaTableStat on $ReferZillaTable.id=$ReferZillaTableStat.id_link group by wp_referzilla.ID";
  $stats = $wpdb->get_results($sql);
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
  $r.='<table>';
  $r.='<tr><th>Link</th><th>Clicks</th></tr>';
  foreach($stats as $stat)
  {
    $l=$_SERVER['PHP_SELF'].'?page=refer-zillla-statistic&do=show-link&id='.$stat->ID;    $r.='<tr><td><a href="'.$l.'">'.$stat->link.'</a></td><td>'.$stat->cnt.'</td></tr>';
  }
  $r.='</table>';


  return $r;
}
function refer_zilla_stat_show($id)
{  global $ZillaName, $wpdb,$ReferZillaTable;
  $r='';
  $ReferZillaTableStat=$ReferZillaTable.'stat';
  $sql="SELECT link, redirect FROM $ReferZillaTable where (id=$id)";
  $dat = $wpdb->get_results($sql);
  $r='<h3>Cliks for `'.$dat[0]->link.'`</h3>'.$dat[0]->redirect.'<hr>';
  $r.='<table>';
  $r.='<tr><th>Id</th><th>Time</th><th>Refer</th><th>Country</th><th>IP</th></tr>';
  $sql="SELECT id, dt, ref, ip, cn FROM $ReferZillaTableStat where (id_link=$id) order by dt desc";
  $stats = $wpdb->get_results($sql);
  foreach($stats as $stat)
  {
    $r.='<tr><td>'.$stat->id.'</td><td>'.$stat->dt.'</td><td>'.$stat->ref.'</td><td>'.$stat->cn.'</td><td>'.$stat->ip.'</td></tr>';
  }
  $r.='</table>';


  return $r;
}
function refer_zilla_stat()
{  global $ZillaName, $wpdb,$ReferZillaTable;
  $r='<h2>'.$ZillaName.' Statistic</h2>';
  $do=GetPostGetParam('do');
  $id=GetPostGetParam('id')+0;
  switch ($do) {
    case 'show-link':	{$r.=refer_zilla_stat_show($id);break;}
    default: $r.=refer_zilla_stat_main();
  }




  return $r;
}

?>