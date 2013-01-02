<?php
	define('API_ALLOCINE_VERSION', '5.3.0');
	
	// Classe Allo abstraite
	include_once "../classe_allocine/Allo.class.php";
	
	// Classe AlloMovie pour les films
	include_once "../classe_allocine/AlloMovie.class.php";
	
	// Classe AlloSerie pour les séries tv
	include_once "../classe_allocine/AlloSerie.class.php";
	
	// Classe AlloPerson pour les acteurs, réalisateurs, ...
	include_once "../classe_allocine/AlloPerson.class.php";
	
	// Classe AlloSearch pour effectuer des recherches
	include_once "../classe_allocine/AlloSearch.class.php";