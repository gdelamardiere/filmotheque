<?php

include_once('classe_allocine/Allo.class.php');
include_once('classe_allocine/AlloMovie.class.php');
include_once('classe_allocine/AlloSerie.class.php');
include_once('classe_allocine/AlloPerson.class.php');
include_once('classe_allocine/AlloSearch.class.php');
require_once('function/base.php'); 
mysql_select_db($database_base, $base);
$path=str_replace("\\", "/", $_GET['dossier']);

$file=array_pop(explode('/', $path));
$dossier=dirname($_GET['dossier'])."/";

echo '<input type="text" id="dossier" value="'.$dossier.'" size="80"><input type="hidden" id="dossierhidden" value="'.$dossier.'" >';



$i=0; 
echo "<table>";

			if(preg_match("#.+MKV$#i",$file)||preg_match("#.+AVI$#i",$file)||preg_match("#.+srt$#i",$file)){
			echo '<tr id="'.$i.'"><td>'.$file.'</td><td><input type="text" id="input'.$i.'" value="'.$file.'" size="80"><input type="hidden" id="hidden'.$i.'" value="'.$file.'" ></td><td><a href="#" onclick="valider(\''.$i.'\');">Valider</a> <a href="#" onclick="document.getElementById(\''.$i.'\').style.display=\'none\';">Supprimer</a></tr>';
			$i++;
		}
	
	

echo "</table>";

 


?>

<SCRIPT language="JavaScript">
	function valider(id){
		var name=document.getElementById('input'+id).value;
		var old_name=document.getElementById('hidden'+id).value;
		var dossier=document.getElementById('dossier').value;
		var dossier_old=document.getElementById('dossierhidden').value;
		document.getElementById(id).style.display='none';
		import_film(name,dossier);
		if(name!=old_name || dossier!=dossier_old){rename(id);}
		window.close();
		
	}
	
	function import_film(name,dossier){
		fichier="function/remplir_film.php?dossier="+dossier+"&creer="+name;
		window.open (fichier);
	}
	
	function rename(id){
		var dossier_old=document.getElementById('dossierhidden').value;
		var dossier=document.getElementById('dossier').value;
		var name=document.getElementById('input'+id).value;
		var old_name=document.getElementById('hidden'+id).value;
		fichier="function/rename.php?dossier="+dossier+"&dossier_old="+dossier_old+"&name="+name+"&old_name="+old_name;
		window.open (fichier);
	}

</script>