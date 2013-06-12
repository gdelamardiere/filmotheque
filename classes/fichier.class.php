<?php
require_once('parametres.class.php');

/**
 * class fichier 
 */
class fichier{
	
	/**
	 * rename un fichier et ses sous-titres si il en a
	 * @param  string $dossier  répertoire ou l'on va déplacer le fichier
	 * @param  string $lien     nouveau nom di fichier
	 * @param  string $lien_old lien absolu vers le fichier actiel
	 * @return boolen
	 */
	public static function rename_fichier($dossier,$lien,$lien_old){
		$lien_old=str_replace("\'","'",$lien_old);
		if (!file_exists($lien_old)) {
			return FALSE;
		}
		$lien=str_replace("\'"," ",$lien);
		if (!file_exists($dossier)&& $dossier!="") {
			$param=array($dosier);
			system("mkdir",$param);
		}		
		
		$extension=pathinfo($lien, PATHINFO_EXTENSION);
		$listeExtSub=parametres::getExtensionsSubtitles();
		foreach($listeExtSub as $sub){
			$sous_titre_old=str_replace($extension,$sub,$lien_old);
			if (file_exists($sous_titre_old)) {
				$sous_titre=str_replace($extension,$sub,$lien);
				$param=array($sous_titre_old,$sous_titre);
				self::executer("mv",$param);
			}	
		}
		$param=array($lien_old,$lien);
		echo self::executer("mv",$param);
		return TRUE;
	}
	
	
	
	
	
	 /**
	  * 
	  * @param  [type] $dossier     [description]
	  * @param  [type] $dossier_old [description]
	  * @param  [type] $name        [description]
	  * @param  [type] $old_name    [description]
	  * @return [type]
	  */
	 public static function rename_fichier_2($dossier,$dossier_old,$name, $old_name){
	 	if(substr($dossier_old,-1)!="/"){$dossier_old=$dossier_old."/";}
	 	if(substr($dossier,-1)!="/"){$dossier=$dossier."/";}	
	 	$dossier_temp=substr($dossier, 0,strlen($dossier)-1);		
	 	self::rename_fichier($dossier_temp,$dossier_old.$file_old,$dossier.$file);
	 }
	 /**
	  * affiche le fichier dans le finder
	  * @param  string $lien lien vers le fichier
	  * @return vide
	  */
	 public static function afficher_lien_finder($lien){
	 	$param=array($lien);
	 	self::executer("open",$param);
	 }
	 
	 
	 
	/**
	 * lance le film avec vlc
	 * @param  string $lien lien vers le fichier
	 * @return vide
	 */
	public static function lancer_video($lien){
		$param=array($lien);
		self::executer("vlc",$param);
	}


	 /**
	  * supprime le fichier
	  * @param  string $lien lien vers le fichier
	  * @return vide
	  * @todo: supprimer egalement les sous-titres
	  */
	 public static function delete_file($link){
	 	$param=array($lien);
	 	self::executer("delete",$param);
	 }
	 
	

	/** 
	 * @param  string $operation (mv, delete, mkdir, vlc, explorateur)
	 * @param  array $aParams   liste des parametres
	 * @return void
	 */
	private static function executer($operation,$aParams){
		$command="";
		switch ($operation){
			case "mv": $command="mv \"".$aParams[0]."\" \"".$aParams[1]."\"";
			break;

			case "delete": $command="rm \"".$aParams[0]."\" ";
			break;

			case "mkdir":$command="mkdir \"".$aParams[0]."\" -p";
			break;

			case "vlc": $command="open /Applications/vlc.app \"".$aParams[0]."\"";
			break;

			case "explorateur":$command="open file:\"".$aParams[0]."\" -R";
			break;
			default:break;
		}

		system($command);
	}


	public static function get_video_repertoire($dirname){
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
		closedir($dir);
		sort($tab,SORT_STRING);	
		return $tab;
	}
}


	?>