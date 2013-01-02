<?php 
require_once("Allo.class.php");
require_once("AlloSerie.class.php");
require_once("AlloPerson.class.php");
require_once("../function/base.php");




nouvelle_serie($_GET['nom'],$database_base, $base);


function nouvelle_serie($str,$database_base, $base){
	mysql_select_db($database_base, $base);
	$serietv = new AlloSerie($str);
	$infos = $serietv->get();
	//print_r($infos);
	$id="";
	if(isset($infos['id'])){$id=$infos['id'];}
	
	$titre_original="";
	if(isset($infos['titre-original'])){$titre_original=htmlspecialchars($infos['titre-original'],ENT_QUOTES);}
	
	$titre="";
	if(isset($infos['titre'])){$titre=htmlspecialchars($infos['titre'],ENT_QUOTES);}
	
	$nb_saison="";
	if(isset($infos['nombre-saisons'])){$nb_saison=htmlspecialchars($infos['nombre-saisons'],ENT_QUOTES);}
	
	$nb_episodes="";
	if(isset($infos['nombre-episodes'])){$nb_episodes=htmlspecialchars($infos['nombre-episodes'],ENT_QUOTES);}
	
	
	$synopsis="";
	if(isset($infos['synopsis'])){$synopsis=htmlspecialchars($infos['synopsis'],ENT_QUOTES);}
	
	$duree_episode="";
	if(isset($infos['duree-episode'])){$duree_episode=htmlspecialchars($infos['duree-episode'],ENT_QUOTES);}
	
	$poster="";
	if(isset($infos['poster'])){$poster=htmlspecialchars($infos['poster'],ENT_QUOTES);}
	
	
	$query_Recordset1 = "INSERT INTO serie (id_allocine,titre_original,titre,nombre_saisons,nombre_episodes,synopsis,duree_episode,poster) VALUES('".$id."','".$titre_original."','".$titre."','".$nb_saison."','".$nb_episodes."','".$synopsis."','".$duree_episode."','".$poster."')";
	$Recordset1 = mysql_query($query_Recordset1, $base)or die(mysql_error());
	
	$id_serie=mysql_insert_id();
	
	if(isset($infos['casting-court']['acteurs'])){
		$acteurs=explode(",",$infos['casting-court']['acteurs']);
		if(isset($acteurs[0])){
			foreach($acteurs as $value){
				$query_Recordset = "SELECT id FROM acteur WHERE nom LIKE '".htmlspecialchars($value,ENT_QUOTES)."'";
				$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
				$row_Recordset = mysql_fetch_assoc($Recordset);
				if(isset($row_Recordset['id'])){
					$query_Recordset2 = "INSERT INTO acteur_serie (id_serie,id_acteur) VALUES ('".$id_serie."','".$row_Recordset['id']."') ";
					$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
				}
				
				else{
					$id_record=0;
					$rea = new AlloPerson($value);
					$infos_rea = $rea->get();
					if(isset($infos_rea['id'])){$id_record=$infos_rea['id'];}			
					
					$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".htmlspecialchars($value,ENT_QUOTES)."','".$id_record."') ";
					$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
					$id_record=mysql_insert_id();
					
					$query_Recordset2 = "INSERT INTO acteur_serie (id_serie,id_acteur) VALUES ('".$id_serie."','".$id_record."') ";
					$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
					
				}	
			}	
		}
	}		
	
	if($nb_saison==1){
		$saison=parsage_saison('http://www.allocine.fr/series/ficheserie-'.$id.'/saisons/');
		
		foreach($saison as $value){
			
			$query_Recordset1 = "INSERT INTO episode (id_serie,num_episode,titre,synopsis) VALUES('".$id_serie."','".$value['num']."','".htmlspecialchars($value['nom'],ENT_QUOTES)."','".htmlspecialchars($value['synopsis'],ENT_QUOTES)."')";
			//echo utf8_decode($query_Recordset1)."<br/>";
			$Recordset1 = mysql_query($query_Recordset1, $base)or die(mysql_error());
	
		}
				
			
	}
	else{
		$saison=array();
		$saison_id=array();
		$ch = curl_init(); 
		$timeout = 5; // set to zero for no timeout 
		curl_setopt ($ch, CURLOPT_URL, 'http://www.allocine.fr/series/ficheserie-'.$id.'/saisons/'); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
		$file_contents = curl_exec($ch); 
		curl_close($ch); 
		$lines = array(); 
		$tab=array();
		$lines = explode('class="seasonsNumber"', $file_contents);
		$tab = explode('</ul>', $lines[1]);
		$temp=str_replace("\n","",$tab[0]); 
		$lines = array(); 
		$lines= explode('<li', $temp);
		for($i=1;$i<$nb_saison;$i++){
			$saison_id[]=preg_replace("#.+series/ficheserie-".$id."/saison-([0-9]+)/.+#",'$1',$lines[$i]);
			
		}
		$saison_id[]=parsage_derniere("http://www.allocine.fr/series/ficheserie-".$id."/saison-".$saison_id[0]."/",$nb_saison);
		for($i=0;$i<$nb_saison;$i++){
			$saison[]=parsage_saison("http://www.allocine.fr/series/ficheserie-".$id."/saison-".$saison_id[$i]."/");
		}
		$i=0;
		foreach($saison as $val){
			$i++;
			foreach($val as $value){
				$query_Recordset1 = "INSERT INTO episode (id_serie,id_saison,num_episode,titre,synopsis) VALUES('".$id_serie."','".$i."','".$value['num']."','".htmlspecialchars($value['nom'],ENT_QUOTES)."','".htmlspecialchars($value['synopsis'],ENT_QUOTES)."')";
			//echo utf8_decode($query_Recordset1)."<br/>";
				$Recordset1 = mysql_query($query_Recordset1, $base)or die(mysql_error());
			}
	
		}
	}
}

