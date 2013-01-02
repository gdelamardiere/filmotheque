<?php
    
    // Emplacement du fichier api-allocine-helper.php
    require "./api-allocine-helper.php";
    
    // L'adresse de la page actuelle, pour les URLs
    $page_actuelle = './index.php';
    
    // Résultats par page
    $count = 10;
    
    
    $q = (!empty($_GET['q'])) ? $_GET['q'] : '';
    $page = (!empty($_GET['page'])) ? (real) $_GET['page'] : 1;
    
    if (!empty( $q ))
    {
        try
        {
            $allo = new AlloHelper;
            $allo->language('fr');
        
            $search = $allo->search($q, $count, $page, 'medium');
            
            $resultats = $search->movie;
            if (empty($resultats))
                throw new ErrorException("Il n'y a aucun résultat!");
            $q = htmlentities($q);
            
            $resultats_count = $search->count;
            $resultats_total = $search->results->movie;
            $pages_total = (((int)($resultats_total / $count)) + ((int) ($resultats_total % $count) > 0));
            $pages = array();
            
            if ($resultats_total > $resultats_count )
            {
                for ($i=1 ; $i<=(((int)($resultats_total / $count)) + ((int) ($resultats_total % $count) > 0)) ; $i++)
                {
                    if ($page == $i)
                        $pages[] = "<a class='cur' href='$page_actuelle?q=$q&page=$i'>$i</a>";
                    else
                        $pages[] = "<a href='$page_actuelle?q=$q&page=$i'>$i</a>";
                }
            }
        }
        catch ( ErrorException $e )
        {
            $error = $e->getMessage();
        }
    }
    else $error = "La chaîne de recherche doit être composée d'au moins 2 caractères.";
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>API Allociné Helper</title>
        <style type="text/css">
            body {
                background-color: #fff;
                margin: 1em 15%;
                margin-bottom: 4em;
            }

            #content {
                width: 80%;
                margin: auto;
                font-family: Lucida Grande, Verdana, Sans-serif;
                font-size: 14px;
                color: #4F5155;
            }

            p {
                margin-bottom: 0.5em;
            }

            a {
                color: #003399;
                background-color: transparent;
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
            }

            h1, h2, h3, h4, h5, h6  {
                color: #444;
                background-color: transparent;
                font-weight: bold;
                margin: 1em 0;
                padding: 0.2em;
            }

            h1, h2, h3  {
                border-bottom: 1px solid #D0D0D0;
            }

            h1  {
                font-size: 1.8em;
            }

            h2  {
                font-size: 1.4em;
            }

            h3  {
                font-size: 1em;
                border: 0;
            }

            h4, h5, h6  {
                font-size: 0.8em;
                border-top: 1px solid #D0D0D0;
            }

            code, pre {
                font-family: Monaco, Verdana, Sans-serif;
                background-color: #f9f9f9;
                border: 1px solid #D0D0D0;
                color: #002166;
                display: block;
                font-size: small;
                margin: 14px 0 14px 0;
                padding: 12px 10px 12px 10px;
            }

            #urlview {
                color: #DBDBDB;
                font-weight: normal;
            }

            #urlview:hover {
                color: #C9C9C9;
            }

            .pages {
                padding-top: 10px;
                text-align: center;
            }

            .pages a {
                padding: 3px;
                text-decoration: none;
            }

            .pages a:not(:hover) {
                border: 1px solid transparent;
            }

            .pages a.cur, .pages a:hover {
                padding: 3px;
                border: 1px solid #498DC4;
                background-color: #498DC4;
                color: white;
                border-radius: 3px;
                text-decoration: none;
            }

            input, textarea {
                font-family: Monaco, Verdana, Sans-serif;
                background-color: #f9f9f9;
                border: 1px solid #D0D0D0;
                color: #002166;
                display: inline-block;
                margin: 0.4em;
                padding: 0.3em 0.5em;
            }

            input[type="submit"], input[type="button"], button {
                font-family: Tahoma, Verdana, Sans-serif;
                font-size: 13px;
                font-weight: bold;
                background-image: url("http://www.allocine.fr/skin/default/img/acsprite.png");
                background-position: 10px -634px;
                border: 1px solid #000000;
                color: #F0F0F0;
                display: inline-block;
                margin: 0.4em;
                padding: 0.3em 0.5em;
                border-radius: 3px;
            }

            input[type="submit"]:hover, input[type="button"]:hover {
                cursor: pointer;
                color: #FFFFFF;
            }

            #error {
                color: red;
            }

            .infos-poster {
                position: absolute;
                margin-left: 650px;
                
                padding: 3px;
                padding-bottom: 0px;
                box-shadow: 1px 1px 6px #555555;
            }

            .infos-part-1 {
                position: absolute;
                margin-left: 400px;
                max-width: 220px;
            }

            .infos-part-2 {
                max-width: 380px;
                overflow: auto;
                min-height: 116px;
            }

            .info {
                display: block;
                line-height: 14px;
                font-size: x-small;
            }

            .info-name {
                color: orange;
            }

            .info-value {
                color: grey;
            }

            .note {
                display: inline-block;
                color: transparent;
                width: 75px;
                height: 14px;
                background-image: url("http://www.allocine.fr/skin/default/img/acsprite.png");
            }

            .n0, .n { background-position: -75px -69px }
            .n1 { background-position: -60px -69px }
            .n2 { background-position: -45px -69px }
            .n3 { background-position: -30px -69px }
            .n4 { background-position: -15px -69px }
            .n5 { background-position: -0px -69px }

            .n05 { background-position: -60px -54px }
            .n15 { background-position: -45px -54px }
            .n25 { background-position: -30px -54px }
            .n35 { background-position: -15px -54px }
            .n45 { background-position: -0px -54px }

            ::selection {
                background-color: #49A7C4;
                color: white;
            }</style>
    </head>
	<body>
		<!-- Contenu -->
		<div id='content'>
			<h2>Recherche</h2>
			<form>
				<input type='text' size="42" maxlenght="42" id='search' name='q' value="<?php echo $q ?>"/>
				<input type='submit' value='Rechercher' /><br />
			</form>
			
			<?php if (!empty($error)): ?>
				<p id='error'>
					<?php echo $error ?>
				</p>
                
			<?php elseif (empty($resultats)): ?>
				<p id='error'>
					Il n'y a aucun résultat
				</p>
                
			<?php else: ?>
            
				<h3><?php echo $resultats_total ?> résultats pour "<?php echo $q ?>"<?php if ($pages_total>1): ?> - Page <?php echo "$page/$pages_total" ?><?php endif ?></h3>
                
                <?php if (!empty($pages)) echo "<h6 class='pages'>". implode(' - ', $pages) ."</h6>" ?>
                
				<?php foreach ($resultats as $r): ?>
                
					<?php
						// Traitement du poster
						if ($r->poster != $r->get_default_value()) {
							$poster = $r->poster->href('img');
							$postermax = $r->poster->href();
							$poster->resize(0, 100);
						}
						else $poster = $postermax = "http://images.allocine.fr/r_x_100/commons/emptymedia/AffichetteAllocine.gif";
						
						// Note
						$notepresse = round(@$r->statistics->pressRating);
						$notepublic = round(@$r->statistics->userRating);
						
						$notepresse_i = ($notepresse == 0) ? "Non-noté" : $notepresse . '/5';
						$notepublic_i = ($notepublic == 0) ? "Non-noté" : $notepublic . '/5';
						
					?>
					
                    <h4><?php echo $r->title ?></h4>
					
                    <div class='infos-poster'><a target='_blank' href="<?php echo $postermax ?>"><img src="<?php echo $poster ?>" /></a></div>
					
                    <p class='x-small infos-part-1'>
						<span class='info'><span class='info-name'>Année de production:</span> <span class='info-value'><?php echo @$r->productionYear ?></span></span>
						<span class='info'><span class='info-name'>Date de sortie:</span> <span class='info-value'><?php echo @$r->release->releaseDate ?></span></span>
						<span class='info line-h-16'><span class='info-name'>Note presse:</span> <span title="<?php echo $notepresse_i ?>" class='note n<?php echo $notepresse ?>'><?php echo $notepresse_i ?></span></span>
						<span class='info line-h-16'><span class='info-name'>Note public:</span> <span title="<?php echo $notepublic_i ?>" class='note n<?php echo $notepublic ?>'><?php echo $notepublic_i ?></span></span>
					</p>
					
                    <p class='x-small infos-part-2'>
						<span class='info'><span class='info-name'>Identifiant Allociné:</span> <span class='info-value'><?php echo $r->code ?></span></span>
						<span class='info'><span class='info-name'>Titre:</span> <span class='info-value'><?php echo $r->title ?></span></span>
						<span class='info'><span class='info-name'>Titre original:</span> <span class='info-value'><?php echo $r->originalTitle ?></span></span>
						<span class='info'><a class='info-name' href="<?php echo @$r->link[0]->href ?>">Fiche complète sur Allociné</a></span>
					</p>
                    
				<?php endforeach ?>
                <?php if (!empty($pages)) echo "<h6 class='pages'>". implode(' - ', $pages) ."</h6>" ?>
			<?php endif ?>
            
            
            <h6 id='urlview'><?php echo $allo->lastURL() ?></h6>
                
		</div>
	</body>
</html>