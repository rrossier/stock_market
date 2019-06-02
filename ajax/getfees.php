<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');

	$id_user=(isset($_POST['id_user'])) ? $_POST['id_user'] : null;
	$amount=(isset($_POST['amount'])) ? $_POST['amount'] : null;
	$hash=(isset($_POST['hash'])) ? $_POST['hash'] : null;

	$results=array();
	if(is_numeric($id_user)){
		$trader=new Trader();
		$trader->fill($id_user);
		//$bcrypt = new Bcrypt(15);
		//$isGood = $bcrypt->verify($trader->__get('name'), $hash);
		$isGood=password_verify($trader->__get('name'), $hash);
		//$isGood=1;
		if($isGood==1){
			if(is_numeric($amount)){
				$broker= $trader->get_broker();
				$frais=$broker->get_frais($amount);
				$results["bool"]=1;
				$results["fees"]=$frais;
			}
			else{
				$results["bool"]=0;
				$results["error"]="non numeric amount";
			}
		}
		else{
			$results["bool"]=0;
			$results["error"]="non recognized user";
		}
	}
	else{
		$results['bool']=0;
		$results["error"]="global error";
	}
	echo json_encode($results);

}