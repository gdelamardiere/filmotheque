<?php 

require_once("function/base.php");
mysql_select_db($database_base, $base);

include_once('classe_allocine/Filmotheque.class.php');




	
	$query_Recordset2 = "SELECT * FROM serie order by titre";
	
	
	if(isset($_GET['titre'])){
		$query_Recordset2 = "SELECT titre,id,poster FROM serie where titre LIKE '%".$_GET['titre']."%' UNION SELECT titre,id,poster FROM film where titre_original LIKE '%".$_GET['titre']."%'";
	}
	$Recordset2 = mysql_query($query_Recordset2, $base) or die(mysql_error());
	$tab=array();
	$tab_episode=array();
	while($row_Recordset2 = mysql_fetch_assoc($Recordset2)){
		$tab[]=$row_Recordset2;
		$query_Recordset3 = "SELECT e.id,e.num_episode,e.titre,e.synopsis,e.id_saison,l.lien,l.nom,l.qualite FROM   episode as e LEFT OUTER JOIN liens as l on e.id=l.id_episode WHERE  id_serie='".$row_Recordset2['id']."' ";
		$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
		while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
			
			if(!isset($tab_episode[$row_Recordset2['id']][$row_Recordset3['id_saison']][$row_Recordset3['num_episode']])){
				$tab_episode[$row_Recordset2['id']][$row_Recordset3['id_saison']][$row_Recordset3['num_episode']]=array(
					"id"=>$row_Recordset3['id'],"num_episode"=>$row_Recordset3['num_episode'],"titre"=>$row_Recordset3['titre'],"synopsis"=>$row_Recordset3['synopsis'],
					"lien"=>array()			
				);
					
			}
			if($row_Recordset3['lien']!= null){
				$tab_episode[$row_Recordset2['id']][$row_Recordset3['id_saison']][$row_Recordset3['num_episode']]['lien'][]=array("lien"=>$row_Recordset3['lien'],"nom"=>$row_Recordset3['nom'],"qualite"=>$row_Recordset3['qualite']);
			}
			
		}
		
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
	var temp=0;
	
</SCRIPT>
<script type="text/javascript" src="js/common.js"></script>
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
			<table style="width:100%" ><tr><td><input type="button" value="Importer un répertoire:" onclick="rep_serie();"><input type="texte" value="" id="rep"></td>
			
