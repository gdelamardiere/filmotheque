<?php

require_once('function/base.php');
mysql_select_db($database_base, $base);
$tab_serie=array();

$query_Recordset0 = "SELECT s.titre,s.titre_original,s.id FROM serie as s order by titre asc";
	$Recordset0 = mysql_query($query_Recordset0, $base) or die(mysql_error());
	while($row_Recordset0 = mysql_fetch_assoc($Recordset0)){
		$query_Recordset1 = "SELECT e.id_saison,e.num_episode FROM episode as e, liens as l WHERE e.id_serie='".$row_Recordset0['id']."' AND e.id=l.id_episode AND l.lien is not null order by e.id_saison desc,  e.num_episode desc LIMIT 1";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
		$tab_serie[str_replace(" ","",$row_Recordset0['titre_original'])]='<span style="color:red;"> S'.$row_Recordset1['id_saison'].'E'.$row_Recordset1['num_episode'].'</span>';
		echo $row_Recordset0['titre']."(".$row_Recordset0['titre_original'].") s".$row_Recordset1['id_saison']." - ep".$row_Recordset1['num_episode']."<br>";
		
	}
		
		
		
	}
	
	

	
	
	
$ch = curl_init();
		 		 $timeout = 5;
		 		 curl_setopt ($ch, CURLOPT_URL, 'http://www.alloshowtv.com/series/vues_s.php?cat=S&vue=j');
		 		 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		 		 curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		 		 $file_contents = curl_exec($ch);
		 		 curl_close($ch);
		 		 $lines = array();
		 		 $tab=array();
		 		 $tab2=array();
		 		 $new_serie=array();
		 		 //$file_contents=str_replace('\n','',$file_contents);
		 		 $lines = explode(' <td width="43%">', $file_contents);
		 		 $lines[0]='';
		 		 for($i=1;$i<count($lines);$i++){
		 		 	$tab[] = explode(' <td width="20%">', $lines[$i]);
		 		 	$tab2=explode('</td>', $tab[$i-1][1]);
		 		 	
		 		 	$tab[$i-1][1]=preg_replace("#.+S([0-9]+)E([0-9]+).+#",'$1',$tab2[0]);
		 		 	$tab[$i-1][]=preg_replace("#.+S([0-9]+)E([0-9]+).+#",'$2',$tab2[0]);
		 		 }
		 		 $lines = array();
		 		 
		 		 for($i=0;$i<count($tab);$i++){
		 		 	if(!isset($lines[$tab[$i][0]])){
		 		 		$lines[$tab[$i][0]]=array($tab[$i][1],$tab[$i][2]);
		 		 	}
		 		 	else{
		 		 		if($lines[$tab[$i][0]][0]==$tab[$i][1]){
		 		 			if($lines[$tab[$i][0]][1]<$tab[$i][2]){
		 		 				$lines[$tab[$i][0]][1]=$tab[$i][2];
		 		 			}
		 		 		}
		 		 		if($lines[$tab[$i][0]][0]<$tab[$i][1]){
		 		 			$lines[$tab[$i][0]][0]=$tab[$i][1];
		 		 			$lines[$tab[$i][0]][1]=$tab[$i][2];
		 		 		}
		 		 		
		 		 	}
		 		 	
		 		 	
		 		 	
		 		 	
		 		 	
		 		 }
		 		 foreach($lines as $lien=>$num){
		 		 	$str = str_replace(CHR(10),"",strip_tags($lien)); 
					$str = str_replace(CHR(13),"",$str);
					$str = str_replace(" ","",$str);  
					$new_serie[$str]=array($lien,$num);
		 		 }
					ksort($new_serie);
					
		 		 
		 		 echo '<table>';
		 		 foreach($new_serie as $key=>$tab){
		 		 	$lien=str_replace('href=','href=http://www.alloshowtv.com/series/',$tab[0]);
			 		 echo '<tr><td>'.$lien.'</td><td>S'.$tab[1][0].'E'.$tab[1][1];
			 		 if(isset($tab_serie[$key])){echo $tab_serie[$key];}
			 		 echo '</td></tr>';
		 		 }
		 		 echo '</table>';
		 		 
		 		/* echo '<table>';
		 		 foreach($lines as $lien=>$num){
		 		 	$lien=str_replace('href=','href=http://www.alloshowtv.com/series/',$lien);
			 		 echo '<tr><td>'.$lien.'</td><td>S'.$num[0].'E'.$num[1].'</td></tr>';
		 		 }
		 		 echo '</table>';*/
		 		 ?>