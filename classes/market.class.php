<?php

class Market{
	private $name;
	private $stocks;
	private $datetime;

	public function __construct(){
	}

	public static function get_list_brokers(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT brokers.id FROM brokers LEFT JOIN users ON users.id_user=brokers.id AND users.actif=1 AND users.type='broker' ORDER BY brokers.date_inscription DESC");
		$req->execute();
		$tab=array();
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$broker=new Broker();
			$broker->fill($row['id']);
			$tab[]=$broker;
		}
		return $tab;
	}

	public static function is_open($location='new-york'){
		switch($location){
			case 'new-york':
			date_default_timezone_set ('America/New_York');
			$closed=array("03-09-2012","01-01-2013","22-11-2012","25-12-2012","21-01-2013","18-02-2013","29-03-2012","27-05-2012","04-07-2013","02-09-2013","28-11-2013","25-12-2012","01-01-2014","20-01-2014","17-02-2014","18-04-2014","26-05-2014","04-07-2014","01-09-2014","27-11-2014","25-12-2014");
			$N=date('N');
			$now=strtotime("now");
			if($N==6 || $N==7){
				return 0;
			}
			if(in_array(date("d-m-Y"), $closed)){
				return 0;
			}
			$open=strtotime("09:30");
			$close=strtotime("16:00");
			if($open<strtotime("now") && strtotime("now")<$close){
				return 1;
			}
			else{
				return 0;
			}
			break;

			
			case 'paris':
			date_default_timezone_set ('Europe/Paris');
			$closed=array("25-12-2012","26-12-2012","01-01-2013","01-05-2013","25-12-2013","26-12-2013","01-01-2014","01-05-2014","25-12-2014","26-12-2014");
			$N=date('N');
			if($N==6 || $N==7){
				return 0;
			}
			if(in_array(date("d-m-Y"), $closed)){
				return 0;
			}
			$open=strtotime("09:30");
			$close=strtotime("17:30");
			if($open<strtotime("now") && strtotime("now")<$close){
				return 1;
			}
			else{
				return 0;
			}
			break;	

			case 'london':
			date_default_timezone_set ('Europe/London');
			$closed=array("25-12-2012","26-12-2012","01-01-2013","01-05-2013","25-12-2013","26-12-2013","01-01-2014","01-05-2014","25-12-2014","26-12-2014");
			$N=date('N');
			if($N==6 || $N==7){
				return 0;
			}
			if(in_array(date("d-m-Y"), $closed)){
				return 0;
			}
			$open=strtotime("08:00");
			$close=strtotime("16:30");
			if($open<strtotime("now") && strtotime("now")<$close){
				return 1;
			}
			else{
				return 0;
			}
			break;

			case 'hong-kong':
			date_default_timezone_set ('Asia/Hong_Kong');
			$closed=array("25-12-2012","26-12-2012","01-01-2013","01-05-2013","25-12-2013","26-12-2013","01-01-2014","01-05-2014","25-12-2014","26-12-2014");
			$N=date('N');
			if($N==6 || $N==7){
				return 0;
			}
			if(in_array(date("d-m-Y"), $closed)){
				return 0;
			}
			$open=strtotime("09:30");
			$close=strtotime("16:00");
			if($open<strtotime("now") && strtotime("now")<$close){
				return 1;
			}
			else{
				return 0;
			}
			break;	

			case 'forex':

			return 1;
			break;		
		}
		
	}

	public function display_time($location='new-york'){
		if($location=='new-york' || $location=='forex'){
			date_default_timezone_set('America/New_York');
		}
		elseif($location=='paris'){
			date_default_timezone_set('Europe/Paris');
		}
		elseif($location=='london'){
			date_default_timezone_set('Europe/London');
		}
		elseif($location=='hong-kong'){
			date_default_timezone_set('Asia/Hong_Kong');
		}
		if($this->is_open($location)){
			echo "<h2 class='market-open'>".date('H:i:s')."</h2>";
		}
		else{
			echo "<h2 class='market-closed'>".date('H:i:s')."</h2>";
		}
	}

	public static function get_best_traders($limit=100){
		$connexion= new Connexion_db();
		$sql="SELECT id FROM traders WHERE active=1 ORDER BY daily_perf DESC LIMIT 0,".$limit;
		$req=$connexion->prepare($sql);
		$req->execute();
		$traders=array();
		while($row=$req->fetch()){
			$tp=new Trader();
			$tp->fill($row['id']);
			$traders[]=$tp;
		}
		return $traders;
	}

}