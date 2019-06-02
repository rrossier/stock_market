<?php

class Gamemaster{

	private $id;
	private $pct_fees_firm_creation;
	private $pct_fees_broker_weekly;
	private $pct_fees_firm_weekly;
	private $fees_firms;
	private $fees_brokers;
	private $pct_fees_sell_shares;

	private static $_instance = null;

	private function __construct(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM gamemaster WHERE id=1");
		$req->execute(array());
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$this->id=$row['id'];
			$this->pct_fees_firm_creation=$row['pct_fees_firm_creation'];
			$this->pct_fees_broker_weekly=$row['pct_fees_broker_weekly'];
			$this->pct_fees_firm_weekly=$row['pct_fees_firm_weekly'];
			$this->fees_firms=$row['fees_firms'];
			$this->fees_brokers=$row['fees_brokers'];
			$this->pct_fees_sell_shares=$row['pct_fees_sell_shares'];
		}
	}

	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();  
		}
		return self::$_instance;
	}

	public static function getPctFeesFirmCreation(){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
			return floatval(self::$_instance->pct_fees_firm_creation);
		}
		return floatval(self::$_instance->pct_fees_firm_creation);
	}

	public static function getPctFeesBrokerWeekly(){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
			return floatval(self::$_instance->pct_fees_broker_weekly);
		}
		return floatval(self::$_instance->pct_fees_broker_weekly);
	}

	public static function getPctFeesFirmWeekly(){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
			return floatval(self::$_instance->pct_fees_firm_weekly);
		}
		return floatval(self::$_instance->pct_fees_firm_weekly);
	}

	public static function getPctFeesSellShares(){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
			return floatval(self::$_instance->pct_fees_sell_shares);
		}
		return floatval(self::$_instance->pct_fees_sell_shares);
	}

	public static function getFeesFirms(){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
			return floatval(self::$_instance->fees_firms);
		}
		return floatval(self::$_instance->fees_firms);
	}

	public static function getFeesBrokers(){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
			return floatval(self::$_instance->fees_brokers);
		}
		return floatval(self::$_instance->fees_brokers);
	}

	public static function collect_fees($type,$amount){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
		}
		switch($type){
			case 'firm':
				self::$_instance->fees_firms+=$amount;
				$connexion=new Connexion_db();
				$req=$connexion->prepare("UPDATE gamemaster SET fees_firms=fees_firms + :amount WHERE id=1");
				$req->execute(array('amount'=>$amount));
			break;

			case 'broker':
				self::$_instance->fees_firms+=$amount;
				$connexion=new Connexion_db();
				$req=$connexion->prepare("UPDATE gamemaster SET fees_brokers=fees_brokers + :amount WHERE id=1");
				$req->execute(array('amount'=>$amount));
			break;
		}
	}

	public static function SaveRequest($array){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
		}
		$connexion=new Connexion_db();
		$req=$connexion->prepare("INSERT INTO pending_requests (id_trader,id_firm,quantity,datetime) VALUES (:id_trader,:id_firm,:quantity,:datetime)");
		$req->execute($array);
		return $req;
	}

	public static function getRequest($id_request){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
		}
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM pending_requests WHERE id=:id_request ORDER BY datetime DESC");
		$req->execute(array('id_request'=>$id_request));
		if($req->rowCount()>0){
			$row=$req->fetch(PDO::FETCH_ASSOC);
			$request=array('id'=>$row['id'],'id_trader'=>$row['id_trader'],'datetime'=>$row['datetime'],'quantity'=>$row['quantity'],'id_firm'=>$row['id_firm']);
			return $request;
		}
		else{
			return FALSE;
		}
	}

	public static function deleteRequest($id_request){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
		}
		$connexion=new Connexion_db();
		$req=$connexion->prepare("DELETE FROM pending_requests WHERE id=:id_request");
		$req->execute(array('id_request'=>$id_request));
		if($req){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public static function deleteTrader($id){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
		}
		$connexion=new Connexion_db();
		$req=$connexion->prepare("DELETE FROM traders WHERE id=:id");
		$req->execute(array('id'=>$id));
		if($req){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public static function deleteBroker($id){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
		}
		$connexion=new Connexion_db();
		$req=$connexion->prepare("DELETE FROM brokers WHERE id=:id");
		$req->execute(array('id'=>$id));
		if($req){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public static function deleteFirm($id){
		if(is_null(self::$_instance)) {
			self::$_instance = new Gamemaster();
		}
		$connexion=new Connexion_db();
		$req=$connexion->prepare("DELETE FROM firms WHERE id=:id");
		$req->execute(array('id'=>$id));
		if($req){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public static function getTickers($market='all'){
		$connexion=new Connexion_db();
		$results=array();
		if($market==='all'){
			$req=$connexion->prepare("SELECT ticker FROM stocks");
			$req->execute();
			while($row=$req->fetch(PDO::FETCH_ASSOC)){
				$results[]=$row['ticker'];
			}
			return $results;
		}
		else{
			$req=$connexion->prepare("SELECT ticker,name FROM stocks WHERE market=:market");
			$req->execute(array('market'=>$market));
			while($row=$req->fetch(PDO::FETCH_ASSOC)){
				$results[]=$row['ticker'];
			}
			return $results;
		}
	}

	public static function getStocks($market='all'){
		$connexion=new Connexion_db();
		$results=array();
		if($market==='all'){
			$req=$connexion->prepare("SELECT ticker FROM stocks ORDER BY ticker ASC");
			$req->execute();
			while($row=$req->fetch(PDO::FETCH_ASSOC)){
				$results[]=$row['ticker'];
			}
			return $results;
		}
		else{
			$req=$connexion->prepare("SELECT ticker,name FROM stocks WHERE market=:market ORDER BY name ASC");
			$req->execute(array('market'=>$market));
			while($row=$req->fetch(PDO::FETCH_ASSOC)){
				$results[]=array('ticker'=>$row['ticker'],'name'=>$row['name']);
			}
			return $results;
		}
	}

	public static function getHistorique($ticker='AAPL'){
		$url="http://ichart.finance.yahoo.com/table.csv?s=".$ticker;
		$file="../data/markets/".$ticker.".csv";
		$contents=file_get_contents($url);
		return (file_put_contents($file, $contents) !== FALSE);
	}

	public static function displayIndexes(){
		$connexion=new Connexion_db();
		$req=$connexion->exec("SELECT * FROM indexes");
		$str=null;
		while($row=$req->fetch()){
			switch($row['symbol']){
				case '^FTSE':
					$str.='<a href="../markets/ftse"><strong>FTSE</strong></a>  ';
				break;
				case '^SBF120':
					$str.='<a href="../markets/sbf120"><strong>SBF 120</strong></a>  ';
				break;
				case '^HSI':
					$str.='<a href="../markets/hsi"><strong>HSI</strong></a>  ';
				break;
				case '^GSPC':
					$str.='<a href="../markets/sp500"><strong>S&P 500</strong></a>  ';
				break;
				case '^IXIC':
					$str.='<a href="../markets/nadasq"><strong>NASDAQ 100</strong></a>  ';
				break;
				case '^VIX':
					$str.='<a href="#"><strong>VIX</strong></a>  ';
				break;
				case 'GLD':
					$str.='<a href="#"><strong>Gold</strong></a>  $';
				break;
				case 'BNO':
					$str.='<a href="#"><strong>US Brent Oil</strong></a>  $';
				break;
			}
			$str.=''.number_format($row['last_trade'],'2','.',' ');
			if($row['change_realtime']>0){
				$str.='  <span class="marquee-up">'.number_format($row['change_realtime'],'2','.',' ').' '.$row['change_pct'].'%</span>';
			}
			else{
				$str.='  <span class="marquee-down">'.number_format($row['change_realtime'],'2','.',' ').' '.$row['change_pct'].'%</span>';
			}
			$str.=' <strong>|</strong> ';
		}
		//$str=substr($str,0,-3);
		return $str;
	}

	public static function getBestTraders(){
		$connexion=new Connexion_db();
		$req=$connexion->exec("SELECT id_trader,last_valuation FROM classement_traders ORDER BY last_valuation DESC LIMIT 0,25");
		$results=array();
		while($row=$req->fetch()){
			$trader=new Trader($row['id_trader']);
			$last_valuation=$row['last_valuation'];
			$results[]=array('trader'=>$trader,'last_valuation'=>$last_valuation);
		}
		return $results;
	}

	public static function getBestFirms(){
		$connexion=new Connexion_db();
		$req=$connexion->exec("SELECT id_firm,last_share_value FROM classement_firms ORDER BY last_share_value DESC LIMIT 0,25");
		$results=array();
		while($row=$req->fetch()){
			$firm=new Firm($row['id_firm']);
			$last_share_value=$row['last_share_value'];
			$results[]=array('firm'=>$firm,'last_share_value'=>$last_share_value);
		}
		return $results;
	}

	public static function getBestWeeklyTraders(){
		$connexion=new Connexion_db();
		$req=$connexion->exec("SELECT id_trader,performance_week FROM classement_traders ORDER BY performance_week DESC LIMIT 0,25");
		$results=array();
		while($row=$req->fetch()){
			$trader=new Trader($row['id_trader']);
			$performance_week=$row['performance_week'];
			$results[]=array('trader'=>$trader,'perf'=>$performance_week);
		}
		return $results;
	}

	public static function getBestWeeklyFirms(){
		$connexion=new Connexion_db();
		$req=$connexion->exec("SELECT id_firm,performance_week FROM classement_firms ORDER BY performance_week DESC LIMIT 0,25");
		$results=array();
		while($row=$req->fetch()){
			$firm=new Firm($row['id_firm']);
			$performance_week=$row['performance_week'];
			$results[]=array('firm'=>$firm,'perf'=>$performance_week);
		}
		return $results;
	}

	public static function getBestMonthlyTraders(){
		$connexion=new Connexion_db();
		$req=$connexion->exec("SELECT id_trader,performance_month FROM classement_traders ORDER BY performance_month DESC LIMIT 0,25");
		$results=array();
		while($row=$req->fetch()){
			$trader=new Trader($row['id_trader']);
			$performance_month=$row['performance_month'];
			$results[]=array('trader'=>$trader,'perf'=>$performance_month);
		}
		return $results;
	}

	public static function getBestMonthlyFirms(){
		$connexion=new Connexion_db();
		$req=$connexion->exec("SELECT id_firm,performance_month FROM classement_firms ORDER BY performance_month DESC LIMIT 0,25");
		$results=array();
		while($row=$req->fetch()){
			$firm=new Firm($row['id_firm']);
			$performance_month=$row['performance_month'];
			$results[]=array('firm'=>$firm,'perf'=>$performance_month);
		}
		return $results;
	}

	public static function sortTradersWeekly(){
		$sql="SELECT @row:=0; UPDATE classement_traders SET classement_week = (@row:=@row+1) ORDER BY performance_week DESC;";
		$connexion=new Connexion_db();
		$req=$connexion->prepare($sql);
		return $req->execute(array());
	}

	public static function sortTradersMonthly(){
		$sql="SELECT @row:=0; UPDATE classement_traders SET classement_month = (@row:=@row+1) ORDER BY performance_month DESC;";
		$connexion=new Connexion_db();
		$req=$connexion->prepare($sql);
		return $req->execute(array());
	}

    public static function createTrader($name){
    	$connexion=new Connexion_db();
    	$req=$connexion->prepare("INSERT INTO traders (name,cash,portfolio,notifications,id_broker,VAD,active,shares_firms) VALUES (:name,:cash,:portfolio,:notifications,:id_broker,:VAD,:active,:shares_firms)");
    	$res=$req->execute(array('name'=>$name,'cash'=>100000,'portfolio'=>'','notifications'=>'','id_broker'=>1,'VAD'=>1,'active'=>1,'shares_firms'=>''));
    	return $res;
    }

    public static function createBroker($name){
    	$connexion=new Connexion_db();
    	$req=$connexion->prepare("INSERT INTO brokers (name,tresorerie,frais_mins,frais,additional_fees,date_inscription,weekly_recette,notifications) VALUES (:name,:tresorerie,:frais_mins,:frais,:additional_fees,:date_inscription,:weekly_recette,:notifications)");
    	$res=$req->execute(array('name'=>$name,'tresorerie'=>100000,'frais_mins'=>'5','frais'=>'0.05','additional_fees'=>'0.03','date_inscription'=>date('Y-m-d H:i:s',time()),'weekly_recette'=>'0','notifications'=>''));
    	return $res;
    }

    public static function getRate($rate){
    	return 0.03;
    }

    public static function registerLoan($id,$mens,$nb){
    	return TRUE;
    }
}