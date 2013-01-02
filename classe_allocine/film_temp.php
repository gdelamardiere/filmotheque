<?php
/**
 * \file Filmotheque.class.php
 * \brief classe principale du site
 * \author Guerric de La Mardière
 * \version 0.1
 * \date 20 aout 2011
 */
 
require_once("API_Allocine.php");

class Filmotheque {
	
	/* BASE DE DONNEES*/
		 
		 private $hostname_base = "localhost";
		 private $database_base = "filmotheque";
		 private $username_base = "root";
		// private $this->password_base = "root";
		private $password_base = "";
		 private $base;
		 
		 
		 
		 
	/*SERIE*/	 
		 
		 
		  /**
		 ajoute dans la base sql les données d'une nouvelle série
		 retourne l'id de la nouvelle série
		 */
		 private function insert_serie($id_allocine,$titre_original,$titre,$nb_saison,$nb_episodes,$synopsis,$duree_episode,$poster){
		 		 /*$this->base = mysql_connect($this->hostname_base, $this->username_base,
$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 		 mysql_select_db($this->database_base, $this->base);*/
		 		 $query = "INSERT INTO serie
(id_allocine,titre_original,titre,nombre_saisons,nombre_episodes,synopsis,duree_episode,poster)
VALUES('".$id_allocine."','".$titre_original."','".$titre."','".$nb_saison."','".$nb_episodes."','".$synopsis."','".$duree_episode."','".$poster."')";
		 		 $Record = mysql_query($query, $this->base)or die(mysql_error());
		 		 return mysql_insert_id();		 		 
		 }
		 
		 
		 private function info_serie($str){
		 		 $serietv = new AlloSerie($str);
		 		 $infos = $serietv->get();
		 		 //print_r($infos);
		 		 $id_allocine="";
		 		 if(isset($infos['id'])){$id_allocine=$infos['id'];}
		 		 
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
		 		 return array("info" => $infos,"id_allocine" => $id_allocine, "titre_original" =>
$titre_original , "titre" => $titre, "nb_saison" => $nb_saison ,
"nb_episodes" => $nb_episodes , "synopsis" => $synopsis ,
"duree_episode" => $duree_episode , "poster" => $poster);
		 }
		 
		 
		 		 
