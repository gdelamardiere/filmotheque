<?php
	abstract class Allo {
		// Petite fonction pour afficher les infos de manière plus colorée avec print_r()
		public function newprint_r($var, $return=false)
		{
			$var = print_r($var, true);
			$var = preg_replace('#=>(.+)\n#i', '=> <span style="color:blue">$1</span>'.PHP_EOL, $var);
			
			if ($return)
				return $var;
			else
				echo $var;
		}
		// Traitement des données -- Général --
		// Pour les données en commun des différentes classes
		protected function traiter($nom, $donnees=NULL, &$option=NULL)
		{
			$t = $this->getTraduct($this->getOptions('cles_accents'), $this->getOptions('cles_tirets'));
			
			// Traitement
			if ($nom == 'normal')
			{
				return (empty($donnees)) ? $this->getOptions('vide') : $donnees;
			}
			
			if ($nom == 'attr-value')
			{
				$dollars = (empty($donnees['$'])) ? $this->getOptions('vide') : $donnees['$'];
				return (empty($donnees['value'])) ? $dollars : $donnees['value'];
			}
			
			if ($nom == 'attr-href')
			{
				return (empty($donnees['href'])) ? $this->getOptions('vide') : $donnees['href'];
			}
			
			if ($nom == 'attr-code')
			{
				return (empty($donnees['code'])) ? $this->getOptions('vide') : $donnees['code'];
			}
			
			if ($nom == 'attr-' && !empty($option))
			{
				return (empty($donnees[$option])) ? $this->getOptions('vide') : $donnees[$option];
			}
			
			if (substr($nom, 0, 9) == 'tab-value')
			{
				$temptab2 = array();
				foreach ($donnees as $tempi => $temptab1) {
					$dollars = (empty($temptab1['$'])) ? $this->getOptions('vide') : $temptab1['$'];
					$temptab2[] = (empty($temptab1['value'])) ? $dollars : $temptab1['value'];
				}
				if ($nom == 'tab-value')
					return $temptab2;
				elseif ($nom == 'tab-value-implode')
					return implode($this->getOptions('glue'), $temptab2);
			}
			
			if ($nom == 'name')
			{
				$nom_personne = array();
				if (!empty($donnees['givenName']))
					$nom_personne[$t['person/name/givenName']] = $donnees['givenName'];
				if (!empty($donnees['name']))
					$nom_personne[$t['person/name/name']] = $donnees['name'];
				return $nom_personne;
			}
			
			if ($nom == 'gender')
			{
				if (array_key_exists('person/gender-'.$donnees, $t))
					return $t['person/gender-'.$donnees];
				else
					return $this->getOptions('vide');
			}
			
			if ($nom == 'activity')
			{
				$act = array();
				foreach ($donnees as $a)
					$act[] = (!empty($a['value'])) ? $a['value'] : $a['$'];
				return $act;
			}
			
			if ($nom == 'activityShort')
			{
				$act = array();
				foreach ($donnees as $a)
					$act[] = (!empty($a['value'])) ? $a['value'] : $a['$'];
				return implode($this->getOptions('glue'), $act);
			}
			
			if ($nom == 'castingShort')
			{
				$castshort = array();
				foreach ($donnees as $role => $persons) {
					if (array_key_exists('castingShort/'.$role, $t))
						$castshort[$t['castingShort/'.$role]] = $persons;
				}
				return $castshort;
			}
			
			if ($nom == 'casting')
			{
				$membrelist = array();
				
				foreach ($donnees as $imembre => $membre) {
					
					// Option 'castingmax' => nombre de personne max dans le casting
					if ($this->getOptions('castingmax') >= 0 AND $imembre > $this->getOptions('castingmax')-1)
						break;
					
					$membrelist[] = array();
					
					// Infos 'person'
					$membrelist[$imembre][$t['castMember//person']] = (!empty($membre['person']['value'])) ? $membre['person']['value'] : $this->getOptions('vide');
					
					// Infos 'activity'
					$membrelist[$imembre][$t['castMember//activity']] = (isset($membre['activity']['value'])) ? $membre['activity']['value'] : $this->getOptions('vide');
					
					// Infos 'role'
					$membrelist[$imembre][$t['castMember//role']] = (isset($membre['role'])) ? $membre['role'] : $this->getOptions('vide');
					
					// Infos 'picture'
					$membrelist[$imembre][$t['castMember//picture']] = (isset($membre['picture'])) ? $membre['picture']['href'] : $this->getOptions('vide');
					
					// Infos 'personVoice'
					if (isset($membre['personVoice'])) {
						$membrelist[$imembre][$t['castMember//personVoice']] = array(
							$t['castMember//personVoice/code'] = $membre['personVoice']['code'],
							$t['castMember//personVoice/value'] = $membre['personVoice']['value']
						);
					}
						
				}
				return $membrelist;
			}
			
			if ($nom == 'castingShort-2')
			{
				// On ne prend ici que le nom de la personne
				
				$membrelist = array();
				
				foreach ($donnees as $imembre => $membre) {
					
					// Option 'castingmax' => nombre de personne max dans le casting
					if ($this->getOptions('castingmax') >= 0 AND $imembre > $this->getOptions('castingmax')-1)
						break;
					
					// Infos 'person'
					$membrelist[$imembre] = (!empty($membre['person']) && is_string($membre['person'])) ? $membre['person'] : "";
					
				}
				return implode($this->getOptions('glue'), $membrelist);
			}
			
			if ($nom == 'duree')
			{
				$minutes = $donnees / 60;
				$temptab = array();
				$temptab[$t['runtime/hour']] = floor( $minutes / 60 );
				$temptab[$t['runtime/min']] = $minutes % 60;
				$temptab[$t['runtime/global']] = floor( $minutes / 60 ) .'h'. $minutes % 60;
				return $temptab;
			}
			
			if ($nom == 'sortie')
			{
				$temptab = array();
				$temptab[$t['release/country']] = (empty($donnees['country'])) ? $this->getOptions('vide') : $donnees['country'];
				$temptab[$t['release/releaseDate']] = (empty($donnees['releaseDate'])) ? $this->getOptions('vide') : $donnees['releaseDate'];
				$temptab[$t['release/releaseState']] = (empty($donnees['releaseState'])) ? $this->getOptions('vide') : $donnees['releaseState'];
				$temptab[$t['release/distributor']] = (empty($donnees['distributor'])) ? $this->getOptions('vide') : $donnees['distributor'];
				return $temptab;
			}
			
			if ($nom == 'stats')
			{
				$temptab = array();
				$temptab[$t['statistics/pressRating']] = (isset($donnees['pressRating']) ? $donnees['pressRating'] : $this->getOptions('vide') );
				$temptab[$t['statistics/pressReviewCount']] = (isset($donnees['pressReviewCount']) ? $donnees['pressReviewCount'] : $this->getOptions('vide') );
				$temptab[$t['statistics/userRating']] = (isset($donnees['userRating']) ? $donnees['userRating'] : $this->getOptions('vide') );
				$temptab[$t['statistics/userReviewCount']] = (isset($donnees['userReviewCount']) ? $donnees['userReviewCount'] : $this->getOptions('vide') );
				$temptab[$t['statistics/userNoteCount']] = (isset($donnees['userNoteCount']) ? $donnees['userNoteCount'] : $this->getOptions('vide') );
				$temptab[$t['statistics/fanCount']] = (isset($donnees['fanCount']) ? $donnees['fanCount'] : $this->getOptions('vide') );
				$temptab[$t['statistics/theaterCount']] = (isset($donnees['theaterCount']) ? $donnees['theaterCount'] : $this->getOptions('vide') );
				$temptab[$t['statistics/theaterCountOnRelease']] = (isset($donnees['theaterCountOnRelease']) ? $donnees['theaterCountOnRelease'] : $this->getOptions('vide') );
				$temptab[$t['statistics/releaseWeekPosition']] = (isset($donnees['releaseWeekPosition']) ? $donnees['releaseWeekPosition'] : $this->getOptions('vide') );
				return $temptab;
			}
				
			
			if ($nom == 'media')
			{
				$media = $donnees;
				$m = array();
				$option = ($donnees['class'] == 'video') ? 'video' : 'image';
				
				/* Traiter vidéo */
				if ($donnees['class'] == 'video')
				{
					$m[$t['media//code']] = (empty($donnees['code'])) ? $this->getOptions('vide') : $donnees['code'];
					$m[$t['media//video-href']] = "http://www.allocine.fr/blogvision/" . (empty($donnees['code'])) ? $this->getOptions('vide') : $donnees['code'];
					$m[$t['media//rcode']] = (empty($donnees['rcode'])) ? $this->getOptions('vide') : $donnees['rcode'];
					$m[$t['media//thumbnail']] = (empty($donnees['thumbnail'])) ? $this->getOptions('vide') : $donnees['thumbnail'];
					$m[$t['media//type']] = (empty($donnees['type'])) ? $this->getOptions('vide') : $donnees['type'];
					$m[$t['media//title']] = (empty($donnees['title'])) ? $this->getOptions('vide') : $donnees['title'];
					$m[$t['media//description']] = (empty($donnees['description'])) ? $this->getOptions('vide') : $donnees['description'];
					$m[$t['media//copyrightHolder']] = (empty($donnees['copyrightHolder'])) ? $this->getOptions('vide') : $donnees['copyrightHolder'];
					
					// Infos 'version'
					if (isset($media['version'])) {
						$m[$t['media//version/language']] = (empty($donnees['version']['value'])) ? $this->getOptions('vide') : $donnees['version']['value'];
						$m[$t['media//version/original']] = (empty($donnees['version']['original'])) ? $this->getOptions('vide') : $donnees['version']['original'];
					}
					
					// Infos statistics
					if (isset($media['statistics'])) {
						$m[$t['media//viewCount']] = (empty($donnees['statistics']['viewCount'])) ? $this->getOptions('vide') : $donnees['statistics']['viewCount'];
						$m[$t['media//commentCount']] = (empty($donnees['statistics']['commentCount'])) ? $this->getOptions('vide') : $donnees['statistics']['commentCount'];
					}
					
					// Interprétation
					if (!empty($media['rendition']))
					{
						$m[$t['media//rendition']] = array();
						foreach ($media['rendition'] as $i => $rendition) {
							$m[$t['media//rendition']][$i][$t['media//rendition/href']] = (empty($donnees['rendition']['href'])) ? $this->getOptions('vide') : $donnees['rendition']['href'];
							$m[$t['media//rendition']][$i][$t['media//rendition/format']] = (empty($donnees['rendition']['format'])) ? $this->getOptions('vide') : $donnees['rendition']['format'];
						}
					}
					return $m;
				}
				
				// Traiter image
				elseif ($donnees['class'] == 'picture')
				{
					$m[$t['media//code']] = (empty($donnees['code'])) ? $this->getOptions('vide') : $donnees['code'];
					$m[$t['media//image-href']] = "http://www.allocine.fr/blogvision/" . (empty($donnees['code'])) ? $this->getOptions('vide') : $donnees['code'];
					$m[$t['media//rcode']] = (empty($donnees['rcode'])) ? $this->getOptions('vide') : $donnees['rcode'];
					$m[$t['media//thumbnail']] = (empty($donnees['thumbnail'])) ? $this->getOptions('vide') : $donnees['thumbnail'];
					$m[$t['media//type']] = (empty($donnees['type'])) ? $this->getOptions('vide') : $donnees['type'];
					$m[$t['media//title']] = (empty($donnees['title'])) ? $this->getOptions('vide') : $donnees['title'];
					$m[$t['media//description']] = (empty($donnees['description'])) ? $this->getOptions('vide') : $donnees['description'];
					$m[$t['media//copyrightHolder']] = (empty($donnees['copyrightHolder'])) ? $this->getOptions('vide') : $donnees['copyrightHolder'];
					
					// Infos statistics
					if (isset($media['statistics'])) {
						$m[$t['media//viewCount']] = (empty($donnees['statistics']['viewCount'])) ? $this->getOptions('vide') : $donnees['statistics']['viewCount'];
						$m[$t['media//commentCount']] = (empty($donnees['statistics']['commentCount'])) ? $this->getOptions('vide') : $donnees['statistics']['commentCount'];
					}
					
					// Interprétation
					if (!empty($media['rendition']))
					{
						$m[$t['media//rendition']] = array();
						foreach ($media['rendition'] as $i => $rendition) {
							$m[$t['media//rendition']][$i][$t['media//rendition/href']] = (empty($donnees['rendition']['href'])) ? $this->getOptions('vide') : $donnees['rendition']['href'];
							$m[$t['media//rendition']][$i][$t['media//rendition/format']] = (empty($donnees['rendition']['format'])) ? $this->getOptions('vide') : $donnees['rendition']['format'];
						}
					}
					return $m;
				}
				
			}
			
			
		}
		
		// Temps d'exécution
		private $exectimer = 0;
		private $exectimer_start = 0;
		
		// Reset & Start
		protected function timer_start()
		{
			$this->exectimer = 0;
			$this->exectimer_start = microtime(true);
		}
		
		// Stop
		protected function timer_stop()
		{
			$this->exectimer = round(microtime(true) - $this->exectimer_start, 4);
		}
		
		// Retourne le temps d'exécution
		public function tempsExec()
		{
			return (float) $this->exectimer;
		}
		
		// Retourne les infos sur l'API Allociné
		public function infos($versionhtml=true)
		{
			if ($versionhtml) {
				$infos =  "<h1>API Allociné V".API_ALLOCINE_VERSION."</h1>";
				$infos .= "<ul><li><strong>Ce code source est sous licence Creative Commons (CC-by)</strong><br />";
				$infos .= "<a href='http://creativecommons.org/licenses/by/2.0/fr/'>http://creativecommons.org/licenses/by/2.0/fr/</a></li>";
				$infos .= "<li>Auteur original de la source: <strong>Etienne GAUVIN</strong></li>";
				$infos .= "<li>Pour informations supplémentaires: <strong><a href='mailto:etn3000@laposte.net'>Etn3000@laposte.net</a></strong></li>";
				$infos .= "<li>Votre version de PHP: <strong>".PHP_VERSION."</strong></li>";
				$infos .= "<li>Compatibilité: <i><strong>";
				if (version_compare(PHP_VERSION, '5.0.0', '<'))
					$infos .= "<span style='color:red'>Non</span></strong></i></li>";
				else
					$infos .= "<span style='color:green'>Oui</span></strong></i></li>";
				$infos .= "<li>Mise à jour disponible: <i><strong>";
				$dernièreversion = @strtr(@file_get_contents("http://www.api-allocine.netne.net/last_version.php"), 1, 4);
				if (empty($dernièreversion))
					$infos .= "<span style='color:red'>?</span></strong></i></li>";
				elseif (version_compare(API_ALLOCINE_VERSION, $dernièreversion, '<'))
					$infos .= "<span style='color:orange'>Oui: V$dernièreversion</span></strong></i></li>";
				else
					$infos .= "<span style='color:green'>Non</span></strong></i></li>";
				
				$infos .= "</ul>";
			} else {
				$infos =  "- API Allociné V".API_ALLOCINE_VERSION." -\r\n";
				$infos .= "Ce code source est sous licence Creative Commons (CC-by)\r\n";
				$infos .= "http://creativecommons.org/licenses/by/2.0/fr/ \r\n";
				$infos .= "Auteur original de la source: Etienne GAUVIN\r\n";
				$infos .= "Pour informations supplémentaires: Etn3000@laposte.net\r\n";
				$infos .= "Votre version de PHP: ".PHP_VERSION."\r\n";
				$infos .= "Compatibilité: ";
				if (version_compare(PHP_VERSION, '5.0.0', '<'))
					$infos .= "Non\r\n";
				else
					$infos .= "Oui\r\n";
				$infos .= "Mise à jour disponible: ";
				$dernièreversion = @strtr(@file_get_contents("http://www.api-allocine.netne.net/last_version.php"), 1, 4);
				if (empty($dernièreversion))
					$infos .= "?";
				elseif (version_compare(API_ALLOCINE_VERSION, $dernièreversion, '<'))
					$infos .= "Oui: V$dernièreversion";
				else
					$infos .= "Non";
				
				$infos .= "\r\n";
			}
			
			
			return $infos;
		}
		
		protected function utf8_adecode($tableau)
		{
			$tableau2 = array();
			if (is_string($tableau)) return utf8_decode($tableau);
			if (!is_array($tableau)) return $tableau;
			foreach ($tableau as $i => $cell) {
				
				if (is_string($cell))
					$tableau2[utf8_decode($i)] = utf8_decode($cell);
				elseif (is_array($cell))
					$tableau2[utf8_decode($i)] = $this->utf8_adecode($cell);
				else
					$tableau2[utf8_decode($i)] = $cell;
			}
			return $tableau2;
		}
		
		protected function utf8_aencode($tableau)
		{
			$tableau2 = array();
			if (is_string($tableau)) return utf8_encode($tableau);
			if (!is_array($tableau)) return $tableau;
			foreach ($tableau as $i => $cell) {
				
				if (is_string($cell))
					$tableau2[utf8_encode($i)] = utf8_encode($cell);
				elseif (is_array($cell))
					$tableau2[utf8_encode($i)] = $this->utf8_aencode($cell);
				else
					$tableau2[utf8_encode($i)] = $cell;
			}
			return $tableau2;
		}
		
		protected function getTraduct($accents=false, $tirets=true)
		{
			$return = array(
			
				'code' =>				'id',
				'title' =>				'titre',
				'movieType' =>			'type-film',
				'originalTitle' =>		'titre-original',
				'keywords' =>			'mots-clés',
				'productionYear' =>		'année-production',
				'nationality-1' =>		'nationalités',
				'nationality-2' =>		'nationalités-court',
				'genre-1' =>			'genre',
				'genre-2' =>			'genres-court',
				'synopsis' =>			'synopsis',
				'synopsisShort' =>		'synopsis-court',
				
				'person' =>				'personne',
				'person/code' =>		'id',
				'person/nationality-1' =>	'nationnalité',
				'person/nationality-2' =>	'nationnalités-court',
				'person/name' =>		'nom',
				'person/realName'=>	'nom-réel',
				'person/name/name' =>	'nom',
				'person/name/givenName'=>'prénom',
				'person/gender' =>		'sexe',
				'person/gender-1' =>	'Homme',
				'person/gender-2' =>	'Femme',
				'person/biography' =>	'biographie',
				'person/biographyShort'=>'biographie-courte',
				'person/activity' =>	'activité',
				'person/activityShort'=>'activité-court',
				'person/birthDate' =>	'date-naissance',
				'person/birthPlace' =>	'lieu-naissance',
				'person/picture' =>	'photo',
				'person/participation'=>'participation',
				
				
				'castMember' =>						'casting',
				'castMember//person' 	=>			'personne',
				'castMember//person/code' =>		'id',
				'castMember//person/value' =>		'nom',
				'castMember//personVoice' =>		'voix',
				'castMember//personVoice/code'=>	'id',
				'castMember//personVoice/value'=>	'nom',
				'castMember//activity' 	=>			'activité',
				'castMember//role' 	=>				'rôle',
				'castMember//picture' =>			'image',
				
				'castingShort' =>			'casting-court',
				'castingShort/actors' 	=>	'acteurs',
				'castingShort/directors'=>	'réalisateurs',
				
				
				'poster' =>			'poster',
				
				'seasonCount' =>		'nombre-saisons',
				'episodeCount' =>		'nombre-épisodes',
				'formatTime' =>			'durée-épisode',
				'yearStart'=>			'année-départ',
				'yearEnd'=>				'année-fin',
				'link'=>				'lien',
				
				'totalResults'=>		'total-résultats',
				'count'=>				'résultats-page',
				'page'=>				'page',
				
				'results'=>				'résultats',
				'results/location'=>	'lieu',
				'results/movie'=>		'film',
				'results/person'=>		'personne',
				'results/theater'=>		'cinéma',
				'results/tvseries'=>	'série',
				'results/media'=>		'media',
				'results/photo'=>		'photo',
				'results/video'=>		'vidéo',
				'results/news'=>		'article',
				
				'trailer' =>			'bande-annonce',
				'trailer/href' =>		'href',
				'trailer/code' =>		'id',
				
				'runtime' =>			'durée',
				'runtime/hour' =>		'heures',
				'runtime/min' =>		'minutes',
				'runtime/sec' =>		'secondes',
				'runtime/global' =>		'global',
				
				'movieCertificate'=>		'certification-film',
				'language'=>				'langage',
				'productionFormat'=>		'format-production',
				'projectionFormat'=>		'format-projection',
				'color'=>					'couleur',
				'language'=>				'langage',
				'soundFormat'=>				'format-son',
			
				'release' =>				'sortie',
				'release/country' =>		'pays',
				'release/releaseDate' =>	'date',
				'release/releaseState' =>	'état',
				'release/distributor' =>	'distributeur',
			
				'trailer' =>			'bande-annonce',
				'trailer/href' =>		'href',
				'trailer/code' =>		'code',
				
				'media' => 						'actu',
				'media-image' => 				'image',
				'media-video' => 				'video',
				'media//code' => 				'code',
				'media//rcode' =>		 		'rcode',
				'media//type' => 				'type',
				'media//title' => 				'titre',
				'media//thumbnail' => 			'href',
				'media//copyrightHolder' => 	'copyright',
				'media//description' => 		'description',
				'media//version/language' => 	'langage',
				'media//version/original' => 	'version-originale',
				'media//viewCount' => 			'compteur-vues',
				'media//commentCount' =>	 	'compteur-commentaires',
				'media//video-href' =>			'href',
				'media//image-href' =>			'href',
				'media//rendition'=>			'interprétation',
				'media//rendition/href'=>		'href',
				'media//rendition/format'=>		'format',
				
				'statistics' =>						'statistiques',
				'statistics/pressRating' =>			'note-presse',
				'statistics/pressReviewCount'=>		'avis-presse',
				'statistics/userRating' =>			'note-public',
				'statistics/userReviewCount' =>		'avis-public',
				'statistics/userNoteCount' =>		'votes-public',
				'statistics/fanCount'=>				'fans',
				'statistics/theaterCount' =>		'cinémas',
				'statistics/theaterCountOnRelease'=>'cinémas-sortie',
				'statistics/releaseWeekPosition'=>	'position-semaine'
			);
			
			if (!$accents) {
				foreach ($return as $k => $entree) {
					$return[$k] = strtr(
						$entree,
						"ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöùúûüýÿ",
						"AAAAAACEEEEIIIINOOOOOUUUUYaaaaaaceeeeiiiinooooouuuuyy"
					);
				}
			}
			
			
			if (!$tirets) {
				foreach ($return as $k => $entree)
					$return[$k] = str_replace(
						"-",
						"_",
						$entree
					);
			}
			
			return $return;
		}
		
		
		// Options
		// Variable des options
		private $options = array();
		
		// Valeurs par défaut
		private $options_defaut = array(
			'glue'=>			', ',
			'infos'=>			'large',
			'numresult'=>		'auto',
			'cles_tirets'=>		true,
			'cles_accents'=>	false,
			'castingmax'=>		-1,
			'partner'=>			3,
			'page'=>			1,
			'vide'=>			'?',
			'count'=>			16
		);
		
		// Retourner aux valeurs par défaut
		public function clearOptions()
		{
			$this->options = $this->options_defaut;
		}
		
		// Récupérer la valeur d'une/de toutes les options
		public function getOptions($option='')
		{
			if (!empty($option) AND array_key_exists($option, $this->options))
				return $this->options[$option];
			else {
				// if (empty($this->options))
					// $this->options = $this->getDefaultOptions();
				return $this->options;
			}
			
		}
		
		// Récupérer la valeur d'une option
		public function getDefaultOptions($option='')
		{
			if (!empty($option) AND array_key_exists($option, $this->options_defaut))
				return $this->options_defaut[$option];
			else
				return $this->options_defaut;
		}
		
		// Modifier/Valider les options
		public function setOptions($options=array())
		{
			// Vérification des options actuelle (juste l'existence)
			// Par rapport aux options par défault
			foreach ($this->options_defaut as $opt => $valdef) {
				if (!isset($this->options[$opt]))
					$this->options[$opt] = $valdef;
			}
			
			// Vérifie que $options est bien un array: on sait jamais
			if (!is_array($options)) {
				$this->addErr('NOTICE_ERR_OPTIONS');
			}
			
			/* Vérification de la validité de chaque option */
			/* Si une option est invalide c'est la valeur par défaut qui est attribuée */
			
			// Option 'glue'
			if (isset($options['glue'])) {
				if (is_string($options['glue']))
					$this->options['glue'] = $options['glue'];
				else {
					$this->addErr('NOTICE_ERR_PARAM_NUMRESULT');
					$this->options['glue'] = $this->options_defaut['glue'];
				}
			}
			
			// Option 'vide'
			if (isset($options['vide'])) {
				if (is_string($options['vide']))
					$this->options['vide'] = $options['vide'];
				else {
					$this->addErr('NOTICE_ERR_PARAM_VIDE');
					$this->options['vide'] = $this->options_defaut['vide'];
				}
			}
			
			// Option 'infos'
			if (!empty($options['infos'])) {
				if (in_array($options['infos'], array('small','medium','large'), true))
					$this->options['infos'] = $options['infos'];
				else {
					$this->addErr('NOTICE_ERR_PARAM_NUMRESULT');
					$this->options['infos'] = $this->options_defaut['infos'];
				}
			}
			
			// Option 'numresult'
			if (!empty($options['numresult'])) {
				if (in_array($options['numresult'], array('auto',1,2,3,4,5,6,7,8,9,10)))
					$this->options['numresult'] = $options['numresult'];
				else {
					$this->addErr('NOTICE_ERR_PARAM_NUMRESULT');
					$this->options['numresult'] = $this->options_defaut['numresult'];
				}
			}
			
			// Option 'cles_tirets'
			if (isset($options['cles_tirets'])) {
				if (is_bool($options['cles_tirets']))
					$this->options['cles_tirets'] = (bool) $options['cles_tirets'];
				else {
					$this->addErr('NOTICE_ERR_PARAM_CLES_TIRETS');
					$this->options['cles_tirets'] = $this->options_defaut['cles_tirets'];
				}
			}
			
			// Option 'cles_accents'
			if (isset($options['cles_accents'])) {
				if (is_bool($options['cles_accents']))
					$this->options['cles_accents'] = (bool) $options['cles_accents'];
				else {
					$this->addErr('NOTICE_ERR_PARAM_CLES_ACCENTS');
					$this->options['cles_accents'] = $this->options_defaut['cles_accents'];
				}
			}
			
			// Option 'castingmax'
			if (isset($options['castingmax'])) {
				if (is_int((int)$options['castingmax']))
					$this->options['castingmax'] = (int) $options['castingmax'];
				else {
					$this->addErr('NOTICE_ERR_PARAM_CASTINGMAX');
					$this->options['castingmax'] = $this->options_defaut['castingmax'];
				}
			}
			
			// Option 'partner'
			if (isset($options['partner'])) {
				if (is_int((int)$options['partner']))
					$this->options['partner'] = abs( (int) $options['partner'] );
				else {
					$this->addErr('NOTICE_ERR_PARAM_PARTNER');
					$this->options['partner'] = $this->options_defaut['partner'];
				}
			}
			
			// Option 'page'
			if (isset($options['page'])) {
				if (is_int((int)$options['page']))
					$this->options['page'] = abs( (int) $options['page'] );
				else {
					$this->addErr('NOTICE_ERR_PARAM_PAGE');
					$this->options['page'] = $this->options_defaut['page'];
				}
			}
			
			// Option 'count'
			if (isset($options['count'])) {
				if (is_int((int)$options['count']))
					$this->options['count'] = abs( (int) $options['count'] );
				else {
					$this->addErr('NOTICE_ERR_PARAM_COUNT');
					$this->options['count'] = $this->options_defaut['count'];
				}
			}
			
		}
	
		// Erreurs
		private $erreurs = array();
		
		// Messages d'erreur
		private $erreurs_msg = array(
			'ERR_DEFAULT'=>					"Erreur !",
			'ERR_FILM_FORMAT'=>				"Format incorrect de FILM !",
			'ERR_SERIE_FORMAT'=>			"Format incorrect de SERIE !",
			'ERR_SERIE_FORMAT'=>			"Format incorrect de SEARCH !",
			'ERR_EMPTY_KEYWORDS'=>			"Mots clés vides !",
			'ERR_KEYWORDS_INVALID_LEN'=>	"Longueur des mots-clés invalide !",
			'ERR_NO_RESULT'=>				"Aucun résultat !",
			'ERR_DATA_DOWNLOAD'=>			"Erreur lors de la récupération des données.",
			'ERR_DATA_JSON'=>				"Erreur lors du traitement des données (JSON)",
			'ERR_ALLOCINE'=>				"Allociné a retourné une erreur !",
			'NOTICE_ERR_OPTIONS'=>			"La variable des options est invalide !",
			'NOTICE_ERR_PARAM_GLUE'=>		"Le paramètre glue est incorrect !",
			'NOTICE_ERR_PARAM_VIDE'=>		"Le paramètre glue est incorrect !",
			'NOTICE_ERR_PARAM_CASTINGMAX'=>	"Le paramètre castingmax est incorrect !",
			'NOTICE_ERR_PARAM_NUMRESULT'=>	"Le paramètre numresult est incorrect !",
			'NOTICE_ERR_PARAM_INFOS'=>		"Le paramètre infos est incorrect !",
			'NOTICE_ERR_PARAM_PARTNER'=>	"Le paramètre partner est incorrect !",
			'NOTICE_ERR_PARAM_PAGE'=>		"Le paramètre page est incorrect !",
			'NOTICE_ERR_PARAM_COUNT'=>		"Le paramètre count est incorrect !",
			'NOTICE_ERR_PARAM_CLES_TIRETS'=>"Le paramètre cles_tirets est incorrect !",
			'NOTICE_ERR_PARAM_CLES_ACCENTS'=>"Le paramètre cles_accents est incorrect !"
		);
		// Ajouter une erreur
		protected function addErr($key, $plus="")
		{
			$errs = $this->erreurs;
			$msgs = $this->erreurs_msg;
			
			if (array_key_exists($key, $msgs))
				$errs[] = array('key'=>$key, 'msg'=>$msgs[$key].$plus, 't'=>date('H:i:s'));
			else
				$errs[] = array('key'=>'ERR_DEFAULT', 'msg'=>$msgs['ERR_DEFAULT'].$plus, 't'=>date('H:i:s'));
			
			
			$this->erreurs = $errs;
		}
		
		// Récupérer toutes les erreurs
		public function errors()
		{ return $this->erreurs; }
		
		// Effacer les erreurs enregistrées
		public function clearErrs()
		{ $this->erreurs = array(); }
	
	}