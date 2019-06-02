<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');
	$q= (isset($_POST['q'])) ? '%'.safe($_POST['q']).'%' : null;
	$str='';
	$results=array();
	if(isset($q)){
		$connexion = new Connexion_db();
		// stocks
		$req=$connexion->prepare("SELECT DISTINCT ticker,name FROM stocks WHERE name LIKE :name LIMIT 0,5 ");
		$req->execute(array('name'=>$q));
		if($req->rowCount()>0){
			//$str.="<ul>";
			while($row=$req->fetch()){
				$str="../trader/stock?stock=".$row['ticker'];
				$results[]=array('href'=>$str,'title'=>$row['name']);
			}
			//$str.="</ul>";
		}
		// traders
		$req=$connexion->prepare("SELECT id,name FROM traders WHERE name LIKE :name LIMIT 0,5 ");
		$req->execute(array('name'=>$q));
		if($req->rowCount()>0){
			//$str.="<ul>";
			while($row=$req->fetch()){
				$str="../trader/trader?id=".$row['id'];
				$results[]=array('href'=>$str,'title'=>$row['name']);
			}
			//$str.="</ul>";
		}
		// firms
		$req=$connexion->prepare("SELECT id,name FROM firms WHERE name LIKE :name LIMIT 0,5 ");
		$req->execute(array('name'=>$q));
		if($req->rowCount()>0){
			//$str.="<ul>";
			while($row=$req->fetch()){
				$str="../firm/?id=".$row['id'];
				$results[]=array('href'=>$str,'title'=>$row['name']);
			}
			//$str.="</ul>";
		}
		// brokers
		$req=$connexion->prepare("SELECT id,name FROM brokers WHERE name LIKE :name LIMIT 0,5 ");
		$req->execute(array('name'=>$q));
		if($req->rowCount()>0){
			//$str.="<ul>";
			while($row=$req->fetch()){
				$str="../broker/view?id=".$row['id'];
				$results[]=array('href'=>$str,'title'=>$row['name']);
			}
			//$str.="</ul>";
		}
		echo json_encode($results);
	}
}
?>