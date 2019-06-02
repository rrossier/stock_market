<?php

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');
	if(isset($_POST['name']) && isset($_POST['slogan']) && isset($_POST['nb_shares'])){
		$id_user=(isset($_POST['id_user'])) ? htmlspecialchars($_POST['id_user']) : null;
		$hash=(isset($_POST['hash'])) ? htmlspecialchars($_POST['hash']) : null;
		$results=array();
		$trader=new Trader();
		$trader->fill($id_user);
		$isGood=password_verify($trader->__get('name'), $hash);
		if($isGood){
			if(is_numeric($_POST['nb_shares'])){
				$nb_shares=htmlspecialchars($_POST['nb_shares']);
				$name=htmlspecialchars($_POST['name']);
				$slogan=htmlspecialchars($_POST['slogan']);
				$id_firm=$trader->create_firm($name,$nb_shares);
				if($id_firm===FALSE){
				  $results['bool']=0;
				  $results['result']='<div class="alert alert-error">Error while registering new firm. Make sure you have enough cash.</div>';
				}
				else{
				  $results['bool']=1;
				  $results['result']='<div class="alert alert-success">New firm correctly registered</div>';
				}
			}
			else{
				$results['bool']=0;
				$results['result']='<div class="alert alert-error">Error. Please enter the number of shares.</div>';
			}
		}
		else{
			$results['bool']=0;
			$results['result']='<div class="alert alert-error">Error. Unrecognized trader.</div>';
		}
		echo json_encode($results);
	}
}

?>