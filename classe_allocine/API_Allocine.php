<?php
	define('API_ALLOCINE_VERSION', '5.3.0');
	
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
	
	class AlloMovie extends Allo {
		
		// Film
		private $film = array();
		
		// Ajouter des données au tableau du film
		private function addData($nom, $donnees)
		{
			$this->film[(string)$nom] = $donnees;
		}
		
		// Ajouter des données au tableau du film
		private function clearData()
		{
			$this->film = array();
		}
		
		/** get, __get et __toArray retournent le tableau du film **/
		// Pour récupérer une info particulière
		public function get($param='') {
			if (empty($param))
				return $this->film;
			elseif (!array_key_exists($param, $this->film))
				return $this->film;
			else
				return $this->film[$param];
		}
		
		// Pour récupérer une propriété
		public function __get($param='') {
			if (empty($param))
				return $this->film;
			elseif (!array_key_exists($param, $this->film))
				return $this->film;
			else
				return $this->film[$param];
		}
		
		// Si est appelé comme un array
		public function __toArray() {
			return $this->get();
		}
		
		/** Retourne tout le tableau, pratique lors du développement **/
		// Si est appelé comme un string
		public function __toString() {
			return parent::newprint_r($this->film, true);
		}
		
		/** __construct et newMovie ont le même effet **/
		// Constructeur, a le même rôle que newMovie
		public function __construct($FILM=0, $options=array()) {
			if (!empty($FILM))
				return $this->newMovie($FILM, $options);
		}
		
		// Fonction newMovie
		public function newMovie($FILM, $options=array()) {
			// Temps d'execution
			parent::timer_start();
			
			// Nettoyage de $this->film
			$this->film = array();
			parent::clearErrs();
			parent::setOptions($options);
			$options = parent::getOptions();
				
			
				/*
					Analyse du paramètre $FILM
					Si c'est un identifiant (int), la récupération des informations est plus rapide.
					Si c'est un titre (string), la récupération des informations est plus lente car deux requêtes sont effectuées (recherche+infos).
				
				*/
			
				// Si $FILM est vide
				if (empty($FILM)) {
					parent::addErr('ERR_EMPTY_KEYWORDS');
					return 0;
				}
				
				// Si $FILM n'est ni de type string ni de type int
				if (!is_string($FILM) AND !is_numeric($FILM)) {
					parent::addErr('ERR_FILM_FORMAT');
					return 0;
				}
			
				// Si $FILM est de type string
				elseif (is_string($FILM)) {
				
					// Va effectuer une recherche sur les mots, sans majuscules/accents/caractères spéciaux et encodés pour le passage en url
					$recherche_motscles = urlencode(strtr(strtolower(trim($FILM)),'àáâãäçèéêëìíîïñòóôõöùúûüýÿ_."\'-','aaaaaceeeeiiiinooooouuuuyy     '));
					// Url de la recherche des films avec les paramètres
					$recherche_url = 'http://api.allocine.fr/xml/search?q='.$recherche_motscles.'&partner='.$options['partner'].'&json=1&count=10';
					// Envoi de la requête et récupération du json
					$recherche_json = @file_get_contents($recherche_url);
					
					// Si erreur dans le JSON
					if (empty($recherche_json)) {
						parent::addErr('ERR_DATA_DOWNLOAD');
						return 0;
					}
					
					// Si le JSON est valide
					else {
						
						// Conversion du JSON en array
						$recherche_array = @json_decode($recherche_json, true);
						
						// Vérification de la présence d'erreur(s)
						if ( !$recherche_array ) {
							
							// En cas d'erreur avec le JSON
							parent::addErr('ERR_DATA_JSON');
							return 0;
							
						} else {
							// Si le JSON est valide
							// Vérification du champ "error"
							if ( !empty($recherche_array["error"]) ) {
								
								// Si le champ erreur n'est pas vide
								parent::addErr('ERR_ALLOCINE', ' "'.$recherche_array["error"]['$'].'"');
								return 0;
								
							} elseif ( empty($recherche_array["feed"]["movie"]) OR count($recherche_array["feed"]["movie"]) < 1) {
								
								// Si il n'y a aucun résultat dans les films
								parent::addErr('ERR_NO_RESULT');
								return 0;
								
							// Si il y a des résultats dans les films
							} else {
								
								// Vérification du nombre de résultats
								// Par rapport à $options['numresult']
								if ($options['numresult'] == 'auto') {
									
									// Mode automatique: prise du résultat les plus ressemblant
									$recherche_ = urldecode($recherche_motscles);
									$correspondance = 0;
									$bestcorres = 0;
									
									foreach ($recherche_array["feed"]["movie"] as $i => $result) {
										
										$result_title = (!empty($result['title'])) ? $result['title'] : $result['originalTitle'];
										similar_text($recherche_, strtolower(trim($result_title)), $sim);
										if (round($sim) == 100) {
											$bestcorres = $i;
											$correspondance = round($sim, 3);
											break;
										}
										if (round($sim > $correspondance)) {
											$bestcorres = $i;
											$correspondance = round($sim, 3);
										}
									}
									
									// Correspondance la plus proche: $bestcorres
									// Récupération de l'ID du film sélectionné
									$ID = (int) $recherche_array["feed"]["movie"][$bestcorres]['code'];
								
									
								} else {
									
									// Si 'numresult' n'est pas précisé comme automatique
									
									if (count($recherche_array["feed"]["movie"]) < $options['numresult']) {
										// Si la quantité de résultats retourné est inférieur à 'numresult'
										$options['numresult'] = count($recherche_array["feed"]["movie"]);
									}
								
									if (!empty($recherche_array["feed"]["movie"][$options['numresult']-1])) {
									
										// Extraction du film sélectionné
										$resultat_selectionne = $recherche_array["feed"]["movie"][$options['numresult']-1];
									} else {
									
										// Erreur qui ne devrait pas se produire normalement
										// Si c'est le cas on prend le 1er résultat
										$resultat_selectionne = $recherche_array["feed"]["movie"][0];
									}
									// Récupération de l'ID du film sélectionné
									$ID = (int) $resultat_selectionne['code'];
								}
								
							}
						}
					}
				
				}
				// Si $FILM est de type int
				elseif (is_numeric($FILM)) {
					$ID = (int) $FILM;
				}	
			
			/*
			****
			***** RECUPERATION DES INFOS
			***** Grâce à $ID
			****
			*/
		
			// Url avec les paramètres
			$req_url = 'http://api.allocine.fr/xml/movie?code='.urlencode($ID).'&partner='.$options['partner'].'&json=1&profile='.$options['infos'];
			// Envoi de la requête et récupération du json
			$req_json = @file_get_contents($req_url);
		
			if (!$req_json) {
				// En cas d'erreur
				parent::addErr('ERR_DATA_DOWNLOAD');
				return 0;
				
			} else {
				// Remplacement des "$" par des "value"
				// + de lisibilité
				$req_json = str_replace('"$"', '"value"', $req_json);
				
				// Conversion du JSON en array
				$req_array = @json_decode($req_json, true);
				
				
				// Vérification de la présence d'erreur(s)
				if ( !$req_array ) {
					
					// En cas d'erreur avec le JSON
					parent::addErr('ERR_DATA_JSON');
					return 0;
					
				} else {
					// Si le JSON est valide
					// Vérification du champ "error"
					if ( !empty($req_array["error"]) ) {
						
						// Si le champ erreur n'est pas vide
						parent::addErr('ERR_ALLOCINE', ' "'.$req_array["error"]['value'].'"');
						return 0;	
					} else {
						
						/*
						****
						***** Début du traitement des données
						****
						*/
						
						// Nettoyage du Tableau de sortie
						$this->clearData();
						
						// Récupération du tableau pour la traduction
						$t = parent::getTraduct($options['cles_accents'], $options['cles_tirets']);
						
						// Décodage de tous les champs (string) de l'utf8
						$alldata = parent::utf8_adecode($req_array["movie"]);
						
						
						// Parcourir toutes les données de $alldata
						// $idata => nom de l'élément
						// $data => valeur de l'élément
						foreach ( $alldata as $idata => $data )
						{
							
							switch ($idata)
							{
								case "code":
									$this->addData($t["code"], parent::traiter('normal', $data));
								break;
								
								case "title":
									$this->addData( $t['title'], parent::traiter('normal', $data));
								break;
								
								case "movieType":
									$this->addData( $t['movieType'], parent::traiter('attr-value', $data));
								break;
								
								case "originalTitle":
									$this->addData( $t['originalTitle'], parent::traiter('normal', $data));
								break;
								
								case "keywords":
									$this->addData( $t['keywords'], parent::traiter('normal', $data));
								break;
								
								case "productionYear":
									$this->addData( $t['productionYear'], parent::traiter('normal', $data));
								break;
								
								case "nationality":
									$this->addData( $t['nationality-1'], parent::traiter('tab-value', $data));
									$this->addData( $t['nationality-2'], parent::traiter('tab-value-implode', $data));
								break;
								
								case "genre":
									$this->addData( $t['genre-1'], parent::traiter('tab-value', $data));
									$this->addData( $t['genre-2'], parent::traiter('tab-value-implode', $data));
								break;
								
								case "synopsis":
									$this->addData( $t['synopsis'], parent::traiter('normal', $data));
								break;
								
								case "synopsisShort":
									$this->addData( $t['synopsisShort'], parent::traiter('normal', $data));
								break;
								
								case "castingShort":
									$this->addData( $t['castingShort'], parent::traiter('castingShort', $data));
								break;
								
								case "castMember":
									$this->addData( $t['castMember'], parent::traiter('casting', $data));
								break;
								
								case "poster":
									$this->addData( $t['poster'], parent::traiter('attr-href', $data));
								break;
								
								case "trailer":
									$this->addData( $t['trailer'], array(
										$t['trailer/href'] => parent::traiter('attr-href', $data),
										$t['trailer/code'] => parent::traiter('attr-code', $data)
									));
								break;
								
								case "runtime":
									$this->addData( $t['runtime'], parent::traiter('duree', $data));
								break;
								
								case "movieCertificate":
									$this->addData( $t['movieCertificate'], parent::traiter('attr-value', $data));
								break;
								
								case "language":
									$this->addData( $t['language'], parent::traiter('attr-value', $data));
								break;
								
								case "productionFormat":
									$this->addData( $t['productionFormat'], parent::traiter('attr-value', $data));
								break;
								
								case "projectionFormat":
									$this->addData( $t['projectionFormat'], parent::traiter('attr-value', $data));
								break;
								
								case "color":
									$this->addData( $t['color'], parent::traiter('attr-value', $data));
								break;
								
								case "soundFormat":
									$this->addData( $t['soundFormat'], parent::traiter('attr-value', $data));
								break;
								
								case "release":
									$this->addData( $t['release'], parent::traiter('sortie', $data));
								break;
								
								case "statistics":
									$this->addData( $t['statistics'], parent::traiter('stats', $data));
								break;
								
								case "media":
									$am = array();
									foreach ($data as $i => $media) {
										$m = parent::traiter('media', $media, $typemedia);
										$am[$t['media-' . $typemedia]][] = $m;
									}
									foreach ($am as $i => $m)
									{
										$this->addData($i, $m);
									}
								break;
							}
						}
						/*
						****
						***** Fin du traitement des données
						****
						*/
						
						parent::timer_stop();
					}
				}
			}
		}
	}
	
	class AlloPerson extends Allo {
		
		// Série
		private $person = array();
		
		// Ajouter des données
		private function addData($nom, $donnees)
		{
			$this->person[(string)$nom] = $donnees;
		}
		
		// Effacer les données
		private function clearData()
		{
			$this->person = array();
		}
		
		/** get, __get et __toArray retournent le tableau **/
		// Pour récupérer une info particulière
		public function get($param='') {
			if (empty($param))
				return $this->person;
			elseif (!array_key_exists($param, $this->person))
				return $this->person;
			else
				return $this->person[$param];
		}
		
		// Pour récupérer une propriété
		public function __get($param='') {
			$this->get($param);
		}
		
		// Si est appelé comme un array
		public function __toArray() {
			return $this->get();
		}
		
		/** Retourne tout le tableau, pratique lors du développement **/
		// Si est appelé comme un string
		public function __toString() {
			return print_r($this->get(), 1);
		}
		
		/** __construct et newPerson ont le même effet **/
		// Constructeur, a le même rôle que newperson
		public function __construct($person=0, $options=array()) {
			if (!empty($person))
				return $this->newPerson($person, $options);
		}
		
		// Fonction newperson
		public function newPerson($person, $options=array()) {
			// Temps d'execution
			parent::timer_start();
			
			// Nettoyage de $this->person
			$this->person = array();
			parent::clearErrs();
			parent::setOptions($options);
			$options = parent::getOptions();
				
			
				/*
					Analyse du paramètre $person
					Si c'est un identifiant (int), la récupération des informations est plus rapide.
					Si c'est un titre (string), la récupération des informations est plus lente car deux requêtes sont effectuées (recherche+infos).
				
				*/
			
				// Si $person est vide
				if (empty($person)) {
					parent::addErr('ERR_EMPTY_KEYWORDS');
					return 0;
				}
				
				// Si $person n'est ni de type string ni de type int
				if (!is_string($person) AND !is_numeric($person)) {
					parent::addErr('ERR_PERSON_FORMAT');
					return 0;
				}
			
				// Si $person est de type string
				elseif (is_string($person)) {
				
					// Va effectuer une recherche sur les mots, sans majuscules/accents/caractères spéciaux et encodés pour le passage en url
					$recherche_motscles = urlencode(strtr(strtolower(trim($person)),'àáâãäçèéêëìíîïñòóôõöùúûüýÿ_."\'-','aaaaaceeeeiiiinooooouuuuyy     '));
					// Url de la recherche des personnes avec les paramètres
					$recherche_url = 'http://api.allocine.fr/xml/search?q='.$recherche_motscles.'&partner='.$options['partner'].'&json=1&count=10';
					// Envoi de la requête et récupération du json
					$recherche_json = @file_get_contents($recherche_url);
					
					// Si erreur dans le JSON
					if (empty($recherche_json)) {
						parent::addErr('ERR_DATA_DOWNLOAD');
						return 0;
					}
					
					// Si le JSON est valide
					else {
						
						// Conversion du JSON en array
						$recherche_array = @json_decode($recherche_json, true);
						
						// Vérification de la présence d'erreur(s)
						if ( !$recherche_array ) {
							
							// En cas d'erreur avec le JSON
							parent::addErr('ERR_DATA_JSON');
							return 0;
							
						} else {
							// Si le JSON est valide
							// Vérification du champ "error"
							if ( !empty($recherche_array["error"]) ) {
								
								// Si le champ erreur n'est pas vide
								parent::addErr('ERR_ALLOCINE', ' "'.$recherche_array["error"]['$'].'"');
								return 0;
								
							} elseif ( empty($recherche_array["feed"]["person"]) OR count($recherche_array["feed"]["person"]) < 1) {
								
								// Si il n'y a aucun résultat
								parent::addErr('ERR_NO_RESULT');
								return 0;
								
							// Si il y a des résultats
							} else {
								
								// Vérification du nombre de résultats
								// Par rapport à $options['numresult']
								if ($options['numresult'] == 'auto') {
									
									// Mode automatique: prise du résultat les plus ressemblant
									$recherche_ = urldecode($recherche_motscles);
									$correspondance = 0;
									$bestcorres = 0;
									
									foreach ($recherche_array["feed"]["person"] as $i => $result) {
										
										$result_title = (!empty($result['name']['name'])) ? $result['name']['name'] : $result['realName']['name'];
										similar_text($recherche_, strtolower(trim($result_title)), $sim);
										if (round($sim) == 100) {
											$bestcorres = $i;
											$correspondance = round($sim, 3);
											break;
										}
										if (round($sim > $correspondance)) {
											$bestcorres = $i;
											$correspondance = round($sim, 3);
										}
									}
									
									// Correspondance la plus proche: $bestcorres
									// Récupération de l'ID sélectionné
									$ID = (int) $recherche_array["feed"]["person"][$bestcorres]['code'];
								
									
								} else {
									
									// Si 'numresult' n'est pas précisé comme automatique
									
									if (count($recherche_array["feed"]["person"]) < $options['numresult']) {
										// Si la quantité de résultats retourné est inférieur à 'numresult'
										$options['numresult'] = count($recherche_array["feed"]["person"]);
									}
								
									if (!empty($recherche_array["feed"]["person"][$options['numresult']-1])) {
									
										// Extraction de l'entrée sélectionnée
										$resultat_selectionne = $recherche_array["feed"]["person"][$options['numresult']-1];
									} else {
									
										// Erreur qui ne devrait pas se produire normalement
										// Si c'est le cas on prend le 1er résultat
										$resultat_selectionne = $recherche_array["feed"]["person"][0];
									}
									// Récupération de l'ID de l'entrée sélectionnée
									$ID = (int) $resultat_selectionne['code'];
								}
								
							}
						}
					}
				
				}
				// Si $person est de type int
				elseif (is_numeric($person)) {
					$ID = (int) $person;
				}	
			
			/*
			****
			***** RECUPERATION DES INFOS
			***** Grâce à $ID
			****
			*/
		
			// Url avec les paramètres
			$req_url = 'http://api.allocine.fr/xml/person?code='.urlencode($ID).'&partner='.$options['partner'].'&json=1&profile=large';
			// Envoi de la requête et récupération du json
			$req_json = @file_get_contents($req_url);
			
			if (!$req_json) {
				// En cas d'erreur
				parent::addErr('ERR_DATA_DOWNLOAD');
				return 0;
				
			} else {
				// Remplacement des "$" par des "value"
				// + de lisibilité
				$req_json = str_replace('"$"', '"value"', $req_json);
				
				// Conversion du JSON en array
				$req_array = @json_decode($req_json, true);
				
				
				// Vérification de la présence d'erreur(s)
				if ( !$req_array ) {
					
					// En cas d'erreur avec le JSON
					parent::addErr('ERR_DATA_JSON');
					return 0;
					
				} else {
					// Si le JSON est valide
					// Vérification du champ "error"
					if ( !empty($req_array["error"]) ) {
						
						// Si le champ erreur n'est pas vide
						parent::addErr('ERR_ALLOCINE', ' "'.$req_array["error"]['value'].'"');
						return 0;	
					} else {
						
						/*
						****
						***** Début du traitement des données
						****
						*/
						
						// Nettoyage du Tableau de sortie
						$this->clearData();
						
						// Récupération du tableau pour la traduction
						$t = parent::getTraduct($options['cles_accents'], $options['cles_tirets']);
						
						// Décodage de tous les champs (string) de l'utf8
						$alldata = parent::utf8_adecode($req_array["person"]);
						
						
						// Parcourir toutes les données de $alldata
						// $idata => nom de l'élément
						// $data => valeur de l'élément
						foreach ( $alldata as $idata => $data )
						{
							
							switch ($idata)
							{
								case "code":
									$this->addData($t["code"], parent::traiter('normal', $data));
								break;
								
								case "nationality":
									$this->addData( $t['nationality-1'], parent::traiter('tab-value', $data));
									$this->addData( $t['nationality-2'], parent::traiter('tab-value-implode', $data));
								break;
								
								case "name":
									$this->addData($t["person/name"], parent::traiter('name', $data));
								break;
								
								case "realName":
									$this->addData($t['person/realName'], parent::traiter('attr-', $data, ($attr='name')));
								break;
								
								case "gender":
									$this->addData($t["person/gender"], parent::traiter('gender', $data));
								break;
								
								case "birthDate":
									$this->addData( $t['person/birthDate'], parent::traiter('normal', $data));
								break;
								
								case "birthPlace":
									$this->addData( $t['person/birthPlace'], parent::traiter('normal', $data));
								break;
								
								case "biography":
									$this->addData( $t['person/biography'], parent::traiter('normal', $data));
								break;
								
								case "biographyShort":
									$this->addData( $t['person/biographyShort'], parent::traiter('normal', $data));
								break;
								
								case "activity":
									$this->addData( $t['person/activity'], parent::traiter('activity', $data));
									$this->addData( $t['person/activityShort'], parent::traiter('activityShort', $data));
								break;
								
								case "picture":
									$this->addData( $t['person/picture'], parent::traiter('attr-href', $data));
								break;
								
								case "statistics":
									$this->addData( $t['statistics'], parent::traiter('stats', $data));
								break;
								
								case "media":
									$am = array();
									foreach ($data as $i => $media) {
										$m = parent::traiter('media', $media, $typemedia);
										$am[$t['media-' . $typemedia]][] = $m;
									}
									foreach ($am as $i => $m)
									{
										$this->addData($i, $m);
									}
								break;
							}
						}
						/*
						****
						***** Fin du traitement des données
						****
						*/

						parent::timer_stop();
					}
				}
			}
		}
	}
	
	class AlloSerie extends Allo {
		
		// Série
		private $serie = array();
		
		// Ajouter des données
		private function addData($nom, $donnees)
		{
			$this->serie[(string)$nom] = $donnees;
		}
		
		// Effacer les données
		private function clearData()
		{
			$this->serie = array();
		}
		
		/** get, __get et __toArray retournent le tableau **/
		// Pour récupérer une info particulière
		public function get($param='') {
			if (empty($param))
				return $this->serie;
			elseif (!array_key_exists($param, $this->serie))
				return $this->serie;
			else
				return $this->serie[$param];
		}
		
		// Pour récupérer une propriété
		public function __get($param='') {
			$this->get($param);
		}
		
		// Si est appelé comme un array
		public function __toArray() {
			return $this->get();
		}
		
		/** Retourne tout le tableau, pratique lors du développement **/
		// Si est appelé comme un string
		public function __toString() {
			return print_r($this->get(), 1);
		}
		
		/** __construct et newSerie ont le même effet **/
		// Constructeur, a le même rôle que newSerie
		public function __construct($serie=0, $options=array()) {
			if (!empty($serie))
				return $this->newSerie($serie, $options);
		}
		
		// Fonction newSerie
		public function newSerie($serie, $options=array()) {
			// Temps d'execution
			parent::timer_start();
			
			// Nettoyage de $this->serie
			$this->serie = array();
			parent::clearErrs();
			parent::setOptions($options);
			$options = parent::getOptions();
				
			
				/*
					Analyse du paramètre $serie
					Si c'est un identifiant (int), la récupération des informations est plus rapide.
					Si c'est un titre (string), la récupération des informations est plus lente car deux requêtes sont effectuées (recherche+infos).
				
				*/
			
				// Si $serie est vide
				if (empty($serie)) {
					parent::addErr('ERR_EMPTY_KEYWORDS');
					return 0;
				}
				
				// Si $serie n'est ni de type string ni de type int
				if (!is_string($serie) AND !is_numeric($serie)) {
					parent::addErr('ERR_SERIE_FORMAT');
					return 0;
				}
			
				// Si $serie est de type string
				elseif (is_string($serie)) {
				
					// Va effectuer une recherche sur les mots, sans majuscules/accents/caractères spéciaux et encodés pour le passage en url
					$recherche_motscles = urlencode(strtr(strtolower(trim($serie)),'àáâãäçèéêëìíîïñòóôõöùúûüýÿ_."\'-','aaaaaceeeeiiiinooooouuuuyy     '));
					// Url de la recherche des series avec les paramètres
					$recherche_url = 'http://api.allocine.fr/xml/search?q='.$recherche_motscles.'&partner='.$options['partner'].'&json=1&count=10';
					// Envoi de la requête et récupération du json
					$recherche_json = @file_get_contents($recherche_url);
					
					// Si erreur dans le JSON
					if (empty($recherche_json)) {
						parent::addErr('ERR_DATA_DOWNLOAD');
						return 0;
					}
					
					// Si le JSON est valide
					else {
						
						// Conversion du JSON en array
						$recherche_array = @json_decode($recherche_json, true);
						
						// Vérification de la présence d'erreur(s)
						if ( !$recherche_array ) {
							
							// En cas d'erreur avec le JSON
							parent::addErr('ERR_DATA_JSON');
							return 0;
							
						} else {
							// Si le JSON est valide
							// Vérification du champ "error"
							if ( !empty($recherche_array["error"]) ) {
								
								// Si le champ erreur n'est pas vide
								parent::addErr('ERR_ALLOCINE', ' "'.$recherche_array["error"]['$'].'"');
								return 0;
								
							} elseif ( empty($recherche_array["feed"]["tvseries"]) OR count($recherche_array["feed"]["tvseries"]) < 1) {
								
								// Si il n'y a aucun résultat
								parent::addErr('ERR_NO_RESULT');
								return 0;
								
							// Si il y a des résultats
							} else {
								
								// Vérification du nombre de résultats
								// Par rapport à $options['numresult']
								if ($options['numresult'] == 'auto') {
									
									// Mode automatique: prise du résultat les plus ressemblant
									$recherche_ = urldecode($recherche_motscles);
									$correspondance = 0;
									$bestcorres = 0;
									
									foreach ($recherche_array["feed"]["tvseries"] as $i => $result) {
										
										$result_title = (!empty($result['title'])) ? $result['title'] : $result['originalTitle'];
										similar_text($recherche_, strtolower(trim($result_title)), $sim);
										if (round($sim) == 100) {
											$bestcorres = $i;
											$correspondance = round($sim, 3);
											break;
										}
										if (round($sim > $correspondance)) {
											$bestcorres = $i;
											$correspondance = round($sim, 3);
										}
									}
									
									// Correspondance la plus proche: $bestcorres
									// Récupération de l'ID sélectionné
									$ID = (int) $recherche_array["feed"]["tvseries"][$bestcorres]['code'];
								
									
								} else {
									
									// Si 'numresult' n'est pas précisé comme automatique
									
									if (count($recherche_array["feed"]["tvseries"]) < $options['numresult']) {
										// Si la quantité de résultats retourné est inférieur à 'numresult'
										$options['numresult'] = count($recherche_array["feed"]["tvseries"]);
									}
								
									if (!empty($recherche_array["feed"]["tvseries"][$options['numresult']-1])) {
									
										// Extraction de l'entrée sélectionnée
										$resultat_selectionne = $recherche_array["feed"]["tvseries"][$options['numresult']-1];
									} else {
									
										// Erreur qui ne devrait pas se produire normalement
										// Si c'est le cas on prend le 1er résultat
										$resultat_selectionne = $recherche_array["feed"]["tvseries"][0];
									}
									// Récupération de l'ID de l'entrée sélectionnée
									$ID = (int) $resultat_selectionne['code'];
								}
								
							}
						}
					}
				
				}
				// Si $serie est de type int
				elseif (is_numeric($serie)) {
					$ID = (int) $serie;
				}	
			
			/*
			****
			***** RECUPERATION DES INFOS
			***** Grâce à $ID
			****
			*/
		
			// Url avec les paramètres
			$req_url = 'http://api.allocine.fr/xml/series?code='.urlencode($ID).'&partner='.$options['partner'].'&json=1&profile=large';
			// Envoi de la requête et récupération du json
			$req_json = @file_get_contents($req_url);
		
			if (!$req_json) {
				// En cas d'erreur
				parent::addErr('ERR_DATA_DOWNLOAD');
				return 0;
				
			} else {
				// Remplacement des "$" par des "value"
				// + de lisibilité
				$req_json = str_replace('"$"', '"value"', $req_json);
				
				// Conversion du JSON en array
				$req_array = @json_decode($req_json, true);
				
				
				// Vérification de la présence d'erreur(s)
				if ( !$req_array ) {
					
					// En cas d'erreur avec le JSON
					parent::addErr('ERR_DATA_JSON');
					return 0;
					
				} else {
					// Si le JSON est valide
					// Vérification du champ "error"
					if ( !empty($req_array["error"]) ) {
						
						// Si le champ erreur n'est pas vide
						parent::addErr('ERR_ALLOCINE', ' "'.$req_array["error"]['value'].'"');
						return 0;	
					} else {
						
						/*
						****
						***** Début du traitement des données
						****
						*/
						
						// Nettoyage du Tableau de sortie
						$this->clearData();
						
						// Récupération du tableau pour la traduction
						$t = parent::getTraduct($options['cles_accents'], $options['cles_tirets']);
						
						// Décodage de tous les champs (string) de l'utf8
						$alldata = parent::utf8_adecode($req_array["tvseries"]);
						
						
						// Parcourir toutes les données de $alldata
						// $idata => nom de l'élément
						// $data => valeur de l'élément
						foreach ( $alldata as $idata => $data )
						{
							
							switch ($idata)
							{
								case "code":
									$this->addData($t["code"], parent::traiter('normal', $data));
								break;
								
								case "title":
									$this->addData( $t['title'], parent::traiter('normal', $data));
								break;
								
								case "originalTitle":
									$this->addData( $t['originalTitle'], parent::traiter('normal', $data));
								break;
								
								case "keywords":
									$this->addData( $t['keywords'], parent::traiter('normal', $data));
								break;
								
								case "productionYear":
									$this->addData( $t['productionYear'], parent::traiter('normal', $data));
								break;
								
								case "seasonCount":
									$this->addData( $t['seasonCount'], parent::traiter('normal', $data));
								break;
								
								case "episodeCount":
									$this->addData( $t['episodeCount'], parent::traiter('normal', $data));
								break;
								
								case "formatTime":
									$this->addData( $t['formatTime'], parent::traiter('normal', $data));
								break;
								
								case "yearStart":
									$this->addData( $t['yearStart'], parent::traiter('normal', $data));
								break;
								
								case "yearEnd":
									$this->addData( $t['yearEnd'], parent::traiter('normal', $data));
								break;
								
								case "nationality":
									$this->addData( $t['nationality-1'], parent::traiter('tab-value', $data));
									$this->addData( $t['nationality-2'], parent::traiter('tab-value-implode', $data));
								break;
								
								case "genre":
									$this->addData( $t['genre-1'], parent::traiter('tab-value', $data));
									$this->addData( $t['genre-2'], parent::traiter('tab-value-implode', $data));
								break;
								
								case "synopsis":
									$this->addData( $t['synopsis'], parent::traiter('normal', $data));
								break;
								
								case "synopsisShort":
									$this->addData( $t['synopsisShort'], parent::traiter('normal', $data));
								break;
								
								case "castingShort":
									$this->addData( $t['castingShort'], parent::traiter('castingShort', $data));
								break;
								
								case "castMember":
									$this->addData( $t['castMember'], parent::traiter('casting', $data));
								break;
								
								case "poster":
									$this->addData( $t['poster'], parent::traiter('attr-href', $data));
								break;
								
								case "statistics":
									$this->addData( $t['statistics'], parent::traiter('stats', $data));
								break;
								
								case "media":
									$am = array();
									foreach ($data as $i => $media) {
										$m = parent::traiter('media', $media, $typemedia);
										$am[$t['media-' . $typemedia]][] = $m;
									}
									foreach ($am as $i => $m)
									{
										$this->addData($i, $m);
									}
								break;
							}
						}
						/*
						****
						***** Fin du traitement des données
						****
						*/

						parent::timer_stop();
					}
				}
			}
		}
	}
	
	class AlloSearch extends Allo {
		
		// Recherche initiale
		private $recherche_initiale = "";
		
		// Infos 
		private $compteur = array();
		
		private $compteur_defaut = array(
			'pages' => array(),
			'pages_defaut' => array(
				'news' => 0,
				'theater' => 0,
				'location' => 0,
				'tvseries' => 0,
				'person' => 0,
				'video' => 0,
				'photo' => 0,
				'movie' => 0
			),
			
			'resultats' => array(),
			'resultats_defaut' => array(
				'news' => 0,
				'theater' => 0,
				'location' => 0,
				'tvseries' => 0,
				'person' => 0,
				'video' => 0,
				'photo' => 0,
				'movie' => 0
			),
		);
		
		// Types traités par l'API
		private $types = array(
			// 'news',
			// 'theater',
			// 'location',
			'tvseries',
			// 'person',
			// 'media',
			'movie'
		);
		
		protected function resetCount() {
			$this->count = $this->count_defaut;
		}
		
		public function getCount() {
			$compt = $this->compteur;
			unset($compt['resultats_defaut'], $compt['pages_defaut']);
			return $compt;
		}
		
		public function page($set=0)
		{
			if ( is_int($set) AND $set > 0 ) {
				parent::setOptions(array('page'=> (int) parent::getOptions('page') + 1));
			}
			if (parent::getOptions('page') === array())
				return 0;
			else
				return parent::getOptions('page');
		}
		
		// Résultats de la recherche
		private $search = array();
		
		private $search_defaut = array(
			'movie' => Array(),
			'theater' => Array(),
			'location' => Array(),
			'tvseries' => Array(),
			'person' => Array(),
			'video' => Array(),
			'photo' => Array(),
			'news' => Array()
		);
		
		// Ajouter des données
		private function addData($type, $nom, $i, $donnees)
		{
			// Si le résultat pour ce type n'existe pas
			if (empty($this->search[(string)$type][(string)$i]))
				// On créé le tableau
				$this->search[(string)$type][(string)$i] = array();
			
			// On enregistre
			$this->search[(string)$type][(string)$i][(string)$nom] = $donnees;
		}
		
		// Effacer les données
		private function clearData()
		{
			$this->search = $this->search_defaut;
		}
		
		/** get, __get et __toArray retournent le tableau **/
		// Pour récupérer une info particulière
		public function get($param='') {
			if (empty($param))
				return $this->search;
			elseif (!array_key_exists($param, $this->search))
				return $this->search;
			else
				return $this->search[$param];
		}
		
		// Pour récupérer une propriété
		public function __get($param='') {
			return $this->get($param);
		}
		
		// Si est appelé comme un array
		public function __toArray() {
			return $this->get();
		}
		
		/** Retourne tout le tableau, pratique lors du développement **/
		// Si est appelé comme un string
		public function __toString() {
			return print_r($this->search, 1);
		}
		
		/** __construct et newsearch ont le même effet **/
		// Constructeur, a le même rôle que newsearch
		public function __construct($search="", $options=array()) {
			if (!empty($search)) {
				$this->newSearch($search, $options);
			}
		}
		
		// Fonction newSearch
		public function newSearch($search, $options=array())
		{
			// Temps d'execution
			parent::timer_start();
			
			// Nettoyage de $this->search
			$this->clearData();
			$this->resetCount();
			parent::clearErrs();
			parent::setOptions($options);
			$options = parent::getOptions();
			
			// Si $search est vide
			if (empty($search)) {
				parent::addErr('ERR_EMPTY_KEYWORDS');
				return 0;
			} elseif (!is_string($search)) {
				parent::addErr('ERR_SEARCH_FORMAT');
				return 0;
			} elseif (strlen($search) < 3) {
				parent::addErr('ERR_KEYWORDS_INVALID_LEN');
				return 0;
			}
			
			// Enregistre les mots-clés
			$this->init_search = (string) $search;
			
			// Va effectuer une recherche sur les mots, sans majuscules/accents/caractères spéciaux et encodés pour le passage en url
			$recherche_motscles = urlencode(strtr(strtolower(trim($search)),'àáâãäçèéêëìíîïñòóôõöùúûüýÿ_."\'-','aaaaaceeeeiiiinooooouuuuyy     '));
			
			// Url de la recherche avec les paramètres
			$recherche_url = 'http://api.allocine.fr/xml/search?q='.$recherche_motscles.'&partner='.$options['partner'].'&json=1&count='.$options['count'].'&page='.$options['page'].'';
			
			// Envoi de la requête et récupération du json
			$recherche_json = @file_get_contents($recherche_url);
			
			// Si erreur dans le JSON
			if (empty($recherche_json)) {
				parent::addErr('ERR_DATA_DOWNLOAD');
				return 0;
			}
			
			// Si le JSON est valide
			else {
				
				// Conversion du JSON en array
				$recherche_array = @json_decode($recherche_json, true);
				
				// Vérification de la présence d'erreur(s)
				if ( !$recherche_array ) {
					
					// En cas d'erreur avec le JSON
					parent::addErr('ERR_DATA_JSON');
					return 0;
					
				} else {
					// Si le JSON est valide
					// Vérification du champ "error"
					if ( !empty($recherche_array["error"]) ) {
						
						// Si le champ erreur n'est pas vide
						parent::addErr('ERR_ALLOCINE', ' "'.$recherche_array["error"]['$'].'"');
						return 0;
						
					} elseif ( empty($recherche_array['feed']['totalResults']) ) {
						
						// Si il n'y a aucun résultat
						parent::addErr('ERR_NO_RESULT');
						return 0;
					}
					
					/*** Traitement des données ***/
					
					// Données
					$all_types = (empty($recherche_array['feed'])) ? array() : parent::utf8_adecode($recherche_array['feed']);
					$t = parent::getTraduct($options['cles_accents'], $options['cles_tirets']);
						
					/** On compte les résultats **/
					// Résultats
					$res = $all_types;
					$res2 = array();
					$pages = array();
					foreach ($res['results'] as $r) {
						$res2[$r['type']] = (int) $r['$'];
						$pages[$r['type']] = ceil((int)$r['$'] / parent::getOptions('count'));
					}
					$this->compteur['resultats'] = $res2;
					$this->compteur['pages'] = $pages;
					$this->compteur['resultats/page'] = parent::getOptions('count');
					
					
					// Boucle sur les types de résultats
					foreach ($all_types as $type => $resultats_type)
					{
						if (in_array($type, $this->types))
						{
							// Boucle sur les résultats
							foreach ($resultats_type as $i => $r)
							{
								if (is_array($r))
								{
									switch ($type)
									{
										// Films
										case "movie":
											foreach ($r as $cle => $data)
											{
												switch ($cle)
												{
													case "code":
														$this->addData($type, $t["code"], $i, parent::traiter('normal', $data));
													break;
													
													case "title":
														$this->addData($type,  $t['title'], $i, parent::traiter('normal', $data));
													break;
													
													case "movieType":
														$this->addData($type,  $t['movieType'], $i, parent::traiter('attr-value', $data));
													break;
													
													case "originalTitle":
														$this->addData($type,  $t['originalTitle'], $i, parent::traiter('normal', $data));
													break;
													
													case "keywords":
														$this->addData($type,  $t['keywords'], $i, parent::traiter('normal', $data));
													break;
													
													case "productionYear":
														$this->addData($type,  $t['productionYear'], $i, parent::traiter('normal', $data));
													break;
													
													case "nationality":
														$this->addData($type,  $t['nationality-1'], $i, parent::traiter('tab-value', $data));
														$this->addData($type,  $t['nationality-2'], $i, parent::traiter('tab-value-implode', $data));
													break;
													
													case "genre":
														$this->addData($type,  $t['genre-1'], $i, parent::traiter('tab-value', $data));
														$this->addData($type,  $t['genre-2'], $i, parent::traiter('tab-value-implode', $data));
													break;
													
													case "synopsis":
														$this->addData($type,  $t['synopsis'], $i, parent::traiter('normal', $data));
													break;
													
													case "synopsisShort":
														$this->addData($type,  $t['synopsisShort'], $i, parent::traiter('normal', $data));
													break;
													
													case "castingShort":
														$this->addData($type,  $t['castingShort'], $i, parent::traiter('castingShort', $data));
													break;
													
													case "castMember":
														$this->addData($type,  $t['castMember'], $i, parent::traiter('castingShort-2', $data));
													break;
													
													case "poster":
														$this->addData($type,  $t['poster'], $i, parent::traiter('attr-href', $data));
													break;
													
													case "trailer":
														$this->addData($type,  $t['trailer'], array(
															$t['trailer/href'] => parent::traiter('attr-href', $data),
															$t['trailer/code'] => parent::traiter('attr-code', $data)
														));
													break;
													
													case "runtime":
														$this->addData($type,  $t['runtime'], $i, parent::traiter('duree', $data));
													break;
													
												}
											}
										break;
										
										// Séries TV
										case "tvseries":
											foreach ($r as $cle => $data)
											{
												switch ($cle)
												{
													case "code":
														$this->addData($type, $t["code"], $i, parent::traiter('normal', $data));
													break;
													
													case "title":
														$this->addData($type,  $t['title'], $i, parent::traiter('normal', $data));
													break;
													
													case "originalTitle":
														$this->addData($type,  $t['originalTitle'], $i, parent::traiter('normal', $data));
													break;
													
													case "keywords":
														$this->addData($type,  $t['keywords'], $i, parent::traiter('normal', $data));
													break;
													
													case "productionYear":
														$this->addData($type, $t['productionYear'], $i, parent::traiter('normal', $data));
													break;
													
													case "seasonCount":
														$this->addData($type, $t['seasonCount'], $i, parent::traiter('normal', $data));
													break;
													
													case "episodeCount":
														$this->addData($type, $t['episodeCount'], $i, parent::traiter('normal', $data));
													break;
													
													case "formatTime":
														$this->addData($type, $t['formatTime'], $i, parent::traiter('normal', $data));
													break;
													
													case "yearStart":
														$this->addData($type, $t['yearStart'], $i, parent::traiter('normal', $data));
													break;
													
													case "yearEnd":
														$this->addData($type, $t['yearEnd'], $i, parent::traiter('normal', $data));
													break;
													
													case "nationality":
														$this->addData($type, $t['nationality-1'], $i, parent::traiter('tab-value', $data));
														$this->addData($type, $t['nationality-2'], $i, parent::traiter('tab-value-implode', $data));
													break;
													
													case "genre":
														$this->addData($type,  $t['genre-1'], $i, parent::traiter('tab-value', $data));
														$this->addData($type,  $t['genre-2'], $i, parent::traiter('tab-value-implode', $data));
													break;
													
													case "synopsis":
														$this->addData($type,  $t['synopsis'], $i, parent::traiter('normal', $data));
													break;
													
													case "synopsisShort":
														$this->addData($type,  $t['synopsisShort'], $i, parent::traiter('normal', $data));
													break;
													
													case "castingShort":
														$this->addData($type,  $t['castingShort'], $i, parent::traiter('castingShort', $data));
													break;
													
													case "castMember":
														$this->addData($type,  $t['castMember'], $i, parent::traiter('castingShort-2', $data));
													break;
													
													case "poster":
														$this->addData($type,  $t['poster'], $i, parent::traiter('attr-href', $data));
													break;
												}
											}
										break;
										
										
									}
								}
							}
						}
					}
				}
			}
			
			parent::timer_stop();
		}
	}
	
	