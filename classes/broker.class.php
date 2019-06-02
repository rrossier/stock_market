<?php
class Broker{
	private $id;
	private $name;
	private $tresorerie;
	private $frais_min;
	private $frais;
	private $additional_fees;
	private $date_inscription;
	private $weekly_recette;
	private $notifications;

	private $markets;
	private $clients;
	private $recette;

	public function __construct($id=null){
		if(!is_null($id)){
			$this->id=$id;
			$this->fill($this->id);
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
	public function reset(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE brokers SET tresorerie=100000, frais_min=5, frais=0.04, frais=0.005, weekly_recette=0, notifications='' WHERE id=:id");
		$req->execute(array('id'=>$this->id));
		if($req){
			return 1;
		}
		return 0;
	}
	public function fill($id){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM brokers WHERE id=:id");
		$req->execute(array("id"=>$id));
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$this->id=$row['id'];
			$this->name=$row['name'];
			$this->tresorerie=$row['tresorerie'];
			$this->frais_min=$row['frais_min'];
			$this->frais=$row['frais'];
			$this->additional_fees=$row['additional_fees'];
			$this->date_inscription=$row['date_inscription'];
			$this->weekly_recette=$row['weekly_recette'];
			$this->notifications=unserialize($row['notifications']);
		}
	}
	public function get_type(){
		return 'broker';
	}

	public function get_cash(){
		return $this->tresorerie;
	}

	public function get_navbar(){
		include('../broker/navbar.php');
	}

	public function get_navbar_bottom(){
		echo '';
	}

	public function get_tresorerie(){
		return $this->tresorerie;
	}

	public function get_frais($amount){
		$min=$this->frais_min;
		$montant=$amount*($this->frais);
		if($montant>$min){
			return $montant;
		}
		else{
			return $min;
		}
	}

	public function pay_taxes(){
		$weekly_recette=$this->weekly_recette;
		$pct_taxes=Gamemaster::getPctFeesBrokerWeekly();
		$taxes=round($weekly_recette*$pct_taxes,2);
		if($taxes>200){
			$this->give_cash($taxes);
		}
		else{
			$this->give_cash(200);
		}
		$this->weekly_recette=0;
		if($this->save()){
			return $frais;
		}
		else{
			return FALSE;
		}
	}

	public function give_cash($amount){
		if($this->tresorerie<$amount){
			$this->tresorerie=0;
		}
		else{
			$this->tresorerie-=round($amount,2);
		}
	}
	public function receive_cash($amount){
		$amount=round($amount,3);
		$this->tresorerie+=$amount;
		$this->weekly_recette+=$amount;
	}
	public function record_transaction(){
		$connexion= new Connexion_db();
		$req=$connexion->prepare("UPDATE brokers SET tresorerie=tresorerie+:recette, weekly_recette=:recette WHERE id=:id");
		$req->execute(array('recette'=>round($this->recette,2),'id'=>$this->id));
		return $req;
	}

	public function save(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE brokers SET name=:name, tresorerie=:tresorerie, frais_min=:frais_min, weekly_recette=:weekly_recette, frais=:frais, additional_fees=:additional_fees WHERE id=:id");
		$req->execute(array(
					'name'=>$this->name,
					'tresorerie'=>round($this->tresorerie,2),
					'frais_min'=>$this->frais_min,
					'weekly_recette'=>$this->weekly_recette,
					'frais'=>$this->frais,
					'additional_fees'=>$this->additional_fees,
					'id'=>$this->id,
					'notifications'=>serialize($this->notifications))
				);
		if($req){
			return 1;
		}
		else{
			return 0;
		}
	}

	public function get_historique(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM trades WHERE id_broker=:id ORDER BY datetime DESC LIMIT 0,50");
		$req->execute(array('id'=>$this->id));
		$tab=array();
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$tab[]=$row;
		}
		return $tab;
	}

	public function display_page($page,$dossier=null){
		if(is_null($dossier)){
			$url=racine().$page.'.php';
		}
		else{
			$url=racine().$dossier.'/'.$page.'.php';
		}
		
		include($url);
	}

	public function add_notification($type='info',$text){
		$id=$this->getNbNotifs()+1;
		$notif='<div class="alert alert-'.$type.'">';
		$notif.='<button type="button" class="close" data-dismiss="alert" data-id="'.$id.'">&times;</button>';
		$notif.=$text.'</div>';
		$this->notifications[]=array('id'=>$id,'notif'=>$notif);
	}

	public function remove_notification($id){
		if(!empty($this->notifications)){
			foreach($this->notifications as $i=>$notification){
				if($notification['id']==$id){
					unset($this->notifications[$i]);
					$this->notifications=array_values($this->notifications);
					return $this->save();
				}
			}
		}
	}

	public function get_list_notifications(){
		return $this->notifications;
	}

	public function getNbNotifs(){
		if(!empty($this->notifications)){
			return count($this->notifications);
		}
		else{
			return 0;
		}
	}
}