<?php 
require_once("api-allocine-helper-1.4.php");



$test= new AlloHelper;
//http://www.allocine.fr/series/ficheseriee_gen_cserie=7268.html
//$infos = $test->search('alphas');
$infos = $test->getDataFromURL('http://api.allocine.fr/rest/v3/tvseries?partner=YW5kcm9pZC12M3M&code=223&profile=large&mediafmt=mp4-lc&format=JSON',true);
	echo "<pre>";
	var_dump($infos["tvseries"]["seasonCount"]);
	echo "</pre>";
	
	echo "<pre>";
	var_dump($infos["tvseries"]["episodeCount"]);
	echo "</pre>";
	
	echo "<pre>";
	var_dump($infos["tvseries"]["season"]);
	echo "</pre>";
	
	
$infos = $test->getDataFromURL('http://api.allocine.fr/rest/v3/season?partner=YW5kcm9pZC12M3M&code=12277&profile=large&mediafmt=mp4-lc&format=JSON',true);
	echo "<pre>";
	var_dump($infos["season"]["episode"]);
	echo "</pre>";

	

?>


