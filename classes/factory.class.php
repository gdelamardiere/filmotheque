<?php
class factory
{
  public static function load($classe)
  {
    if (file_exists($chemin = $classe . '.class.php'))
    {
      require $chemin;
      return new $classe;
    }
    else
    {
      throw new RuntimeException('La classe <strong>' . $classe . '</strong> n\'a pu être trouvée !');
    }
  }
}
?>