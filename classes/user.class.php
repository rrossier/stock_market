<?php
class User{
	private $id;
	private $email;
	private $mdp;
	private $type;
	private $id_user;
	private $authenticated;

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
	public function authenticate($pwd){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM users WHERE email=:email AND actif=1");
		$req->execute(array('email'=>$this->email));
		$row=$req->fetch(PDO::FETCH_ASSOC);
		//$bcrypt = new Bcrypt(15);
		//$isGood = $bcrypt->verify($pwd, $row['mdp']);
		$isGood=password_verify($pwd,$row['mdp']);
		if($isGood==1){
			$this->authenticated=true;
			$this->type=$row['type'];
			$this->id_user=$row['id_user'];
			return true;
		}
		else{
			$this->authenticated=false;
			return false;
		}
	}
	public function get_type(){
		return $this->type;
	}

	public function fill($id){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM users WHERE id=:id");
		$req->execute(array("id"=>$id));
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$this->email=$row['email'];
			$this->mdp=$row['mdp'];
			$this->type=$row['type'];
			$this->id_user=$row['id_user'];
		}
	}

	public function create(){
		switch($this->type){
			case 'trader':
			$temp=new Trader();
			$temp->fill($this->id_user);
			break;

			case 'broker':
			$temp=new Broker();
			$temp->fill($this->id_user);
			break;

			default:
			$temp=new Trader();
			$temp->fill($this->id_user);
		}
		return $temp;
	}

	public function save(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE users SET mdp=:mdp WHERE id=:id");
		$req->execute(array('mdp'=>$this->mdp,'id'=>$this->id)
				);
		if($req){
			return 1;
		}
		else{
			return 0;
		}
	}

}