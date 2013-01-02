<?php 

class film{

	
		
		 
		 		 
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
		 
		 
		
		
		
		
		
		
		 
		
		 
		 
		 
		 
		 private function verif_existe_film($id_allocine){
		 		 $query_Recordset = "SELECT id FROM film WHERE id_allocine = ".$id_allocine." ";
				 $Recordset = mysql_query($query_Recordset, $this->base) ;
				 $row_Recordset = mysql_fetch_assoc($Recordset);
				 if(isset($row_Recordset['id'])){return true;}
				 else return false;
		 }
		 
		  
		 
		 private function insert_film($id_allocine,$titre_original,$titre,$annee,$duree,$synopsis,$bande,$lien,$poster,$fanart){
		 		 $query = "INSERT INTO film (id_allocine,titre_original,titre,annee_production,duree,synopsis,bande_annonce,poster,fanart) VALUES('".$id_allocine."','".$titre_original."','".$titre."','".$annee."','".$duree."','".$synopsis."','".$bande."','".$poster."','".$fanart."')";
		 		 $Record = mysql_query($query, $this->base);
		 		 $id=mysql_insert_id();
		 		 $query= 'INSERT INTO liens (id_film,lien,nom) VALUES('.$id.', "'.$lien.'","liens1") ';
				$Recordset = mysql_query($query, $this->base) ;
		 		 return $id;	
		 }
		 
		 
				 
		 
		 		 
		 
		 
		 
		 public function modifer_interet_film($id,$value){
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);	
			$query = "update film SET interet='".$value."' where id='".$id."' ";
			$Recordset = mysql_query($query, $this->base) ;
		 }
		 
		 
		 
		 /*******fin film***/
		 
		 
		 
		 

		 
		 
		  
		 
		 
		 public function modifier_film_manuellement(){}
		 
		
		 
		 
		 
		
		 

}

?>