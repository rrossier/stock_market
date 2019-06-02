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
	$account=(isset($_GET['account'])) ? htmlspecialchars($_GET['account']) : null;
	if(!in_array($mode,array('buy','sell'))){
		$mode='buy';
	}
	if(!in_array($type,array('market','limit','stop'))){
		$type='market';
	}
	$results=array();
	$stock=new Stock();
	$stock->__set('uuid',$uuid_stock);
	$exists=$stock->doesExist();
	if($stock->get_market()=="forex"){
		$uuid_stock.="=X";
	}
	$open=$stock->is_market_open();

	if(is_numeric($id_user) && $exists){
		$trader=new Trader();
		$trader->fill($id_user);
		$isGood=password_verify($trader->__get('name'), $hash);
		if($isGood){
			if($account!='perso'){
				if(is_numeric($account)){
					if($trader->has_firm($account)){
						$firm = new Firm($account);
						if($trader->isManager($account)){
							if(!$open || $type=='limit' || $type=='stop'){
								$connexion=new Connexion_db();
								$req=$connexion->prepare("INSERT INTO orderbook_stocks(id_trader,id_stock,id_firm,quantity,price,type,datetime) VALUES (:id_trader,:id_stock,:id_firm,:quantity,:price,:type,:datetime)");
								try{
									$req->execute(array('id_trader'=>$id_user,'id_stock'=>$uuid_stock,'id_firm'=>$id_firm,'quantity'=>$qty,'price'=>$price,'type'=>$type,'datetime'=>time()));
								}
								catch (PDOException $e){
								    echo $e->getMessage();
								    $connexion->rollBack();
								}
								$results['bool']=1;
								$results['result']='<div class="alert alert-info">Waiting for execution. Transaction correctly saved.</div>';
								$results['valuation']=number_format($firm->get_valuation(),0,'.',' ');
								// $results['portfolio']=$firm->get_portfolio($hash);
							}
							else if($open && $type=='market'){
								if($mode==='buy'){
									if($firm->buy($stock,$qty)){
										$results['bool']=1;
										$results['result']='<div class="alert alert-success">Success. Transaction correctly executed.</div>';
										$results['valuation']=number_format($firm->get_valuation(),0,'.',' ');
										// $results['portfolio']=$firm->get_portfolio($hash);
									}
									else{
										$results['bool']=0;
										$results['result']='<div class="alert alert-error">Error. Check your wallet.</div>';
									}
								}
								elseif($mode==='sell'){
									if($firm->sell($stock,$qty)){
										$results['bool']=1;
										$results['result']='<div class="alert alert-success">Success. Transaction correctly executed.</div>';
										$results['valuation']=number_format($firm->get_valuation(),0,'.',' ');
										// $results['portfolio']=$firm->get_portfolio($hash);
									}
									else{
										$results['bool']=0;
										$results['result']='<div class="alert alert-error">Error. You can\'t sell that.</div>';
									}
								}
							}
						}
						else{
							$connexion=new Connexion_db();
							$req=$connexion->prepare("INSERT INTO orderbook_stocks(id_trader,id_stock,id_firm,quantity,price,type,datetime) VALUES (:id_trader,:id_stock,:id_firm,:quantity,:price,:type,:datetime)");
							try{
								$req->execute(array('id_trader'=>$id_user,'id_stock'=>$uuid_stock,'id_firm'=>$id_firm,'quantity'=>$qty,'price'=>$price,'type'=>$type,'datetime'=>time()));
							}
							catch (PDOException $e){
							    echo $e->getMessage();
							    $connexion->rollBack();
							}
							$results['bool']=1;
							$results['result']='<div class="alert alert-infor">Waiting for approbation. Transaction correctly saved.</div>';
							$results['valuation']=number_format($firm->get_valuation(),0,'.',' ');
							// $results['portfolio']=$firm->get_portfolio($hash);
						}
					}
					else{
						$results['bool']=0;
						$results['result']='<div class="alert alert-error">Error. No power over this firm.</div>';
					}
				}
				else{
					$results['bool']=0;
					$results['result']='<div class="alert alert-error">Error. Unrecognized firm.</div>';
				}
			}
			else{
				// personal account of the trader
				if(!$open || $type=='limit' || $type=='stop'){
					$connexion=new Connexion_db();
					$req=$connexion->prepare("INSERT INTO orderbook_stocks(id_trader,id_stock,id_firm,quantity,price,type,datetime) VALUES (:id_trader,:id_stock,:id_firm,:quantity,:price,:type,:datetime)");
					try{
						$req->execute(array('id_trader'=>$id_user,'id_stock'=>$uuid_stock,'id_firm'=>'perso','quantity'=>$qty,'price'=>$price,'type'=>$type,'datetime'=>time()));
					}
					catch (PDOException $e){
					    echo $e->getMessage();
					    $connexion->rollBack();
					}
					$results['bool']=1;
					$results['result']='<div class="alert alert-info">Waiting for execution. Transaction correctly saved.</div>';
					$results['valuation']=number_format($user->get_valuation(),0,'.',' ');
					$results['portfolio']=$user->get_portfolio($hash);
				}
				else if($open && $type=='market'){
					if($mode==='buy'){
						if($trader->buy($stock,$qty)){
							$results['bool']=1;
							$results['result']='<div class="alert alert-success">Success. Transaction correctly executed.</div>';
							$results['valuation']=number_format($trader->get_valuation(),0,'.',' ');
							$results['portfolio']=$trader->get_portfolio($hash);
						}
						else{
							$results['bool']=0;
							$results['result']='<div class="alert alert-error">Error. Check your wallet.</div>';
						}
					}
					elseif($mode==='sell'){
						if($trader->sell($stock,$qty)){
							$results['bool']=1;
							$results['result']='<div class="alert alert-success">Success. Transaction correctly executed.</div>';
							$results['valuation']=number_format($trader->get_valuation(),0,'.',' ');
							$results['portfolio']=$trader->get_portfolio($hash);
						}
						else{
							$results['bool']=0;
							$results['result']='<div class="alert alert-error">Error. You can\'t sell that.</div>';
						}
					}
				}
			}
		}
		else{
			$results['bool']=0;
			$results['result']='<div class="alert alert-error">Error. Unrecognized trader.</div>';
		}
	}
	else{
		$results['bool']=0;
		$results['result']='<div class="alert alert-error">Error. incorrect id for trader.</div>';
	}
	echo json_encode($results);
}
?>