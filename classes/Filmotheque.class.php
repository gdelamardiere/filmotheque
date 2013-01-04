<?php



if(isset($_GET['fonction'])){
	$temp=new Filmotheque();
	$var=array();
	if(isset($_GET['nb_var'])&&$_GET['nb_var']!=0){
		for($i=0;$i<$_GET['nb_var'];$i++){
			$var[]=$_GET['var'.$i];
		}
	}
	$fct=array($temp,$_GET['fonction']);
	call_user_func_array($fct,$var);
}

if(isset($_POST['fonction'])){
	$temp=new Filmotheque();
	$var=array();
	if(isset($_POST['nb_var'])&&$_POST['nb_var']!=0){
		for($i=0;$i<$_POST['nb_var'];$i++){
			$var[]=$_POST['var'.$i];
		}
	}
	$fct=array($temp,$_POST['fonction']);
	call_user_func_array($fct,$var);
}


?>

