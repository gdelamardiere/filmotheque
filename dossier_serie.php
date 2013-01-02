<?php


require_once("classe_allocine/conf.php");
if(!isset($_GET['dossier'])||(isset($_GET['dossier'])&&$_GET['dossier']=="")){
	$dossier=DOWNLOAD;	   
  header('Location: dossier_serie.php?dossier='.$dossier);   
 }
else{
	$dossier=$_GET['dossier']."/";
	$dossier=str_replace("//","/",$dossier);
}

include_once('classe_allocine/Filmotheque.class.php');
$f=new Filmotheque();
$tabs=$f->lister_dossier_serie($dossier);
$dossiers=$f->lister_parametre_destination();
$serie=$f->lister_serie();
$i=0;



foreach($tabs['tab'] as $name){
	$coinc=$f->cherche_coincidence($name);
	if($coinc!=false){
		if(isset($tabs['saisons'][$i]) && isset($tabs['episodes'][$i])) {
			$new_name=str_replace(" ","_",$coinc['titre']);
			$new_name=str_replace(":","",$new_name);
			$new_name=str_replace("&","",$new_name);
			$new_name=str_replace("__","_",$new_name);
			$rep=$dossiers[0].$new_name."/";
			$rep_init=$dossier;
			$new_name.="_s".$tabs['saisons'][$i]."_e".$tabs['episodes'][$i].".".pathinfo($name, PATHINFO_EXTENSION);
			echo $name." -> ".$new_name." dir =".$rep."<br>";
			try{
				$f->ajouter_lien_serie($coinc['id'],$tabs['saisons'][$i],$tabs['episodes'][$i],$rep,$rep_init,$new_name,$name);
			}
			catch(PDOException $e){
			   
			}
		
		}	
	}

	$i++;
}
?>






<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Import épisode</title>
<link href="style.css" rel="stylesheet" type="text/css" />

<SCRIPT language="JavaScript">

function lancer_script(fichier,bool){
		if(window.XMLHttpRequest) // FIREFOX
		xhr_object = new XMLHttpRequest();
		else if(window.ActiveXObject) // IE
		xhr_object = new ActiveXObject("Msxml2.XMLHTTP");
		else
		return(false);
		xhr_object.open("GET", fichier, bool);
		xhr_object.send(null);
		if(xhr_object.readyState == 4) return(xhr_object.responseText);
		else return(false);
	}
	
function valider(id){
		var name=document.getElementById('input'+id).value;		
		var dossier=document.getElementById('dossier').value;
		var episode=document.getElementById('episode'+id).value;		
		var saison=document.getElementById('saison'+id).value;
		var id_serie=document.getElementById('id_serie').value;	
		document.getElementById("tr0"+id).style.display='none';
		document.getElementById("tr1"+id).style.display='none';
		document.getElementById("spacer"+id).style.display='none';
		document.getElementById("spacer2"+id).style.display='none';
		var old_name=document.getElementById('hidden'+id).value;
		var dossier_old=document.getElementById('dossierhidden').value;
		fichier="classe_allocine/Filmotheque.class.php?fonction=ajouter_lien_serie&nb_var=7&var0="+id_serie+"&var1="+saison+"&var2="+episode+"&var3="+dossier+"&var4="+dossier_old+"&var5="+name+"&var6="+old_name;
		lancer_script(fichier,true);		
	}
	
	function cacher(id){
		document.getElementById("tr0"+id).style.display='none';
		document.getElementById("tr1"+id).style.display='none';
		document.getElementById("spacer"+id).style.display='none';
		document.getElementById("spacer2"+id).style.display='none';
	}
	
	function maj_all_serie(){
		fichier="classe_allocine/Filmotheque.class.php?fonction=maj_all_serie";
		lancer_script(fichier,false);			
		document.location.href="dossier_serie.php?dossier=<?php echo $dossier;?>";
	}
	
	
	function rename(id){
		var dossier_old=document.getElementById('dossierhidden').value;
		var dossier=document.getElementById('dossier').value;
		var name=document.getElementById('input'+id).value;
		var old_name=document.getElementById('hidden'+id).value;
		fichier="classe_allocine/Filmotheque.class.php?fonction=rename_fichier2&nb_var=4&var0="+dossier+"&var1="+dossier_old+"&var2="+name+"&var3="+old_name;
		lancer_script(fichier,true);
	}

		
	function rep(){
		var repertoire=document.getElementById('rep').value;
		document.location.href="dossier.php?dossier="+repertoire;
	}
	
	
	function acteur(){
		var acteur=document.getElementById('acteur').value;
		if(acteur==""){alert("l'acteur n'a pas été renseigné!!");return;}
		else{
			document.location.href="fiche_acteur_recherche.php?nom="+acteur;
		}	
	}
	function creer_serie(){
		var serie=document.getElementById('creer_serie').value;
		if(serie==""){alert("la serie n'a pas été renseigné!!");return;}
		else{
			fichier="classe_allocine/Filmotheque.class.php?fonction=creer_serie&nb_var=1&var0="+serie;
			lancer_script(fichier,false);			
			document.location.href="dossier_serie.php?dossier=<?php echo $dossier;?>";
		}	
	}
	function film(){
		var film=document.getElementById('film').value;
		if(film==""){alert("le film n'a pas été renseigné!!");return;}
		else{
			document.location.href="index.php?titre="+film;
		}	
	}
	
	
	function select_dossier(str){
		chemin=str.options[str.options.selectedIndex].value;
		sel_serie=document.getElementById('serie');
		serie=sel_serie.options[sel_serie.options.selectedIndex].text;
		serie=serie.replace(/&/gi, '');
		serie=serie.replace(/ /gi, '_');
		serie=serie.replace(/:/gi, '');
		serie=serie.replace(/__/gi, '_');
		document.getElementById('dossier').value=chemin+serie+"/";
	}
	function rename_episode(){
		for(i=0;i<<?php echo $tabs['nb'];?>;i++){
			rename_episode2(i);
		}		
	}
	
	function rename_episode2(i){
		extension=document.getElementById('extension'+i).value;
		 ep=document.getElementById('episode'+i).value;
		s=document.getElementById('saison'+i).value;
		sel_serie=document.getElementById('serie');
		serie=sel_serie.options[sel_serie.options.selectedIndex].text;
		serie=serie.replace(/&/gi, '');
		serie=serie.replace(/ /gi, '_');
		serie=serie.replace(/:/gi, '');
		serie=serie.replace(/__/gi, '_');
		document.getElementById('input'+i).value=serie+"_s"+s+"_e"+ep+"."+extension;
	}

	
	function select_serie(str){
		chemin=str.options[str.options.selectedIndex].value;
		document.getElementById('id_serie').value=chemin;
		chargement();
	}
	
	function chargement(){
		rename_episode();
		sel_rep=document.getElementById('repo');
		select_dossier(sel_rep);
	}
	
		
	
