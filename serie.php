<?php 
require_once('classes/factory.class.php');
$s=factory::load("serie");
$liste_series=$s->list_serie($_GET);
$type="film";
$id=$liste_series[0]['id_serie'];
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Série</title>
	<link href="style2.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="fx.slide.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/mootools-1.2-core-yc.js"></script>
	<SCRIPT language="JavaScript">
	<?php  
	echo "var x = new Array(";
	for($i=0;$i<count($liste_series)-1;$i++){
		$name=str_replace(" ","_",$liste_series[$i]['titre_original']);
			$name=str_replace("-","",$name);
			$name=str_replace(":","",$name);
			$name=str_replace("/","_",$name);
			$name=str_replace("__","_",$name);
			echo "'image_series/".$name."-".$liste_series[$i]['num_fanart'].".jpg',";}
			$name=str_replace(" ","_",$liste_series[$i]['titre_original']);
			$name=str_replace("-","",$name);
			$name=str_replace(":","",$name);
			$name=str_replace("/","_",$name);
			$name=str_replace("__","_",$name);
	echo "'image_series/".$name."-".$liste_series[$i]['num_fanart'].".jpg'";
	
	echo ");";
?>
var temp=0;

</SCRIPT>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/serie.js"></script>
</head>

<body id="body" onload="chargement();" >

	<?php require_once("tpl_entete.php");?>

	<div id="conteneur">

		<div id="principale3" style="margin-top:150px;">
			<div id="sous_principale3"></div>

			<table id="table_principale" style="width:95%; height:auto;">
				<tr>
					<td style="width:85%">
						<div id="content_left">
							<div id="test" style="overflow-y:auto;max-height:700px;min-height:600px;">
								<?php  require_once("tpl_serie.php"); ?>

							</div>
						</div>
					</td>
					<td style="vertical-align:bottom;">
						<select name="select_principale" id="select_principale" size="<?php if(count($liste_series)<38){echo count($liste_series);} else{echo "38";}?>" onchange="change(this);">
							<?php 
							$i=0;
							foreach($liste_series as  $val){ 
								echo "<option value=\"".$val['id_serie']."\"";
								if($i==0){echo 'selected="selected"';}
								echo ">".str_replace(":","",utf8_encode($val['titre']))."</option>";
								$i++;
							}?>


						</select>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>


