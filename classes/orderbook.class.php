<?php

class Orderbook{
	
	private $orders;

	private static $_instance = null;

	private function __construct(){
		
	}

	public static function getInstance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new Orderbook();  
		}
		return self::$_instance;
	}

	public function getOrdersFromTrader($id_trader){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM orderbook_firms WHERE id_trader=:id_trader");
		$req->execute(array('id_trader'=>$id_trader));
		$this->orders=array();
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$this->orders[]=array('id'=>$row['id'],
							'position'=>$row['position'],
							'id_firm'=>$row['id_firm'],
							'quantity'=>$row['quantity'],
							'price'=>$row['price'],
							'id_trader'=>$row['id_trader'],
							'datetime'=>$row['datetime'],
							'status'=>$row['status']);
		}
		return $this->orders;
	}

	public function getBuyOrdersForFirm($id_firm){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM orderbook_firms WHERE id_firm=:id_firm AND position='buy' ORDER BY price DESC");
		$req->execute(array('id_firm'=>$id_firm));
		$this->orders=array();
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$this->orders[]=array('id'=>$row['id'],
							'position'=>$row['position'],
							'id_firm'=>$row['id_firm'],
							'quantity'=>$row['quantity'],
							'price'=>$row['price'],
							'id_trader'=>$row['id_trader'],
							'datetime'=>$row['datetime'],
							'status'=>$row['status']);
		}
		return $this->orders;
	}

	public function getSellOrdersForFirm($id_firm){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM orderbook_firms WHERE id_firm=:id_firm AND position='sell' ORDER BY price ASC");
		$req->execute(array('id_firm'=>$id_firm));
		$this->orders=array();
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$this->orders[]=array('id'=>$row['id'],
							'position'=>$row['position'],
							'id_firm'=>$row['id_firm'],
							'quantity'=>$row['quantity'],
							'price'=>$row['price'],
							'id_trader'=>$row['id_trader'],
							'datetime'=>$row['datetime'],
							'status'=>$row['status']);
		}
		return $this->orders;
	}

	public function deleteOrder($id_order){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("DELETE FROM orderbook_firms WHERE id=:id ");
		$req->execute(array('id'=>$id_order));
		
	}

	public function modifyOrder($id_order,$qty){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE orderbook_firms SET quantity=:qty WHERE id=:id");
		$req->execute(array('id'=>$id_order,'qty'=>$qty));
	}


}