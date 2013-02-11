<?php
if(empty($_GET['id_film'])){exit;}
require_once('classes/factory.class.php');
$f=factory::load("film");
$a=factory::load("acteurs");
$g=factory::load("genre");
$infos_film=$f->list_film(array('id_film'=>$_GET['id_film']),1);
?>

<h1><?php echo utf8_encode($infos_film['titre']); ?> <?php if($infos_film['titre']!=$infos_film['titre_original']){echo "(".$infos_film['titre_original'].")";}?> </h1>
<p style="float:left; margin-right:100px;" id="synopsis" ><?php echo utf8_encode(html_entity_decode($infos_film['synopsis'])); ?></p>
<p>
	<b>Date: </b><?php echo $infos_film['annee_production']; ?><br/>
	<b>Duree:</b> <?php echo $infos_film['duree']; ?><br/>
	<b>Genres: </b>

	<?php
	$genres=$g->genre_film($infos_film['id_film']);
	foreach($genres as $genre){
		echo utf8_encode($genre['nom'])."&nbsp;";
	}
	?>




	<br/>
	<b>Réalisateur: </b>
	<?php
	$realisateurs=$a->list_realisateur_film($infos_film['id_film']);
	foreach($realisateurs as $realisateur){

		echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$realisateur['id_acteur'].".html\">".utf8_encode($realisateur['nom'])."</a><br/>";
	}

	?>
	Interet: 
	<?php if($infos_film['interet']==''){ echo "non défini";
	for($k=1;$k<=5;$k++){?>
	<a href="#" onclick="voter_interet('<?php echo $infos_film['id_film']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $infos_film['id_film'].'_'.$k; ?>" src="images/etoile_vide.png"/></a>
	<?php }
}	
else{
	for($k=1;$k<=$infos_film['interet'];$k++){?>
	<a href="#" onclick="voter_interet('<?php echo $infos_film['id_film']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $infos_film['id_film'].'_'.$k; ?>" src="images/etoile_pleine.png"/></a>
	<?php }
	for($k=$infos_film['interet']+1;$k<=5;$k++){?>
	<a href="#" onclick="voter_interet('<?php echo $infos_film['id_film']; ?>','<?php echo $k;?>')"><img id="etoile_<?php echo $infos_film['id_film'].'_'.$k; ?>" src="images/etoile_vide.png"/></a>
	<?php }
}?>


</p>
<table border="0" cellspacing="10" cellpadding="0" style="width: 75%;">


	<tr>
		<td colspan="2">Acteurs:<br/>
			<?php
			$acteurs=$a->list_acteur_film($infos_film['id_film']);
			foreach($acteurs as $acteur){

				echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$acteur['id_acteur'].".html\">".utf8_encode($acteur['nom'])."(".utf8_encode($acteur['role']).")</a><br/>";
			}

			?></td>
			
			<td colspan="2"></td>
			<td>
				<p>

					<?php $liens=factory::load("liens")->liste_lien_film($infos_film['id_film']);
					foreach($liens as $lien){?>  


					<a href="#" title="<?php echo $lien['lien']; ?>" onclick="lancer_film('<?php echo $lien['lien']; ?>')"><?php echo $lien['nom'];?></a> 
					&nbsp;&nbsp;<a href="#" onclick="afficher('<?php echo $lien['lien']; ?>')">Afficher </a> 
					&nbsp;&nbsp;<a href="#" onclick="supprimer_lien('<?php echo $lien['lien']; ?>')">Supprimer </a> 
					&nbsp;&nbsp;<select STYLE="width:100px" name="select_qualite_<?php echo $infos_film['id_film']; ?>" id="select_qualite_<?php echo $infos_film['id_film']; ?>" size="1" onchange="voter_qualite('<?php echo $lien['id_liens'];?>',this);">
					<option value="" <?php if($lien['qualite']==""){?>selected='selected'<?php }?>></option>
					<option value="TS" <?php if($lien['qualite']=="TS"){?>selected='selected'<?php }?>>TS</option>
					<option value="Moyenne" <?php if($lien['qualite']=="Moyenne"){?>selected='selected'<?php }?>>Moyenne</option>
					<option value="Bonne" <?php if($lien['qualite']=="Bonne"){?>selected='selected'<?php }?>>Bonne</option>
					<option value="HD" <?php if($lien['qualite']=="HD"){?>selected='selected'<?php }?>>HD</option>



				</select>
				<br/>
				<?php }?>
				<br/><a href="#" onclick="suppr(<?php echo $infos_film['id_film']; ?>)">Delete</a></p>
			</td>
			<td>
				<a href="http://www.allocine.fr/film/fichefilm_gen_cfilm=<?php echo $infos_film['id_film']; ?>.html" target="_blank"><img src="images/allocine.png"/></a> <br/>
				<?php if($infos_film['bande_annonce']!=""){echo '<a href="'.$infos_film['bande_annonce'].'" target="_blank"><img src="images/bande_annonce.png"/></a>';} ?>
			</td>
		</tr>

	</table>










