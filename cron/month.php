<?php
// cron monthly
$connexion=new Connexion_db();

// trader 
$req=$connexion->exec("SELECT id,active FROM traders WHERE active=1");
while($row=$req->fetch()){
	$trader=new Trader($row['id']);
	// trader update classement week
	$res=$trader->updateClassementMonthly();
	$str='save classement month of trader id: '.$trader->id.'. Result: '.$res;
	log($str);
}
$res=Gamemaster::sortTradersMonthly();
$str='sort traders monthly. Result: '.$res.'. time:'.date('d-m-Y H:i:s',time());
log($str);

// firm
$req=$connexion->exec("SELECT id,status FROM firms WHERE status='registered'");
while($row=$req->fetch()){
	$firm=new Firm($row['id']);
	// firm update classement week
	$res=$firm->updateClassementMonthly();
	$str='save classement month of firm id: '.$firm->id.'. Result: '.$res;
	log($str);
}

// broker collect loan
?>