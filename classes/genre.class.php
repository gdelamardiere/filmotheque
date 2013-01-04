<?php
require_once('database.class.php');

class genre{
	private $pdo;
	
	function __construct(){
		$this->pdo=database::getInstance();
	}


	public function display_film_genre($id_genre){
			if($id_genre!="0"){
				$stmt = $this->pdo->prepare("SELECT f.titre,f.id_film FROM film as f,genre_film as g, genre as i where f.id_film=g.id_film AND i.id_genre=g.id_genre AND g.id_genre=:id_genre order by f.titre");
				$stmt->execute(array('id_genre' => $id_genre));
			}
			else{
				$query_Recordset1 = "";
				$stmt = $this->pdo->prepare("SELECT f.titre,f.id_film FROM film as f order by f.titre");
				$stmt->execute();
			} 				
			$i=0;
			while($val = $stmt->fetch(PDO::FETCH_ASSOC)){					
				echo ' <option value="'.$val['id_film'].'" ';
		  		if($i==0){echo 'selected="selected"';}
				echo '>'.str_replace(":","",utf8_encode($val['titre'])).'</option> ';					
				$i++;					
			}						 
		 }

		 function ajout_genres_film($infos,$id_film){
			$tab=$this->lister_genre();
			foreach($infos as $genres){
					$id_genre=(isset($tab[$genres['$']]))?$tab[$genres['$']]:$this->creer_genre($genres['$']);
					$this->inserer_genre_film($id_genre,$id_film);
				}
		}



	 function inserer_genre_film($id_genre,$id_film){
			$stmt = $this->pdo->prepare("INSERT INTO genre_film (id_genre,id_film) VALUES(:id_genre,:id_film)");
			$stmt->execute(array("id_genre"=>$id_genre,"id_film"=>$id_film));
		}


	private function creer_genre($name){
			$stmt = $this->pdo->prepare("INSERT INTO genre (nom) VALUES(:name)");
			$stmt->execute(array("name"=>$name));
			return PDO::lastInsertId(); 
		 }

		  public function lister_genre(){
			$stmt = $this->pdo->prepare("SELECT nom,id_genre FROM genre");
			$stmt->execute() ;
			$tab= array();
			while($row_Recordset2 = $stmt->fetch(PDO::FETCH_ASSOC)){
				$tab[$row_Recordset2['nom']]=$row_Recordset2['id_genre'];
			}
			return $tab;			 
		 }





/**todo*/
		 public function deplacer_all_film_repertoire_genre(){ 		 	
		 	$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
		 	$query_Genre = "SELECT *  from genre order by ordre, nom";
			$Record = mysql_query($query_Genre) or die(mysql_error());
			while($row_Genre = mysql_fetch_assoc($Record)){
				$query_Recordset1 = "SELECT l.id_liens,l.lien from liens l, genre_film gf where l.id_film=gf.id_film	AND gf.id_genre = ".$row_Genre['id_genre']." AND l.classe='0'";
				$Recordset1 = mysql_query($query_Recordset1) or die(mysql_error());
				while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
					$racine_dossier_old=dirname($row_Recordset1['lien']);
					$racine_dossier="/Volumes/DDE_FILM/film/".$row_Genre['rep_dest'];
					$file=$racine_dossier."/".basename($row_Recordset1['lien']);
					var_dump($racine_dossier,$file,$row_Recordset1['lien']);echo "<br>";
					if($racine_dossier_old!=$racine_dossier){		
						$this->rename_fichier($racine_dossier,$file,$row_Recordset1['lien']);						
					}
					$query="update liens set lien='".$file."',classe='1' WHERE id_liens =".$row_Recordset1['id_liens'];					
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



?>