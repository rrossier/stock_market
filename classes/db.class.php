<?php

//classes/db.class.php

class Connexion_db {
	protected $host;
	protected $database;
	protected $login;
	protected $password;
	protected $req;
	protected $bdd;

	public function __construct() {
		$this->host = DB_HOST;
		$this->database = DB_NAME;
		$this->login = DB_USER;
		$this->password = DB_PASSWORD;
		$this->bdd = null;
		//$this->connect();
	}

	public function connect() {
		if ($this->bdd == null) {
			try {
				$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
				$pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
				$this->bdd = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database, $this->login, $this->password, $pdo_options);

			} catch(Exception $e) {
				die('Erreur de connexion : ' . $e->getMessage());
			}
		}

		return $this->bdd;
	}

	public function beginTransaction(){
		return $this->bdd->beginTransaction();
	}
	public function commit(){
		return $this->bdd->commit();
	}
	public function rollBack(){
		return $this->bdd->rollBack();
	}
	public function exec($sql){
		$this->req = null;
		$bdd = $this->connect();
		$this->req = $bdd->prepare($sql);
		$this->req->execute(array());
		return $this->req;
	}
	public function load($class) {
		$answer = $this->doRequest($class::get_select_query());
		$return_array = array();

		// For each element
		while ($data = $answer->fetch()) {
			array_push($return_array, new $class($data));
		}
		
		return $return_array;

	}

	public function save($object) {
		// Check the class extends Model.
		$class = new ReflectionObject($object);
		if (!$class->isSubclassOf('Model')) {
			throw new InvalidArgumentException("Called serialize_data with a " . $class->getName() . " object, that doesn't inherit from the Model class.");
		}
		$this->prepare($object->get_insert_query());
		$this->execute($object->get_insert_array());
	}

	public function getLastInsert() {
		return $this->bdd->lastInsertId();
	}

	public function prepare($query) {
		$this->req = null;
		$bdd = $this->connect();
		$this->req = $bdd->prepare($query);
		return $this->req;
	}

	public function execute($anArray=array()) {
		if ($this->req != null) {
			$this->req->execute($anArray);
		}
	}

	public function fetchColumn() {
		if ($this->req != null) {
			$this->req->fetchColumn();
		}
	}

	public function fetch($param=PDO::FETCH_ASSOC) {
		if ($this->req != null) {
			$this->req->fetch($param);
		}
	}

	public function rowCount() {
		if ($this->req != null) {
			$this->req->rowCount();
		}
	}

	public function doRequest($req) {
		$bdd = $this->connect();
		$reponse = $bdd->query($req);
		return $reponse;
	}
}