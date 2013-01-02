<?php 

function film_acteur_allocine($id_allocine){

$ch = curl_init(); 
$timeout = 5; // set to zero for no timeout 
curl_setopt ($ch, CURLOPT_URL, 'http://www.allocine.fr/personne/filmographie_gen_cpersonne='.$id_allocine.'.html'); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
$file_contents = curl_exec($ch); 
curl_close($ch); 
$tab=array();
$lines = array(); 
$lines = explode('<div class="rubric">', $file_contents); 

// display file line by line 
for($i=3;$i<count($lines)-3;$i++) { 
$tab[]= '<div class="rubric">'.$lines[$i]; 

} 

$lines = array(); 
	$lines = explode('<div class="poster">', $file_contents);
	$lines2 = array(); 
	$lines2 = explode('<img', $lines[1]);
	$poster = '<img '.$lines2[1];







$value=array();
foreach($tab as $v){
	
	$lines = array(); 
	$lines = explode('<img', $v);
	$lines2 = array(); 
	$lines2 = explode('</a>', $lines[1]);
	
	
	$lines = array(); 
	$lines = explode('<h2>', $v);
	$lines3 = array(); 
	$lines3 = explode('</h2>', $lines[1]);
	$temp=str_replace("\n","",$lines3[0]);
	$id=preg_replace("#.+=([0-9]+)\.html.+#",'$1',$temp);
	if($id==$temp){
		$id=preg_replace("#.+-([0-9]+)/.>.+#",'$1',$temp);
	}
	$titre=preg_replace("#.+=[0-9]+.+>(.+)</a>#",'$1',$temp);
	if($titre==$temp){
		$titre=preg_replace("#.+-[0-9]+/.>(.+)</a>#",'$1',$temp);
	}
	$lines = array(); 
	$lines = explode('<p>', $v);
	$lines4 = array(); 
	$lines4 = explode('</p>', $lines[2]);
	

	
	
	$value[]=array('img'=> "<img".$lines2[0],'lien'=>$temp,'id'=>$id,'titre'=>$titre,'role'=>$lines4[0]); 
	
	
	
}

return array($poster,$value);
}
	

?> 