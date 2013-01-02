<?php
$file_old=str_replace("\'","'",$_GET["old_name"]);
$file=str_replace("\'"," ",$_GET["name"]);
if(substr($_GET["dossier_old"],-1)!="/"){$dossier_old=$_GET["dossier_old"]."/";}else{$dossier_old=$_GET["dossier_old"];}
if(substr($_GET["dossier"],-1)!="/"){$dossier=$_GET["dossier"]."/";}else{$dossier=$_GET["dossier"];}

$dossier_temp=substr($dossier, 0,strlen($dossier)-1);
if (!file_exists($dossier_temp)) {
	system("mkdir \"".$dossier_temp."\" -p");
}


system("mv \"".$dossier_old.$file_old."\" \"".$dossier.$file."\"");
echo '<script language="javascript" type="text/javascript"> window.close(); </script>';
?>