<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');
	$id_user=(isset($_POST['id_user'])) ? htmlspecialchars($_POST['id_user']) : null;
	$id_cible=(isset($_POST['id'])) ? htmlspecialchars($_POST['id']) : null;
	$hash=(isset($_POST['hash'])) ? htmlspecialchars($_POST['hash']) : null;
	$id_firm=(isset($_POST['id_firm'])) ? htmlspecialchars($_POST['id_firm']) : null;

	$results=array();
	if(isset($id_user) && is_numeric($id_user)){
		$trader=new Trader();
		$trader->fill($id_user);
		$isGood=password_verify($trader->__get('name'), $hash);
		if($isGood && is_numeric($id_firm)){
			$isManager=$trader->isManager($id_firm);
			if($isManager){
				$firm=new Firm($id_firm);
				$firm->__set('id_manager',$id_cible);
				if($firm->save()){
					$cible=new Trader($id_cible);
					$name=htmlspecialchars($firm->get_name());
					$str='You have been promoted Manager of '.$name;
					$cible->add_notification($'success',$str);
					$results['result']='<div class="alert alert-success">Manager updated.</div>';
				}
				else{
					$results['result']='<div class="alert alert-error">Error 1.</div>';
				}
			}
			else{
				$results['result']='<div class="alert alert-error">Error 2.</div>';
			}
		}
		else{
			$results['result']='<div class="alert alert-error">Error 3.</div>';
		}
	}
	else{
		$results['result']='<div class="alert alert-error">Error 4.</div>';
	}
	echo json_encode($results);
}
?>