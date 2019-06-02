<?php
// cron weekly
$connexion=new Connexion_db();

// broker
$req=$connexion->exec("SELECT id FROM brokers");
while($row=$req->fetch()){
	$broker=new Broker($row['id']);
	// broker pay fees
	$fees=$broker->pay_taxes();
	if($fees>0){
		Gamemaster::collect_fees('broker',$fees);
	}
	// broker reset daily receipt
	$broker->__set('weekly_recette',0);
	$broker->save();
}

// trader 
$req=$connexion->exec("SELECT id,active FROM traders WHERE active=1");
while($row=$req->fetch()){
	$trader=new Trader($row['id']);
	// trader update classement week
	$res=$trader->updateClassementWeekly();
	$str='save classement week of trader id: '.$trader->id.'. Result: '.$res.'. time:'.date('d-m-Y H:i:s',time());;
	log($str);
}
$res=Gamemaster::sortTradersWeekly();
$str='sort traders weekly. Result: '.$res.'. time:'.date('d-m-Y H:i:s',time());
log($str);

// firm
$req=$connexion->exec("SELECT id,status FROM firms WHERE status='registered'");
while($row=$req->fetch()){
	$firm=new Firm($row['id']);
	// firm update classement week
	$res=$firm->updateClassementWeekly();
	$str='save classement week of firm id: '.$firm->id.'. Result: '.$res.'. time:'.date('d-m-Y H:i:s',time());;
	log($str);
	// firm pay fees
	$fees=$firm->pay_taxes();
	if($fees>0){
		Gamemaster::collect_fees('firm',$fees);
	}
}

// broker collect loan
?>