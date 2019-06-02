<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');
	$id_user=(isset($_POST['id_user'])) ? htmlspecialchars($_POST['id_user']) : null;
	$id_notif=(isset($_POST['id'])) ? htmlspecialchars($_POST['id']) : null;
	$hash=(isset($_POST['hash'])) ? htmlspecialchars($_POST['hash']) : null;

	$results=array();
	if(isset($id_user) && is_numeric($id_user)){
		$trader=new Trader();
		$trader->fill($id_user);
		$isGood=password_verify($trader->__get('name'), $hash);
		if($isGood && is_numeric($id_notif)){
			if($trader->remove_notification($id_notif)){
				$results=true;
			}
			else{
				$results=false;
			}
		}
	}
	else{
		$results=false;
	}
	echo json_encode($results);
}

?>