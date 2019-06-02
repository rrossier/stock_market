<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');
	
	$id_user=(isset($_GET['id_user'])) ? htmlspecialchars($_GET['id_user']) : null;
	$mode=(isset($_GET['mode'])) ? htmlspecialchars($_GET['mode']) : null;
	$uuid_stock=(isset($_GET['uuid_stock'])) ? htmlspecialchars($_GET['uuid_stock']) : null;
	$qty=(isset($_GET['qty'])) ? htmlspecialchars($_GET['qty']) : 10;
	$hash=(isset($_GET['hash'])) ? htmlspecialchars($_GET['hash']) : null;
	$type=(isset($_GET['type_order'])) ? htmlspecialchars($_GET['type_order']) : null;
	$price=(isset($_GET['price'])) ? htmlspecialchars($_GET['price']) : null;
	if(!in_array($mode,array('buy','sell'))){
		$mode='buy';
	}
	if(!in_array($type,array('market','limit'))){
		$type='market';
	}
	$results=array();
	$stock=new Stock();
	$stock->__set('uuid',$uuid_stock);
	if($stock->get_market()=="forex"){
		$uuid_stock.="=X";
	}
	$open=$stock->is_market_open();
	if($open){
		$user=new Trader();
		$user->fill($id_user);
		//$bcrypt = new Bcrypt(15);
		//$isGood = $bcrypt->verify($user->__get('name'), $hash);
		$isGood=password_verify($user->__get('name'), $hash);
		//$isGood=1;
		if($isGood==1){
			if(is_numeric($qty)){
				if($type=='market'){
					if($mode==='buy'){
						if($user->buy($stock,$qty)){
							$results['bool']=1;
							$results['result']='<div class="alert alert-success">Success. Transaction correctly executed</div>';
							$results['valuation']=number_format($user->get_valuation(),0,'.',' ');
							$results['portfolio']=$user->get_portfolio($hash);
						}
						else{
							$results['bool']=0;
							$results['result']='<div class="alert alert-error">Error. Check your wallet.</div>';
						}
					}
					elseif($mode==='sell'){
						if($user->sell($stock,$qty)){
							$results['bool']=1;
							$results['result']='<div class="alert alert-success">Success. Transaction correctly executed</div>';
							$results['valuation']=number_format($user->get_valuation(),0,'.',' ');
							$results['portfolio']=$user->get_portfolio($hash);
						}
						else{
							$results['bool']=0;
							$results['result']='<div class="alert alert-error">Error. You can\'t sell that.</div>';
						}
					}
				}
				else if($type=='limit'){
					$clause= ($mode=='buy') ? 'inferior' : 'superior';
					//create_listener($id_user,$uuid_stock,$clause,$qty,$price,time());
					$connexion=new Connexion_db();
					$req=$connexion->prepare("INSERT INTO listeners(id_user,ticker,clause,quantity,value,datetime) VALUES (:id_user,:ticker,:clause,:quantity,:price,:datetime)");
					try{
						$req->execute(array('id_user'=>$id_user,'ticker'=>$uuid_stock,'clause'=>$clause,'quantity'=>$qty,'price'=>$price,'datetime'=>time()));
					}
					catch (PDOException $e){
					    echo $e->getMessage();
					    $connexion->rollBack();
					}
					$results['bool']=1;
					$results['result']='<div class="alert alert-success">Success. Transaction correctly saved</div>';
					$results['valuation']=number_format($user->get_valuation(),0,'.',' ');
					$results['portfolio']=$user->get_portfolio($hash);
				}
			}
		}
		else{
			$results['bool']=0;
			$results['result']='<div class="alert alert-error">Error</div>';

		}
	}
	else{
		$results['bool']=0;
		$results['result']='<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>
							<h4 class="alert-heading">Warning!</h4>Markets are closed. Go see the real world, outside!</div>';
	}
		echo json_encode($results);
}
?>