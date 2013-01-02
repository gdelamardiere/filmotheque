<?php
	
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