<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');
	$id_user=(isset($_POST['id_user'])) ? htmlspecialchars($_POST['id_user']) : null;
	$id_firm=(isset($_POST['id_firm'])) ? htmlspecialchars($_POST['id_firm']) : null;
	$hash=(isset($_POST['hash'])) ? htmlspecialchars($_POST['hash']) : null;
	$qty=(isset($_POST['qty'])) ? htmlspecialchars($_POST['qty']) : null;

	$results=array();
	if(isset($id_user) && is_numeric($id_user)){
		$trader=new Trader();
		$trader->fill($id_user);
		$isGood=password_verify($trader->__get('name'), $hash);
		if($isGood && is_numeric($id_firm) && is_numeric($qty)){
			$request = array('id_trader'=>$id_user,'id_firm'=>$id_firm,'quantity'=>$qty,'datetime'=>time());
			$result = Gamemaster::SaveRequest($request);
			if($result){
				$results['result']='<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Candidacy posted.</div>';
			}
			else{
				$results['result']='<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Error while posting your candidacy.</div>';
			}
		}
		else{
			$results['result']='<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Error.</div>';
		}
	}
	else{
		$results['result']='<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Error.</div>';
	}
	echo json_encode($results);
}

?>