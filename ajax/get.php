<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');

	$connexion=new Connexion_db();
	$req=$connexion->prepare("SELECT * FROM config");
	$req->execute();
	$row=$req->fetch();
	$last_update=$row['last_update'];
	$last_taxes=$row['last_taxes'];
	date_default_timezone_set ('America/New_York');
	$datetime=time();
	$ecart=($datetime-$last_update);

	$market=(isset($_GET['market'])) ? htmlspecialchars($_GET['market']) : 'dowjones';
	$override=(isset($_GET['override'])) ? htmlspecialchars($_GET['override']) : 0;
	$override=true;
	$bool_ny=($override) ? true : Market::is_open('new-york');
	$bool_paris=($override) ? true : Market::is_open('paris');
	$bool_london=($override) ? true : Market::is_open('london');
	$bool_hongkong=($override) ? true : Market::is_open('hong-kong');
	$latence=($override) ? 0 : 90;
	$stocks=array();
	$connexion= new Connexion_db();
	switch($market){
		case "dowjones":
		$currency="$";
		if($ecart>$latence && $bool_ny){
			$req=$connexion->prepare("SELECT * FROM dowjones_temp ORDER BY name ASC");
			$req->execute();
			$str='[';
			$str.='{"update":"1"},[';
			while($data=$req->fetch()){
				$str.='{';
				$str.='"symbol":"'.$data['symbol'].'",';
				$str.='"name":"'.$data['name'].'",';
				$str.='"last_trade":"'.$data['last_trade'].'",';
				$str.='"bid_realtime":"'.$data['bid_realtime'].'",';
				$str.='"ask_realtime":"'.$data['ask_realtime'].'",';
				$str.='"bid":"'.$data['bid'].'",';
				$str.='"ask":"'.$data['ask'].'",';
				$str.='"date":"'.stripslashes($data['date']).'",';
				$str.='"time":"'.$data['time'].'",';
				$str.='"volume":"'.number_format($data['volume'],0,'.',' ').'",';
				$str.='"change_realtime":"'.$data['change_realtime'].'",';
				$str.='"currency":"'.$currency.'"';
				$str.='},';
			}
			$str=substr($str,0,-1);
			$str.=']]';
			echo $str;
		}
		else{
			$str='[{"update":"0"}]';
			echo $str;
		}
		break;

		case "nasdaq":
		$currency="$";
		if($ecart>$latence && $bool_ny){
			$req=$connexion->prepare("SELECT * FROM nasdaq_temp ORDER BY name ASC");
			$req->execute();
			$str='[';
			$str.='{"update":"1"},[';
			while($data=$req->fetch()){
				$str.='{';
				$str.='"symbol":"'.$data['symbol'].'",';
				$str.='"name":"'.$data['name'].'",';
				$str.='"last_trade":"'.$data['last_trade'].'",';
				$str.='"bid_realtime":"'.$data['bid_realtime'].'",';
				$str.='"ask_realtime":"'.$data['ask_realtime'].'",';
				$str.='"bid":"'.$data['bid'].'",';
				$str.='"ask":"'.$data['ask'].'",';
				$str.='"date":"'.stripslashes($data['date']).'",';
				$str.='"time":"'.$data['time'].'",';
				$str.='"volume":"'.number_format($data['volume'],0,'.',' ').'",';
				$str.='"change_realtime":"'.$data['change_realtime'].'",';
				$str.='"currency":"'.$currency.'"';
				$str.='},';
			}
			$str=substr($str,0,-1);
			$str.=']]';
			echo $str;
		}
		else{
			$str='[{"update":"0"}]';
			echo $str;
		}
		break;

		case "ftse":
		$currency="£";
		if($ecart>$latence && $bool_london){
			$req=$connexion->prepare("SELECT * FROM ftse_temp ORDER BY name ASC");
			$req->execute();
			$str='[';
			$str.='{"update":"1"},[';
			while($data=$req->fetch()){
				$str.='{';
				$str.='"symbol":"'.$data['symbol'].'",';
				$str.='"name":"'.$data['name'].'",';
				$str.='"last_trade":"'.$data['last_trade'].'",';
				$str.='"bid_realtime":"'.$data['bid_realtime'].'",';
				$str.='"ask_realtime":"'.$data['ask_realtime'].'",';
				$str.='"bid":"'.$data['bid'].'",';
				$str.='"ask":"'.$data['ask'].'",';
				$str.='"date":"'.stripslashes($data['date']).'",';
				$str.='"time":"'.$data['time'].'",';
				$str.='"volume":"'.number_format($data['volume'],0,'.',' ').'",';
				$str.='"change_realtime":"'.$data['change_realtime'].'",';
				$str.='"currency":"'.$currency.'"';
				$str.='},';
			}
			$str=substr($str,0,-1);
			$str.=']]';
			echo $str;
		}
		else{
			$str='[{"update":"0"}]';
			echo $str;
		}
		break;

		case "hsi":
		$currency="$";
		if($ecart>$latence && $bool_hongkong){
			$req=$connexion->prepare("SELECT * FROM hsi_temp ORDER BY name ASC");
			$req->execute();
			$str='[';
			$str.='{"update":"1"},[';
			while($data=$req->fetch()){
				$str.='{';
				$str.='"symbol":"'.$data['symbol'].'",';
				$str.='"name":"'.$data['name'].'",';
				$str.='"last_trade":"'.$data['last_trade'].'",';
				$str.='"bid_realtime":"'.$data['bid_realtime'].'",';
				$str.='"ask_realtime":"'.$data['ask_realtime'].'",';
				$str.='"bid":"'.$data['bid'].'",';
				$str.='"ask":"'.$data['ask'].'",';
				$str.='"date":"'.stripslashes($data['date']).'",';
				$str.='"time":"'.$data['time'].'",';
				$str.='"volume":"'.number_format($data['volume'],0,'.',' ').'",';
				$str.='"change_realtime":"'.$data['change_realtime'].'",';
				$str.='"currency":"'.$currency.'"';
				$str.='},';
			}
			$str=substr($str,0,-1);
			$str.=']]';
			echo $str;
		}
		else{
			$str='[{"update":"0"}]';
			echo $str;
		}
		break;

		case "sp500":
		$currency="$";
		if($ecart>$latence && $bool_paris){
			//$tickers_market=$sp500_tickers;
			if(isset($_GET['sortby'])){
				$page=0;
				$sortby=htmlspecialchars($_GET['sortby']);
				$values=array('symbol','ask','bid','change','volume');
				$keys=array('symbol','last_trade','last_trade','change_realtime','volume');
				$sortby=(in_array($sortby,$values)) ? $sortby : 'symbol';
				$sortby=$keys[array_search($sortby, $values)];
				$order=(isset($_GET['order']) && $_GET['order']=='ASC') ? 'ASC' : 'DESC';
				$order2=($order=='DESC') ? 'ASC' : 'DESC';
				$sql="SELECT * FROM sp500_temp ORDER BY ".$sortby." ".$order." LIMIT 0,50 ";
				$req=$connexion->prepare($sql);
				$req->bindParam('page',$page, PDO::PARAM_INT);
			}
			else if(isset($_GET['page'])){
				$p=htmlspecialchars($_GET['page']);
				$page=($p-1)*50;
				$req=$connexion->prepare("SELECT * FROM sp500_temp ORDER BY name ASC LIMIT :page,50 ");
				$req->bindValue('page',$page, PDO::PARAM_INT);
				$order2='DESC';
			}
			else{
				$page=0;
				$req=$connexion->prepare("SELECT * FROM sp500_temp ORDER BY name ASC LIMIT :page,50 ");
				$req->bindValue('page',$page, PDO::PARAM_INT);
				$order2='DESC';
			}
			
			$req->execute();
			$str='[';
			$str.='{"update":"1"},[';
			while($data=$req->fetch()){
				$str.='{';
				$str.='"symbol":"'.$data['symbol'].'",';
				$str.='"name":"'.$data['name'].'",';
				$str.='"last_trade":"'.$data['last_trade'].'",';
				$str.='"bid_realtime":"'.$data['bid_realtime'].'",';
				$str.='"ask_realtime":"'.$data['ask_realtime'].'",';
				$str.='"bid":"'.$data['bid'].'",';
				$str.='"ask":"'.$data['ask'].'",';
				$str.='"date":"'.stripslashes($data['date']).'",';
				$str.='"time":"'.$data['time'].'",';
				$str.='"volume":"'.number_format($data['volume'],0,'.',' ').'",';
				$str.='"change_realtime":"'.$data['change_realtime'].'",';
				$str.='"currency":"'.$currency.'"';
				$str.='},';
			}
			$str=substr($str,0,-1);
			$str.='],{"order":"'.$order2.'"}';
			$str.=']';
			echo $str;
		}
		else{
			$str='[{"update":"0"}]';
			echo $str;
		}
		break;

		case "sbf120":
		$currency="€";
		if($ecart>$latence && $bool_paris){
			//$tickers_market=$sbf120_tickers;
			if(isset($_GET['sortby'])){
				$page=0;
				$sortby=htmlspecialchars($_GET['sortby']);
				$values=array('symbol','ask','bid','change','volume');
				$keys=array('symbol','last_trade','last_trade','change_realtime','volume');
				$sortby=(in_array($sortby,$values)) ? $sortby : 'symbol';
				$sortby=$keys[array_search($sortby, $values)];
				$order=(isset($_GET['order']) && $_GET['order']=='ASC') ? 'ASC' : 'DESC';
				$order2=($order=='DESC') ? 'ASC' : 'DESC';
				$sql="SELECT * FROM sbf120_temp ORDER BY ".$sortby." ".$order." LIMIT 0,50 ";
				$req=$connexion->prepare($sql);
				$req->bindParam('page',$page, PDO::PARAM_INT);
			}
			else if(isset($_GET['page'])){
				$p=htmlspecialchars($_GET['page']);
				$page=($p-1)*50;
				$req=$connexion->prepare("SELECT * FROM sbf120_temp ORDER BY name ASC LIMIT :page,50 ");
				$req->bindValue('page',$page, PDO::PARAM_INT);
				$order2='DESC';
			}
			else{
				$page=0;
				$req=$connexion->prepare("SELECT * FROM sbf120_temp ORDER BY name ASC LIMIT :page,50 ");
				$req->bindValue('page',$page, PDO::PARAM_INT);
				$order2='DESC';
			}
			
			$req->execute();
			$str='[';
			$str.='{"update":"1"},[';
			while($data=$req->fetch()){
				$str.='{';
				$str.='"symbol":"'.$data['symbol'].'",';
				$str.='"name":"'.$data['name'].'",';
				$str.='"last_trade":"'.$data['last_trade'].'",';
				$str.='"bid_realtime":"'.$data['bid_realtime'].'",';
				$str.='"ask_realtime":"'.$data['ask_realtime'].'",';
				$str.='"bid":"'.$data['bid'].'",';
				$str.='"ask":"'.$data['ask'].'",';
				$str.='"date":"'.stripslashes($data['date']).'",';
				$str.='"time":"'.$data['time'].'",';
				$str.='"volume":"'.number_format($data['volume'],0,'.',' ').'",';
				$str.='"change_realtime":"'.$data['change_realtime'].'",';
				$str.='"currency":"'.$currency.'"';
				$str.='},';
			}
			$str=substr($str,0,-1);
			$str.='],{"order":"'.$order2.'"}';
			$str.=']';
			echo $str;
		}
		else{
			$str='[{"update":"0"}]';
			echo $str;
		}
		break;

		case "forex":
		$currency="$";
		if($ecart>$latence){
			$req=$connexion->prepare("SELECT * FROM forex_temp ORDER BY name ASC");
			$req->execute();
			$str='[';
			$str.='{"update":"1"},[';
			while($data=$req->fetch()){
				$str.='{';
				$str.='"symbol":"'.$data['symbol'].'",';
				$str.='"name":"'.$data['name'].'",';
				$str.='"last_trade":"'.$data['last_trade'].'",';
				$str.='"bid_realtime":"'.$data['bid_realtime'].'",';
				$str.='"ask_realtime":"'.$data['ask_realtime'].'",';
				$str.='"bid":"'.$data['bid'].'",';
				$str.='"ask":"'.$data['ask'].'",';
				$str.='"date":"'.stripslashes($data['date']).'",';
				$str.='"time":"'.$data['time'].'",';
				$str.='"day_range":"'.$data['day_range'].'",';
				$str.='"wk_range":"'.$data['wk_range'].'",';
				$str.='"currency":"'.$currency.'"';
				$str.='},';
			}
			$str=substr($str,0,-1);
			$str.=']]';
			echo $str;
		}
		else{
			$str='[{"update":"0"}]';
			echo $str;
		}
		break;

		default:
		if($ecart>$latence && $bool_ny){
			$req=$connexion->prepare("SELECT * FROM dowjones_temp ORDER BY name ASC");
			$req->execute();
			$str='[';
			$str.='{"update":"1"},[';
			while($data=$req->fetch()){
				$str.='{';
				$str.='"symbol":"'.$data['symbol'].'",';
				$str.='"name":"'.$data['name'].'",';
				$str.='"last_trade":"'.$data['last_trade'].'",';
				$str.='"bid_realtime":"'.$data['bid_realtime'].'",';
				$str.='"ask_realtime":"'.$data['ask_realtime'].'",';
				$str.='"bid":"'.$data['bid'].'",';
				$str.='"ask":"'.$data['ask'].'",';
				$str.='"date":"'.stripslashes($data['date']).'",';
				$str.='"time":"'.$data['time'].'",';
				$str.='"volume":"'.number_format($data['volume'],0,'.',' ').'",';
				$str.='"change_realtime":"'.$data['change_realtime'].'",';
				$str.='"currency":"'.$currency.'"';
				$str.='},';
			}
			$str=substr($str,0,-1);
			$str.=']]';
			echo $str;
		}
		else{
			$str='[{"update":"0"}]';
			echo $str;
		}
	}
	

}
?>