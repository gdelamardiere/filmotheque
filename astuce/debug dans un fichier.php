<?php

//var_dump($aDatas);die();
	ob_start(); 
echo "<pre>";
print_r(debug_backtrace());
echo "</pre>";

$tab_debug=ob_get_contents(); 
ob_end_clean(); 
	file_put_contents("test_guerric.html", $tab_debug);
	?>