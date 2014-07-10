<div id="container" style="float:right;">
 	<div id="top">
 		<!-- login -->
 		<ul class="login">
 			<li class="left">&nbsp;</li>		        
 			<li><a id="toggleLogin" href="#" onclick="ouvrir();">Menu</a></li>
 		</ul> <!-- / login -->
 	</div> <!-- / top -->
 </div><!-- / container -->
 <div id="login" >
 	<div class="entete">
 		<div id="sous_entete" style="height: 160px;"></div>
 		<div class="loginClose"><a href="#" id="closeLogin" onclick="fermer();">Close Panel</a></div>			
 		<table style="width:100%" >
 			<tr>
 				<td><input type="button" value="Importer un répertoire:" onclick="rep_film();">
 					<input type="texte" value="" id="rep">
 				</td>
 				<td><h1>Menu Principal</h1></td>
 				<td><input type="button" value="Rechercher un acteur:" onclick="acteur_film();"><input type="texte" value="" id="acteur"></td>
 				<td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
 			</tr>
 			<tr name="entete" id="entete">
 				<td colspan="2">
 					<input type="button" value="Fanart(www.themoviedb.org):" onclick="maj_fanart()"/> <input type="text" name="new_fanart" id="new_fanart"/>
 				</td>
 				<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Film"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a>
 					<br><a href="http://localhost/filmotheque/film_affichage_liste.php"><input type="button" value="Affichage liste"></a></h3>
 				</td>
				<td >
					<input type="button" value="id_allocine:" onclick="modifier_film_id()"/> <input type="text" name="new_id_allocine" id="new_id_allocine"/>
					<input type="hidden" name="id_film" id="id_film" value="<?php echo $value['id_film']; ?>"/>
				</td>
				<td >
					<a href="modifier_fiche_<?php echo $type;?>.php?id=<?php echo $id; ?>">Modifier manuellement</a>
				</td>
			</tr>
		</table>
 	</div>
</div>
