<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');

	$montant=(isset($_POST['montant'])) ? htmlspecialchars($_POST['montant']) : 0;
	$rate=(isset($_POST['rate'])) ? strtolower(htmlspecialchars($_POST['rate'])) : 'overnight';
	$rates_available=array('overnight','week','weeks','month');
	$rates_files=array('overnight.csv','1-week.csv','2-weeks.csv','1-month.csv');
	$durees=array('1','7','14','30');
	$key=array_search($rate, $rates_available);
	//var_dump($key);
	if($key!==false){
		$rate=$rates_files[$key];
	}
	$taux=Gamemaster::getRate($rate);
	$nb=$durees[$key];
	$CRD=$montant;
	$mens=0;
	$results=array();
	$int=0;
	$amort=0;
	$f1=0;
	$f2=0;
	$cout=0;

	for($i=1;$i<=$nb;$i++){
		$f2=(1-pow((1+$taux), ($i-1-$nb)));
		$mens=$taux*$CRD/$f2;
		$int=$CRD*$taux;
		$amort=$mens-$int;
		$CRD=$CRD-$amort;
		$results[]=array('mens'=>number_format($mens,'0','.',' '),'amort'=>number_format($amort,'0','.',' '),'interests'=>number_format($int,'0','.',' '),'CRD'=>number_format($CRD,'0','.',' '));
	}
	echo json_encode($results);
}

?>