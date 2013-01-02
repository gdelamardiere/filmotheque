<?php
require_once("API_Allocine.php");
//require_once("api-allocine-helper-1.4.php");
require_once("api-allocine-helper-2.1.php");
require_once("conf.php");

class Filmotheque {
		 
		 private $hostname_base = HOSTNAME_BASE;
		 private $database_base = DATABASE_BASE;
		 private $username_base = USERNAME_BASE;
		 private $password_base = PASSWORD_BASE;
		 private $base;
		 
		 function Filmotheque(){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	 
		 }
		 
		 
		 
		 
		 
		 
		 
		 
		 /*****creation d'une série***********/
		 
		 
		 public function cherche_coincidence($name){
		 	$tab=$this->lister_serie();
		 	$name=preg_replace("#[^a-zA-Z0-9]#","",$name);
		 //	die($name);
		 	foreach($tab as $line){
		 		//echo $line['titre'];
		 		if(stripos($name,preg_replace("#[^a-zA-Z0-9]#","",$line['titre']))!==FALSE){
		 			return $line;
		 		}
		 	}
		 	return false;
		 }
		 
		 
		 /**
		 ajoute dans la base sql les données d'une nouvelle série
		 retourne l'id de la nouvelle série
		 */
		 private function insert_serie($id_allocine,$titre_original,$titre,$nb_saison,$nb_episodes,$synopsis,$duree_episode,$poster){
		 		 $query = "INSERT INTO serie
(id_allocine,titre_original,titre,nombre_saisons,nombre_episodes,synopsis,duree_episode,poster)
VALUES('".$id_allocine."','".$titre_original."','".$titre."','".$nb_saison."','".$nb_episodes."','".$synopsis."','".$duree_episode."','".$poster."')";
		 		 $Record = mysql_query($query, $this->base);
		 		 return mysql_insert_id();		 		 
		 }
		 
		 
		 private function info_serie($str){
		 		// $serietv = new AlloSerie($str);var_dump($serietv);die();
		 		 $allo= new AlloHelper;
		 		 $info=$allo->search( $str, 1, 10, false, array("tvseries") );
		 		 $infos=$info->getArray();
		 		
		 		 $id_allocine="";
		 		 if(isset($infos["tvseries"][0]['code'])){
		 		 	$id_allocine=$infos["tvseries"][0]['code'];
		 		 	$infos=$allo->tvserie($id_allocine);
		 		 	$infos=$infos->getArray();
		 		 	//echo "<pre>"; var_dump($infos);echo "</pre>"; 
		 		 }
		 	 //die();
		 		 $titre_original="";
		 		 if(isset($infos['originalTitle'])){$titre_original=htmlspecialchars($infos['originalTitle'],ENT_QUOTES);}
		 		 
		 		 $titre="";
		 		 if(isset($infos['originalTitle'])){$titre=htmlspecialchars($infos['originalTitle'],ENT_QUOTES);}
		 		 
		 		 $nb_saison="";
		 		 if(isset($infos['seasonCount'])){$nb_saison=htmlspecialchars($infos['seasonCount'],ENT_QUOTES);}
		 		 
		 		 $nb_episodes="";
		 		 if(isset($infos['episodeCount'])){$nb_episodes=htmlspecialchars($infos['episodeCount'],ENT_QUOTES);}
		 		 
		 		 
		 		 $synopsis="";
		 		 if(isset($infos['synopsisShort'])){$synopsis=htmlspecialchars($infos['synopsisShort'],ENT_QUOTES);}
		 		 
		 		 $duree_episode="";
		 		 if(isset($infos['formatTime'])){$duree_episode=htmlspecialchars($infos['formatTime'],ENT_QUOTES);}
		 		 
		 		 $poster="";
		 		 if(isset($infos['poster'][0])){$poster=htmlspecialchars($infos['poster'][0],ENT_QUOTES);}
		 		 return array("info" => $infos,"id_allocine" => $id_allocine, "titre_original" =>
$titre_original , "titre" => $titre, "nb_saison" => $nb_saison ,
"nb_episodes" => $nb_episodes , "synopsis" => $synopsis ,
"duree_episode" => $duree_episode , "poster" => $poster);
		 }
		 
		 
		 		 
		 private function ajout_acteur_serie($id,$infos){
		 		 if(isset($infos['castingShort']['actors'])){
		 		 		 $acteurs=explode(",",$infos['castingShort']['actors']);
						 //print_r($acteurs);die();
		 		 		 if(isset($acteurs[0])){
		 		 		 		 foreach($acteurs as $value){
		 		 		 		 		 $query_Recordset = "SELECT id FROM acteur WHERE nom LIKE
'".htmlspecialchars($value,ENT_QUOTES)."'";
		 		 		 		 		 $Recordset = mysql_query($query_Recordset, $this->base) ;
		 		 		 		 		 $row_Recordset = mysql_fetch_assoc($Recordset);
		 		 		 		 		 if(isset($row_Recordset['id'])){
		 		 		 		 		 		 $query_Recordset2 = "INSERT INTO acteur_serie
(id_serie,id_acteur) VALUES
('".$id."','".$row_Recordset['id']."') ";
		 		 		 		 		 		 $Recordset2 = mysql_query($query_Recordset2, $this->base) ;
		 		 		 		 		 }
		 		 		 		 		 
		 		 		 		 		 else{
		 		 		 		 		 		 $id_record=0;
		 		 		 		 		 		 $rea = new AlloPerson($value);
		 		 		 		 		 		 $infos_rea = $rea->get();var_dump($infos_rea);
		 		 		 		 		 		 if(isset($infos_rea['id'])){$id_record=$infos_rea['id'];}		 		 		 
		 		 		 		 		 		 
		 		 		 		 		 		 $query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES
('".htmlspecialchars($value,ENT_QUOTES)."','".$id_record."') ";
		 		 		 		 		 		 $Recordset2 = mysql_query($query_Recordset2, $this->base) ;
		 		 		 		 		 		 $id_record=mysql_insert_id();
		 		 		 		 		 		 
		 		 		 		 		 		 $query_Recordset2 = "INSERT INTO acteur_serie
(id_serie,id_acteur) VALUES ('".$id."','".$id_record."') ";
		 		 		 		 		 		 $Recordset2 = mysql_query($query_Recordset2, $this->base) ;
		 		 		 		 		 		 
		 		 		 		 		 }		 
		 		 		 		 }		 
		 		 		 }
		 		 }		 		 		 
		 }		 
		 
		 
		 /**
		 parsage pour recuperer le synopsis des episodes
		 */
		 private function ajout_all_episode($id,$id_serie,$nb_saison){
		 		 if($nb_saison==1){
		 		 		 $saison=$this->parsage_saison('http://www.allocine.fr/series/ficheserie-'.$id.'/saisons/');
		 		 		 foreach($saison as $value){		 		 		 		 
		 		 		 		 $query_Recordset1 = "INSERT INTO episode
(id_serie,num_episode,titre,synopsis,id_saison)
VALUES('".$id_serie."','".$value['num']."','".htmlspecialchars($value['nom'],ENT_QUOTES)."','".htmlspecialchars($value['synopsis'],ENT_QUOTES)."','1')";
		 		 		 		 $Recordset1 = mysql_query($query_Recordset1, $this->base);
		 		 
		 		 		 }
		 		 }
		 		 else{
		 		 		 $saison=array();
		 		 		 $saison_id=array();
		 		 		 $ch = curl_init();
		 		 		 $timeout = 5; // set to zero for no timeout
		 		 		 curl_setopt ($ch, CURLOPT_URL,
'http://www.allocine.fr/series/ficheserie-'.$id.'/saisons/');
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
		 		 		 $saison_id[]=$this->parsage_derniere("http://www.allocine.fr/series/ficheserie-".$id."/saison-".$saison_id[0]."/",$nb_saison);
		 		 		 for($i=0;$i<$nb_saison;$i++){
		 		 		 		 $saison[]=$this->parsage_saison("http://www.allocine.fr/series/ficheserie-".$id."/saison-".$saison_id[$i]."/");
		 		 		 }
		 		 		 $i=0;
		 		 		 foreach($saison as $val){
		 		 		 		 $i++;
		 		 		 		 foreach($val as $value){
		 		 		 		 		 $query_Recordset1 = "INSERT INTO episode
(id_serie,id_saison,num_episode,titre,synopsis)
VALUES('".$id_serie."','".$i."','".$value['num']."','".htmlspecialchars($value['nom'],ENT_QUOTES)."','".htmlspecialchars($value['synopsis'],ENT_QUOTES)."')";
		 		 		 		 //echo utf8_decode($query_Recordset1)."<br/>";
		 		 		 		 		 $Recordset1 = mysql_query($query_Recordset1, $this->base);
		 		 		 		 }
		 		 		 }
		 		 }
		 }
		 		 
		 
		 
