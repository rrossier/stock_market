<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');
	$id_user=(isset($_POST['id_user'])) ? htmlspecialchars($_POST['id_user']) : null;
	$id_request=(isset($_POST['id'])) ? htmlspecialchars($_POST['id']) : null;
	$hash=(isset($_POST['hash'])) ? htmlspecialchars($_POST['hash']) : null;
	$use=(isset($_POST['use'])) ? htmlspecialchars($_POST['use']) : null;

	$results=array();
	if(isset($id_user) && is_numeric($id_user)){
		$trader=new Trader();
		$trader->fill($id_user);
		$isGood=password_verify($trader->__get('name'), $hash);
		if($isGood && is_numeric($id_request)){
			$request=Gamemaster::getRequest($id_request);
			if($request!==FALSE){
				$id_firm=$request['id_firm'];
				$id_cible=$request['id_trader'];
				$datetime=$request['datetime'];
				$quantity=$request['quantity'];
				$isManager=$trader->isManager($id_firm);
				if($isManager){
					if(in_array($use,array('accept','reject'))){
						switch($use){
							case 'accept':
								if($trader->join_firm($id_firm,$quantity)===TRUE){
									$firm=new Firm($id_firm);
									$name=htmlspecialchars($firm->get_name());
									$str='You have been accepted into '.$name;
									$trader->add_notification('success',$str);
									$results['result']='<div class="alert alert-success">Candidacy accepted.</div>';	
									Gamemaster::deleteRequest($id_request);
								}
								else{
									$results['result']='<div class="alert alert-error">Unable to complete buy-in.</div>';	
								}
							break;

							case 'reject':
								$firm=new Firm($id_firm);
								$name=htmlspecialchars($firm->get_name());
								$str='You have been rejected from '.$name;
								$trader->add_notification('warning',$str);
								$results['result']='<div class="alert alert-success">Candidacy rejected.</div>';
								Gamemaster::deleteRequest($id_request);
							break;
						}
					}
					else{
						$results['result']='<div class="alert alert-error">Error use.</div>';	
					}
				}
				else{
					$results['result']='<div class="alert alert-error">Error manager.</div>';	
				}
			}
			else{
				$results['result']='<div class="alert alert-error">Error request.</div>';	
			}
		}
		else{
			$results['result']='<div class="alert alert-error">Error 4.</div>';
		}
	}
	else{
		$results['result']='<div class="alert alert-error">Error 5.</div>';
	}
	echo json_encode($results);
}
?>