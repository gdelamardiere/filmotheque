<?php 

class serie{

		 /**
		* ajoute dans la base sql les données d'une nouvelle série
		*retourne l'id de la nouvelle série
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


		public function lister_dossier_film($dirname){

			return array("tab"=>$tab,"nb"=>sizeof($tab)); 
		}

		public function lister_dossier_serie($dirname){
			$episodes=array();
			$saisons=array();
			$i=0; 
			$tab=fichier::get_video_repertoire($dirname);
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
			return array("tab"=>$tab,"saisons"=>$saisons,"episodes"=>$episodes,"nb"=>$nb); 
		} 


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

	}

	?>