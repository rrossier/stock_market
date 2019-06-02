<?php

$connexion=new Connexion_db();
$req=$connexion->prepare("SELECT * FROM config");
$req->execute();
$row=$req->fetch();
$last_update=$row['last_update'];
$last_taxes=$row['last_taxes'];
date_default_timezone_set ('America/New_York');
$datetime=time();
$ecart=($datetime-$last_update);
$bool_ny=Market::is_open('new-york');
$bool_forex=Market::is_open('forex');
$bool_paris=Market::is_open('paris');
$bool_london=Market::is_open('london');
$bool_hongkong=Market::is_open('hong-kong');
$refresh=60;
if($ecart>$refresh && $bool_paris){
	include('batch_realtime_srd.php');
	$req=$connexion->prepare("UPDATE config SET last_update=:last");
	$req->execute(array('last'=>$datetime));
}
if($ecart>$refresh && $bool_hongkong){
	include('batch_realtime_hsi.php');
	$req=$connexion->prepare("UPDATE config SET last_update=:last");
	$req->execute(array('last'=>$datetime));
}
if($ecart>$refresh && $bool_london){
	include('batch_realtime_ftse.php');
	$req=$connexion->prepare("UPDATE config SET last_update=:last");
	$req->execute(array('last'=>$datetime));
}
if($ecart>$refresh && $bool_ny){
	include('batch_realtime_dowjones.php');
	include('batch_realtime_forex.php');
	include('batch_realtime_nasdaq.php');
	include('batch_realtime_sp500.php');
	$req=$connexion->prepare("UPDATE config SET last_update=:last");
	$req->execute(array('last'=>$datetime));
}
elseif($bool_forex && $ecart>$refresh){
	include('batch_realtime_forex.php');
	include('batch_realtime_indexes.php');
	$req=$connexion->prepare("UPDATE config SET last_update=:last");
	$req->execute(array('last'=>$datetime));
}
/*
elseif(!$bool_ny && !$bool_paris && date('H',$last_update)<'16'){
	include('batch_realtime_dowjones.php');
	include('batch_realtime_forex.php');
	include('batch_realtime_nasdaq.php');
	include('batch_realtime_sp500.php');
	$req=$connexion->prepare("UPDATE config SET last_update=:last");
	
	//$newdate=mktime(16,0,0,date('n',$datetime),date('j',$datetime),date('Y',$datetime));
	//$req->execute(array('last'=>$newdate));
}
*/
$days=abs(date('z',$datetime)-date('z',$last_taxes));
if($days>0){
	for($i=1;$i<=$days;$i++){
		$brokers=Market::get_list_brokers();
		foreach($brokers as $broker){
			$broker->pay_taxes();
		}
	}
	$req=$connexion->prepare("UPDATE config SET last_taxes=:datetime");
	$req->execute(array('datetime'=>$datetime));
}
