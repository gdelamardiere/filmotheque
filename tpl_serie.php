<?php
$id=(isset($_GET['id']))?$_GET['id']:$id;
if(empty($id)){exit;}
require_once('classes/factory.class.php');
$s=factory::load("serie");
$a=factory::load("acteurs");
$g=factory::load("genre");
$infos_serie=$s->list_serie(array('id_serie'=>$id),1);
$liste_episodes=$s->liste_episodes_serie($id);
$last_episode=$s->last_lien($id);
?>



<h1><?php echo utf8_encode(html_entity_decode($infos_serie['titre'])); ?> </h1>
<p id="synopsis" ><?php echo utf8_encode(html_entity_decode($infos_serie['synopsis'])); ?></p>
<table border="0" cellspacing="10" cellpadding="0">
	<tr>
		<td colspan="2">Acteurs:<br/>
			<?php
			$acteurs=$a->list_acteur_film($infos_film['id_film']);
			foreach($acteurs as $acteur){

				echo "<a target=\"_blanck\" href=\"http://www.allocine.fr/personne/filmographie_gen_cpersonne=".$acteur['id_acteur'].".html\">".utf8_encode($acteur['nom'])."(".utf8_encode($acteur['role']).")</a><br/>";
			}

			?>
		</td>
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
			</p>
		</td>
		<td>Dernier episode: 
			<a href="#" onclick="lancer_video('<?php echo $last_episode['lien']; ?>')">
				S<?php echo $last_episode['id_saison'];?>E<?php echo $last_episode['num_episode'];?>
			</a> 
		</td>
	</tr>
</table>

<?php
foreach($liste_episodes as $saison=>$aEpisodes){?>
	<div id="saison_<?php echo $saison;?>">
		<hr>  
		<h2>Saison <?php echo $saison;?></h2>
		<table>
		<?php
			$nb=count($aEpisodes);
			for($i=1;$i<$nb;$i+=2) {?>
			<tr>
				<td colspan="2">
					<b>Saison <?php echo $saison;?> -  Episode  <?php echo $i;?>:<br><?php echo utf8_encode($aEpisodes[$i]['titre']); ?></b>
				</td>
				<td colspan="2">
					<b>Saison <?php echo $saison;?> -  Episode  <?php $a=$i+1; echo $a;?>:<br><?php echo utf8_encode($aEpisodes[$i+1]['titre']); ?></b>
				</td>
			</tr>
			<tr>
				<td style="text-align: justify; vertical-align: top; padding-top: 5px; width: 500px;">
					<?php echo utf8_encode($aEpisodes[$i]['synopsis']); ?>
				</td>
				<td style="text-align: center; width: 200px;vertical-align: top; ">
					<?php $liens=$aEpisodes[$i]['lien'];
					for($li=0;$li<count($liens);$li++){?>  
						<a href="#" onclick="lancer_video('<?php echo $liens[$li]['lien']; ?>')">
							<input type="button" value="<?php echo $liens[$li]['nom']; ?>">
						</a> 
					<?php }?><br/>
					<a href="episode_update.php?id=<?php echo $aEpisodes[$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
					<a href="episode_update.php?id=<?php echo $aEpisodes[$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
				</td>
				<td style="text-align: justify; vertical-align: top;width: 500px; ">
					<?php echo utf8_encode($aEpisodes[$i+1]['synopsis']); ?>
				</td>
				<td style="text-align: center; width: 200px;vertical-align: top; padding-top: 20px;">
				<?php $liens=$aEpisodes[$i+1]['lien'];  		
					for($li=0;$li<count($liens);$li++){?>  
						<a href="#" onclick="lancer_video('<?php echo $liens[$li]['lien']; ?>')">
							<input type="button" value="<?php echo $liens[$li]['nom']; ?>">
						</a> 
					<?php }?><br/>
					<a href="episode_update.php?id=<?php echo $aEpisodes[$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
					<a href="episode_update.php?id=<?php echo $aEpisodes[$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
				</td>
			</tr>
			<tr>
				<td colspan="5" id="spacer">&nbsp;</td>
			</tr>


		<?php } 
		if(isset($aEpisodes[$i])){?>
			<tr>
				<td colspan="2">
					<b>Saison <?php echo $saison;?> -  Episode  <?php echo $i;?>:
						<br\><?php echo utf8_encode($aEpisodes[$i]['titre']); ?></b>
				</td>
				<td width="20"></td><td colspan="2"></td>
			</tr>
			<tr>
				<td style="text-align: justify; vertical-align: top; padding-top: 5px;width: 500px;">
					<?php echo utf8_encode($aEpisodes[$i]['synopsis']); ?>
				</td>
				<td style="text-align: center; width: 200px;vertical-align: top; ">
					<?php $liens=$aEpisodes[$i]['lien'];
					for($li=0;$li<count($liens);$li++){?>  
						<a href="#" onclick="lancer_video('<?php echo $liens[$li]['lien']; ?>')">
							<input type="button" value="<?php echo $liens[$li]['nom']; ?>">
						</a> 
					<?php }?><br/>
					<a href="episode_update.php?id=<?php echo $aEpisodes[$i]['id'];?>"><input type="button" value="Modifier"></a><br/>
					<a href="episode_update.php?id=<?php echo $aEpisodes[$i]['id'];?>"><input type="button" value="Mettre à jour"></a>
				</td>  		
				<td></td>
				<td style="text-align: center; width: 200px;"></td>
			</tr>
		<?php }?>
		</table>
	</div>
<?php }?>








