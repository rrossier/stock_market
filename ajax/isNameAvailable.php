<?php

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');
	if(isset($_POST['name'])){
	    $firm=new Firm();
	    $bool=$firm->isNameAvailable(htmlspecialchars($_POST['name']));
	    if($bool){
	    	echo '[true]';
	    }
	    else{
	    	echo '[false]';
	    }
    }
    else{
    	echo '[false]';
    }
}


?>