function parsage_derniere($str,$nb_saison){
	$ch = curl_init();
	$timeout = 5; 
	curl_setopt ($ch, CURLOPT_URL, $str); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
	$file_contents = curl_exec($ch); 
	curl_close($ch); 
	$lines = array(); 
	$tab=array();
	$lines = explode('class="seasonsNumber"', $file_contents);
	$tab = explode('</ul>', $lines[1]);
	$temp=str_replace("\n","",$tab[0]); 
	$lines = array(); 
	$lines= explode('<li', $temp);
	return preg_replace("#.+series/ficheserie-[0-9]+/saison-([0-9]+)/.+#",'$1',$lines[$nb_saison]);

	
	
}



function parsage_saison($str){
	$saison=array();
	$ch = curl_init();
	$timeout = 5; 
	curl_setopt ($ch, CURLOPT_URL, $str); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
	$file_contents = curl_exec($ch); 
	curl_close($ch); 
	$lines = array(); 
	$tab=array();
	$lines = explode('class="serie_items_list"', $file_contents);
	$tab = explode('<div class="spacer vmargin20b"></div>', $lines[1]);
	$temp=str_replace("\n","",$tab[0]); 
	$lines = array(); 
	$lines = explode('<div class="serie_itemopener">', $temp);
	for($i=1;$i<count($lines);$i++){
		$tab=explode('<div class="serie_item_infos hide">', $lines[$i]);
		$nom=html_entity_decode(utf8_decode(strip_tags(preg_replace("#.+(Episode.+\").+#",'$1',$tab[0]))));
		$lines2 = array();
		$lines2=explode('<div class="vpadding5">', $tab[1]);		
		$synopsis=html_entity_decode(utf8_decode(strip_tags(preg_replace("#.+<div class=\"vpadding5b _content\">\"(.+)\"</div>.+#",'$1',$lines2[0]))));
		$saison[]=array("num"=>$i,"nom"=>$nom,"synopsis"=>$synopsis);

	}	
	return $saison;
}


?>


