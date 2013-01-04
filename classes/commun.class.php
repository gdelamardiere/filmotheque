<?php


class commun{

	public static function  racine_dossier($dossier){
		if($dossier==""){return $dossier;}
		if(substr($dossier,-1)!="/"){$dossier=$dossier."/";} 
		return substr($dossier, 0,strlen($dossier)-1);
	}

	public static function  mef_lien($dossier,$dossier_old,$file,$file_old){
		$racine_dossier=$self::racine_dossier($dossier);
		$racine_dossier_old=$self::racine_dossier($dossier_old);
		$file=$racine_dossier."/".$file;
		$lien_old=($file_old!=""&&$racine_dossier_old!="")?$racine_dossier_old."/".$file_old:"";
		return array($racine_dossier,$file,$lien_old);
	}

}

?>