<?php 

require_once("function/base.php");







	mysql_select_db($database_base, $base);
	$query_Recordset2 = "SELECT * FROM serie order by titre";
	$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	
	if(isset($_GET['titre'])){
		$query_Recordset2 = "SELECT titre,id,poster FROM serie where titre LIKE '%".$_GET['titre']."%' UNION SELECT titre,id,poster FROM film where titre_original LIKE '%".$_GET['titre']."%'";
		$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());		
	}
	$tab=array();
	while($row_Recordset2 = mysql_fetch_assoc($Recordset2)){
		$tab[]=$row_Recordset2;
		
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Série</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="fx.slide.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/mootools-1.2-core-yc.js"></script>
<SCRIPT language="JavaScript">
<?php  
	echo "var x = new Array(";
	for($i=0;$i<count($tab)-1;$i++){
		$name=str_replace(" ","_",$tab[$i]['titre_original']);
			$name=str_replace("-","",$name);
			$name=str_replace(":","",$name);
			$name=str_replace("/","_",$name);
			$name=str_replace("__","_",$name);
			echo "'image_series/".$name."-".$tab[$i]['num_fanart'].".jpg',";}
			$name=str_replace(" ","_",$tab[$i]['titre_original']);
			$name=str_replace("-","",$name);
			$name=str_replace(":","",$name);
			$name=str_replace("/","_",$name);
			$name=str_replace("__","_",$name);
	echo "'image_series/".$name."-".$tab[$i]['num_fanart'].".jpg'";
	
	echo ");";


?>
	function rep(){
		var repertoire=document.getElementById('rep').value;
		document.location.href="dossier_serie.php?dossier="+repertoire;	
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
	function maj_serie(id){
		fichier="classe_allocine/Filmotheque.class.php?fonction=maj_serie&nb_var=1&var0="+id;
		lancer_script(fichier,true);
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
	
	
	function lancer_film(id,i){
	fichier="function/lancer_film.php?serie=1&id="+id+"&lien="+i;
		if(window.XMLHttpRequest) // FIREFOX
		xhr_object = new XMLHttpRequest();
		else if(window.ActiveXObject) // IE
		xhr_object = new ActiveXObject("Msxml2.XMLHTTP");
		else
		return(false);
		xhr_object.open("GET", fichier, true);
		xhr_object.send(null);
		if(xhr_object.readyState == 4) return(xhr_object.responseText);
		else return(false);
	}
	var temp=0;
	function change(liste){
		
		document.getElementById('content_left_'+temp).style.display="none";
		document.getElementById('entete_'+temp).style.display="none";
		id=liste.options[liste.options.selectedIndex].value;
		temp=id;
		titre=liste.options[liste.options.selectedIndex].text;
		document.getElementById('body').style.background="url('"+x[temp]+"') TOP CENTER";
		document.getElementById('content_left_'+id).style.display="inline";
		document.getElementById('entete_'+id).style.display="table-row";
		document.getElementById('principale3').style.marginTop='0px';
		 /*if(document.body)
			{
			hauteur = parseInt(document.body.clientHeight);
			}
		else
		{
		hauteur = parseInt(window.innerHeight);
		}*/
		hauteur = parseInt(window.innerHeight);		
		
		/*document.getElementById('image_sous').style.height=hauteur+"px";
		document.getElementById('image_sous').src='image_series/'+titre+'.jpg';
		document.getElementById('sous').style.marginBottom='-'+hauteur+'px';*/

		hauteur=hauteur - parseInt(document.getElementById('entete_'+id).offsetHeight);
		hauteur = hauteur - parseInt(document.getElementById('table_principale').offsetHeight)-93;
		if(hauteur>0){document.getElementById('principale3').style.marginTop=hauteur+'px';}
	}
	
	function chargement(){
		sel_rep=document.getElementById('select_principale');
		change(sel_rep);
	}
	
	function ouvrir(){
		document.getElementById('login').style.overflow='visible';
		document.getElementById('container').style.display='none';
		hauteur=parseInt(document.getElementById('principale3').style.marginTop)+38;
		document.getElementById('principale3').style.marginTop=hauteur+'px';
		
	}
	
	function fermer(){
		document.getElementById('login').style.overflow='hidden';
		document.getElementById('container').style.display='inline';
		hauteur=parseInt(document.getElementById('principale3').style.marginTop)-38;
		document.getElementById('principale3').style.marginTop=hauteur+'px';
	}

	
	
	
	
</SCRIPT>
</head>

<body id="body" onload="chargement();" >






<div id="container" style="float:right;">
		<div id="top">
		<!-- login -->
			<ul class="login">
		    	<li class="left">&nbsp;</li>		        
				<li><a id="toggleLogin" href="#" onclick="ouvrir();">Menu</a></li>
			</ul> <!-- / login -->
		</div> <!-- / top -->

        
	</div><!-- / container -->
<?php /*<div id="sous"  style="margin-bottom:0px"><img id="image_sous" src="image_series/<?php echo $tab[0]['titre'];?>.jpg" style="height:10px; width:auto;"  /></div>*/?>
<div id="login" >
		<div id="entete" class="entete">
			<div id="sous_entete" style="height: 160px;"></div>
			<div class="loginClose"><a href="#" id="closeLogin" onclick="fermer();">Close Panel</a></div>
			<table style="width:100%" ><tr><td><input type="button" value="Importer un répertoire:" onclick="rep();"><input type="texte" value="" id="rep"></td>
			
<td><h1>Série</h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur();"><input type="texte" value="" id="acteur"></td>
<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr>
<?php $j=0;
foreach($tab as $value){?>
	<tr name="entete_<?php echo $j;?>" id="entete_<?php echo $j;?>" style="display:<?php if($j==0){echo"table-row"; }else{echo "none";}?>"><td>
<a href="http://www.allocine.fr/series/ficheserie-<?php echo $value['id_allocine']; ?>/saisons/" target="_blank"><img src="allocine.png"/></a>
</td>
<td><input type="button" value="mettre a jour:" onclick="maj_serie(<?php echo $value['id_allocine']; ?>);"></td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Accueil"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a></h3></td>
<td ><FORM METHOD="POST"ACTION="function/remplir_serie.php" >
          	id_allocine: <input type="text" name="id" id="id"/>
          	<input type="hidden" name="id_old" id="id_old" value="<?php echo $value['id']; ?>"/>
          
          </FORM>

</td>
<td >
          <a href="modifier_fiche_film.php?id=<?php echo $value['id']; ?>">Modifier manuellement</a></td>
</tr>

<?php $j++;}?>


</table>

			
			
			
		</div>
		
		
	</div>





































<div id="conteneur">
    
	<div id="principale3" style="margin-top:150px;">
      <div id="sous_principale3"></div>
      
      <table id="table_principale" style="width:95%; height:auto;">
<tr>
<td style="width:85%">
	<?php
	$j=0;
      foreach($tab as  $val){ ?>
      
      	<div id="content_left_<?php echo $j;?>" style="display:<?php if($j==0){echo"inline"; }else{echo "none";}?>"/>
      	
		 <h1><?php echo utf8_encode($val['titre']); ?> </h1>
		 <p id="synopsis" ><?php echo utf8_encode(html_entity_decode($val['synopsis'])); ?></p>
      <table border="0" cellspacing="10" cellpadding="0">
       
        
        <tr>
          <td colspan="2">Acteurs:<br/>
          	 <?php
          	$query_Recordset3 = "SELECT a.id_allocine, a.nom as nom,a.id as i FROM acteur as a,acteur_serie as l where a.id = l.id_acteur AND l.id_serie='".$val['id']."'";
          	$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
			
				echo "<a href=\"fiche_acteur_allocine.php?id=".$row_Recordset3['id_allocine']."\">".utf8_encode($row_Recordset3['nom'])."</a><br/>";
			}
          
			?></td>
			<td><p>Accéder aux saisons:</p>
      <p>
      <ul>
     
      <?php
      for($i=1;$i<=$val['nombre_saisons'];$i++){?>
      <li>
      <a href="fiche_saison.php?id_serie=<?php echo $val['id']; ?>&num_saison=<?php echo $i; ?>">Saison <?php echo $i; ?></a>
      </li>
      <?php }?>
      </ul>
</td>
        </tr>
                
      </table>
      
      
     
             </div>
	<?php	$j++;
	}?>
	

  
  </td><td style="vertical-align:bottom;"><select name="select_principale" id="select_principale" size="<?php if(count($tab)<20){echo count($tab);} else{echo "20";}?>" onchange="change(this);">
  <?php 
	$i=0;
      foreach($tab as  $val){ 
  echo "<option value=\"".$i."\"";
  if($i==0){echo 'selected="selected"';}
  echo ">".str_replace(":","",utf8_encode($val['titre']))."</option>";
  $i++;
      }?>
  
  
  </select>
  </td></tr></table>
</div>


</div>
</body>
</html>