		 private function parsage_derniere($str,$nb_saison){
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



		 private function parsage_saison($str){
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
		 		 		 $synopsis=html_entity_decode(utf8_decode(strip_tags(preg_replace("#.+<div
class=\"vpadding5b _content\">\"(.+)\"</div>.+#",'$1',$lines2[0]))));
		 		 		 $saison[]=array("num"=>$i,"nom"=>$nom,"synopsis"=>$synopsis);
		 
		 		 }		 
		 		 return $saison;
		 }

		 
		 
		 
		 private function verif_existe_serie($name){
		 		 $query_Recordset = "SELECT id FROM serie WHERE titre LIKE
'%".htmlspecialchars($name,ENT_QUOTES)."%' OR titre_original LIKE
'%".htmlspecialchars($name,ENT_QUOTES)."%'";
				 $Recordset = mysql_query($query_Recordset, $this->base) ;
				 $row_Recordset = mysql_fetch_assoc($Recordset);
				 if(isset($row_Recordset['id'])){return true;}
				 else return false;
		 }
		 
		 
		 
		 
		 
		 
		public function creer_serie($name){
		 		 $this->base = mysql_connect($this->hostname_base, $this->username_base,
$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 		 mysql_select_db($this->database_base, $this->base);
		 		 if(!$this->verif_existe_serie($name)){
		 		 		 $tab=$this->info_serie($name);
		 		 		/* echo"<pre>";
		 		 		 var_dump($tab);
		 		 		 echo"</pre>";die();*/
						 if ($tab['nb_saison']==0){$tab['nb_saison']=1;}
		 		 		 $id=$this->insert_serie($tab['id_allocine'],$tab['titre_original'],$tab['titre'],$tab['nb_saison'],$tab['nb_episodes'],$tab['synopsis'],$tab['duree_episode'],$tab['poster']);
						 $this->ajout_acteur_serie($id,$tab['info']);						 
		 		 		// $this->ajout_all_episode($tab['id_allocine'],$id,$tab['nb_saison']);
		 		 		$this->maj_serie($tab['id_allocine']);
		 		 		 $this->fanart_serie($id);
		 		 }
		 }
		 
		 
		 
		 public function delete_serie($id,$supr=false){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			if($supr=="true" || $supr == true){$this->delete_lien_serie($id);}
			$query="delete from serie WHERE id='".$id."'";
			$Recordset = mysql_query($query, $this->base) ;			
		 }
		 
		 
		 
		 
		 
		 
		 
		 public function lister_dossier_serie($dirname){
			$dir = opendir($dirname);
			$tab=array();
			$episodes=array();
			$saisons=array();
			$i=0; 			
			while($file = readdir($dir)) {
				if($file != '.' && $file != '..' && !is_dir($dirname.$file))
				{
					if((preg_match("#.+MKV$#i",$file)||preg_match("#.+AVI$#i",$file)||preg_match("#.+MP4$#i",$file)) && $file[0]!='.'){
						$tab[]=$file;			
						$i++;
					}
				}				
			}	
		
			sort($tab,SORT_STRING);	
			$i=0;
			foreach($tab as $file){				
				$saisons[]="";$episodes[]="";		
				if(preg_match("#.+([0-9])([0-9]{2}).+#i",$file,$matches1)){$saisons[$i]='0'.$matches1[1];$episodes[$i]=$matches1[2];}//*103*//				
				if(preg_match("#.+([0-9][1-9])([0-9]{2}).+#i",$file,$matches0)){$saisons[$i]=$matches0[1];$episodes[$i]=$matches0[2];}//*0103*/			
				if(preg_match("#.+\.([0-9])([0-9]{2}).+#i",$file,$matches1)){$saisons[$i]='0'.$matches1[1];$episodes[$i]=$matches1[2];}//*.103*/					
				if(preg_match("#.+ ([0-9])([0-9]{2}).+#i",$file,$matches1)){$saisons[$i]='0'.$matches1[1];$episodes[$i]=$matches1[2];}//* 103*//					
				if(preg_match("#.+([0-9])x([0-9]{2}).+#i",$file,$matches2)){$saisons[$i]='0'.$matches2[1];$episodes[$i]=$matches2[2];}//*1x09*/						
				if(preg_match("#.+\[s([0-9]{1,2})\]_\[e([0-9]{2})\].+#i",$file,$matches3)){$saisons[$i]=$matches3[1];$episodes[$i]=$matches3[2];}//*[s01]_[e01].*/						
				if(preg_match("#.+s([0-9]{1,2})e([0-9]{2}).+#i",$file,$matches4)){$saisons[$i]=$matches4[1];$episodes[$i]=$matches4[2];}//*s01e01*/						
				if(preg_match("#.+s([0-9]{1,2})\.e([0-9]{2}).+#i",$file,$matches5)){$saisons[$i]=$matches5[1];$episodes[$i]=$matches5[2];}//*s01.e01*/						
				if(preg_match("#.+s([0-9]{1,2})_e([0-9]{2}).+#i",$file,$matches6)){$saisons[$i]=$matches6[1];$episodes[$i]=$matches6[2];}//*s01_e01*/
				$i++;
			}	
			$nb=$i;			
			closedir($dir);
			return array("tab"=>$tab,"saisons"=>$saisons,"episodes"=>$episodes,"nb"=>$nb); 
		 } 
		 
		 
		 
		  public function lister_serie(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			$query = "SELECT titre_original,id FROM serie order by titre_original";
			$Recordset = mysql_query($query, $this->base) ;
			$serie=array();
			while($row = mysql_fetch_assoc($Recordset)){
				$serie[]=array('id'=>$row['id'],'titre'=>$row['titre_original']);
			}
			return $serie;
		 }
		 
		 
		 public function lien_allocine_serie($id_serie){
			return "http://www.allocine.fr/series/ficheserie_gen_cserie=".$id_serie.".html";			
		}
		
		public function lien_allocine_saison($id_serie,$id_episode){
			return "http://www.allocine.fr/series/ficheserie-".$id_serie."/saisons/";			
			
		}
		 
		 
		 
		 
		 
		 public function ajouter_lien_serie($id_serie,$saison,$episode,$dossier,$dossier_old,$file, $file_old){
			$racine_dossier=$this->racine_dossier($dossier);
			$racine_dossier_old=$this->racine_dossier($dossier_old);
			$file=$racine_dossier."/".$file;
			$lien_old=($file_old!=""&&$racine_dossier_old!="")?$racine_dossier_old."/".$file_old:"";
			$this->ajouter_lien_serie2($id_serie,$saison,$episode,$file);		
			$this->rename_fichier($racine_dossier,$file,$lien_old);	
		 }
		 
		 public function ajouter_lien_serie2($id_serie,$saison,$episode,$lien){
			$extension=pathinfo($lien, PATHINFO_EXTENSION);
			$base_lien=str_replace(".".$extension,"",$lien);
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			$query_Recordset1 = "SELECT e.id, COUNT( DISTINCT l.id ) as nb FROM  `liens` as l, episode as e WHERE e.id=l.id_episode and e.id_serie = '".$id_serie."' AND id_saison = '".$saison."' AND num_episode = '".$episode."' ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$nb= $row_Recordset1['nb']+1;
			if($nb > 1){				
				$lien_pc=$base_lien."_".$nb.".".$extension;	
			}
			else{
				$query_Recordset1 = "SELECT id FROM  episode WHERE id_serie = '".$id_serie."' AND id_saison = '".$saison."' AND num_episode = '".$episode."' ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
				$lien_pc=$lien;
			}			
			$query= 'INSERT INTO liens (id_episode,lien,nom) VALUES('.$row_Recordset1['id'].', "'.$lien_pc.'","lien'.$nb.'") ';
			$Recordset = mysql_query($query, $this->base) ;
		 }
		 
		 
		 
		 
		 
		 public function fanart_serie($id_serie){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			$query_Recordset1 = "SELECT titre_original FROM serie where id=".$id_serie."  ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$name=str_replace(" ","+",$row_Recordset1['titre_original']);
			$ch = curl_init(); 
			$timeout = 5;  
			curl_setopt ($ch, CURLOPT_URL, 'http://thetvdb.com/?string='.$name.'&searchseriesid=&tab=listseries&function=Search'); 
			//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			$file_contents = curl_exec($ch); 
			curl_close($ch); 
			
			if( strstr($file_contents, 'odd')){
				$tab=array();
				$lines = array(); 
				$lines = explode('<td class="odd"', $file_contents); 
				$tab=explode('<a href', $lines[3]);
				$fanart=preg_replace("#\>([0-9]+)<.+#",'$1',$tab[0]);
				$fanart = (int)$fanart;
				$query = "UPDATE serie set fanart='".$fanart."' where id=".$id_serie."  ";
				$Recordset = mysql_query($query, $this->base) ;
				$this->download_fanart($id_serie,1);				
			}	
		}
		
		public function download_fanart($id_serie,$num){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			$query_Recordset1 = "SELECT fanart,titre_original FROM serie where id=".$id_serie."  ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$name=str_replace(" ","_",$row_Recordset1['titre_original']);
			$name=str_replace("-","",$name);
			$name=str_replace(":","",$name);
			$name=str_replace("/","_",$name);
			$name=str_replace("__","_",$name);		
			if($this->ftp_curl_get($row_Recordset1['fanart'],$num, '../image_series/'.$name.'-'.$num.'.jpg')!==FALSE){
				$query = "UPDATE serie set num_fanart='".$num."' where id=".$id_serie."  ";
				$Recordset = mysql_query($query, $this->base) ;
			}	
		}
		
		
		private function ftp_curl_get($fanart,$num, $sortie, $timeout = 10)
		{
			$url='http://thetvdb.com/banners/fanart/original/'.$fanart.'-'.$num.'.jpg';
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
		
		
		
		
		public function maj_serie($id_allocine){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			
			$allo= new AlloHelper;			
			$infos=$allo->tvserie($id_allocine,"large")->getArray();			
			$nombre_saisons=$infos["seasonCount"];
			$nombre_episodes=$infos["episodeCount"];		
			
			$query_Recordset1 = "update serie set nombre_saisons='".$nombre_saisons."',nombre_episodes='".$nombre_episodes."'  where id_allocine=".$id_allocine."  ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			
			foreach($infos["season"] as $saison){
				$this->maj_saison($id_allocine,$saison['code']);
			}			
		}
		
		public function maj_all_serie(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			$query = "SELECT distinct(id_allocine) FROM serie ";
			$Recordset = mysql_query($query, $this->base) ;
			while($row = mysql_fetch_assoc($Recordset)){
				$this->maj_serie($row['id_allocine']);
			}			
		}
		
		
		public function maj_saison($id_serie_allocine,$code_saison){
			
			$allo= new AlloHelper;			
			$infos=$allo->season($code_saison,"large")->getArray();
			$num_saison=$infos["seasonNumber"];
			/*echo "<pre>";
			var_dump($infos);
			echo "</pre>";die();*/
			
			
			
			
			
			
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			
		/*	$allo= new AlloHelper;
			$infos = $allo->getDataFromURL('http://api.allocine.fr/rest/v3/season?partner=YW5kcm9pZC12M3M&code='.$tab['code'].'&profile=large&mediafmt=mp4-lc&format=JSON',true);
			$num_saison=$tab["seasonNumber"];*/
			
			foreach($infos['episode'] as $episode){
				$titre=(isset($episode["title"]))?htmlspecialchars($episode["title"],ENT_QUOTES):htmlspecialchars($episode["originalTitle"],ENT_QUOTES);
				$num_episode=$episode["episodeNumberSeason"];
				//$synopsis=(isset($episode["synopsis"]))?$episode["synopsis"]:"";
				$synopsis=(isset($episode["synopsis"]))?($episode["synopsis"]):"";
				
				$id_episode=$this->id_episode($id_serie_allocine,$num_saison,$num_episode);
				if($id_episode==0){
					$query_Recordset1 = "INSERT INTO episode (id_serie,id_saison,num_episode,titre,synopsis) VALUES('".$this->id_serie($id_serie_allocine)."','".$num_saison."','".$num_episode."','".$titre."','".htmlspecialchars($synopsis,ENT_QUOTES)."')";				
				}
				else{
					$query_Recordset1 = "update episode set titre='".$titre."',synopsis='".htmlspecialchars($synopsis,ENT_QUOTES)."'  where id=".$id_episode."  ";				
				}	
				$Recordset1 = mysql_query($query_Recordset1, $this->base) ;						
			}			
		}
		
		private function id_episode($id_serie_allocine,$num_saison,$num_episode){
			mysql_select_db($this->database_base, $this->base);
			$query_Recordset1 = "SELECT e.id FROM episode as e, serie as s where e.id_serie=s.id AND s.id_allocine=".$id_serie_allocine." AND e.id_saison=".$num_saison." AND e.num_episode=".$num_episode." ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			if(isset($row_Recordset1['id']) && $row_Recordset1['id']!=null){
				return $row_Recordset1['id'];
			}
			else{return 0;}
			
		}
		
		private function id_serie($id_serie_allocine){
			mysql_select_db($this->database_base, $this->base);
			$query_Recordset1 = "SELECT id FROM serie where id_allocine=".$id_serie_allocine."  ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			if(isset($row_Recordset1['id']) && $row_Recordset1['id']!=null){
				return $row_Recordset1['id'];
			}
			else{return 0;}
			
		}

		 
		 /****fin serie**/
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 /***film     *******/
 		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 private function delete_all_liens_film($id_film){
			$query_Recordset1 = "SELECT lien FROM liens where id_film='".$id_film."'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
				$lien=$row_Recordset1 ["lien"];
				if ($lien!=""){
					system("rm \"".$lien."\" " );
				}
			}
			$query_Recordset1 = "DELETE FROM liens where id_film='".$id_film."'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
		}
		
		public function delete_lien_film($lien){
			$query_Recordset1 = 'DELETE FROM liens where lien like"'.$lien.'" LIMIT 1';
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			if ($lien!=""){
					system("rm \"".$lien."\" " );				
			}
		}
		 
		 
		 
		 private function return_image($str){
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
		 
		 
		 public function fanart($titre,$annee){
		 	$titre=utf8_encode($titre);
		 	$name=str_replace("'","+",$titre);
		 	$name=str_replace("&#039;","+",$name);	 	
			$name=str_replace(" ","+",$name);
			$name=str_replace("-","",$name);
			$name=str_replace("/","+",$name);
			$name=str_replace("++","+",$name);
			$name=str_ireplace("(tv)","",$name);
			$ch = curl_init(); 
			$timeout = 5; // set to zero for no timeout 
			//curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
			curl_setopt ($ch, CURLOPT_URL, "http://www.themoviedb.org/search/movie?query=".$name."+".$annee);
			
			//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			
			//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			$file_contents = curl_exec($ch); 
			curl_close($ch); 
			preg_match("#href=\"http://www.themoviedb.org/movie/([0-9]+)-.+\"#",$file_contents,$temps);
			
			if( !isset($temps[1])){
				$ch = curl_init(); 
				curl_setopt ($ch, CURLOPT_URL, "http://www.themoviedb.org/search/movie?query=".$name);			
				//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				
				//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
				$file_contents = curl_exec($ch); 
				curl_close($ch); 
				preg_match("#href=\"http://www.themoviedb.org/movie/([0-9]+)-.+\"#",$file_contents,$temps);
			}
			
			if( !isset($temps[1])){
				$temp="0";
			}			
			else{
				$name=$name."+".$annee;
				//echo $name;
				$ch = curl_init(); 
				$timeout = 5; // set to zero for no timeout 
				curl_setopt ($ch, CURLOPT_URL, "http://www.themoviedb.org/movie/".$temps[1]."-test/backdrops"); 
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				$file_contents2 = curl_exec($ch); 
				curl_close($ch); 
				preg_match('#<a href="http://cf2.imgobject.com/t/p/original/([^"]+).jpg"#',$file_contents2,$temps);
				if( isset($temps[1])){
					$temp='http://cf2.imgobject.com/t/p/original/'.$temps[1].'.jpg';
				}
				else{
					$temp="0";
				}	
			}	
			//	echo $temp;
			return $temp;			
		}


/*public function fanart($titre,$annee){
			$name=str_replace(" ","+",$titre);
			$name=str_replace("-","",$name);
			$name=str_replace("/","+",$name);
			$name=str_replace("++","+",$name);
			$ch = curl_init(); 
			$timeout = 5; // set to zero for no timeout 
			//curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
			curl_setopt ($ch, CURLOPT_URL, "http://www.themoviedb.org/search/movie?query=".$name."+".$annee."&movie_page=&person_page=&company_page=&keyword_page=&active=0");
			
			//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			
			//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			$file_contents = curl_exec($ch); 
			curl_close($ch); var_dump($file_contents);die();
			
			if( strstr($file_contents, 'carousel_container_style')){
				$temp=$this->return_image($file_contents);
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
					$temp=$this->return_image($file_contents2);	
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
							$temp=$this->return_image($file_contents2);						
						}
						else{$temp="0";}
					}
					else{ $temp="0";}					
				}
			}			
			return $temp;			
		}*/
		 
		 
		 
		private function update_fanart($id){
			$query = "SELECT id,titre_original,annee_production FROM film where id = '".$id."' ";
			$Recordset = mysql_query($query, $this->base) ;
			$row_Recordset = mysql_fetch_assoc($Recordset);
			$temp=$this->fanart($row_Recordset['titre_original'],$row_Recordset['annee_production']);
			$query = "UPDATE film SET fanart='".$temp."' where id='".$id."'  ";
			$Recordset = mysql_query($query, $this->base) ;
			$this->download_fanart_film($id,$temp);
		}
		 
		 
		 public function update_fanart_t(){
			$query = "SELECT id,titre_original,annee_production FROM film where fanart like '%http://cf1.imgobject.com%' order by titre ";
			$Recordset0 = mysql_query($query, $this->base) ;
			while($row_Recordset = mysql_fetch_assoc($Recordset0)){
			$temp=$this->fanart($row_Recordset['titre_original'],$row_Recordset['annee_production']);
			$id=$row_Recordset['id'];
			$query = "UPDATE film SET fanart='".$temp."' where id='".$id."'  ";
			$Recordset = mysql_query($query, $this->base) ;
			}
		}
		 
		 
		 public function download_fanart_film_all(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
		 	$timeout = 0;
			$query_Recordset1 = "SELECT fanart,id FROM film order by id";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
					
				
				
				$url=$row_Recordset1['fanart'];
			    if ($fp = fopen('../images_film/film'.$row_Recordset1['id'].'_1.jpg', 'w')) {
			        $ch = curl_init($url);
			        curl_setopt($ch, CURLOPT_FILE, $fp);
			        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			        $ret = curl_exec($ch);
			        curl_close($ch);
			        fclose($fp);
			    }
			}
		}
		
		 public function download_fanart_film($id,$fanart,$nb=1){
			$timeout = 0;				
			$url=$fanart;
		    if ($fp = fopen('../images_film/film'.$id.'_'.$nb.'.jpg', 'w')) {
		        $ch = curl_init($url);
		        curl_setopt($ch, CURLOPT_FILE, $fp);
		        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		        $ret = curl_exec($ch);
		        curl_close($ch);
		        fclose($fp);
		    }
			
		}

		
		
		
		 
		 
		 
		 
		 
		 
		 
		 
		 
		public function film_acteur_allocine($id_allocine){
			$ch = curl_init(); 
			$timeout = 5; // set to zero for no timeout 
			curl_setopt ($ch, CURLOPT_URL, 'http://www.allocine.fr/personne/filmographie_gen_cpersonne='.$id_allocine.'.html'); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			$file_contents = curl_exec($ch); 
			curl_close($ch); 
			$tab=array();
			$lines = array(); 
			$lines = explode('<div class="rubric">', $file_contents); 
			
			// display file line by line 
			for($i=3;$i<count($lines)-3;$i++) { 
				$tab[]= '<div class="rubric">'.$lines[$i];			
			} 
			
			$lines = array(); 
				$lines = explode('<div class="poster">', $file_contents);
				$lines2 = array(); 
				$lines2 = explode('<img', $lines[1]);
				$poster = '<img '.$lines2[1];			
			
			$value=array();
			foreach($tab as $v){
				$lines = array(); 
				$lines = explode('<img', $v);
				$lines2 = array(); 
				$lines2 = explode('</a>', $lines[1]);				
				$lines = array(); 
				$lines = explode('<h2>', $v);
				$lines3 = array(); 
				$lines3 = explode('</h2>', $lines[1]);
				$temp=str_replace("\n","",$lines3[0]);
				$id=preg_replace("#.+=([0-9]+)\.html.+#",'$1',$temp);
				if($id==$temp){
					$id=preg_replace("#.+-([0-9]+)/.>.+#",'$1',$temp);
				}
				$titre=preg_replace("#.+=[0-9]+.+>(.+)</a>#",'$1',$temp);
				if($titre==$temp){
					$titre=preg_replace("#.+-[0-9]+/.>(.+)</a>#",'$1',$temp);
				}
				$lines = array(); 
				$lines = explode('<p>', $v);
				$lines4 = array(); 
				$lines4 = explode('</p>', $lines[2]);				
				$value[]=array('img'=> "<img".$lines2[0],'lien'=>$temp,'id'=>$id,'titre'=>$titre,'role'=>$lines4[0]); 
			}			
			return array($poster,$value);
		} 
		 
		 
		 
		 
		 
		 
		 public function liste_film_genre($id){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			if($id!="0"){
				$query_Recordset1 = "SELECT f.titre,f.id FROM film as f,genre_film as g, genre as i where f.id=g.id_film AND i.id=g.id_genre AND g.id_genre='".$id."' order by f.titre";
				$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
				$i=0;
				while($val = mysql_fetch_assoc($Recordset1)){					
					echo ' <option value="'.$val['id'].'" ';
			  		if($i==0){echo 'selected="selected"';}
					echo '>'.str_replace(":","",utf8_encode($val['titre'])).'</option> ';					
					$i++;					
				}				
			}
			else{
				$query_Recordset1 = "SELECT f.titre,f.id FROM film as f order by f.titre";
				$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
				$i=0;
				while($val = mysql_fetch_assoc($Recordset1)){					
					echo ' <option value="'.$val['id'].'" ';
			  		if($i==0){echo 'selected="selected"';}
					echo '>'.str_replace(":","",utf8_encode($val['titre'])).'</option> ';					
					$i++;					
				}
			} 			 
		 }
		 
		 public function maj_fanart_film($id,$lien){
		 	if($lien!="" and $lien !=null){
				$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
			 	mysql_select_db($this->database_base, $this->base);
				$query_Recordset1 = "UPDATE film set fanart='".$lien."' where id='".$id."' limit 1";
				$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
				$this->download_fanart_film($id,$lien);
		 	}
		 	else{
		 		$this->update_fanart($id);
		 	}
		 }
		 
		 
		 
		 
		 
		 
		  public function remplacer_lien_film($id_allocine,$dossier,$dossier_old,$file, $file_old,$supr=false){
			$racine_dossier=$this->racine_dossier($dossier);
			$racine_dossier_old=$this->racine_dossier($dossier_old);
			$lien=$racine_dossier."/".$file;
			$lien_old=($file_old!=""&&$racine_dossier_old!="")?$racine_dossier_old."/".$file_old:"";
			$this->remplacer_lien_film2($id_allocine,$lien,$supr); 
			$this->rename_fichier($racine_dossier,$lien,$lien_old);			
		 }
		 
		 public function remplacer_lien_film2($id_allocine,$lien,$supr=false){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			$query_Recordset = "SELECT id FROM film WHERE id_allocine = ".$id_allocine." ";
			 $Recordset = mysql_query($query_Recordset, $this->base) ;
			 $row_Recordset = mysql_fetch_assoc($Recordset);
			 $id=$row_Recordset['id'];
			if($supr=="true" || $supr == true){$this->delete_lien_film($id);}
			$query="DELETE FROM liens WHERE id_film='".$id."'";
			$Recordset = mysql_query($query, $this->base) ;
			$query='INSERT INTO liens (id_film,lien,nom) VALUES('.$id.', "'.$lien.'","lien1")';
			$Recordset = mysql_query($query, $this->base) ;			
		 }
		 
		 public function ajouter_lien_film($id_allocine,$dossier,$dossier_old,$file, $file_old){
			$racine_dossier=$this->racine_dossier($dossier);
			$racine_dossier_old=$this->racine_dossier($dossier_old);
			$file=$racine_dossier."/".$file;
			$lien_old=($file_old!=""&&$racine_dossier_old!="")?$racine_dossier_old."/".$file_old:"";
			$file=$this->ajouter_lien_film2($id_allocine,$file);
			$this->rename_fichier($racine_dossier,$file,$lien_old);
		 }
		 
		 public function ajouter_lien_film2($id_allocine,$lien){
			$extension=pathinfo($lien, PATHINFO_EXTENSION);
			$base_lien=str_replace(".".$extension,"",$lien); 
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			$query_Recordset1 = "SELECT f.id, COUNT( DISTINCT l.id ) as nb FROM  `liens` as l, film as f WHERE f.id=l.id_film and id_allocine = '".$id_allocine."' ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$nb= $row_Recordset1['nb']+1;
			if($nb > 1){				
				$lien_pc=$base_lien."_".$nb.".".$extension;	
			}
			else{
				$lien_pc=$lien;
			}
			$query= 'INSERT INTO liens (id_film,lien,nom) VALUES('.$row_Recordset1['id'].', "'.$lien_pc.'","lien'.$nb.'") ';
			$Recordset = mysql_query($query, $this->base) ;
			return $lien_pc;
		 }
		 
		 		 
		 public function creer_film_new($id_allocine, $dossier,$dossier_old,$file, $file_old){
			$racine_dossier=$this->racine_dossier($dossier);
			$racine_dossier_old=$this->racine_dossier($dossier_old);
			$lien=$racine_dossier."/".$file;
			$lien_old=($file_old!=""&&$racine_dossier_old!="")?$racine_dossier_old."/".$file_old:"";
			$this->rename_fichier($racine_dossier,$lien,$lien_old);			 
			return $this->creer_film2($id_allocine,$lien);			 
		 }
		 
		 
		 public function creer_film2($id_allocine, $lien){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			if(!$this->verif_existe_film($id_allocine)){
				$allo= new AlloHelper;			
	 		 	$info=$allo->movie( $id_allocine);
	 		 	$infos=$info->getArray();
	
			}
			$titre_original="";
			if(isset($infos['originalTitle'])){$titre_original=htmlspecialchars($infos['originalTitle'],ENT_QUOTES);}
			$titre="";
			if(isset($infos['title'])){$titre=htmlspecialchars($infos['title'],ENT_QUOTES);}
			$annee="";
			if(isset($infos['productionYear'])){$annee=str_replace('-','',htmlspecialchars($infos['productionYear'],ENT_QUOTES));}
			$duree="";
			if(isset($infos['runtime'])){$duree=$infos['runtime'];}
			$synopsis="";
			if(isset($infos['synopsis'])){$synopsis=htmlspecialchars($infos['synopsis'],ENT_QUOTES);}
			$bande="";
			if(isset($infos['trailer']['href'])){$bande=htmlspecialchars($infos['trailer']['href'],ENT_QUOTES);}
			$poster="";
			if(isset($infos['poster']["href"])){$poster=htmlspecialchars($infos['poster']["href"],ENT_QUOTES);}
			$fanart=$this->fanart($titre,$annee);			
			$id_film=$this->insert_film($id_allocine,$titre_original,$titre,$annee,$duree,$synopsis,$bande,$lien,$poster,$fanart);
			//remplissage du genre
			$tab=$this->lister_genre();
			if(isset($infos['genre'])){
				foreach($infos['genre'] as $genres){
					$id_genre=(isset($tab[$genres['$']]))?$tab[$genres['$']]:$this->creer_genre($genres['$']);
					$this->inserer_genre_film($id_genre,$id_film);
				}
			}
			
			
			//$this->ajout_realisateur_film($id_film,$infos);
			
			//acteurs
			$this->ajout_acteur_film($id_film,$infos);
			return $id_film;
		 }
		 
		 
		 
		  public function modifier_film($id_allocine,$id_old){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			$query = "SELECT lien FROM liens where id_film='".$id_old."'";
			$Recordset = mysql_query($query, $this->base) ;
			$liens=array();
			$i=0;
			while($row = mysql_fetch_assoc($Recordset)){
				$liens[]=$row['lien'];
				$i++;
			}
			
			//suppression du vieux film
			$this->delete_film($id_old);
			
			//si ce film existe deja on modifie juste le lien_pc			
			$query = "SELECT id FROM film where id_allocine='".$id_allocine."'";
			$Recordset1 = mysql_query($query, $this->base) ;
			$row1 = mysql_fetch_assoc($Recordset1);
			if(isset($row1['id'])&&$i>0){
				foreach($liens as $lien){
					$this->ajouter_lien_film2($id_allocine,$lien);	
				}	
				return $row1['id'];
			}
			if($i==0){
				$liens[0]='NULL';
				$id = $this->creer_film2($id_allocine, $liens[0]);
			}
			else{
				$id = $this->creer_film2($id_allocine, $liens[0]);
				for($j=1; $j<$i;$j++){
					$this->ajouter_lien_film2($id_allocine,$lien);
				}
			}			
			return $id;			
		}


				 
		 public function delete_film($id,$supr=false){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			if($supr=="true" || $supr == true){$this->delete_all_liens_film($id);}
			$query="delete from film WHERE id='".$id."'";
			$Recordset = mysql_query($query, $this->base) ;			
		 }
		 
		 
		 
		 
		 public function lister_dossier_film($dirname){
			$dir = opendir($dirname);
			$tab=array();
			$i=0; 			
			while($file = readdir($dir)) {
				if($file[0] != '.' && !is_dir($dirname.$file))
				{
					if(preg_match("#.+MKV$#i",$file)||preg_match("#.+AVI$#i",$file)||preg_match("#.+MP4$#i",$file)){
						$tab[]=$file;			
						$i++;
					}
				}				
			}			
			$nb=$i;			
			closedir($dir);
			sort($tab,SORT_STRING);
			return array("tab"=>$tab,"nb"=>$nb); 
		 }
		 
		
		 
		 public function preparer_lien_allocine($lien){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
			mysql_select_db($this->database_base, $this->base);
		 	$extension=pathinfo($lien, PATHINFO_EXTENSION);
		 	$lien=" ".str_replace($extension,'',$lien);
		 	$query = "SELECT mot FROM mot_corrige";	
			$Recordset = mysql_query($query, $this->base);	
			$lien=preg_replace('#[^a-z0-9]#i',' ',$lien);
			$lien=preg_replace('# ([a-z]) #i',' ',$lien);
		 	$lien=preg_replace('#[0-9]{4}#i','',$lien);		 	
			while($row_Recordset = mysql_fetch_assoc($Recordset)){
				$lien=str_ireplace(" ".$row_Recordset['mot']." ",' ',$lien);
			}

		 	$lien=preg_replace('# {2,}#i',' ',$lien);
		 	
		 	//$lien_final=urlencode($lien);
		 	return trim($lien).".".$extension;
		 }
		 
		 private function update_mot_corrige($lien_init,$lien_final){	
		 	if($lien_init!=$lien_final){
			 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
			 	mysql_select_db($this->database_base, $this->base);
			 	$extension=pathinfo($lien_init, PATHINFO_EXTENSION);
			 	$lien_init=str_replace(".".$extension,'',$lien_init);			 	
			 	$lien_final=str_replace(".".$extension,'',$lien_final);	  	
			 	$correctif=str_replace($lien_final,"",$lien_init);
			 	$aCorrectifs=explode(" ",$correctif);
			 	foreach($aCorrectifs as $value){
			 		$value=strtoupper($value);
			 		if($value!=""){ 
						$query = "INSERT INTO mot_corrige (mot) SELECT '".$value."' FROM mot_corrige WHERE NOT EXISTS (SELECT NULL FROM mot_corrige WHERE mot = '".$value."' limit 1) limit 1";	
						$Record = mysql_query($query, $this->base);	
					}	
			 	}
		 	}
		 }

		 	
		 
		 
		 
		 
		 
		  		
		
		/**a faire
		afficher la bande annonce*/
		public function rechercher_film_allocine($name,$name_init,$echo=TRUE){
			$this->update_mot_corrige($name_init,$name);
			$film=str_replace(".avi","",htmlspecialchars($name,ENT_QUOTES));
			$film=str_replace(".mkv","",$film);
			$film=str_replace(".mp4","",$film);
			$film=str_replace(".AVI","",$film);
			$film=str_replace(".MKV","",$film);
			$film=str_replace(".MP4","",$film);
			 $allo= new AlloHelper;
	 		 $info=$allo->search( $film, 1, 15, false, array("movie") );
	 		 $infos=$info->getArray();
	 		
	 		if($infos["totalResults"]==1){	 		  
		 		 if(isset($infos["movie"][0]["code"])){
		 		 	$id_allocine=$infos["movie"][0]["code"];
		 		 	$info=$allo->movie( $id_allocine, 'small');
		 		 	$infos=$info->getArray();
		 		 	$poster="";
					if(isset($infos["poster"]["href"])){$poster=htmlspecialchars($infos["poster"]["href"],ENT_QUOTES);}
					if(!$this->verif_existe_film($id_allocine)){
						$synopsis=(isset($infos["synopsisShort"]))?$infos["synopsisShort"]:"";
					}
					else{$synopsis='<p onclick="lancer_film(\''.$this->lien_film_id_allocine($id_allocine).'\')">ce film existe deja</p>';}
					$tab=array($id_allocine,$synopsis,$poster);		 		 	
		 		 }
		 		 else{$tab=array("","film introuvable","");}
				if($echo){echo utf8_encode("1__synopsis__".$tab[0]."__synopsis__".$tab[1]."__synopsis__".$tab[2]); }
	 		}
	 		else if($infos["totalResults"]>1){$tab=array();
	 			if($echo){echo '<table>';}
	 			foreach($infos["movie"] as $array){
	 				$id_allocine=$array["code"];
	 				$titre=$array["originalTitle"];	 				
	 				$annee=$array["productionYear"];
		 		 	$poster=$array["posterURL"];					
					if($echo){			
						if($this->verif_existe_film($id_allocine)){
							echo '<tr><td><img  onclick="lancer_film(\''.$this->lien_film_id_allocine($id_allocine).'\')" alt="poster" src="'.$poster.'" width="50px" /><br>'.$titre.' ('.$annee.')<br>'.$id_allocine;
							echo '<br>ce film existe deja';
						}
						else{
							echo '<tr><td><img src="'.$poster.'" width="50px" /><br>'.$titre.' ('.$annee.')<br>'.$id_allocine;
						}
						echo '</td></tr>';						
					}
					$tab[]=array($id_allocine,$titre,$annee,$poster);	 
	 			}
	 			if($echo){echo '</table>';}
	 		}
	 		
			
			
			
			else{
				$tab=array("0","","film introuvable","");
				if($echo){echo "film introuvable"; }
			}
			
			return $tab;
		}
		
		
		
		/**a faire
		afficher la bande annonce*/
		public function rechercher_film_id_allocine($id,$echo=TRUE){
			 $allo= new AlloHelper;			
 		 	$info=$allo->movie( $id, 'small');
 		 	$infos=$info->getArray();
 		 	$poster="";
			if(isset($infos["poster"]["href"])){$poster=htmlspecialchars($infos["poster"]["href"],ENT_QUOTES);}
			if(!$this->verif_existe_film($id)){
				$synopsis=(isset($infos["synopsisShort"]))?$infos["synopsisShort"]:"";
			}
			else{$synopsis="ce film existe deja";}
			$tab=array($id,$synopsis,$poster);	
			if(!isset($infos["code"])){$tab=array("","film introuvable","");}
			if($echo){echo utf8_encode($tab[0]."__synopsis__".$tab[1]."__synopsis__".$tab[2]); }
			return $tab;
		}
		 
		 
		public function lien_allocine_film($id_allocine){
			return "http://www.allocine.fr/film/fichefilm_gen_cfilm=".$id_allocine.".html";			
			
		}
		
		
		
		
		
		 
		
		 
		 private function inserer_genre_film($id_genre,$id_film){
			$query = "INSERT INTO genre_film (id_genre,id_film) VALUES('".$id_genre."','".$id_film."')";
			$Recordset = mysql_query($query, $this->base) ;
		}
		 
		 
		 
		 private function verif_existe_film($id_allocine){
		 		 $query_Recordset = "SELECT id FROM film WHERE id_allocine = ".$id_allocine." ";
				 $Recordset = mysql_query($query_Recordset, $this->base) ;
				 $row_Recordset = mysql_fetch_assoc($Recordset);
				 if(isset($row_Recordset['id'])){return true;}
				 else return false;
		 }
		 
		  private function lien_film_id_allocine($id_allocine){
		 		 $query_Recordset = "SELECT lien FROM liens,film WHERE id_film=film.id AND id_allocine = ".$id_allocine." limit 1 ";
				 $Recordset = mysql_query($query_Recordset, $this->base) ;
				 $row_Recordset = mysql_fetch_assoc($Recordset);
				 return $row_Recordset['lien'];
		 }
		 
		 private function insert_film($id_allocine,$titre_original,$titre,$annee,$duree,$synopsis,$bande,$lien,$poster,$fanart){
		 		 $query = "INSERT INTO film (id_allocine,titre_original,titre,annee_production,duree,synopsis,bande_annonce,poster,fanart) VALUES('".$id_allocine."','".$titre_original."','".$titre."','".$annee."','".$duree."','".$synopsis."','".$bande."','".$poster."','".$fanart."')";
		 		 $Record = mysql_query($query, $this->base);
		 		 $id=mysql_insert_id();
		 		 $query= 'INSERT INTO liens (id_film,lien,nom) VALUES('.$id.', "'.$lien.'","liens1") ';
				$Recordset = mysql_query($query, $this->base) ;
		 		 return $id;	
		 }
		 
		 
		private function ajout_realisateur_film($id_film,$infos){
			for ($i=0;$i<10;$i++){
				if(isset($infos['castMember'][$i]) && $infos['castMember'][$i]['activity']=='Réalisateur'){
					$query_Recordset = "SELECT id FROM acteur WHERE id_allocine = ".$infos['castMember'][$i]['person']['code']."";
					$Recordset = mysql_query($query_Recordset, $this->base) ;
					$row_Recordset = mysql_fetch_assoc($Recordset);
					if(isset($row_Recordset['id'])){
						$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_acteur) VALUES ('".$id_film."','".$row_Recordset['id']."'";
						$Recordset2 = mysql_query($query_Recordset2, $this->base) ;
					}					
					else{
						$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".htmlspecialchars($infos['castMember'][$i]['person']['name'],ENT_QUOTES)."','".$infos['castMember'][$i]['person']['code']."') ";
						$Recordset2 = mysql_query($query_Recordset2, $this->base) ;
						$id=mysql_insert_id();						
						$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_acteur) VALUES ('".$id_film."','".$id."'";
						$Recordset2 = mysql_query($query_Recordset2, $this->base) ;						
						}
				}
			}
		 }		 
		 
		 
		 		 
		 private function ajout_acteur_film($id_film,$infos){
		 	for ($i=0;$i<10;$i++){				
					$query_Recordset = "SELECT id FROM acteur WHERE id_allocine = ".$infos['castMember'][$i]['person']['code']."";
					$Recordset = mysql_query($query_Recordset, $this->base) ;
					$row_Recordset = mysql_fetch_assoc($Recordset);
					if(isset($row_Recordset['id'])){
						if(isset($infos['castMember'][$i]) && $infos['castMember'][$i]['activity']["$"]!='Réalisateur'){
							$query_Recordset2 = "INSERT INTO acteur_film (id_film,id_acteur,role) VALUES ('".$id_film."','".$row_Recordset['id']."','".htmlspecialchars($infos['castMember'][$i]['role'],ENT_QUOTES)."') ";
							$Recordset2 = mysql_query($query_Recordset2, $this->base) ;
						}
						else{
							$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_realisateur) VALUES ('".$id_film."','".$row_Recordset['id']."')";
							$Recordset2 = mysql_query($query_Recordset2, $this->base) ;
						}
					}					
					else{
						$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".htmlspecialchars($infos['castMember'][$i]['person']['name'],ENT_QUOTES)."','".$infos['castMember'][$i]['person']['code']."') ";
						$Recordset2 = mysql_query($query_Recordset2, $this->base) ;
						$id=mysql_insert_id();						
						if(isset($infos['castMember'][$i]) && $infos['castMember'][$i]['activity']["$"]!='Réalisateur'){
							$query_Recordset2 = "INSERT INTO acteur_film (id_film,id_acteur,role) VALUES ('".$id_film."','".$id."','".htmlspecialchars($infos['castMember'][$i]['role'],ENT_QUOTES)."') ";
							$Recordset2 = mysql_query($query_Recordset2, $this->base) ;
						}
						else{
							$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_realisateur) VALUES ('".$id_film."','".$id."')";
							$Recordset2 = mysql_query($query_Recordset2, $this->base) ;
						}						
					}
				}
		 }		 
		 
		 
		 public function modifer_interet_film($id,$value){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			$query = "update film SET interet='".$value."' where id='".$id."' ";
			$Recordset = mysql_query($query, $this->base) ;
		 }
		 
		 public function modifer_qualite_lien_film($id,$value){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			$query = "update liens SET qualite='".$value."' where id='".$id."' ";
			$Recordset = mysql_query($query, $this->base) ;
		 }
		 
		 /*******fin film***/
		 
		 private function creer_genre($name){
			$query = "INSERT INTO genre (nom) VALUES('".$name."')";
			$Recordset = mysql_query($query, $this->base) ;
			return mysql_insert_id(); 
		 }
		 
		 private function racine_dossier($dossier){
			 if($dossier==""){return $dossier;}
			if(substr($dossier,-1)!="/"){$dossier=$dossier."/";} 
			 return substr($dossier, 0,strlen($dossier)-1);
		 }

		 
		 public function lister_genre(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			$query = "SELECT nom,id FROM genre";
			$Recordset = mysql_query($query, $this->base) ;
			$tab= array();
			while($row_Recordset2 = mysql_fetch_assoc($Recordset)){
				$tab[$row_Recordset2['nom']]=$row_Recordset2['id'];
			}
			return $tab;			 
		 }
		 
		  public function lister_parametre_destination(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			$query_Recordset1 = "SELECT * FROM parametres where id='1'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$dossiers=explode(";",$row_Recordset1['repertoires']);
			return $dossiers;
		 }
		 
		 public function lister_parametre_download(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			$query_Recordset1 = "SELECT * FROM parametres where id='2'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$dossiers=explode(";",$row_Recordset1['repertoires']);
			return $dossiers;
		 }
		 
		 public function rename_fichier($dossier,$lien,$lien_old){
			$lien_old=str_replace("\'","'",$lien_old);
			if (!file_exists($lien_old)) {
				return FALSE;
			}
			$lien=str_replace("\'"," ",$lien);
			if (!file_exists($dossier)&& $dossier!="") {
				system("mkdir \"".$dossier."\" -p");
			}		
			
			$extension=pathinfo($lien, PATHINFO_EXTENSION);
			$sous_titre_old1=str_replace($extension,"srt",$lien_old);
			$sous_titre_old2=str_replace($extension,"idx",$lien_old);
			$sous_titre_old3=str_replace($extension,"sub",$lien_old);	
			if (file_exists($sous_titre_old1)) {
				$sous_titre=str_replace($extension,"srt",$lien);
				system("mv \"".$sous_titre_old1."\" \"".$sous_titre."\"");
			}	
			if (file_exists($sous_titre_old2)) {
				$sous_titre=str_replace($extension,"idx",$lien);
				system("mv \"".$sous_titre_old2."\" \"".$sous_titre."\"");
			}
			if (file_exists($sous_titre_old3)) {
				$sous_titre=str_replace($extension,"sub",$lien);
				system("mv \"".$sous_titre_old3."\" \"".$sous_titre."\"");
			}
		

			echo system("mv \"".$lien_old."\" \"".$lien."\"");
			return TRUE;
		 }
		 
		 
		
		 

		 public function rename_fichier_2($dossier,$dossier_old,$name, $old_name){
			if(substr($dossier_old,-1)!="/"){$dossier_old=$dossier_old."/";}
			if(substr($dossier,-1)!="/"){$dossier=$dossier."/";}	
			$dossier_temp=substr($dossier, 0,strlen($dossier)-1);		
			$this->rename_fichier($dossier_temp,$dossier_old.$file_old,$dossier.$file);
		 }
		 
		 public function afficher_lien_finder($lien){
		 	system("open file:\"".$lien."\" -R");
		 }
		 
		public function lancer_film($lien){
			system("open /Applications/vlc.app \"".$lien."\"");
		 }
		 
		 public function modifier_film_manuellement(){}
		 
		
		 
		 
		 
		 public function lister_lien_film($id){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
		 	$liens=array();
			$query_Recordset1 = "SELECT id,qualite,lien,nom FROM liens where id_film='".$id."'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
				$liens[]=array('id'=>$row_Recordset1['id'],'qualite'=>$row_Recordset1['qualite'],'lien'=>$row_Recordset1['lien'],'nom'=>$row_Recordset1['nom']);
			}
			return $liens;		 	
		 }
		 
		 
		 public function deplacer_dossier_film($dossier_init,$dossier_fin){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);		
		 	$query="update liens set lien=
					REPLACE(lien,'".$dossier_init."','".$dossier_fin."')
					 WHERE lien like '".$dossier_init."%' AND id_film is not null";
					
		 	$Recordset1 = mysql_query($query, $this->base) ;
		 }
		 
		 public function deplacer_dossier_serie($dossier_init,$dossier_fin){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);		
		 	$query="update liens set lien=
					REPLACE(lien,'".$dossier_init."','".$dossier_fin."')
					 WHERE lien like '".$dossier_init."%' AND id_episode is not null";
					
		 	$Recordset1 = mysql_query($query, $this->base) ;
		 }
		 
		 
		 
		 
		 public function deplacer_all_film_repertoire_genre(){ 		 	
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
		 	$query_Genre = "SELECT *  from genre order by ordre, nom";
			$Record = mysql_query($query_Genre) or die(mysql_error());
			while($row_Genre = mysql_fetch_assoc($Record)){
				$query_Recordset1 = "SELECT l.id,l.lien from liens l, genre_film gf where l.id_film=gf.id_film	AND gf.id_genre = ".$row_Genre['id']." AND l.classe='0'";
				$Recordset1 = mysql_query($query_Recordset1) or die(mysql_error());
				while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
					$racine_dossier_old=dirname($row_Recordset1['lien']);
					$racine_dossier="/Volumes/DDE_FILM/film/".$row_Genre['rep_dest'];
					$file=$racine_dossier."/".basename($row_Recordset1['lien']);
					var_dump($racine_dossier,$file,$row_Recordset1['lien']);echo "<br>";
					if($racine_dossier_old!=$racine_dossier){		
						$this->rename_fichier($racine_dossier,$file,$row_Recordset1['lien']);						
					}
					$query="update liens set lien='".$file."',classe='1' WHERE id =".$row_Recordset1['id'];					
					$Recordset = mysql_query($query, $this->base) or die(mysql_error());
			
				}
		 	}
		 }
		 
		 /*public function deplacer_all_film_repertoire_genre(){
		 	$reps=array("Western");
		 	
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
		 	foreach($reps as $genre){
			 	$query_Recordset1 = "SELECT l.id,l.lien,g.nom  from liens l, genre_film gf, genre g where l.id_film=gf.id_film	AND gf.id_genre=g.id AND l.lien  like '/Volumes/gdelamardiere/MesFichiers/MesVideos/film/%' AND g.nom = '".$genre."'";
				$Recordset1 = mysql_query($query_Recordset1) or die(mysql_error());
				while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
					var_dump($row_Recordset1);echo "<br>";
					$racine_dossier_old=dirname($row_Recordset1['lien']);
					$racine_dossier="/Volumes/DDE_FILM/film/0".$genre;
					$file=$racine_dossier."/".basename($row_Recordset1['lien']);
					//var_dump($racine_dossier,$file,$row_Recordset1['lien']);echo "<br>";
					if($this->rename_fichier($racine_dossier,$file,$row_Recordset1['lien'])){
						$query="update liens set lien='".$file."' WHERE id =".$row_Recordset1['id'];					
					 	$Recordset = mysql_query($query, $this->base) ;	
					}				
				}
		 	}
		 }
*/
		 
}




if(isset($_GET['fonction'])){
	$temp=new Filmotheque();
	$var=array();
	if(isset($_GET['nb_var'])&&$_GET['nb_var']!=0){
		for($i=0;$i<$_GET['nb_var'];$i++){
			$var[]=$_GET['var'.$i];
		}
	}
	$fct=array($temp,$_GET['fonction']);
	call_user_func_array($fct,$var);
}

if(isset($_POST['fonction'])){
	$temp=new Filmotheque();
	$var=array();
	if(isset($_POST['nb_var'])&&$_POST['nb_var']!=0){
		for($i=0;$i<$_POST['nb_var'];$i++){
			$var[]=$_POST['var'.$i];
		}
	}
	$fct=array($temp,$_POST['fonction']);
	call_user_func_array($fct,$var);
}


?>

