<?php

class Stock{
	private $uuid;
	private $name;
	private $price;
	private $bid;
	private $ask;
	private $change;
	private $volume;
	private $market;
	private $currency;

	public function __construct($tab=array()){
		foreach($tab as $key=>$value){
			$this->$key=$value;
		}
	}
	public function __set($property,$value)
	{
		$this->$property=$value;
	}
	public function __get($property)
	{
		return $this->$property;
	}
	public function get_price(){
		return $this->price;
	}
	public function get_bid(){
		return $this->bid;
	}
	public function get_ask(){
		return $this->ask;
	}
	public function get_name(){
		if(!empty($this->name)){
			return $this->name;
		}
		$this->retrieve_name();
		return $this->name;
	}
	public function get_uuid(){
		return $this->uuid;
	}
	public function reevaluate($new_price){
		$this->price=$new_price;
	}
	public function doesExist(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM stocks WHERE ticker=:ticker");
		$req->execute(array('ticker'=>$this->uuid));
		if($req->rowCount()>0){
			return 1;
		}
		else{
			return 0;
		}
	}
	public function get_last_values(){
		if(!empty($this->uuid)){
			$connexion=new Connexion_db();
			if($this->get_market()===FALSE){
				return 0;
			}
			$sql="SELECT * FROM ".$this->market."_temp WHERE symbol=:uuid";
			$req=$connexion->prepare($sql);
			$req->execute(array('uuid'=>$this->uuid));
			while($tab=$req->fetch()){
				$this->name=$tab['name'];
				$this->ask=($tab['ask_realtime']===null || $tab['ask_realtime']=='N/A') ? (($tab['ask']===null || $tab['ask']=='N/A') ? 0 : $tab['ask']) : $tab['ask_realtime'];
				$this->bid=($tab['bid_realtime']===null || $tab['bid_realtime']=='N/A') ? (($tab['bid']===null || $tab['bid']=='N/A') ? 0 : $tab['bid']) : $tab['bid_realtime'];
				$this->ask=($this->ask==0) ? (($this->bid==0) ? $tab['last_trade'] : $this->bid) : $this->ask;
				$this->bid=($this->bid==0) ? (($this->ask==0) ? $tab['last_trade'] : $this->ask) : $this->bid;
				if($this->market!='forex'){
					$this->change=($tab['change_realtime']==null) ? 0 : $tab['change_realtime'];
					$this->volume=($tab['volume']==null) ? 0 : $tab['volume'];
				}
				$this->price=round(($this->bid+$this->ask)/2,2);
			}
			if($this->price==0){
				return 0;
			}
			else{
				return 1;
			}
		}
		else{
			return 0;
		}
	}

	public function retrieve_name(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM stocks WHERE ticker=:ticker");
		$req->execute(array('ticker'=>$this->uuid));
		$row=$req->fetch(PDO::FETCH_ASSOC);
		$name=$row['name'];
		$this->name=$name;
		return $name;
	}

	public function get_market(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM stocks WHERE ticker=:ticker");
		$req->execute(array('ticker'=>$this->uuid));
		if($req->rowCount()>0){
			$row=$req->fetch(PDO::FETCH_ASSOC);
			$market=$row['market'];
			$this->market=$market;
		}
		else{
			return FALSE;
		}
		return $market;
	}

	public function load_historical_data(){
		if(!empty($this->uuid)){
			$target="../historicaldata/".$this->uuid.".csv";
			if($handle = @fopen($target, "r")){
				$val=array();
				while($data = fgetcsv($handle, 4096, ',')){
		    //var_dump($data);
		    		$val[]=$data;
				}
				fclose($handle);
				return $val;
			}
			else{
				return 0;
			}
		}
		else{
			return 0;
		}
	}

	public function is_market_open(){
		$this->get_market();
		switch($this->market){
			case 'srd':
			$open=Market::is_open('paris');
			break;

			case 'forex':
			$open=true;
			break;

			case 'london':
			$open=Market::is_open('london');
			break;

			case 'hong-kong':
			$open=Market::is_open('hong-kong');
			break;

			default:
			$open=Market::is_open('new-york');
		}
		return $open;
	}

	public function get_currency(){
		if(empty($this->market)){
			$this->get_market();
		}
		switch($this->market){
			case 'sbf120':
			$this->currency='euro';
			$symbol="€";
			break;

			case 'forex':
			$this->currency='dollar';
			$symbol="$";
			break;

			case 'london':
			$this->currency='pound';
			$symbol="£";
			break;

			case 'hong-kong':
			$this->currency='hkd';
			$symbol="$";
			break;

			default:
			$this->currency='dollar';
			$symbol="$";
		}
		return $symbol;
	}

	public function get_price_converted(){
		if(empty($this->currency)){
			$this->get_currency();
		}
		if(empty($this->price)){
			throw new Exception('Pas de prix fixé.');
		}
		switch($this->currency){
			case 'euro':
				$change=get_change('EURUSD=X');
			break;

			case 'pound':
				$change=get_change('GBPUSD=X');
			break;

			case 'hkd':
				$change=get_change('HKDUSD=X');
			break;

			default:
			return $this->price;
		}
		$this->price=$this->price*$change;
		return($this->price);
	}
}