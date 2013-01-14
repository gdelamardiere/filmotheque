<?php
require_once('factory.class.php');


if(isset($_REQUEST['fonction']) && isset($_REQUEST['class'])){
	$temp=factory::load($_REQUEST['class']);
	$var=array();
	if(isset($_REQUEST['nb_var'])&&$_REQUEST['nb_var']!=0){
		for($i=0;$i<$_REQUEST['nb_var'];$i++){
			$var[]=$_REQUEST['var'.$i];
		}
	}
	$fct=array($temp,$_REQUEST['fonction']);
	call_user_func_array($fct,$var);
}



?>

