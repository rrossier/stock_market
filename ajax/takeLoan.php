<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');

	$montant=(isset($_POST['montant'])) ? htmlspecialchars($_POST['montant']) : 0;
	$id=(isset($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
	$hash=(isset($_POST['hash'])) ? htmlspecialchars($_POST['hash']) : 0;
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
	$f2=(1-pow((1+$taux), (0-$nb)));
	$mens=$taux*$CRD/$f2;
	if(is_numeric($id)){
		$trader=new Trader($id);
		$isGood=password_verify($trader->__get('name'), $hash);
		if($isGood){
			if(Gamemaster::registerLoan($id,$mens,$nb)){
				$trader->give_cash($montant);
				if($trader->save()){
					$result='<div class="alert alert-success">Loan correctly registered. Your first payment will debute tomorrow.</div>';
				}
			}
			else{
				$result='<div class="alert alert-error">Loan rejected.</div>';
			}
		}
	}
	echo json_encode($result);
}

?>