<?php
require_once('database.class.php');

class parametres {

	public static getExtensionsSubtitles(){
		return array("idx","sub","srt");
	}

	public function lister_parametre_destination(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			$query_Recordset1 = "SELECT * FROM parametres where id_parametres='1'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$dossiers=explode(";",$row_Recordset1['repertoires']);
			return $dossiers;
		 }
		 
		 public function lister_parametre_download(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);			
			$query_Recordset1 = "SELECT * FROM parametres where id_parametres='2'";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$dossiers=explode(";",$row_Recordset1['repertoires']);
			return $dossiers;
		 }

}


?>
