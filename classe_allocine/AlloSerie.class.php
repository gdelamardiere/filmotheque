<?php
	
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