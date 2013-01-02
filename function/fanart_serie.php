<?php



//require_once("include.php");

function return_image($str){
	
	$tab=array();
	$lines = array(); 
	$lines = explode('<td class="odd"', $str); 
	$tab=explode('<a href', $lines[3]);
	$temp=str_replace("\n","",$tab[0]);
	$temp=preg_replace("#\>([0-9]+).+#",'$1',$temp);

	return $temp;
	
	
}




function fanart($titre){
	$name=str_replace(" ","+",$titre);
	$name=str_replace("-","",$name);
	$name=str_replace(":","",$name);
	$name=str_replace("/","+",$name);
	$name=str_replace("++","+",$name);
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
		return "http://thetvdb.com/banners/fanart/original/".return_image($file_contents)."-1.jpg";
		
	}
	else{
		return "0";	
		
		
	}
	
}


function ftp_curl_get($url, $sortie, $timeout = 10)
{
    if ($fp = fopen($sortie, 'w')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $ret;
    }
    return FALSE;
}



include('base.php'); 
mysql_select_db($database_base, $base);

$query_Recordset1 = "SELECT id,titre_original FROM serie where fanart IS NOT NULL  order by ID ASC LIMIT 1 ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
		
		$temp=fanart($row_Recordset1['titre_original']);
		if($temp!="0"){
			$name=str_replace(" ","_",$row_Recordset1['titre_original']);
			$name=str_replace("-","",$name);
			$name=str_replace(":","",$name);
			$name=str_replace("/","_",$name);
			$name=str_replace("__","_",$name);
			ftp_curl_get('$temp', '../image_series/'.$name.'.jpg') ;
			$query_Recordset0 = "UPDATE serie SET fanart='".$temp."' where id='".$row_Recordset1['id']."'  ";
			$Recordset0 = mysql_query($query_Recordset0, $base) or die(mysql_error());
			
		}
		else{
			$query_Recordset0 = "UPDATE serie SET fanart='0' where id='".$row_Recordset1['id']."'  ";
	$Recordset0 = mysql_query($query_Recordset0, $base) or die(mysql_error());
		}
	}

/*$query_Recordset1 = "SELECT id,titre_original,annee_production FROM film where id='5' ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
echo html_entity_decode($row_Recordset1['titre_original'])."<br/>".$row_Recordset1['titre_original'];

*/
?>


