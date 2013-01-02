<?php

require_once('function/base.php'); 
mysql_select_db($database_base, $base);

$path=str_replace("\\", "/", $_GET['dossier']);

$file=array_pop(explode('/', $path));
$dossier=dirname($_GET['dossier'])."/";
$dossier=str_replace("//","/",$dossier);

if(isset($_POST['dossier'])){
	$name=$_POST['dossier'].$_POST['input0'];
	$episode=intval($_POST['episode']);
	$saison=intval($_POST['saison']);
	$id_serie=$_POST['id_serie'];
	
	
	$query_Recordset = "SELECT id,lien FROM episode WHERE id_serie = '".$id_serie."' AND id_saison = '".$saison."' AND num_episode = '".$episode."'";
	$Recordset = mysql_query($query_Recordset, $base) or die(mysql_error());
	$row_Recordset = mysql_fetch_assoc($Recordset);
	if($row_Recordset['lien']!=NULL){
		$nb=substr_count($row_Recordset['lien'],";")+1 ;
		$d=$_POST['dossier'].$nb."_".$_POST['input0'];
		exec("mv \"".$dossier.$file."\" \"".$d."\" 1>/dev/null 2>&1");
		
		$query_Recordset1 = "UPDATE episode SET lien='".$row_Recordset['lien'].";".$d."' WHERE id = '".$row_Recordset['id']."'";
		$Recordset1 = mysql_query($query_Recordset1, $base)or die(mysql_error());
	}
	else{
		$query_Recordset1 = "UPDATE episode SET lien='".$name."' WHERE id = '".$row_Recordset['id']."'";
		$Recordset1 = mysql_query($query_Recordset1, $base)or die(mysql_error());
		exec("mv \"".$dossier.$file."\" \"".$name."\" 1>/dev/null 2>&1");
	}
	
	
	header('Location: fiche_saison.php?id_serie='.$id_serie.'&num_saison='.$saison);


}







$query_Recordset1 = "SELECT * FROM parametres where id='1'";
$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$dossiers=explode(";",$row_Recordset1['repertoires']);

$query_Recordset2 = "SELECT titre,id FROM serie ";
$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
$serie=array();
while($row_Recordset2 = mysql_fetch_assoc($Recordset2)){
	$serie[]=array('id'=>$row_Recordset2['id'],'titre'=>$row_Recordset2['titre']);
}








if(preg_match("#.+([0-9][1-9])([0-9]{2}).+#i",$file,$matches0)){$saison=$matches0[1];$episode=$matches0[2];}//*0103*

if(preg_match("#.+\.([0-9])([0-9]{2}).+#i",$file,$matches1)){$saison=$matches1[1];$episode=$matches1[2];}//*.103*

if(preg_match("#.+ ([0-9])([0-9]{2}).+#i",$file,$matches1)){$saison=$matches1[1];$episode=$matches1[2];}//* 103*

if(preg_match("#.+([0-9])x([0-9]{2}).+#i",$file,$matches2)){$saison=$matches2[1];$episode=$matches2[2];}//*1x09*

if(preg_match("#.+\[s([0-9]{2})\]_\[e([0-9]{2})\].+#i",$file,$matches3)){$saison=$matches3[1];$episode=$matches3[2];}//*[s01]_[e01].*

if(preg_match("#.+s([0-9]{2})e([0-9]{2}).+#i",$file,$matches4)){echo"test"; $saison=$matches4[1];$episode=$matches4[2];}//*s01e01*

if(preg_match("#.+s([0-9]{2})\.e([0-9]{2}).+#i",$file,$matches5)){$saison=$matches5[1];$episode=$matches5[2];}//*s01.e01*

if(preg_match("#.+s([0-9]{2})_e([0-9]{2}).+#i",$file,$matches6)){$saison=$matches6[1];$episode=$matches6[2];}//*s01_e01*







?>














<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Import épisode</title>
<link href="style.css" rel="stylesheet" type="text/css" />

<SCRIPT language="JavaScript">	
		
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
	function rename_episode(){
		extension="<?php echo pathinfo($file, PATHINFO_EXTENSION);?>";
		 ep=document.getElementById('episode').value;
		s=document.getElementById('saison').value;
		sel_serie=document.getElementById('serie');
		serie=sel_serie.options[sel_serie.options.selectedIndex].text;
		document.getElementById('input0').value=serie+"_s"+s+"_e"+ep+"."+extension;
	}

	
	function select_serie(str){
		chemin=str.options[str.options.selectedIndex].value;
		document.getElementById('id_serie').value=chemin;
		rename_episode();
	}
	
		
	
</SCRIPT>
</head>

<body onload="rename_episode()">
<div id="entete">
<table><tr><td><input type="button" value="Importer un répertoire:" onclick="rep();"><input type="texte" value="" id="rep"></td>
<td><input type="button" value="Importer un fichier:" onclick="fichier();"><input type="texte" value="" id="fichier"></td>
<td><h1>Import épisode</h1></td>
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
    Choix de la série: &nbsp;&nbsp;&nbsp;    <SELECT name="serie" id="serie" size="1" onchange="select_serie(this)">
<?php foreach($serie as $value){
	echo '<OPTION value="'.$value['id'].'">'.utf8_encode($value['titre']).'</OPTION>';

}?>
</SELECT>&nbsp;&nbsp;&nbsp;Saison<input type="texte" value="<?php if(isset($saison)) echo $saison;?>" name="saison" id="saison" onchange="rename_episode();"/>
&nbsp;&nbsp;&nbsp;Episode<input type="texte" value="<?php if(isset($episode)) echo $episode;?>" name="episode" id="episode" onchange="rename_episode();"/><br/><br/><br/>
&nbsp;&nbsp;&nbsp;

<input type="hidden" value="<?php echo $serie[0]['id'];?>" name="id_serie" id="id_serie"/>


  Répertoire de destination: &nbsp;&nbsp;&nbsp;    <SELECT name="rep" size="1" onchange="select_dossier(this)">
<?php foreach($dossiers as $value){
	echo '<OPTION value="'.$value.'">'.$value.'</OPTION>';

}?>
</SELECT>&nbsp;&nbsp;&nbsp;







<?php

echo '<input type="text" id="dossier" name="dossier" value="'.$dossier.'" size="80"><input type="hidden" name="dossierhidden" id="dossierhidden" value="'.$dossier.'" ><br/><br/>';



$i=0; 
echo "<table>";

			if(preg_match("#.+MKV$#i",$file)||preg_match("#.+AVI$#i",$file)||preg_match("#.+srt$#i",$file)){
			echo '<tr id="'.$i.'"><td >Nom du fichier actuel:&nbsp;&nbsp;'.$file.'</td></tr><tr><td >Nom du fichier de destination:&nbsp;&nbsp;<input type="text" id="input'.$i.'" name="input'.$i.'" value="'.$file.'" size="80"><input type="hidden" id="hidden'.$i.'" value="'.$file.'" ></td></tr><tr><td style="padding-top:20px;"><a href="#" onclick="valider(\''.$i.'\');"><input type="submit" value="Valider"/></a> </tr>';
			$i++;
		}
	
	

echo "</table>";
?>
</FORM>
  </div>
</div>

</body>
</html>