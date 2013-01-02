<?php

require_once('base.php');
mysql_select_db($database_base, $base);
if($_GET['id']!="0"){

	$query_Recordset1 = "SELECT f.titre,f.id FROM film as f,genre_film as g, genre as i where f.id=g.id_film AND i.id=g.id_genre AND g.id_genre='".$_GET['id']."'";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$i=0;
	while($val = mysql_fetch_assoc($Recordset1)){
		
		echo ' <option value="'.$val['id'].'" ';
  if($i==0){echo 'selected="selected"';}
echo '>'.str_replace(":","",utf8_encode($val['titre'])).'</option> ';
		
		$i++;
		
	}
	
}
else{
	$query_Recordset1 = "SELECT f.titre,f.id FROM film as f";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$i=0;
	while($val = mysql_fetch_assoc($Recordset1)){
		
		echo ' <option value="'.$val['id'].'" ';
  if($i==0){echo 'selected="selected"';}
echo '>'.str_replace(":","",utf8_encode($val['titre'])).'</option> ';
		
		$i++;
		
	}
}