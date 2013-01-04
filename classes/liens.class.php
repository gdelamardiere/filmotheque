<?php
require_once('parametres.class.php');
require_once('fichier.class.php');
class liens{

	
	private $pdo;

	
	
	function __construct(){
		$this->pdo=database::getInstance();
	}

	public static function lien_allocine_serie($id_serie){
		return "http://www.allocine.fr/series/ficheserie_gen_cserie=".$id_serie.".html";			
	}

	public static function lien_allocine_saison($id_serie,$id_episode){
		return "http://www.allocine.fr/series/ficheserie-".$id_serie."/saisons/";			

	}

	public static function lien_allocine_film($id_film){
		return "http://www.allocine.fr/film/fichefilm_gen_cfilm=".$id_film.".html";			

	}

	private function lien_film_unique($id_film){
		$stmt= $this->pdo->prepare("SELECT lien FROM liens WHERE id_film = :id_film limit 1 ");
		$stmt->execute(array('id_film' => $id_film)) ;
		$row_Recordset = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row_Recordset['lien'];
	}

	public function liste_lien_film($id_film){
		$stmt= $this->pdo->prepare("SELECT id,qualite,lien,nom FROM liens where id_film=:id_film ");
		$stmt->execute(array('id_film' => $id_film)) ;
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	private function delete_all_liens_film($id_film){
		$stmt= $this->pdo->prepare("SELECT lien FROM liens WHERE id_film = :id_film ");
		$stmt->execute(array('id_film' => $id_film));
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			fichier::delete_file($row["lien"]);
		}
		$stmt= $this->pdo->prepare("DELETE FROM liens where id_film= :id_film ");
		$stmt->execute(array('id_film' => $id_film)) ;
	}

	public function delete_lien_film($lien){
		$stmt= $this->pdo->prepare("DELETE FROM liens where lien like:lien LIMIT 1");
		$stmt->execute(array('lien' => $lien));
		fichier::delete_file($lien);
	}



	public function remplacer_lien_film($id_film,$dossier,$dossier_old,$file, $file_old){
		$this->delete_all_liens_film($id_film);
		$this->ajouter_lien_film($id_allocine,$dossier,$dossier_old,$file, $file_old);
	}

	public function update_liens_id_film($id_new,$id_old){
		$stmt= $this->pdo->prepare("UPDATE FROM liens SET id_film=:id_new where id_film=:id_old ");
		$stmt->execute(array('id_new' => $id_new,'id_old' => $id_old)) ;
	}



	public function ajouter_lien_film($id_film,$dossier,$dossier_old,$file, $file_old){
		list($racine_dossier,$file,$lien_old)=commun::mef_lien($dossier,$dossier_old,$file, $file_old);
		$file=$this->insert_lien_film($id_film,$file);
		fichier::rename_fichier($racine_dossier,$file,$lien_old);
	}

	public function insert_lien_film($id_film,$lien){
		$extension=pathinfo($lien, PATHINFO_EXTENSION);
		$base_lien=str_replace(".".$extension,"",$lien); 
		$stmt= $this->pdo->prepare("SELECT COUNT( DISTINCT l.id ) as nb FROM  liens WHERE id_film = :id_film ");
		$stmt->execute(array('id_film' => $id_film)) ;
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$nb= $row['nb']+1;
		if($nb > 1){				
			$lien_pc=$base_lien."_".$nb.".".$extension;	
		}
		else{
			$lien_pc=$lien;
		}
		$stmt= $this->pdo->prepare("INSERT INTO liens (id_film,lien,nom) VALUES(:id_film, :lien_pc,:nom)");
		$stmt->execute(array('id_film' => $id_film,"lien_pc"=>$lien_pc,"nom"=>"lien".$nb)) ;
		return $lien_pc;
	}



	public function ajouter_lien_serie($id_serie,$saison,$episode,$dossier,$dossier_old,$file, $file_old){
		list($racine_dossier,$file,$lien_old)=commun::mef_lien($dossier,$dossier_old,$file, $file_old);
		$this->insert_lien_serie($id_serie,$saison,$episode,$file);		
		$this->rename_fichier($racine_dossier,$file,$lien_old);	
	}

	public function insert_lien_serie($id_serie,$saison,$episode,$lien){
		$extension=pathinfo($lien, PATHINFO_EXTENSION);
		$base_lien=str_replace(".".$extension,"",$lien);
		$stmt= $this->pdo->prepare("SELECT e.id, COUNT( DISTINCT l.id ) as nb FROM  liens as l, episode as e 
			WHERE e.id=l.id_episode and e.id_serie = :id_serie AND id_saison = :saison AND num_episode = :episode");
		$stmt->execute(array("id_serie" => $id_serie,"saison"=>$saison,"episode"=>$episode));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$nb= $row['nb']+1;
		if($nb > 1){				
			$lien_pc=$base_lien."_".$nb.".".$extension;	
		}
		else{
			$stmt= $this->pdo->prepare("SELECT id FROM  episode WHERE id_serie = :id_serie AND id_saison = :saison AND num_episode = :episode");
			$stmt->execute(array("id_serie" => $id_serie,"saison"=>$saison,"episode"=>$episode)) ;
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$lien_pc=$lien;
		}			
		$stmt= $this->pdo->prepare("INSERT INTO liens (id_serie,lien,nom) VALUES(:id_serie, :lien_pc,:nom)");
		$stmt->execute(array('id_serie' => $row['id'],"lien_pc"=>$lien_pc,"nom"=>"lien".$nb)) ;
	}


	public function modifer_qualite_lien($id,$value){
		$stmt= $this->pdo->prepare("UPDATE liens SET qualite=:value WHERE id=:id");
		$stmt->execute(array("value" => $value,"id"=>$id)) ;
	}


	

	public function deplacer_dossier_film($dossier_init,$dossier_fin){
		$stmt= $this->pdo->prepare("UPDATE liens SET lien = REPLACE (lien,:dossier_init,:dossier_fin)
			WHERE lien like :dossier_init AND id_film is not null");
		$stmt->execute(array("dossier_init"=>$dossier_init,"dossier_fin"=>$dossier_fin)) ;
	}

	public function deplacer_dossier_serie($dossier_init,$dossier_fin){
		$stmt= $this->pdo->prepare("UPDATE liens SET lien = REPLACE (lien,:dossier_init,:dossier_fin)
			WHERE lien like :dossier_init AND id_serie is not null");
		$stmt->execute(array("dossier_init"=>$dossier_init,"dossier_fin"=>$dossier_fin)) ;
	}



}