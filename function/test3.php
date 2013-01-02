<?php
/*
$ch = curl_init(); 
$timeout = 5; // set to zero for no timeout 
curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
$file_contents = curl_exec($ch); 
curl_close($ch); 

*/

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

// Exemple d'utilisation :
include('base.php'); 
mysql_select_db($database_base, $base);


$query_Recordset1 = "SELECT * FROM serie where fanart IS NOT NULL AND TITRE != 'Chuck' order by ID ASC LIMIT 10 ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
		$name=str_replace(" ","_",$row_Recordset1['titre_original']);
			$name=str_replace("-","",$name);
			$name=str_replace(":","",$name);
			$name=str_replace("/","_",$name);
			$name=str_replace("__","_",$name);
			
		
if (!ftp_curl_get($row_Recordset1['fanart'], '../image_series/'.$name.'.jpg')) {
    die("Le fichier ".$row_Recordset1['fanart']." n'a pu être récupéré");
}
	}
?>

