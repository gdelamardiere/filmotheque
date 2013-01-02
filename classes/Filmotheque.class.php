<?php


class Filmotheque {
		 
		
		 
		 
		 
		 
		 
		 
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

