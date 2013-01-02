<?php
	
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