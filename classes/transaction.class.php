<?php

class Transaction{
	private $user;
	private $firm;
	private $mode;
	private $stock;
	private $quantity;
	private $broker;

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

	public function set_user(Trader &$user){
		$this->user=$user;
	}

	public function set_firm(Firm &$firm){
		$this->firm=$firm;
	}

	public function set_mode($mode='buy'){
		$this->mode=$mode;
	}
	public function set_quantity($quantity=1){
		$this->quantity=$quantity;
	}

	public function set_stock(Stock &$stock){
		$this->stock=$stock;
	}

	public function set_broker(Broker &$broker){
		$this->broker=$broker;
	}

	public function execute(){
		if(!empty($this->user) && !empty($this->stock) && !empty($this->quantity) && !empty($this->broker)){
			//Ultra important! récupérer directement le cours
			//plutôt que de le passer en paramètre!!!
			if(!$this->stock->get_last_values()){
				return 0;
			}
			if($this->mode=='buy'){
				$this->stock->reevaluate($this->stock->get_ask());
				$amount=$this->stock->get_price_converted()*$this->quantity;
				$frais=$this->broker->get_frais($amount);
				if(($amount+$frais)>$this->user->get_cash()){
					return 0;
				}
				$this->user->give_cash($amount+$frais);
				$this->broker->receive_cash($frais);
				$this->broker->save();
				$this->user->receive_stock($this->stock,$this->quantity,$frais);
				return $this->record();
			}
			else if($this->mode=='sell'){
				if(!$this->user->has_stock($this->stock,$this->quantity)){
					return 0;
				}
				$this->stock->reevaluate($this->stock->get_bid());
				$amount=$this->stock->get_price_converted()*$this->quantity;
				$frais=$this->broker->get_frais($amount);
				$this->user->give_stock($this->stock,$this->quantity, $frais);
				$this->broker->receive_cash($frais);
				$this->broker->save();
				$this->user->receive_cash($amount-$frais);
				return $this->record();
			}
		}
		else{
			return 0;
		}
	}

	public function execute_firm(){
		if(!empty($this->firm) && !empty($this->stock) && !empty($this->quantity) && !empty($this->broker)){
			//Ultra important! récupérer directement le cours
			//plutôt que de le passer en paramètre!!!
			if(!$this->stock->get_last_values()){
				return 0;
			}
			if($this->mode=='buy'){
				$this->stock->reevaluate($this->stock->get_ask());
				$amount=$this->stock->get_price_converted()*$this->quantity;
				$frais=$this->broker->get_frais($amount);
				if(($amount+$frais)>$this->firm->get_cash()){
					return 0;
				}
				$this->firm->give_cash($amount+$frais);
				$this->broker->receive_cash($frais);
				$this->broker->save();
				$this->firm->receive_stock($this->stock,$this->quantity,$frais);
				return $this->record_firm();
			}
			else if($this->mode=='sell'){
				if(!$this->firm->has_stock($this->stock,$this->quantity)){
					return 0;
				}
				$this->stock->reevaluate($this->stock->get_bid());
				$amount=$this->stock->get_price_converted()*$this->quantity;
				$frais=$this->broker->get_frais($amount);
				$this->firm->give_stock($this->stock,$this->quantity, $frais);
				$this->broker->receive_cash($frais);
				$this->broker->save();
				$this->firm->receive_cash($amount-$frais);
				return $this->record_firm();
			}
		}
		else{
			return 0;
		}
	}

	public function record(){
		$connexion=new Connexion_db();
		$connexion->beginTransaction();
		$req2=$connexion->prepare("INSERT INTO trades (uuid_stock, id_user, price, quantity, datetime, id_broker) VALUES (:uuid, :id, :price, :qty, :datetime, :id_broker) ");
		$stock=$this->stock;
		date_default_timezone_set ('America/New_York');
		$datetime=date('Y-m-d H:i:s');
		$qty=($this->mode=='buy') ? (+$this->quantity) : (-$this->quantity);
		$req2->execute(array('uuid'=>$stock->get_uuid(),'id'=>$this->user->__get('id'),'price'=>$stock->get_price(),'qty'=>$qty,'datetime'=>$datetime,'id_broker'=>$this->broker->__get('id')));
		
		if(!$this->user->record_transaction()){
			$connexion->rollBack();
			return 0;
		}
		if(!$this->broker->record_transaction()){
			$connexion->rollBack();
			return 0;
		}
		$connexion->commit();
		return 1;
	}

	public function record_firm(){
		$connexion=new Connexion_db();
		$connexion->beginTransaction();
		$req2=$connexion->prepare("INSERT INTO trades (uuid_stock, id_user, price, quantity, datetime, id_broker) VALUES (:uuid, :id, :price, :qty, :datetime, :id_broker) ");
		$stock=$this->stock;
		date_default_timezone_set ('America/New_York');
		$datetime=date('Y-m-d H:i:s');
		$qty=($this->mode=='buy') ? (+$this->quantity) : (-$this->quantity);
		$req2->execute(array('uuid'=>$stock->get_uuid(),'id'=>$this->firm->__get('id'),'price'=>$stock->get_price(),'qty'=>$qty,'datetime'=>$datetime,'id_broker'=>$this->broker->__get('id')));
		
		if(!$this->firm->record_transaction()){
			$connexion->rollBack();
			return 0;
		}
		if(!$this->broker->record_transaction()){
			$connexion->rollBack();
			return 0;
		}
		$connexion->commit();
		return 1;
	}
}

?>