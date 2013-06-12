<?php

/*rajouter bande annonce*/




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
$tabs=$f->lister_dossier_film($dossier);
$dossiers=$f->lister_parametre_destination();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Import FILM</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/common.js"></script>
<SCRIPT language="JavaScript">
	function valider(id){
		var name=document.getElementById('input'+id).value;
		var id_allocine=document.getElementById('id_allocine'+id).value;
		if(id_allocine!=""){
			var old_name=document.getElementById('hidden'+id).value;
			var dossier=document.getElementById('dossier').value;
			var dossier_old=document.getElementById('dossierhidden').value;
			document.getElementById("tr0"+id).style.display='none';
			document.getElementById("tr1"+id).style.display='none';
			fichier="classe_allocine/Filmotheque.class.php?fonction=creer_film_new&nb_var=5&var0="+id_allocine+"&var1="+dossier+"&var2="+dossier_old+"&var3="+name+"&var4="+old_name;
			lancer_script(fichier,true);
		}
		else{chercher(id);}
	}
	
	function remplacer(id){
		var name=document.getElementById('input'+id).value;
		var id_allocine=document.getElementById('id_allocine'+id).value;
		if(id_allocine!=""){
			var old_name=document.getElementById('hidden'+id).value;
			var dossier=document.getElementById('dossier').value;
			var dossier_old=document.getElementById('dossierhidden').value;
			document.getElementById("tr0"+id).style.display='none';
			document.getElementById("tr1"+id).style.display='none';
			fichier="classe_allocine/Filmotheque.class.php?fonction=remplacer_lien_film&nb_var=6&var0="+id_allocine+"&var1="+dossier+"&var2="+dossier_old+"&var3="+name+"&var4="+old_name+"&var5=true";
			lancer_script(fichier,true);
		}
		else{chercher(id);}
	}
	function ajouter(id){
		var name=document.getElementById('input'+id).value;
		var id_allocine=document.getElementById('id_allocine'+id).value;
		if(id_allocine!=""){
			var old_name=document.getElementById('hidden'+id).value;
			var dossier=document.getElementById('dossier').value;
			var dossier_old=document.getElementById('dossierhidden').value;
			document.getElementById("tr0"+id).style.display='none';
			document.getElementById("tr1"+id).style.display='none';
			fichier="classe_allocine/Filmotheque.class.php?fonction=ajouter_lien_film&nb_var=5&var0="+id_allocine+"&var1="+dossier+"&var2="+dossier_old+"&var3="+name+"&var4="+old_name;
			lancer_script(fichier,true);
		}
		else{chercher(id);}
	}
	
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
	
	function chercher(id){
		document.getElementById('valider'+id).style.display='none';
		document.getElementById('remplacer'+id).style.display='none';
		document.getElementById('ajouter'+id).style.display='none';
		var name=document.getElementById('input'+id).value;	
		var name_init=document.getElementById('valeur_init'+id).value;		
		
		fichier="classe_allocine/Filmotheque.class.php?fonction=rechercher_film_allocine&nb_var=2&var0="+name+"&var1="+name_init;
		//alert(fichier);
		texte=lancer_script(fichier,false);
		temp=texte.split('__synopsis__');
		if(temp[0]=="1"){
			document.getElementById('id_allocine'+id).value=temp[1];
			document.getElementById('texte_'+id).innerHTML=temp[2];
			document.getElementById('image_'+id).innerHTML='<img src="'+temp[3]+'" width="150px" />';
			if(temp[1]!=''&&temp[2]!="ce film existe deja"){document.getElementById('valider'+id).style.display='inline';}
			if(temp[2]=="ce film existe deja"){
				document.getElementById('remplacer'+id).style.display='inline';
				document.getElementById('ajouter'+id).style.display='inline';
			}
		}
		else{
			document.getElementById('texte_'+id).innerHTML=texte;
			
		}

	}
	function chercher_id(id){
		document.getElementById('valider'+id).style.display='none';
		document.getElementById('remplacer'+id).style.display='none';
		document.getElementById('ajouter'+id).style.display='none';
		var i=document.getElementById('id_allocine'+id).value;	
		fichier="classe_allocine/Filmotheque.class.php?fonction=rechercher_film_id_allocine&nb_var=1&var0="+i;
		texte=lancer_script(fichier,false);
		temp=texte.split('__synopsis__');	
		document.getElementById('id_allocine'+id).value=temp[0];
		document.getElementById('texte_'+id).innerHTML=temp[1];
		document.getElementById('image_'+id).innerHTML='<img src="'+temp[2]+'" width="150px" />';
		if(temp[0]!=''&&temp[1]!="ce film existe deja"){document.getElementById('valider'+id).style.display='inline';}
		if(temp[1]=="ce film existe deja"){
			document.getElementById('remplacer'+id).style.display='inline';
			document.getElementById('ajouter'+id).style.display='inline';
		}
	}		
	function rep(){
		var repertoire=document.getElementById('rep').value;
		if(repertoire==""){alert("le répertoire n'a pas été renseigné!!");return;}
		else{
			document.location.href="dossier.php?dossier="+repertoire;
		}	
	}
	function fichier(){
		var repertoire=document.getElementById('fichier').value;
		if(repertoire==""){alert("le fichier n'a pas été renseigné!!");return;}
		else{
			document.location.href="fichier.php?dossier="+repertoire;
		}	
	}
	function acteur(){
		var acteur=document.getElementById('acteur').value;
		if(acteur==""){alert("l'acteur n'a pas été renseigné!!");return;}
		else{
			document.location.href="fiche_acteur_recherche.php?nom="+acteur;
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
		document.getElementById('dossier').value=chemin;
	}
	
		
	
</SCRIPT>
</head>

<body>
<div id="entete">
<table><tr><td><input type="button" value="Importer un répertoire:" onclick="rep();"><input type="texte" value="" id="rep"></td>
<td><input type="button" value="Importer un fichier:" onclick="fichier();"><input type="texte" value="" id="fichier"></td>
<td><h1>Import FILM</h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur();"><input type="texte" value="" id="acteur"></td>
<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr><tr><td colspan="2"></td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Accueil"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a></h3></td>
<td colspan="2"></td>
</tr></table>
</div>
<div id="conteneur">
   
    <div id="principale3">
   <FORM METHOD="POST" ACTION="#" > 
    

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
				echo '<tr><td id="spacer2" colspan="3"></td></tr>';
			
			echo '<tr id="tr0'.$i.'"><td style="width:200px;" onclick="lancer_video(\''.$dossier.$value.'\')">'.$value.'<br></td>
			<td rowspan="2" id="image_'.$i.'" style="width:200px;"></td><td rowspan="2" id="texte_'.$i.'" style="width:500px;"></td>
			<td><input type="text" id="input'.$i.'" value="'.$f->preparer_lien_allocine($value).'" size="30" onchange="chercher(\''.$i.'\');">
			<input type="hidden" id="valeur_init'.$i.'" value="'.$f->preparer_lien_allocine($value).'" ><br/>
			<input type="text" id="id_allocine'.$i.'" name="id_allocine'.$i.'" value="" onchange="chercher_id(\''.$i.'\');"/>
			</td></tr>';
			echo '<tr id="tr1'.$i.'"><td>&nbsp;';			
			echo '<input type="hidden" id="hidden'.$i.'" value="'.$value.'" ></td><td><span onclick="chercher(\''.$i.'\');">Chercher</span> <span onclick="valider(\''.$i.'\');" id="valider'.$i.'" style="display:none"; >Valider</span> <span onclick="remplacer(\''.$i.'\');" id="remplacer'.$i.'" style="display:none"; >Remplacer</span> <span onclick="ajouter(\''.$i.'\');" id="ajouter'.$i.'" style="display:none"; >Ajouter</span> <span onclick="document.getElementById(\'tr0'.$i.'\').style.display=\'none\';document.getElementById(\'tr1'.$i.'\').style.display=\'none\';">Supprimer</span></td>
			</tr>
			<tr><td id="spacer" colspan="3"></td></tr>';
			echo "<script>chercher('".$i."');</script>";
			$i++;
			}
	

echo "</table>";
?>
</FORM>
  </div>
</div>

</body>
</html>