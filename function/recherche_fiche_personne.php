<?php

function recherche_id_imdb($id){

//require_once("include.php");
include('base.php'); 
mysql_select_db($database_base, $base);
require_once("imdb/imdbsearch.class.php");
require_once("imdb/imdb_person.class.php");
$search = new imdbpsearch();
$headname = "Person";

if(is_numeric($id)){
$query_Recordset1 = "SELECT nom,id  FROM acteur where id=".$id."  ";
	$Recordset1 = mysql_query($query_Recordset1, $base) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
	
	$search->setsearchname($row_Recordset1["nom"]);
	$results = $search->results ();
	/*foreach($results as $r){
	$d=$r->getSearchDetails();
	echo $d;
	
	echo $r->imdbid();
	
}*/

//$details = $results[0]->getSearchDetails();
     // if (!empty($details)) {
       // $hint = " (".$details["role"]." in <a href='imdb.php?mid=".$details["mid"]."'>".$details["moviename"]."</a> (".$details["year"]."))";
       $query_Recordset2 = "UPDATE acteur SET id_imdb=".$results[0]->imdbid()." WHERE id=".$row_Recordset1["id"]."";
	$Recordset2 = mysql_query($query_Recordset2, $base)or die(mysql_error());
       
    //  }
    //  else{echo $row_Recordset1["nom"];}
    //  echo " <a href='person.php?mid=".$results[0]->imdbid()."'>".$results[0]->name()."</a>$hint<br/>";
     //  echo "<a href='http://".$search->imdbsite."/title/nm".$results[0]->imdbid()."'>imdb page</a>\n";



}
else{
	$search->setsearchname($id);

$results = $search->results ();
}		
		
		
		
		
		
		
		
		
		
	return $results[0]->imdbid();



}




?>