<?php
// cron daily
$connexion=new Connexion_db();

// broker collect loan

// trader
$req=$connexion->exec("SELECT id,active FROM traders WHERE active=1");
while($row=$req->fetch()){
	$trader=new Trader($row['id']);
	// trader save valuation into file
	$trader->registerValuation();
	// trader update classement day
	$res=$trader->updateClassementDaily();
	$str='save classement day of trader id: '.$trader->id.'. Result: '.$res.'. time:'.date('d-m-Y H:i:s',time());
	log($str);
	// trader pay loan
}

// firm
$req=$connexion->exec("SELECT id,status FROM firms WHERE status='registered'");
while($row=$req->fetch()){
	$firm=new Firm($row['id']);
	// firm save valuation into file
	$firm->registerValuation();
	// firm update classement day
	$res=$firm->updateClassementDaily();
	$str='save classement day of firm id: '.$firm->id.'. Result: '.$res.'. time:'.date('d-m-Y H:i:s',time());
	log($str);
	// firm pay loan
}

// tickers historiques
$tickers=Gamemaster::getTickers();
foreach ($tickers as $stock) {
	$res=Gamemaster::getHistorique($stock);
	$str='retrieve historique from ticker: '.$stock.'. Result: '.$res.'. time:'.date('d-m-Y H:i:s',time());
	log($str);
}
?>