		 private function ajout_acteur_serie($id,$infos){
		 		 if(isset($infos['casting-court']['acteurs'])){
		 		 		 $acteurs=explode(",",$infos['casting-court']['acteurs']);
						 print_r($acteurs);
		 		 		 if(isset($acteurs[0])){
		 		 		 		 foreach($acteurs as $value){
		 		 		 		 		 $query_Recordset = "SELECT id FROM acteur WHERE nom LIKE
'".htmlspecialchars($value,ENT_QUOTES)."'";
		 		 		 		 		 $Recordset = mysql_query($query_Recordset, $this->base) or die(mysql_error());
		 		 		 		 		 $row_Recordset = mysql_fetch_assoc($Recordset);
		 		 		 		 		 if(isset($row_Recordset['id'])){
		 		 		 		 		 		 $query_Recordset2 = "INSERT INTO acteur_serie
(id_serie,id_acteur) VALUES
('".$id."','".$row_Recordset['id']."') ";
		 		 		 		 		 		 $Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());
		 		 		 		 		 }
		 		 		 		 		 
		 		 		 		 		 else{
		 		 		 		 		 		 $id_record=0;
		 		 		 		 		 		 $rea = new AlloPerson($value);
		 		 		 		 		 		 $infos_rea = $rea->get();
		 		 		 		 		 		 if(isset($infos_rea['id'])){$id_record=$infos_rea['id'];}		 		 		 
		 		 		 		 		 		 
		 		 		 		 		 		 $query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES
('".htmlspecialchars($value,ENT_QUOTES)."','".$id_record."') ";
		 		 		 		 		 		 $Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());
		 		 		 		 		 		 $id_record=mysql_insert_id();
		 		 		 		 		 		 
		 		 		 		 		 		 $query_Recordset2 = "INSERT INTO acteur_serie
(id_serie,id_acteur) VALUES ('".$id."','".$id_record."') ";
		 		 		 		 		 		 $Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());
		 		 		 		 		 		 
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
		 		 		 		 $Recordset1 = mysql_query($query_Recordset1, $this->base)or die(mysql_error());
		 		 
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
		 		 		 		 		 $Recordset1 = mysql_query($query_Recordset1, $this->base)or die(mysql_error());
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
				 $Recordset = mysql_query($query_Recordset, $this->base) or die(mysql_error());
				 $row_Recordset = mysql_fetch_assoc($Recordset);
				 if(isset($row_Recordset['id'])){return true;}
				 else return false;
		 }
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 function Filmotheque(){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	 
		 }
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		
		 
		 
		 
		 
		 /**
		 finir la fonction
		 */
		 private function delete_lien_film($id){
			$query_Recordset1 = "SELECT lien_pc FROM film where id='".$id."'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) or die(mysql_error());
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$liens=array();
			$liens=explode(",",$row_Recordset1 ["lien_pc"]);
			foreach($liens as $lien){
				if ($lien!=""){
					system("rm \"".$lien."\" " );
				}
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
		 
		 
		 private function fanart($titre,$annee){
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
		}

		 
		 
		 
		private function update_fanart($id){
			$query = "SELECT id,titre_original,annee_production FROM film where id = '".$id."' ";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
			$row_Recordset = mysql_fetch_assoc($Recordset);
			$temp=$this->fanart($row_Recordset['titre_original'],$row_Recordset['annee_production']);
			$query = "UPDATE film SET fanart='".$temp."' where id='".$id."'  ";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
		}
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 /**
		 *liste tous les films d'un acteur et recupere sa photo
		 *\return $poster,$value = array('img','lien','id','titre','role')
		 */ 
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
		 
		 
		 
		 
		 public function lancer_film($id,$lien=0,$serie=""){
			if($serie==""){	
				$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 		mysql_select_db($this->database_base, $this->base);
				$query_Recordset1 = "SELECT lien_pc FROM film where id='".$id."'";
				$Recordset1 = mysql_query($query_Recordset1, $this->base) or die(mysql_error());
				$row_Recordset1 = mysql_fetch_assoc($Recordset1);
				$liens=explode(";",$row_Recordset1['lien_pc']);
				system("open /Applications/vlc.app \"".$liens[$lien]."\"");
			}
			else{
				system("open /Applications/vlc.app \"".$serie."\"");
			} 			 
		 }
		 
		 public function liste_film_genre($id){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			if($id!="0"){
				$query_Recordset1 = "SELECT f.titre,f.id FROM film as f,genre_film as g, genre as i where f.id=g.id_film AND i.id=g.id_genre AND g.id_genre='".$id."'";
				$Recordset1 = mysql_query($query_Recordset1, $this->base) or die(mysql_error());
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
				$Recordset1 = mysql_query($query_Recordset1, $this->base) or die(mysql_error());
				$i=0;
				while($val = mysql_fetch_assoc($Recordset1)){					
					echo ' <option value="'.$val['id'].'" ';
			  		if($i==0){echo 'selected="selected"';}
					echo '>'.str_replace(":","",utf8_encode($val['titre'])).'</option> ';					
					$i++;					
				}
			} 			 
		 }
		 
		 
		 
		 
		 public function remplacer_lien_film($id_allocine,$dossier,$dossier_old,$file, $file_old,$supr=false){
			$racine_dossier=$this->racine_dossier($dossier);
			$racine_dossier_old=$this->racine_dossier($dossier_old);
			$lien=$racine_dossier."/".$file;
			$lien_old=($file_old!=""&&$racine_dossier_old!="")?$racine_dossier_old."/".$file_old:"";					 
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			$query_Recordset = "SELECT id FROM film WHERE id_allocine = ".$id_allocine." ";
			 $Recordset = mysql_query($query_Recordset, $this->base) or die(mysql_error());
			 $row_Recordset = mysql_fetch_assoc($Recordset);
			 $id=$row_Recordset['id'];
			if($supr){$this->delete_lien_film($id);}
			$query="UPDATE film SET lien_pc='".$lien."' WHERE id='".$id."'";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());			
			$this->rename_fichier($racine_dossier,$lien,$lien_old);			
		 }
		 
		 
		 
		 
		 public function delete_film($id,$supr=false){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			if($supr){$this->delete_lien_film($id);}
			$query="delete from film WHERE id='".$id."'";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());			
		 }
		 
		 
		 public function creer_serie($name){
		 		 $this->base = mysql_connect($this->hostname_base, $this->username_base,
$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 		 mysql_select_db($this->database_base, $this->base);
		 		 if(!$this->verif_existe_serie($name)){
		 		 		 $tab=$this->info_serie($name);
						 if ($tab['nb_saison']==0){$tab['nb_saison']=1;}
		 		 		 $id=$this->insert_serie($tab['id_allocine'],$tab['titre_original'],$tab['titre'],$tab['nb_saison'],$tab['nb_episodes'],$tab['synopsis'],$tab['duree_episode'],$tab['poster']);
						 $this->ajout_acteur_serie($id,$tab['info']);						 
		 		 		 $this->ajout_all_episode($tab['id_allocine'],$id,$tab['nb_saison']);
		 		 }
		 }
		 
		 
		 
		 public function delete_serie($id,$supr=false){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			if($supr){$this->delete_lien_film($id);}
			$query="delete from serie WHERE id='".$id."'";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());			
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
					if(preg_match("#.+MKV$#i",$file)||preg_match("#.+AVI$#i",$file)){
						$tab[]=$file;
						$saisons[]="";$episodes[]="";						
						if(preg_match("#.+([0-9][1-9])([0-9]{2}).+#i",$file,$matches0)){$saisons[$i]=$matches0[1];$episodes[$i]=$matches0[2];}//*0103*			
						if(preg_match("#.+\.([0-9])([0-9]{2}).+#i",$file,$matches1)){$saisons[$i]=$matches1[1];$episodes[$i]=$matches1[2];}//*.103*						
						if(preg_match("#.+ ([0-9])([0-9]{2}).+#i",$file,$matches1)){$saisons[$i]=$matches1[1];$episodes[$i]=$matches1[2];}//* 103*						
						if(preg_match("#.+([0-9])x([0-9]{2}).+#i",$file,$matches2)){$saisons[$i]=$matches2[1];$episodes[$i]=$matches2[2];}//*1x09*						
						if(preg_match("#.+\[s([0-9]{1,2})\]_\[e([0-9]{2})\].+#i",$file,$matches3)){$saisons[$i]=$matches3[1];$episodes[$i]=$matches3[2];}//*[s01]_[e01].*						
						if(preg_match("#.+s([0-9]{1,2})e([0-9]{2}).+#i",$file,$matches4)){$saisons[$i]=$matches4[1];$episodes[$i]=$matches4[2];}//*s01e01*						
						if(preg_match("#.+s([0-9]{1,2})\.e([0-9]{2}).+#i",$file,$matches5)){$saisons[$i]=$matches5[1];$episodes[$i]=$matches5[2];}//*s01.e01*						
						if(preg_match("#.+s([0-9]{1,2})_e([0-9]{2}).+#i",$file,$matches6)){$saisons[$i]=$matches6[1];$episodes[$i]=$matches6[2];}//*s01_e01*			
						$i++;
					}
				}				
			}			
			$nb=$i;			
			closedir($dir);
			return array("tab"=>$tab,"saisons"=>$saisons,"episodes"=>$episodes,"nb"=>$nb); 
		 }
		 
		 public function lister_dossier_film($dirname){
			$dir = opendir($dirname);
			$tab=array();
			$i=0; 			
			while($file = readdir($dir)) {
				if($file != '.' && $file != '..' && !is_dir($dirname.$file))
				{
					if(preg_match("#.+MKV$#i",$file)||preg_match("#.+AVI$#i",$file)){
						$tab[]=$file;			
						$i++;
					}
				}				
			}			
			$nb=$i;			
			closedir($dir);
			return array("tab"=>$tab,"nb"=>$nb); 
		 }
		 
		 public function ajouter_lien_film($id_allocine,$dossier,$dossier_old,$file, $file_old){
			$racine_dossier=$this->racine_dossier($dossier);
			$racine_dossier_old=$this->racine_dossier($dossier_old);
			$file=$racine_dossier."/".$file;
			$lien_old=($file_old!=""&&$racine_dossier_old!="")?$racine_dossier_old."/".$file_old:"";
			$this->ajouter_lien_film2($id_allocine,$file);			
			$this->rename_fichier($racine_dossier,$file,$lien_old);
		 }
		 
		 public function ajouter_lien_film2($id_allocine,$lien){
			$extension=pathinfo($file, PATHINFO_EXTENSION);
			$base_lien=str_replace(".".$extension,"",$lien);
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			$query_Recordset1 = "SELECT lien_pc FROM film where id_allocine = '".$id_allocine."' ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) or die(mysql_error());
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			if($row_Recordset1['lien']!="" && $row_Recordset1['lien']!=NULL){
				$nb= substr_count($row_Recordset1['lien'],",")+1;
				$lien_pc=$row_Recordset1['lien'].",".$base_lien."_".$nb.".".$extension;	
			}
			else{
				$lien_pc=$lien;
			}			
			$query= "UPDATE film SET lien_pc = '".$lien_pc."' where id_allocine = '".$id_allocine."' ";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
		 }
		 
		 public function ajouter_lien_episode($id,$lien){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);		 
			$query= "UPDATE episode SET lien = CONCAT(lien,',".$lien."') where id = '".$id."' ";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
		 }
		 
		 
		 public function lister_parametre_destination(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			$query_Recordset1 = "SELECT * FROM parametres where id='1'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) or die(mysql_error());
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$dossiers=explode(";",$row_Recordset1['repertoires']);
			return $dossiers;
		 }
		 
		 public function lister_serie(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			$query = "SELECT titre,id FROM serie ";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
			$serie=array();
			while($row = mysql_fetch_assoc($Recordset)){
				$serie[]=array('id'=>$row['id'],'titre'=>$row['titre']);
			}
			return $serie;
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
			system("mv \"".$lien_old."\" \"".$lien."\"");
			return TRUE;
		 }
		 
		 private function racine_dossier($dossier){
			 if($dossier==""){return $dossier;}
			if(substr($dossier,-1)!="/"){$dossier=$dossier."/";} 
			 return substr($dossier, 0,strlen($dossier)-1);
		 }
		 
		 public function creer_film($id_allocine, $dossier,$dossier_old,$file, $file_old){
			$racine_dossier=$this->racine_dossier($dossier);
			$racine_dossier_old=$this->racine_dossier($dossier_old);
			$lien=$racine_dossier."/".$file;
			$lien_old=($file_old!=""&&$racine_dossier_old!="")?$racine_dossier_old."/".$file_old:"";
			$this->rename_fichier($racine_dossier,$lien,$lien_old);			 
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			if(!$this->verif_existe_film($id_allocine)){
				$movie = new AlloMovie((int)$id_allocine);
				$infos = $movie->get();
			}
			$titre_original="";
			if(isset($infos['titre-original'])){$titre_original=htmlspecialchars($infos['titre-original'],ENT_QUOTES);}
			$titre="";
			if(isset($infos['titre'])){$titre=htmlspecialchars($infos['titre'],ENT_QUOTES);}
			$annee="";
			if(isset($infos['annee-production'])){$annee=str_replace('-','',htmlspecialchars($infos['annee-production'],ENT_QUOTES));}
			$duree="";
			if(isset($infos['duree']['global'])){$duree=htmlspecialchars($infos['duree']['global'],ENT_QUOTES);}
			$synopsis="";
			if(isset($infos['synopsis'])){$synopsis=htmlspecialchars($infos['synopsis'],ENT_QUOTES);}
			$bande="";
			if(isset($infos['bande-annonce']['href'])){$bande=htmlspecialchars($infos['bande-annonce']['href'],ENT_QUOTES);}
			$poster="";
			if(isset($infos['poster'])){$poster=htmlspecialchars($infos['poster'],ENT_QUOTES);}
			$fanart=$this->fanart($titre,$annee);			
			$id_film=$this->insert_film($id_allocine,$titre_original,$titre,$annee,$duree,$synopsis,$bande,$lien,$poster,$fanart);
			//remplissage du genre
			$tab=$this->lister_genre();
			if(isset($infos['genre'])){
				foreach($infos['genre'] as $genre){
					$id_genre=(isset($tab[$genre]))?$tab[$genre]:$this->creer_genre($genre);
					$this->inserer_genre_film($id_genre,$id_film);
				}
			}
			
			//realisateur
			if(isset($infos['casting-court']['realisateurs'])){
				$realisateur=htmlspecialchars($infos['casting-court']['realisateurs'],ENT_QUOTES);
			}
			else{
				$realisateur="";			
			}
			$this->ajout_realisateur_film($realisateur,$id_film);
			
			//acteurs
			$this->ajout_acteur_film($id_film,$infos);
			return $id_film;
		 }
		 
		 
		 
		 
		  public function modifier_film($id,$id_old){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			$query = "SELECT lien_pc FROM film where id='".$id_old."'";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
			$row = mysql_fetch_assoc($Recordset);
			$lien=$row['lien_pc'];
			
			//suppression du vieux film
			$this->delete_film($id_old);
			
			//si ce film existe deja on modifie juste le lien_pc			
			$query = "SELECT id,id_allocine FROM film where id_allocine='".$id."'";
			$Recordset1 = mysql_query($query, $this->base) or die(mysql_error());
			$row1 = mysql_fetch_assoc($Recordset1);
			if(isset($row1['id'])){
				$this->ajouter_lien_film($row1['id_allocine'],$lien);
				return $row1['id'];		
			}

			return creer_film($id, $lien);
		}
		
		
		public function rechercher_film_allocine($name,$echo=TRUE){
			$film=str_replace(".avi","",htmlspecialchars($name,ENT_QUOTES));
			$film=str_replace(".mkv","",$film);
			$film=str_replace(".AVI","",$film);
			$film=str_replace(".MKV","",$film);
			$movie = new AlloMovie($film);
			$infos = $movie->get();
			if(isset($infos['id'])){
				$id_allocine=$infos['id'];
				$poster="";
				if(isset($infos['poster'])){$poster=htmlspecialchars($infos['poster'],ENT_QUOTES);}
				if(!$this->verif_existe_film($id_allocine)){
					$synopsis=(isset($infos['synopsis']))?$infos['synopsis']:"";
				}
				else{$synopsis="ce film existe deja";}
				$tab=array($id_allocine,$synopsis,$poster);
			}
			else{$tab=array("","film introuvable","");}
			if($echo){echo utf8_encode($tab[0]."__synopsis__".$tab[1]."__synopsis__".$tab[2]); }
			return $tab;
		}
		
		public function rechercher_film_id_allocine($id,$echo=TRUE){
			$movie = new AlloMovie((int)$id);
			$infos = $movie->get();
			if(isset($infos['id'])){
				$id_allocine=$infos['id'];
				$poster="";
				if(isset($infos['poster'])){$poster=htmlspecialchars($infos['poster'],ENT_QUOTES);}
				if(!$this->verif_existe_film($id_allocine)){
					$synopsis=(isset($infos['synopsis']))?$infos['synopsis']:"";
				}
				else{$synopsis="ce film existe deja";}
				$tab=array($id_allocine,$synopsis,$poster);
			}
			else{$tab=array("","film introuvable","");}
			if($echo){echo utf8_encode($tab[0]."__synopsis__".$tab[1]."__synopsis__".$tab[2]); }
			return $tab;
		}
		 
		 
		 private function creer_genre($name){
			$query = "INSERT INTO genre (nom) VALUES('".$name."')";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
			return mysql_insert_id(); 
		 }
		 
		 private function inserer_genre_film($id_genre,$id_film){
			$query = "INSERT INTO genre_film (id_genre,id_film) VALUES('".$id_genre."','".$id_film."')";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
		}
		 
		 public function lister_genre(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			$query = "SELECT nom,id FROM genre";
			$Recordset = mysql_query($query, $this->base) or die(mysql_error());
			$tab= array();
			while($row_Recordset2 = mysql_fetch_assoc($Recordset)){
				$tab[$row_Recordset2['nom']]=$row_Recordset2['id'];
			}
			return $tab;			 
		 }
		 
		 private function verif_existe_film($id_allocine){
		 		 $query_Recordset = "SELECT id FROM film WHERE id_allocine = ".$id_allocine." ";
				 $Recordset = mysql_query($query_Recordset, $this->base) or die(mysql_error());
				 $row_Recordset = mysql_fetch_assoc($Recordset);
				 if(isset($row_Recordset['id'])){return true;}
				 else return false;
		 }
		 
		 private function insert_film($id_allocine,$titre_original,$titre,$annee,$duree,$synopsis,$bande,$lien,$poster,$fanart){
		 		 /*$this->base = mysql_connect($this->hostname_base, $this->username_base,
$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 		 mysql_select_db($this->database_base, $this->base);*/
		 		 $query = "INSERT INTO film (id_allocine,titre_original,titre,annee_production,duree,synopsis,bande_annonce,lien_pc,poster,fanart) VALUES('".$id_allocine."','".$titre_original."','".$titre."','".$annee."','".$duree."','".$synopsis."','".$bande."','".$lien."','".$poster."','".$fanart."')";
		 		 $Record = mysql_query($query, $this->base)or die(mysql_error());
		 		 return mysql_insert_id();	
		 }
		 
		 
		private function ajout_realisateur_film($realisateur,$id_film){
			$query_Recordset = "SELECT id FROM acteur WHERE nom LIKE '".$realisateur."'";
			$Recordset = mysql_query($query_Recordset, $this->base) or die(mysql_error());
			$row_Recordset = mysql_fetch_assoc($Recordset);
			if(isset($row_Recordset['id'])){
				$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_realisateur) VALUES ('".$id_film."','".$row_Recordset['id']."') ";
				$Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());
			}			
			else{
				$id=0;
				if($realisateur!=""){
					$rea = new AlloPerson($realisateur);
					$infos_rea = $rea->get();
					if(isset($infos_rea['id'])){$id=$infos_rea['id'];}			
				}
				$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".$realisateur."','".$id."') ";
				$Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());
				$id=mysql_insert_id();
				
				$query_Recordset2 = "INSERT INTO realisateur_film (id_film,id_realisateur) VALUES ('".$id_film."','".$id."') ";
				$Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());				
			}			
		}
		 
		 
		 		 
		 private function ajout_acteur_film($id_film,$infos){
		 	for ($i=0;$i<7;$i++){
				if(isset($infos['casting'][$i]) && $infos['casting'][$i]['activite']!='Réalisateur'){
					$query_Recordset = "SELECT id FROM acteur WHERE nom LIKE '".htmlspecialchars($infos['casting'][$i]['personne'],ENT_QUOTES)."'";
					$Recordset = mysql_query($query_Recordset, $this->base) or die(mysql_error());
					$row_Recordset = mysql_fetch_assoc($Recordset);
					if(isset($row_Recordset['id'])){
						$query_Recordset2 = "INSERT INTO acteur_film (id_film,id_acteur,role) VALUES ('".$id_film."','".$row_Recordset['id']."','".htmlspecialchars($infos['casting'][$i]['role'],ENT_QUOTES)."') ";
						$Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());
					}					
					else{
						$id=0;
						$rea = new AlloPerson($infos['casting'][$i]['personne']);
						$infos_rea = $rea->get();
						if(isset($infos_rea['id'])){$id=$infos_rea['id'];}	
						$query_Recordset2 = "INSERT INTO acteur (nom,id_allocine) VALUES ('".htmlspecialchars($infos['casting'][$i]['personne'],ENT_QUOTES)."','".$id."') ";
						$Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());
						$id=mysql_insert_id();						
						$query_Recordset2 = "INSERT INTO acteur_film (id_film,id_acteur,role) VALUES ('".$id_film."','".$id."','".htmlspecialchars($infos['casting'][$i]['role'],ENT_QUOTES)."') ";
						$Recordset2 = mysql_query($query_Recordset2, $this->base) or die(mysql_error());						
					}
				}
			}
		 }		 
		 
		 
		
		 
		 
		 
		 
		
		 
		 public function modifier_film_manuellement(){}
		 
		 public function inserer_lien_film($lien){}
		 
		 public function supprimer_lien(){}
		 
		 public function lister_lien(){}
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

