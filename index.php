<?php 
require_once('classes/factory.class.php');
$f=factory::load("film");
$g=factory::load("genre");
$liste_films=$f->list_film($_GET);
$liste_genres=$g->list_genre_film();
$type="film";
$id=$liste_films[0]['id_film'];
?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Accueil</title>
  <link href="style2.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="fx.slide.css" type="text/css" media="screen" />
  <script type="text/javascript" src="js/mootools-1.2-core-yc.js"></script>
  <SCRIPT language="JavaScript">
  var x = new Array();
  <?php  for($i=0;$i<count($liste_films);$i++){echo "x[".$liste_films[$i]['id_film']."] = '".$liste_films[$i]['fanart']."';\n";} ?>
  </SCRIPT>
  <script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/film.js"></script>


</head>

<body id="body" onload="chargement();" >   
 <?php require_once("tpl_entete.php");?>
    

<div id="conteneur" >

	<div id="principale3" style="margin-top:150px;">
    <div id="sous_principale3"></div>

    <table id="table_principale" style="width:95%; height:auto;">
      <tr>
        <td style="width:85%">
          <div id="content_left"/>
             <?php require_once("tpl_film.php");?>
          </div>

      </td><td style="vertical-align:bottom;">
      <select name="select_genre" id="select_genre" size="1" onchange="change_genre(this);">
        <option value="0" selected='selected'>Tous les genres</option>
        <?php 

        foreach($liste_genres as  $val){ 
          echo "<option value=\"".$val['id_genre']."\">".str_replace(":","",utf8_encode($val['nom']))."(".$val['c'].")</option>";

        }?>


      </select><br/>

      <select name="select_principale" id="select_principale" size="<?php if(count($liste_films)<20){echo count($liste_films);} else{echo "20";}?>" onchange="change(this);">
        <?php 
        $i=0;
        foreach($liste_films as  $val){ 
          echo "<option value=\"".$val['id_film']."\"";
          if($i==0){echo 'selected="selected"';}
          echo ">".str_replace(":","",utf8_encode($val['titre']))."</option>";
          $i++;
        }?>


      </select>
    </td>

    


  </tr></table>
</div>


</body>
</html>