<td><h1>Série</h1></td>
<td><input type="button" value="Rechercher un acteur:" onclick="acteur();"><input type="texte" value="" id="acteur"></td>
<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
</tr>
<?php $j=0;
foreach($tab as $value){?>
	<tr name="entete_<?php echo $j;?>" id="entete_<?php echo $j;?>" style="display:<?php if($j==0){echo"table-row"; }else{echo "none";}?>"><td>
<a href="http://www.allocine.fr/series/ficheserie-<?php echo $value['id_allocine']; ?>/saisons/" target="_blank"><img src="images/allocine.png"/></a>
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
      	<div id="test<?php echo $j;?>" style="overflow-y:auto;max-height:700px;min-height:600px;">
      	
      	
		 <h1><?php echo utf8_encode(html_entity_decode($val['titre'])); ?> </h1>
		 <p id="synopsis" ><?php echo utf8_encode(html_entity_decode($val['synopsis'])); ?></p>
      <table border="0" cellspacing="10" cellpadding="0">
       
        
        <tr>
          <td colspan="2">Acteurs:<br/>
          	 <?php
          	$query_Recordset3 = "SELECT a.id_allocine, a.nom as nom,a.id as i FROM acteur as a,acteur_serie as l where a.id = l.id_acteur AND l.id_serie='".$val['id']."'";
          	$Recordset3 = mysql_query($query_Recordset3, $base) or die(mysql_error());
			while($row_Recordset3 = mysql_fetch_assoc($Recordset3)){
			
				echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$row_Recordset3['id_allocine'].".html\">".utf8_encode($row_Recordset3['nom'])."</a><br/>";
			}
          
			?></td>
			<td><p>Accéder aux saisons:</p>
      <p>
      <ul>
     
      <?php
      for($i=1;$i<=$val['nombre_saisons'];$i++){?>
      <li>
      <a href="#saison_<?php echo $i."_".$j; ?>">Saison <?php echo $i; ?></a>
      </li>
      <?php }?>
      </ul>
</td>
<td>Dernier episode: <?php 
		$query_last_ep = "SELECT l.lien,e.num_episode,e.id_saison FROM episode e,liens l WHERE id_serie='".$val['id']."' AND l.id_episode=e.id order by e.id_saison desc,e.num_episode desc limit 1 ";
		$Recordset_last_ep = mysql_query($query_last_ep, $base) or die(mysql_error());
		$last_ep = mysql_fetch_assoc($Recordset_last_ep); ?>
		<a href="#" onclick="lancer_film('<?php echo $last_ep['lien']; ?>')">S<?php echo $last_ep['id_saison'];?>E<?php echo $last_ep['num_episode'];?></a> 
        </tr>
                
      </table>
      
      <?php
      for($k=1;$k<=$val['nombre_saisons'];$k++){?>
      <div id="saison_<?php echo $k."_".$j;?>">
      
       <hr>   
     
     <h2>Saison <?php echo $k;?></h2>
     
     
     
     
     <table>
  <?php
  $nb=count($tab_episode[$val['id']][$k]);
  for($i=1;$i<$nb;$i+=2) {?>
  		<tr><td colspan="2"><b>Saison <?php echo $k;?> -  Episode  <?php echo $i;?>:<br><?php echo utf8_encode($tab_episode[$val['id']][$k][$i]['titre']); ?></b></td>
  		
  		<td colspan="2"><b>Saison <?php echo $k;?> -  Episode  <?php $a=$i+1; echo $a;?>:<br><?php echo utf8_encode($tab_episode[$val['id']][$k][$i+1]['titre']); ?></b></td></tr>
  		
  		<tr><td style="text-align: justify; vertical-align: top; padding-top: 5px; width: 500px;"><?php echo utf8_encode($tab_episode[$val['id']][$k][$i]['synopsis']); ?></td>
  		<td style="text-align: center; width: 200px;vertical-align: top; ">
  		<?php 
  		
  		$liens=$tab_episode[$val['id']][$k][$i]['lien'];
  		          for($li=0;$li<count($liens);$li++){?>  
          
          
          <a href="#" onclick="lancer_film('<?php echo $liens[$li]['lien']; ?>')"><input type="button" value="<?php echo $liens[$li]['nom']; ?>"></a> 
          <?php }
  			
  			?>

  		<br/><a href="episode_update.php?id=<?php echo $tab_episode[$val['id']][$k][$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
  		<a href="episode_update.php?id=<?php echo $tab_episode[$val['id']][$k][$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
  		</td>
  				
  		<td style="text-align: justify; vertical-align: top;width: 500px; "><?php echo utf8_encode($tab_episode[$val['id']][$k][$i+1]['synopsis']); ?></td>
  		<td style="text-align: center; width: 200px;vertical-align: top; padding-top: 20px;">
  		<?php 
  		
  		$liens=$tab_episode[$val['id']][$k][$i+1]['lien'];  		
  		for($li=0;$li<count($liens);$li++){?>  
          
          
          <a href="#" onclick="lancer_film('<?php echo $liens[$li]['lien']; ?>')"><input type="button" value="<?php echo $liens[$li]['nom']; ?>"></a> 
          <?php }
  			
  			?>
  			  		<br/><a href="episode_update.php?id=<?php echo $tab_episode[$val['id']][$k][$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
  		<a href="episode_update.php?id=<?php echo $tab_episode[$val['id']][$k][$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
  		</td>
  		</tr>
  		
  		<tr><td colspan="5" id="spacer">&nbsp;</td></tr>
			
				
			<?php } 
			if(isset($tab_episode[$val['id']][$k][$i])){?>
			<tr><td colspan="2"><b>Saison <?php echo $k;?> -  Episode  <?php echo $i;?>:<br><?php echo utf8_encode($tab_episode[$val['id']][$k][$i]['titre']); ?></b></td>
  		<td width="20"></td><td colspan="2"></td></tr>
  		
  		<tr><td style="text-align: justify; vertical-align: top; padding-top: 5px;width: 500px;"><?php echo utf8_encode($tab_episode[$val['id']][$k][$i]['synopsis']); ?></td>
  		<td style="text-align: center; width: 200px;vertical-align: top; ">
  		<?php 
  		
  		$liens=$tab_episode[$val['id']][$k][$i]['lien'];
  		for($li=0;$li<count($liens);$li++){?>  
          
          
          <a href="#" onclick="lancer_film('<?php echo $liens[$li]['lien']; ?>')"><input type="button" value="<?php echo $liens[$li]['nom']; ?>"></a> 
          <?php }
  			
  			?>
  		<br/><a href="episode_update.php?id=<?php echo $tab_episode[$val['id']][$k][$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
  		<a href="episode_update.php?id=<?php echo $tab_episode[$val['id']][$k][$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
  		</td>  		
  		<td></td><td style="text-align: center; width: 200px;"></td>
  		</tr>
  		
  					
			<?php }?>
			</table>

     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
     
	
	
		
	
	
	
	
	

	
	
	
	
	
      
      
      
      
      </div>
      <?php }?>
      
      </div>
     
             </div>
	<?php	$j++;
	}?>
	

  
  </td><td style="vertical-align:bottom;"><select name="select_principale" id="select_principale" size="<?php if(count($tab)<38){echo count($tab);} else{echo "38";}?>" onchange="change(this);">
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


