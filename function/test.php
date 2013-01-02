<?php



//require_once("include.php");

function return_image($str){
	
	$tab=array();
	$lines = array(); 
	$lines = explode('carousel_container_style', $str); 
	//print_r($lines);
	//echo $lines[1];
	$tab=explode("href=\"", $lines[1]);
	$lines=array();
	$lines=explode("\"", $tab[1]);
	$temp=$lines[0];
	echo $temp;
	
	
}








include('base.php'); 
mysql_select_db($database_base, $base);

$query_Recordset1 = "SELECT titre_original,annee_production FROM film where id=".$_GET['id']."  ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
	$name=str_replace(" ","+",$row_Recordset1['titre_original']);
	$ch = curl_init(); 
$timeout = 5; // set to zero for no timeout 
curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
$file_contents = curl_exec($ch); 
curl_close($ch); 

if( strstr($file_contents, 'carousel_container_style')){
	return_image($file_contents);
	
}
else{
	$name=$name."+".$row_Recordset1['annee_production'];
	$ch = curl_init(); 
	$timeout = 5; // set to zero for no timeout 
	curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$file_contents2 = curl_exec($ch); 
	curl_close($ch); 
	if( strstr($file_contents2, 'carousel_container_style')){
		return_image($file_contents2);				
	}
	else{
		
		$tab=array();
		$lines = array(); 
		$lines = explode('"result"', $file_contents); 	
		$tab=explode("href=\"", $lines[1]);
		$lines=array();
		$lines=explode('">', $tab[1]);
		$temp=$lines[0];
		
		$ch = curl_init(); 
		$timeout = 5; // set to zero for no timeout 
		curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/'.$temp); 
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$file_contents2 = curl_exec($ch); 
		curl_close($ch); 
		if( strstr($file_contents, 'carousel_container_style')){
			return_image($file_contents);
		
		}
		else{echo "pas trouve";}
		
	}
	
	
	
}






?>


