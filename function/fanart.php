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
	return $temp;
	
	
}




function fanart($titre,$annee){
	$name=str_replace(" ","+",$titre);
	$name=str_replace("-","",$name);
	$name=str_replace("/","+",$name);
	$name=str_replace("++","+",$name);
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
		$temp=return_image($file_contents);
		
	}
	else{
		$name=$name."+".$annee;
		//echo $name;
		$ch = curl_init(); 
		$timeout = 5; // set to zero for no timeout 
		curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$file_contents2 = curl_exec($ch); 
		curl_close($ch); 
		if( strstr($file_contents2, 'carousel_container_style')){
			$temp=return_image($file_contents2);	
						
		}
		else{
			
			if( strstr($file_contents, '"result"')){
				
				$tab=array();
				$lines = array(); 
				$lines = explode('"result"', $file_contents); 	
				$tab=explode("href=\"", $lines[1]);
				$lines=array();
				$lines=explode('">', $tab[1]);
				$temp=$lines[0];
				//echo $temp;
				$ch = curl_init(); 
				$timeout = 5; // set to zero for no timeout 
				curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org'.$temp); 
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				
				$file_contents2 = curl_exec($ch); 
				curl_close($ch); 
				//echo $file_contents2;
				if( strstr($file_contents2, 'carousel_container_style')){
					$temp=return_image($file_contents2);
				
				}
				else{$temp="0";}
			}
			else{ $temp="0";}
			
		}
		
		
		
	}
	
	return $temp;
	
}



include('base.php'); 
mysql_select_db($database_base, $base);

$query_Recordset1 = "SELECT id,titre_original,annee_production FROM film where fanart IS NULL  order by ID ASC LIMIT 50 ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
		$temp=fanart($row_Recordset1['titre_original'],$row_Recordset1['annee_production']);
		if($temp!="0"){
			$query_Recordset0 = "UPDATE film SET fanart='".$temp."' where id='".$row_Recordset1['id']."'  ";
	$Recordset0 = mysql_query($query_Recordset0, $base) or die(mysql_error());
			
		}
		else{
			$query_Recordset0 = "UPDATE film SET fanart='0' where id='".$row_Recordset1['id']."'  ";
	$Recordset0 = mysql_query($query_Recordset0, $base) or die(mysql_error());
		}
	}

/*$query_Recordset1 = "SELECT id,titre_original,annee_production FROM film where id='5' ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
echo html_entity_decode($row_Recordset1['titre_original'])."<br/>".$row_Recordset1['titre_original'];

*/
?>


