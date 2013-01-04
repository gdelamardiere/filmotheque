<?php
require_once('database.class.php');

class acteurs{
	private $pdo;

	public function getIdAllocine()
	
	{

		return $this->id_allocine;

	}
	
	
	
	public function setIdAllocine($id_allocine)
	
	{

		$this->id_allocine = $id_allocine;

		return $this;

	}
	
	function __construct(){
		$this->pdo=database::getInstance();
		//$this->helper=new AlloHelper();
	}



	

	



	/**
	 * @param  [type] $id          id_allocine du film
	 * @param  [type] $aCastMember [castMember] => Array([0] => Array([person] => Array([code] => 38756,[name] => Patrick Lussier),
	 *                             									[activity] => Array([$] => Réalisateur),
	 *                                      						[role] => John Milton)
	 * @return [type]              [description]
	 */
	function ajoutActeursFilm($id_film,$aCastMember){
		$stmt_count = $this->pdo->prepare("SELECT count(id_acteur) as nb FROM acteur WHERE id_acteur = :id_acteur");
		$stmt_acteur = $this->pdo->prepare("INSERT INTO acteur(id_acteur,nom) values(:id_acteur,:name)");
		$stmt_insert_realisateur = $this->pdo->prepare("REPLACE INTO realisateur_film(id_realisateur,id_film) values(:id_realisateur,:id_film)");
		$stmt_insert_acteur = $this->pdo->prepare("REPLACE INTO acteur_film(id_acteur,id_film,role) values(:id_acteur,:id_film,:role)");
		foreach($aCastMember as $value){
			$stmt_count->execute(array('id_acteur' => $value['person']['code']));
			$nb=$stmt_count->fetch(PDO::FETCH_ASSOC);
			if($nb['nb']==0){
				$stmt_acteur->execute(array('id_acteur' => $value['person']['code'],'nom' => $value['person']['name']));
			}
			if($value['activity']['$']=="Réalisateur"){
				$stmt_insert_realisateur->execute(array('id_realisateur' => $value['person']['code'],'id_film'=>$id_film));
			}
			elseif($value['activity']['$']=="Acteur"){
				$stmt_insert_acteur->execute(array('id_acteur' => $value['person']['code'],'id_film'=>$id_film,'role'=>$value['role']));
			}
		}
	}

	function ajoutActeursSerie($id_serie,$aCastMember){		
		$stmt_count = $this->pdo->prepare("SELECT count(id_acteur) as nb FROM acteur WHERE id_acteur = :id_acteur");
		$stmt_acteur = $this->pdo->prepare("INSERT INTO acteur(id_acteur,nom) values(:id_acteur,:name)");
		$stmt_insert_acteur = $this->pdo->prepare("REPLACE INTO acteur_serie(id_acteur,id_serie) values(:id_acteur,:id_serie)");
		foreach($aCastMember as $value){
			$stmt_count->execute(array('id_acteur' => $value['person']['code']));
			$nb=$stmt_count->fetch(PDO::FETCH_ASSOC);
			if($nb['nb']==0){
				$stmt_acteur->execute(array('id_acteur' => $value['person']['code'],'nom' => $value['person']['name']));
			}if($value['activity']['$']=="Acteur"){
				$stmt_insert_acteur->execute(array('id_acteur' => $value['person']['code'],'id_serie'=>$id_serie));
			}
		}
	}

/*
function findIDActeurSerie($name){
		$acteur=$this->helper->search( $name, 1, 1, false, array("person"));
		if($acteur['totalResults']==1){
			$stmt = $this->pdo->prepare("INSERT INTO acteur (id_serie,id_acteur) VALUES (:id_serie,:id_acteur");
			$stmt->execute(array('id_serie' => $id_serie,'id_acteur' => $id_acteur));
			return $acteur['person'][0]['code'];
		}
		else{return null;}
	}
 */
		/* function addActeurSerie($id_serie,$id_acteur){
		 	if(empty($id_acteur)) return false;
		 	$stmt = $this->pdo->prepare("INSERT INTO acteur_serie (id_serie,id_acteur) VALUES (:id_serie,:id_acteur");
			$stmt->execute(array('id_serie' => $id_serie,'id_acteur' => $id_acteur));
			return true;
		}*/

		 /*function findIDActeurFilm($name){
		$acteur=$this->helper->search( $name, 1, 1, false, array("person"));
		if($acteur['totalResults']==1){
			$stmt = $this->pdo->prepare("INSERT INTO acteur_serie (id_serie,id_acteur) VALUES (:id_serie,:id_acteur");
			$stmt->execute(array('id_serie' => $id_serie,'id_acteur' => $id_acteur));
			return $acteur['person'][0]['code'];
		}
		else{return null;}
	}*/
/*public function film_acteur_allocine($id_allocine){
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
		} */
	




}



?>