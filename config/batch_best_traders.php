<?php

include('../header.php');

$connexion=new Connexion_db();
$req=$connexion->prepare("SELECT * FROM traders WHERE active=1");
$req->execute();
while($row=$req->fetch(PDO::FETCH_ASSOC)){
	$trader=new Trader($row);
	$valuation=$trader->portfolio_valuation();
	$trader->precise_save('daily_perf',$valuation);
}