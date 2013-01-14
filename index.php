<?php 
require_once('classes/factory.class.php');
$f=factory::load("film");
$g=factory::load("genre");
$liste_films=$f->list_film($_GET);
$liste_genres=$g->list_genre_film();
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



</head>

<body id="body" onload="chargement();" >
  <div id="container" style="float:right;">
    <div id="top">
      <!-- login -->
      <ul class="login">
       <li class="left">&nbsp;</li>		        
       <li><a id="toggleLogin" href="#" onclick="ouvrir();">Menu</a></li>
     </ul> <!-- / login -->
   </div> <!-- / top -->
 </div><!-- / container -->
 <div id="login" >
  <div class="entete">
   <div id="sous_entete" style="height: 160px;"></div>
   <div class="loginClose"><a href="#" id="closeLogin" onclick="fermer();">Close Panel</a></div>			
   <table style="width:100%" >
    <tr>
      <td><input type="button" value="Importer un répertoire:" onclick="rep_film();">
      <input type="texte" value="" id="rep">
    </td>
   <td><h1>Menu Principal</h1></td>
   <td><input type="button" value="Rechercher un acteur:" onclick="acteur_film();"><input type="texte" value="" id="acteur"></td>
   <td><input type="button" value="Rechercher un film:" onclick="film();"><input type="texte" value="" id="film"></td>
 </tr>
 <tr name="entete" id="entete">
  <td colspan="2">
  <input type="button" value="Fanart(www.themoviedb.org):" onclick="maj_fanart()"/> <input type="text" name="new_fanart" id="new_fanart"/>
</td>
<td style="font-size:16px;text-align:center;"><h3><a href="http://localhost/filmotheque/index.php"><input type="button" value="Film"></a> <a href="http://localhost/filmotheque/serie.php"><input type="button" value="Série"></a>
  <br><a href="http://localhost/filmotheque/film_affichage_liste.php"><input type="button" value="Affichage liste"></a></h3></td>
  <td >
   <input type="button" value="id_allocine:" onclick="modifier_film_id()"/> <input type="text" name="new_id_allocine" id="new_id_allocine"/>
   <input type="hidden" name="id_film" id="id_film" value="<?php echo $value['id_film']; ?>"/>
 </td>
 <td >
  <a href="modifier_fiche_film.php?id=<?php echo $value['id_film']; ?>">Modifier manuellement</a></td>
</tr>
</table>




</div>


</div>




<div id="conteneur" >

	<div id="principale3" style="margin-top:150px;">
    <div id="sous_principale3"></div>

    <table id="table_principale" style="width:95%; height:auto;">
      <tr>
        <td style="width:85%">
          <div id="content_left"/>
          <?php 
          $_GET['id_film']=$liste_films[0]['id_film'];
          require_once("tpl_film.php");
          ?>

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
