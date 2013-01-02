<?php
require_once('conf.php'); 

class database extends PDO {

	/**
	 * @var Singleton
	 * @access private
	 * @static
	 */
	private static $_instance = null;

	 /**
		* Constructeur de la classe
		*
		* @param void
		* @return void
		*/
	 public function __construct() { 
		try{
			$db_config = array();
			$db_config['SGBD']  = 'mysql';
			$db_config['HOST']  = HOSTNAME_BASE;
			$db_config['DB_NAME'] = DATABASE_BASE;
			$db_config['USER']  = USERNAME_BASE;
			$db_config['PASSWORD']  = PASSWORD_BASE;
			$db_config['OPTIONS'] = array(
						// Activation des exceptions PDO :
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						// Change le fetch mode par défaut sur FETCH_ASSOC ( fetch() retournera un tableau associatif ) :
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);

			$pdo = parent::__construct($db_config['SGBD'] .':host='. $db_config['HOST'] .';dbname='. $db_config['DB_NAME'],
				$db_config['USER'],
				$db_config['PASSWORD'],
				$db_config['OPTIONS']);
			unset($db_config);
		}
		catch(Exception $e)
		{
			trigger_error($e->getMessage(), E_USER_ERROR);
		} 
	}

	 /**
		* Méthode qui crée l'unique instance de la classe
		* si elle n'existe pas encore puis la retourne.
		*
		* @param void
		* @return Singleton
		*/
	 public static function getInstance() {

		 if(is_null(self::$_instance)) {
			 self::$_instance = new database();  
		 }

		 return self::$_instance;
	 }
 }
 
 ?>