</SCRIPT>
</head>

<body onload="chargement()">
<div id="entete">
<table><tr><td><input type="button" value="Importer un répertoire:" onclick="rep();"><input type="texte" value="" id="rep"></td>
<td><input type="button" value="Créer une série:" onclick="creer_serie();"><input type="texte" value="" id="creer_serie"></td>
<td><h1>Import épisode</h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur();"><input type="texte" value="" id="acteur"></td>
<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr><tr><td><input type="button" value="Mettre a jour les series:" onclick="maj_all_serie();"> </td><td ></td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Accueil"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a></h3></td>
<td colspan="2"></td>
</tr></table>
</div>
<div id="conteneur">
   
    <div id="principale3">
   <FORM METHOD="POST" ACTION="#" > 
    Choix de la série: &nbsp;&nbsp;&nbsp;    <SELECT name="serie" id="serie" size="1" onchange="select_serie(this)">
<?php foreach($serie as $value){
	echo '<OPTION value="'.$value['id'].'">'.utf8_encode($value['titre']).'</OPTION>';

}?>
</SELECT><br/><br/><br/>


<input type="hidden" value="<?php echo $serie[0]['id'];?>" name="id_serie" id="id_serie"/>


  Répertoire de destination: &nbsp;&nbsp;&nbsp;    <SELECT name="rep" id="repo" size="1" onchange="select_dossier(this)" >
<?php foreach($dossiers as $value){
	echo '<OPTION value="'.$value.'">'.$value.'</OPTION>';

}?>
</SELECT>&nbsp;&nbsp;&nbsp;







<?php

echo '<input type="text" id="dossier" name="dossier" value="'.$dossier.'" size="80"><input type="hidden" name="dossierhidden" id="dossierhidden" value="'.$dossier.'" ><br/><br/>';



$i=0; 
echo "<table>";

			
			foreach($tabs['tab'] as $value){
				echo '<tr><td id="spacer2'.$i.'" class="spacer2" colspan="3"></td></tr>';
			
			echo '<tr id="tr0'.$i.'"><td>'.$value.'</td><td><input type="text" id="input'.$i.'" value="'.$value.'" size="80"><br/></td></tr>';
			echo '<tr id="tr1'.$i.'"><td>&nbsp;&nbsp;&nbsp;Saison<input type="texte" value="';
			if(isset($tabs['saisons'][$i])) {echo $tabs['saisons'][$i];}
			echo '" name="saison'.$i.'" id="saison'.$i.'" onchange="rename_episode('.$i.');"/>';
			echo '&nbsp;&nbsp;&nbsp;Episode<input type="texte" value="';
			if(isset($tabs['episodes'][$i])) {echo $tabs['episodes'][$i];}
			echo '" name="episode'.$i.'" id="episode'.$i.'" onchange="rename_episode('.$i.');"/>';
			
			
			echo '<input type="hidden" id="extension'.$i.'" name="extension'.$i.'" value="'.pathinfo($value, PATHINFO_EXTENSION).'"/>';
			
			
			echo '<input type="hidden" id="hidden'.$i.'" value="'.$value.'" ></td><td><a href="#" onclick="valider(\''.$i.'\');">Valider</a> <a href="#" onclick="cacher(\''.$i.'\')">Supprimer</a></td></tr>
			<tr><td id="spacer'.$i.'" class="spacer" colspan="3"></td></tr>';
			$i++;
			}
	

echo "</table>";
?>
</FORM>
  </div>
</div>

</body>
</html>