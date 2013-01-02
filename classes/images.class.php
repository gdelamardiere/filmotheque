<?php

//require_once ('simple_html_dom.php');
class images{

	public function fanart_serie($id_serie){
		 	/*$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			$query_Recordset1 = "SELECT titre_original FROM serie where id=".$id_serie."  ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);*/
			$name=str_replace(" ","+",$row_Recordset1['titre_original']);
			$ch = curl_init(); 
			$timeout = 5;  
			curl_setopt ($ch, CURLOPT_URL, 'http://thetvdb.com/?string='.$name.'&searchseriesid=&tab=listseries&function=Search'); 
			//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			$file_contents = curl_exec($ch); 
			curl_close($ch); 
			
			if( strstr($file_contents, 'odd')){
				$tab=array();
				$lines = array(); 
				$lines = explode('<td class="odd"', $file_contents); 
				$tab=explode('<a href', $lines[3]);
				$fanart=preg_replace("#\>([0-9]+)<.+#",'$1',$tab[0]);
				$fanart = (int)$fanart;var_dump($fanart);die();
				$query = "UPDATE serie set fanart='".$fanart."' where id=".$id_serie."  ";
				$Recordset = mysql_query($query, $this->base) ;
				$this->download_fanart($id_serie,1);				
			}	
		}
		
		public function download_fanart($id_serie,$num){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
			$query_Recordset1 = "SELECT fanart,titre_original FROM serie where id=".$id_serie."  ";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			$name=str_replace(" ","_",$row_Recordset1['titre_original']);
			$name=str_replace("-","",$name);
			$name=str_replace(":","",$name);
			$name=str_replace("/","_",$name);
			$name=str_replace("__","_",$name);		
			if($this->ftp_curl_get($row_Recordset1['fanart'],$num, '../image_series/'.$name.'-'.$num.'.jpg')!==FALSE){
				$query = "UPDATE serie set num_fanart='".$num."' where id=".$id_serie."  ";
				$Recordset = mysql_query($query, $this->base) ;
			}	
		}
		
		
		private function ftp_curl_get($fanart,$num, $sortie, $timeout = 10)
		{
			$url='http://thetvdb.com/banners/fanart/original/'.$fanart.'-'.$num.'.jpg';
		    if ($fp = fopen($sortie, 'w')) {
		        $ch = curl_init($url);
		        curl_setopt($ch, CURLOPT_FILE, $fp);
		        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		        $ret = curl_exec($ch);
		        curl_close($ch);
		        fclose($fp);
		        return $ret;
		    }
		    return FALSE;
		}




		 
		 private function return_image($str){
			$tab=array();
			$lines = array(); 
			$lines = explode('carousel_container_style', $str); 
			//print_r($lines);
			//echo $lines[1];
			$tab=explode("href=\"", $lines[1]);
			$lines=array();
			$lines=explode("\"", $tab[1]);
			$temp=$lines[0];
			return $temp;			
		}
		 
		 
		 public static function fanart($titre,$annee){
		 	$titre=utf8_encode($titre);
		 	$name=str_replace("'","+",$titre);
		 	$name=str_replace("&#039;","+",$name);	 	
			$name=str_replace(" ","+",$name);
			$name=str_replace("-","",$name);
			$name=str_replace("/","+",$name);
			$name=str_replace("++","+",$name);
			$name=str_ireplace("(tv)","",$name);
			$ch = curl_init(); 
			$timeout = 5; // set to zero for no timeout 
			//curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
			curl_setopt ($ch, CURLOPT_URL, "http://www.themoviedb.org/search/movie?query=".$name."+".$annee);
			
			//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			
			//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			$file_contents = curl_exec($ch); 
			curl_close($ch); 
			preg_match("#href=\"http://www.themoviedb.org/movie/([0-9]+)-.+\"#",$file_contents,$temps);
			
			if( !isset($temps[1])){
				$ch = curl_init(); 
				curl_setopt ($ch, CURLOPT_URL, "http://www.themoviedb.org/search/movie?query=".$name);			
				//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				
				//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
				$file_contents = curl_exec($ch); 
				curl_close($ch); 
				preg_match("#href=\"http://www.themoviedb.org/movie/([0-9]+)-.+\"#",$file_contents,$temps);
			}
			
			if( !isset($temps[1])){
				$temp="0";
			}			
			else{
				$name=$name."+".$annee;
				//echo $name;
				$ch = curl_init(); 
				$timeout = 5; // set to zero for no timeout 
				curl_setopt ($ch, CURLOPT_URL, "http://www.themoviedb.org/movie/".$temps[1]."-test/backdrops"); 
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				$file_contents2 = curl_exec($ch); 
				curl_close($ch); 
				preg_match('#<a href="http://cf2.imgobject.com/t/p/original/([^"]+).jpg"#',$file_contents2,$temps);
				if( isset($temps[1])){
					$temp='http://cf2.imgobject.com/t/p/original/'.$temps[1].'.jpg';
				}
				else{
					$temp="0";
				}	
			}	
				echo $temp;
			return $temp;			
		}


/*public function fanart($titre,$annee){
			$name=str_replace(" ","+",$titre);
			$name=str_replace("-","",$name);
			$name=str_replace("/","+",$name);
			$name=str_replace("++","+",$name);
			$ch = curl_init(); 
			$timeout = 5; // set to zero for no timeout 
			//curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
			curl_setopt ($ch, CURLOPT_URL, "http://www.themoviedb.org/search/movie?query=".$name."+".$annee."&movie_page=&person_page=&company_page=&keyword_page=&active=0");
			
			//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			
			//curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
			$file_contents = curl_exec($ch); 
			curl_close($ch); var_dump($file_contents);die();
			
			if( strstr($file_contents, 'carousel_container_style')){
				$temp=$this->return_image($file_contents);
			}
			else{
				$name=$name."+".$annee;
				//echo $name;
				$ch = curl_init(); 
				$timeout = 5; // set to zero for no timeout 
				curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org/search?search='.$name); 
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				$file_contents2 = curl_exec($ch); 
				curl_close($ch); 
				if( strstr($file_contents2, 'carousel_container_style')){
					$temp=$this->return_image($file_contents2);	
				}
				else{					
					if( strstr($file_contents, '"result"')){						
						$tab=array();
						$lines = array(); 
						$lines = explode('"result"', $file_contents); 	
						$tab=explode("href=\"", $lines[1]);
						$lines=array();
						$lines=explode('">', $tab[1]);
						$temp=$lines[0];
						//echo $temp;
						$ch = curl_init(); 
						$timeout = 5; // set to zero for no timeout 
						curl_setopt ($ch, CURLOPT_URL, 'http://www.themoviedb.org'.$temp); 
						curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
						curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
						
						$file_contents2 = curl_exec($ch); 
						curl_close($ch); 
						//echo $file_contents2;
						if( strstr($file_contents2, 'carousel_container_style')){
							$temp=$this->return_image($file_contents2);						
						}
						else{$temp="0";}
					}
					else{ $temp="0";}					
				}
			}			
			return $temp;			
		}*/
		 
		 
		 
