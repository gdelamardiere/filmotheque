<?php
require_once('database.class.php');

class mot_corrige{
	private $pdo;
	
	function __construct(){
		$this->pdo=database::getInstance();
	}
	public  function update_mot_corrige($lien_init,$lien_final){	
		if($lien_init!=$lien_final){
			$extension=pathinfo($lien_init, PATHINFO_EXTENSION);
			$lien_init=str_replace(".".$extension,'',$lien_init);			 	
			$lien_final=str_replace(".".$extension,'',$lien_final);	  	
			$correctif=str_replace($lien_final,"",$lien_init);
			$aCorrectifs=explode(" ",$correctif);
			foreach($aCorrectifs as $value){
				$value=strtoupper($value);
				if($value!=""){ 
					$stmt= $this->pdo->prepare("INSERT INTO mot_corrige (mot) SELECT :value FROM mot_corrige WHERE NOT EXISTS (SELECT NULL FROM mot_corrige WHERE mot = :value limit 1) limit 1");
					$stmt->execute(array('value' => $value));
				}	
			}
		}
	}

	public function preparer_lien_allocine($lien){
		$stmt= $this->pdo->prepare("SELECT mot FROM mot_corrige");
		$stmt->execute() ;
		$extension=pathinfo($lien, PATHINFO_EXTENSION);
		$lien=" ".str_replace($extension,'',$lien);	
		$lien=preg_replace('#[^a-z0-9]#i',' ',$lien);
		$lien=preg_replace('# ([a-z]) #i',' ',$lien);
		$lien=preg_replace('#[0-9]{4}#i','',$lien);		 	
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$lien=str_ireplace(" ".$row['mot']." ",' ',$lien);
		}
		$lien=preg_replace('# {2,}#i',' ',$lien);
		return trim($lien).".".$extension;
	}




}

?>