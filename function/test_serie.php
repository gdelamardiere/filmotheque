<?php



//require_once("include.php");

function return_image($str){
	
	$tab=array();
	$lines = array(); 
	$lines = explode('<td class="odd"', $str); 
	$tab=explode('<a href', $lines[3]);
	$temp=preg_replace("#\>([0-9]+)#",'$1',$tab[0]);
	echo $temp;		
}








include('base.php'); 
mysql_select_db($database_base, $base);

$query_Recordset1 = "SELECT titre_original FROM serie where id=".$_GET['id']."  ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
	$name=str_replace(" ","+",$row_Recordset1['titre_original']);
	$ch = curl_init(); 
$timeout = 5; // set to zero for no timeout 
curl_setopt ($ch, CURLOPT_URL, 'http://thetvdb.com/?string='.$name.'&searchseriesid=&tab=listseries&function=Search'); 
//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
$file_contents = curl_exec($ch); 
curl_close($ch); 

if( strstr($file_contents, 'odd')){
	return_image($file_contents);
	
}
else{
	echo "pas trouve";	
	
	
}


//http://thetvdb.com/banners/fanart/original/80348-1.jpg

?>


