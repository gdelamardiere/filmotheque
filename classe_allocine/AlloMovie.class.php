<?php
	
	class AlloMovie extends Allo {
		
		// Film
		private $film = array();
		
		// Ajouter des donn�es au tableau du film
		private function addData($nom, $donnees)
		{
			$this->film[(string)$nom] = $donnees;
		}
		
		// Ajouter des donn�es au tableau du film
		private function clearData()
		{
			$this->film = array();
		}
		
		/** get, __get et __toArray retournent le tableau du film **/
		// Pour r�cup�rer une info particuli�re
		public function get($param='') {
			if (empty($param))
				return $this->film;
			elseif (!array_key_exists($param, $this->film))
				return $this->film;
			else
				return $this->film[$param];
		}
		
		// Pour r�cup�rer une propri�t�
		public function __get($param='') {
			if (empty($param))
				return $this->film;
			elseif (!array_key_exists($param, $this->film))
				return $this->film;
			else
				return $this->film[$param];
		}
		
		// Si est appel� comme un array
		public function __toArray() {
			return $this->get();
		}
		
		/** Retourne tout le tableau, pratique lors du d�veloppement **/
		// Si est appel� comme un string
		public function __toString() {
			return parent::newprint_r($this->film, true);
		}
		
		/** __construct et newMovie ont le m�me effet **/
		// Constructeur, a le m�me r�le que newMovie
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
					Analyse du param�tre $FILM
					Si c'est un identifiant (int), la r�cup�ration des informations est plus rapide.
					Si c'est un titre (string), la r�cup�ration des informations est plus lente car deux requ�tes sont effectu�es (recherche+infos).
				
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
				
					// Va effectuer une recherche sur les mots, sans majuscules/accents/caract�res sp�ciaux et encod�s pour le passage en url
					$recherche_motscles = urlencode(strtr(strtolower(trim($FILM)),'��������������������������_."\'-','aaaaaceeeeiiiinooooouuuuyy     '));
					// Url de la recherche des films avec les param�tres
					$recherche_url = 'http://api.allocine.fr/xml/search?q='.$recherche_motscles.'&partner='.$options['partner'].'&json=1&count=10';
					// Envoi de la requ�te et r�cup�ration du json
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
						
						// V�rification de la pr�sence d'erreur(s)
						if ( !$recherche_array ) {
							
							// En cas d'erreur avec le JSON
							parent::addErr('ERR_DATA_JSON');
							return 0;
							
						} else {
							// Si le JSON est valide
							// V�rification du champ "error"
							if ( !empty($recherche_array["error"]) ) {
								
								// Si le champ erreur n'est pas vide
								parent::addErr('ERR_ALLOCINE', ' "'.$recherche_array["error"]['$'].'"');
								return 0;
								
							} elseif ( empty($recherche_array["feed"]["movie"]) OR count($recherche_array["feed"]["movie"]) < 1) {
								
								// Si il n'y a aucun r�sultat dans les films
								parent::addErr('ERR_NO_RESULT');
								return 0;
								
							// Si il y a des r�sultats dans les films
							} else {
								
								// V�rification du nombre de r�sultats
								// Par rapport � $options['numresult']
								if ($options['numresult'] == 'auto') {
									
									// Mode automatique: prise du r�sultat les plus ressemblant
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
									// R�cup�ration de l'ID du film s�lectionn�
									$ID = (int) $recherche_array["feed"]["movie"][$bestcorres]['code'];
								
									
								} else {
									
									// Si 'numresult' n'est pas pr�cis� comme automatique
									
									if (count($recherche_array["feed"]["movie"]) < $options['numresult']) {
										// Si la quantit� de r�sultats retourn� est inf�rieur � 'numresult'
										$options['numresult'] = count($recherche_array["feed"]["movie"]);
									}
								
									if (!empty($recherche_array["feed"]["movie"][$options['numresult']-1])) {
									
										// Extraction du film s�lectionn�
										$resultat_selectionne = $recherche_array["feed"]["movie"][$options['numresult']-1];
									} else {
									
										// Erreur qui ne devrait pas se produire normalement
										// Si c'est le cas on prend le 1er r�sultat
										$resultat_selectionne = $recherche_array["feed"]["movie"][0];
									}
									// R�cup�ration de l'ID du film s�lectionn�
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
			***** Gr�ce � $ID
			****
			*/
		
			// Url avec les param�tres
			$req_url = 'http://api.allocine.fr/xml/movie?code='.urlencode($ID).'&partner='.$options['partner'].'&json=1&profile='.$options['infos'];
			// Envoi de la requ�te et r�cup�ration du json
			$req_json = @file_get_contents($req_url);
		
			if (!$req_json) {
				// En cas d'erreur
				parent::addErr('ERR_DATA_DOWNLOAD');
				return 0;
				
			} else {
				// Remplacement des "$" par des "value"
				// + de lisibilit�
				$req_json = str_replace('"$"', '"value"', $req_json);
				
				// Conversion du JSON en array
				$req_array = @json_decode($req_json, true);
				
				
				// V�rification de la pr�sence d'erreur(s)
				if ( !$req_array ) {
					
					// En cas d'erreur avec le JSON
					parent::addErr('ERR_DATA_JSON');
					return 0;
					
				} else {
					// Si le JSON est valide
					// V�rification du champ "error"
					if ( !empty($req_array["error"]) ) {
						
						// Si le champ erreur n'est pas vide
						parent::addErr('ERR_ALLOCINE', ' "'.$req_array["error"]['value'].'"');
						return 0;	
					} else {
						
						/*
						****
						***** D�but du traitement des donn�es
						****
						*/
						
						// Nettoyage du Tableau de sortie
						$this->clearData();
						
						// R�cup�ration du tableau pour la traduction
						$t = parent::getTraduct($options['cles_accents'], $options['cles_tirets']);
						
						// D�codage de tous les champs (string) de l'utf8
						$alldata = parent::utf8_adecode($req_array["movie"]);
						
						
						// Parcourir toutes les donn�es de $alldata
						// $idata => nom de l'�l�ment
						// $data => valeur de l'�l�ment
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
						***** Fin du traitement des donn�es
						****
						*/
						
						parent::timer_stop();
					}
				}
			}
		}
	}