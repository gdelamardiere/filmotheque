<?php
class factory
{
  public static function load($classe)
  {
    if (file_exists($chemin = __DIR__."/".$classe . '.class.php'))
    {
      require_once $chemin;
      return new $classe;
    }

    else
    {
      throw new RuntimeException('La classe <strong>' . $classe . '</strong> n\'a pu être trouvée !');
    }
  }
}
?>