		private function update_fanart($id){
			$query = "SELECT id,titre_original,annee_production FROM film where id = '".$id."' ";
			$Recordset = mysql_query($query, $this->base) ;
			$row_Recordset = mysql_fetch_assoc($Recordset);
			$temp=$this->fanart($row_Recordset['titre_original'],$row_Recordset['annee_production']);
			$query = "UPDATE film SET fanart='".$temp."' where id='".$id."'  ";
			$Recordset = mysql_query($query, $this->base) ;
			$this->download_fanart_film($id,$temp);
		}
		 
		 
		 public function update_fanart_t(){
			$query = "SELECT id,titre_original,annee_production FROM film where fanart like '%http://cf1.imgobject.com%' order by titre ";
			$Recordset0 = mysql_query($query, $this->base) ;
			while($row_Recordset = mysql_fetch_assoc($Recordset0)){
			$temp=$this->fanart($row_Recordset['titre_original'],$row_Recordset['annee_production']);
			$id=$row_Recordset['id'];
			$query = "UPDATE film SET fanart='".$temp."' where id='".$id."'  ";
			$Recordset = mysql_query($query, $this->base) ;
			}
		}
		 
		 
		 public function download_fanart_film_all(){
			$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
		 	mysql_select_db($this->database_base, $this->base);
		 	$timeout = 0;
			$query_Recordset1 = "SELECT fanart,id FROM film order by id";
			$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
			while($row_Recordset1 = mysql_fetch_assoc($Recordset1)){
					
				
				
				$url=$row_Recordset1['fanart'];
			    if ($fp = fopen('../images_film/film'.$row_Recordset1['id'].'_1.jpg', 'w')) {
			        $ch = curl_init($url);
			        curl_setopt($ch, CURLOPT_FILE, $fp);
			        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			        $ret = curl_exec($ch);
			        curl_close($ch);
			        fclose($fp);
			    }
			}
		}
		
		 public function download_fanart_film($id,$fanart,$nb=1){
			$timeout = 0;				
			$url=$fanart;
		    if ($fp = fopen('../images_film/film'.$id.'_'.$nb.'.jpg', 'w')) {
		        $ch = curl_init($url);
		        curl_setopt($ch, CURLOPT_FILE, $fp);
		        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		        $ret = curl_exec($ch);
		        curl_close($ch);
		        fclose($fp);
		    }
			
		}

		
		
		
		 
		 
		 
		 
		 
		 
		 
		 
		 public function maj_fanart_film($id,$lien){
		 	if($lien!="" and $lien !=null){
				$this->base = mysql_connect($this->hostname_base, $this->username_base,$this->password_base) or trigger_error(mysql_error(),E_USER_ERROR);
			 	mysql_select_db($this->database_base, $this->base);
				$query_Recordset1 = "UPDATE film set fanart='".$lien."' where id='".$id."' limit 1";
				$Recordset1 = mysql_query($query_Recordset1, $this->base) ;
				$this->download_fanart_film($id,$lien);
		 	}
		 	else{
		 		$this->update_fanart($id);
		 	}
		 }
		 
		 
		 
		 





}


images::fanart('avatar',2010);


?>