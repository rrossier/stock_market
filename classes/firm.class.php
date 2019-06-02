<?php

class Firm
{
	private $id;
	private $name;
	private $id_founder;
	private $id_manager;
	private $portfolio;
	private $cash;
	private $threshold;
	private $number_shares;
	private $share_value;
	private $orderbook_id;
	private $id_private_shareholders;
	private $id_broker;
	private $nb_shares_public;
	private $nb_shares_private;
	private $status;
	private $datetime;

	private $historique;

	public function __construct($id=null)
	{
		if(isset($id)){
			$this->fill($id);
		}
	}

	public function fill($id=null){
		if(isset($id)){
			$connexion = new Connexion_db();
			$req=$connexion->prepare("SELECT * FROM firms WHERE id=:id");
			$req->execute(array("id"=>$id));
			while($row=$req->fetch(PDO::FETCH_ASSOC)){
				$this->id=$id;
				$this->name=$row['name'];
				$this->id_founder=$row['id_founder'];
				$this->id_manager=$row['id_manager'];
				$this->portfolio=(!empty($row['portfolio'])) ? unserialize($row['portfolio']) : array();
				$this->cash=$row['cash'];
				$this->threshold=$row['threshold'];
				$this->number_shares=$row['number_shares'];
				$this->share_value=$row['share_value'];
				$this->orderbook_id=$row['orderbook_id'];
				$this->id_private_shareholders=(!empty($row['id_private_shareholders'])) ? unserialize($row['id_private_shareholders']) : array();
				$this->id_broker=$row['id_broker'];
				$this->nb_shares_public=$row['nb_shares_public'];
				$this->nb_shares_private=$row['nb_shares_private'];
				$this->status=$row['status'];
				$this->datetime=$row['datetime'];
			}
		}
		$this->retrieveValuation();
	}

	public function register(){
		$this->status='registered';
		$connexion = new Connexion_db();
		$req=$connexion->prepare("INSERT INTO firms(name,id_founder,id_manager,portfolio,cash,threshold,number_shares,share_value,orderbook_id,id_private_shareholders,id_broker,nb_shares_public,nb_shares_private,status,datetime) VALUES (:name,:id_founder,:id_manager,:portfolio,:cash,:threshold,:number_shares,:share_value,:orderbook_id,:id_private_shareholders,:id_broker,:nb_shares_public,:nb_shares_private,:status,:datetime)");
		$req->execute(array("name"=>$this->name,
							"id_founder"=>$this->id_founder,
							"id_manager"=>$this->id_manager,
							"portfolio"=>serialize($this->portfolio),
							"cash"=>$this->cash,
							"threshold"=>$this->threshold,
							"number_shares"=>$this->number_shares,
							"share_value"=>$this->share_value,
							"orderbook_id"=>$this->orderbook_id,
							"id_private_shareholders"=>serialize($this->id_private_shareholders),
							"id_broker"=>$this->id_broker,
							"nb_shares_public"=>$this->nb_shares_public,
							"nb_shares_private"=>$this->nb_shares_private,
							"status"=>$this->status,
							"datetime"=>time()));
		return $connexion->getLastInsert();
	}

	public function save(){
		$connexion = new Connexion_db();
		$req=$connexion->prepare("UPDATE firms SET name=:name,id_founder=:id_founder,id_manager=:id_manager,portfolio=:portfolio,cash=:cash,number_shares=:number_shares,share_value=:share_value,orderbook_id=:orderbook_id,id_private_shareholders=:id_private_shareholders,id_broker=:id_broker,nb_shares_public=:nb_shares_public,nb_shares_private=:nb_shares_private,status=:status WHERE id=:id");
		$req->execute(array("name"=>$this->name,
							"id_founder"=>$this->id_founder,
							"id_manager"=>$this->id_manager,
							"portfolio"=>serialize($this->portfolio),
							"cash"=>$this->cash,
							"threshold"=>$this->threshold,
							"number_shares"=>$this->number_shares,
							"share_value"=>$this->share_value,
							"orderbook_id"=>$this->orderbook_id,
							"id_private_shareholders"=>serialize($this->id_private_shareholders),
							"id_broker"=>$this->id_broker,
							"nb_shares_public"=>$this->nb_shares_public,
							"nb_shares_private"=>$this->nb_shares_private,
							"status"=>$this->status,
							"id"=>$this->id));
		if($req){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

    public function __set($name,$value){
      $this->$name=$value;
    }

    public function __get($name){
      return $this->$name;
    }
    
	public function get_cash(){
		return $this->cash;
	}

	public function get_name(){
		return $this->name;
	}

	public function get_type(){
		return 'firm';
	}

    public function isNameAvailable($name){
    	$connexion = new Connexion_db();
    	$req = $connexion->prepare("SELECT COUNT(*) as nb FROM firms WHERE name=:name");
    	$req->execute(array('name'=>$name));
    	$row=$req->fetch(PDO::FETCH_ASSOC);
    	return ($row['nb']==0);
    }

    public function getFounder(){
    	if(!empty($this->id_founder)){
    		$user= new Trader();
    		$user->fill($this->id_founder);
    		return $user;
    	}
    	else{

    	}
    }

    public function getManager(){
    	if(!empty($this->id_manager)){
    		$user= new Trader();
    		$user->fill($this->id_manager);
    		return $user;
    	}
    	else{

    	}
    }

    public function is_founder($id_trader){
    	return ($id_trader==$this->id_founder);
    }

    public function is_manager($id_trader){
    	return ($id_trader==$this->id_manager);
    }

    public function invite_member($id){
    	if(!empty($id)){

    	}
    }

    public function reevaluate($new_price){
		$this->share_value=$new_price;
	}

    public function add_private_shareholder($id,$nb_shares){
    	$found=false;
    	if(!empty($this->id_private_shareholders)){
    		foreach ($this->id_private_shareholders as $key=>$value) {
    			if($id==$value['id']){
    				$this->id_private_shareholders[$key]['nb_shares']+=$nb_shares;
    				$found=TRUE;
    			}
    		}
    		if(!$found){
    			$this->id_private_shareholders[]=array('id'=>$id,'nb_shares'=>$nb_shares);
    		}
    	}
    	else{
    		$this->id_private_shareholders[]=array('id'=>$id,'nb_shares'=>$nb_shares);
    	}
	    $this->nb_shares_private+=$nb_shares;
	    $this->number_shares+=$nb_shares;
	    $this->cash += $nb_shares*$this->share_value;
    }

    public function shareholder_sells($id_trader,$qty){
    	if($this->is_shareholder($id_trader)){
    		foreach ($this->id_private_shareholders as $key => $line) {
    			if($line['id']==$id_trader){
    				if($qty<$line['nb_shares']){
						$this->id_private_shareholders[$key]['nb_shares'] -=$qty;
					}
					else{
						unset($this->id_private_shareholders[$key]);
						$this->id_private_shareholders=array_values($this->id_private_shareholders);
					}
					return $this->save();
    			}
    		}
    	}
    	else{
    		return FALSE;
    	}
    }

    public function register_new($name,$nb_shares,$id_founder){
    	if($this->isNameAvailable($name)){
    		$this->share_value=PRICE_IPO;
			$this->name=$name;
			$this->id_founder=$id_founder;
			$this->id_manager=$id_founder;
	    	$this->id_private_shareholders[]=array('id'=>$id_founder,'nb_shares'=>$nb_shares);
	    	$this->nb_shares_private+=$nb_shares;
	    	$this->number_shares+=$nb_shares;
	    	$amount=$nb_shares*$this->share_value;
	    	$this->cash+=$amount;
	    	$this->threshold=50.01;
			$fees=round($amount*Gamemaster::getPctFeesFirmCreation(),2);
			$total= ($amount + $fees);
			$this->id_broker=1;
			$this->status='registering';
			return array('amount'=>$amount,'fees'=>$fees);
		}
		else{
			return FALSE;
		}
    }

    public function portfolio_valuation(){
		if(!empty($this->portfolio)){
			$somme=0;
			$portfolio=$this->portfolio;
			foreach($portfolio as $line){
				$st=clone $line['stock'];
				$st->get_last_values();
				$st->get_price_converted();
				$amount=($line['quantity']>0) ? $st->get_price()*$line['quantity'] : -$st->get_price()*$line['quantity'];
				$somme+=$amount;
			}
			return $somme;
		}else{
			return 0;
		}
	}

	public function get_valuation(){
		$somme=$this->cash;
		$somme +=$this->portfolio_valuation();
		return $somme;
	}

	public function compute_share_value(){
		$nb_shares=$this->number_shares;
		$portfolio_valuation=$this->portfolio_valuation();
		$cash=$this->cash;
		$share_value=($cash+$portfolio_valuation)/$nb_shares;
		$this->share_value=$share_value;
	}

	public function get_share_value(){
		$connexion = new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM firms WHERE id=:id");
		$req->execute(array("id"=>$this->id));
		$row=$req->fetch(PDO::FETCH_ASSOC);
		if(!empty($row['share_value'])){
			return $row['share_value'];
		}
		else{
			$this->compute_share_value();
			return $this->share_value;
		}
	}

	public function is_shareholder($id_trader){
		foreach ($this->id_private_shareholders as $value) {
			if($id_trader==$value['id']){
				return 1;
			}
		}
		return 0;
	}

	public function get_quotepart($id_trader){
		if($this->is_shareholder($id_trader)){
			foreach ($this->id_private_shareholders as $value) {
				if($id_trader==$value['id']){
					return (100*$value['nb_shares']/$this->number_shares);
				}
			}
		}
		return 0;
	}

	public function has_majority($id_trader){
		$quote_part=$this->get_quotepart($id_trader);
		return ($quote_part>$this->threshold);
	}

	public function get_broker(){
		if(!empty($this->id_broker)){
			$broker=new Broker();
			$broker->fill($this->id_broker);
		}
		else{
			$broker=new Broker();
			$broker->fill(1);
		}
		return $broker;
	}

	public function get_list_stocks(){
		$tab=array();
		if(!empty($this->portfolio)){
			foreach($this->portfolio as $line){
				$tab[]=$line['stock']->get_uuid();
			}
		}
		return $tab;
	}

	public function buy(Stock &$stock, $quantity){
		$transaction= new Transaction();
		$transaction->set_mode('buy');
		$transaction->set_firm($this);
		$transaction->set_stock($stock);
		$transaction->set_quantity($quantity);
		$broker=$this->get_broker();
		$transaction->set_broker($broker);
		if($transaction->execute()){
			//return "Success. Transaction correctly executed.<br/>";
			return 1;
		}
		else{
			//return "Error. Check your wallet.<br/>";
			return 0;
		}
	}

	public function sell(Stock &$stock, $quantity){
		$transaction= new Transaction();
		$transaction->set_mode('sell');
		$transaction->set_firm($this);
		$transaction->set_stock($stock);
		$transaction->set_quantity($quantity);
		$broker=$this->get_broker();
		$transaction->set_broker($broker);
		if($transaction->execute()){
			//return "Success. Transaction correctly executed.<br/>";
			return 1;
		}
		else{
			//return "Error. You can't sell that.<br/>";
			return 0;
		}
	}

	public function quantity_of_stock($uuid){
		$tab=$this->get_list_stocks();
		$key=array_search($uuid, $tab);
		if($key===FALSE){
			return 0;
		}
		else{
			return $this->portfolio[$key]['quantity'];
		}
	}

	public function has_stock(Stock $stock,$quantity=0){
		$tab=$this->get_list_stocks();
		$key=array_search($stock->get_uuid(), $tab);
		if($key===FALSE){
			return false;
		}
		else{
			if($this->portfolio[$key]['stock']->get_uuid()==$stock->get_uuid()){
				if($this->portfolio[$key]['quantity']>=$quantity){
					return true;
				}
				else{
					return false;
				}
			}
		}
	}

	public function give_cash($amount){
		if($this->cash<$amount){
			$this->cash=0;
		}
		else{
			$this->cash-=round($amount,2);
		}
	}

	public function receive_cash($amount){
		$this->cash+=round($amount,2);
	}

	public function give_stock(Stock &$stock,$quantity, $frais=0){
		if(!empty($this->portfolio)){
			$found=false;
			foreach($this->portfolio as $i=>$line){
				if($line['stock']->get_uuid()==$stock->get_uuid()){
					$found=true;
					if($this->portfolio[$i]['quantity']==$quantity){
						unset($this->portfolio[$i]);
						$this->portfolio=array_values($this->portfolio);
					}
					else{
						$nv_prix=($line['stock']->get_price()*$line['quantity']-($stock->get_price()*$quantity)+$frais)/($line['quantity']-$quantity);
						$nv_prix=round($nv_prix,5);
						$this->portfolio[$i]['stock']->reevaluate($nv_prix);
						$line['stock']->reevaluate($nv_prix);
						$this->portfolio[$i]['quantity']-=$quantity;
					}
				}
			}
			if(!$found){
				$this->portfolio[]=array('stock'=>$stock,'quantity'=>-$quantity);
			}
		}
		else{
			$this->portfolio[]=array('stock'=>$stock,'quantity'=>-$quantity);
		}
	}

	public function receive_stock(Stock &$stock, $quantity, $frais=0){
		$false=false;
		if(!empty($this->portfolio)){
			foreach($this->portfolio as $i=>$line){
				if($line['stock']->get_uuid()==$stock->get_uuid()){
					$false=true;
					if($this->portfolio[$i]['quantity']+$quantity==0){
						unset($this->portfolio[$i]);
						$this->portfolio=array_values($this->portfolio);
					}
					else{
						$nv_prix=($line['stock']->get_price()*$line['quantity']+$frais+($stock->get_price()*$quantity))/($line['quantity']+$quantity);
						$nv_prix=round($nv_prix,5);
						$this->portfolio[$i]['stock']->reevaluate($nv_prix);
						$line['stock']->reevaluate($nv_prix);
						$this->portfolio[$i]['quantity']+=$quantity;
					}
				}
			}
			if(!$false){
				$this->portfolio[]=array('stock'=>$stock,'quantity'=>$quantity);
				$nv_prix=round(($stock->get_price()*$quantity+$frais)/$quantity,5);
				$this->portfolio[count($this->portfolio)-1]['stock']->reevaluate($nv_prix);
			}
		}
		else{
			$this->portfolio[]=array('stock'=>$stock,'quantity'=>$quantity);
			$nv_prix=round(($stock->get_price()*$quantity+$frais)/$quantity,5);
			$this->portfolio[0]['stock']->reevaluate($nv_prix);
		}
	}

	public function get_portfolio(){
		$str=null;
		$portfolio=$this->portfolio;
		$cash=$this->cash;
		if(!empty($portfolio)){
			$str.= '<table class="table table-condensed"><tr><th>Stock</th><th>Quantity</th>';
			$str.= '<th>Historical Price</th><th>Current Price</th><th>Current Value</th><th>Change</th><th>Variation</th><th>Market</th><th>Sell</th></tr>';
			$qty_sum=0;
			$amt_sum=0;
			$val_sum=0;
			foreach($portfolio as $line){
				/*ULTRA IMPORTANT!
				*
				* clone is used in order to NOT update prices of stocks in the portfolio
				* these prices are used to get valuation!
				* 
				*/
				$st= clone $line['stock'];
				if($st->get_market()=='forex');
				$st->get_last_values();
				$qty=$line['quantity'];
				$qty_sum+=$qty;
				$oldprice=$line['stock']->get_price();
				$amountinvested=$qty*$oldprice;
				$amt_sum+=$amountinvested;
				$st->reevaluate($st->get_bid());
				$newprice=$st->get_price_converted();
				$currentamount=$qty*$newprice;
				$val_sum+=$currentamount;
				$evolution=round(100*$newprice/$oldprice-100,2);
				$global_evolution=round(100*($cash+$val_sum)/100000-100,2);
				$class=($newprice>=$oldprice) ? 'up' : 'down';
				$margin=round($newprice-$oldprice,4)*$qty;
				$open=$st->is_market_open();
				$str.= "<tr class='".$class."'><td><a href='../trader/stock.php?stock=".$st->get_uuid()."'>".$st->get_name()."</a></td>";
				$str.="<td>".number_format($qty,0,'.',' ')."</td>";
				$str.="<td>\$".number_format($oldprice,4,'.',' ')."</td>";
				$str.="<td>\$".number_format($newprice,4,'.',' ')."</td>";
				$str.="<td>\$".number_format($currentamount,2,'.',' ')."</td>";
				$str.="<td>\$".$margin."</td>";
				$str.="<td>".$evolution."%</td>";
				$str.="<td>".(($open)? 'Open' : 'Closed' ) ."</td>";
				$str.="</tr>";
			}
			$str.="<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
			$evolution=(round(100*$val_sum/$amt_sum-100,2));
			$class=($evolution>=0) ? 'up' : 'down';
			$class_globale=($global_evolution>=0) ? 'up' : 'down';
			$margin=round($val_sum-$amt_sum,2);
			$str.="<tr><td></td><td>".$qty_sum."</td><td></td>";
			$str.="<td></td><td>\$".number_format($val_sum,2,'.',' ')."</td><td>\$".$margin."</td><td class='".$class."'>".$evolution." %</td><td></td><td></td></tr>";
			$str.="<tr><td></td><td></td><td></td><td><strong>Cash:</strong></td><td><strong>\$".number_format($cash,2,'.',' ')."</strong></td><td></td><td></td><td></td><td></td></tr>";
			$str.="<tr><td></td><td></td><td></td><td><strong>Valuation:</strong></td><td><strong>\$".number_format(($cash+$val_sum),2,'.',' ')."</strong></td><td></td><td class='".$class_globale."'>".$global_evolution." %</td><td></td></tr>";
			$str.= "</table>";
		}
		else{
			$str.= "<strong>\t\tno portfolio invested in stocks</strong><br/>";
		}
		return $str;
	}

	public function record_transaction(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE firms SET cash=:cash, portfolio=:portfolio WHERE id=:id");
		$req->execute(array('cash'=>round($this->cash,2),'portfolio'=>serialize($this->portfolio),'id'=>$this->id));
		if($req){
			return 1;
		}
		else{
			return 0;
		}
	}

	public function getPendingRequests(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM pending_requests WHERE id_firm=:id_firm ORDER BY datetime DESC");
		$req->execute(array('id_firm'=>$this->id));
		$results=array();
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$results[]=array('id'=>$row['id'],'id_trader'=>$row['id_trader'],'datetime'=>$row['datetime'],'quantity'=>$row['quantity'],'id_firm'=>$row['id_firm']);
			
		}
		return $results;
	}

	public function isManager($id_trader){
		return ($this->id_manager==$id_trader);
	}

	public function isFounder($id_trader){
		return ($this->id_founder==$id_trader);
	}

	public function retrieveValuation(){
		$file="../data/firms/".$this->id.".txt";
		$handle=@file_get_contents($file);
		if($handle!==FALSE){
			$this->historique=unserialize($handle);
		}
		else{
			$this->historique=null;
		}
	}

	public function registerValuation(){
		$file="../data/firms/".$this->id.".txt";
		$historique=$this->retrieveValuation();
		$share_value=$this->get_share_value();
		$date=strtotime('Y-m-d',time());
		$new_historique = array('date'=>$date,'share_value'=>$share_value);
		$this->historique[]=$new_historique;
		$result=file_put_contents($file,serialize($this->historique));
		if($result!==FALSE){
			return TRUE;
		}
		return FALSE;
	}

	public function getValuationByDate($date){
		if(!empty($this->historique)){
			$first_date=strtotime($this->historique[0]['date']);
			if($first_date>$date){
				return $this->historique[0]['share_value'];
			}
			else{
				foreach ($this->historique as $line) {
					$date_historique=strtotime($line['date']);
					if($date_historique==$date){
						return $line['share_value'];
					}
				}
				return FALSE;
			}
		}
		else{
			return 0;
		}
	}

	public function computePerformance($date){
		if(!empty($this->historique)){
			$old_share_value=$this->getValuationByDate($date);
			if($old_share_value!==FALSE){
				$new_share_value=$this->get_share_value();
				$perf=round((($new_share_value-$old_share_value)/$old_share_value)*100,2);
				return $perf;
			}
			return 0;
		}
		return 0;
	}

	public function getPerformance($period="week"){
			$m=date('m',time());
			$d=date('d',time());
			$y=date('Y',time());
		switch ($period) {
			default:
			case 'week':
				$old_date=strtotime('last sunday',time());
				break;
			
			case 'month':
				$old_date=mktime(0,0,0,$m,1,$y);
				break;
			
			case 'year':
				$old_date=mktime(0,0,0,1,1,$y);
				break;
		}
		$perf=$this->computePerformance($old_date);
		return $perf;
	}

	public function updateClassementDaily(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE classement_firms SET last_share_value=:share_value, performance_year=:perf WHERE id_firm=:id");
		$share_value=$this->get_share_value();
		$perf_year=$this->getPerformance('year');
		$req->execute(array('id'=>$this->id,'share_value'=>$share_value,'perf'=>$perf));
		return $req;
	}

	public function updateClassementWeekly(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE classement_firms SET last_share_value=:share_value, performance_week=:perf WHERE id_firm=:id");
		$share_value=$this->get_share_value();
		$perf_year=$this->getPerformance('week');
		$req->execute(array('id'=>$this->id,'share_value'=>$share_value,'perf'=>$perf));
		return $req;
	}

	public function updateClassementMonthly(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE classement_firms SET last_share_value=:share_value, performance_month=:perf WHERE id_firm=:id");
		$share_value=$this->get_share_value();
		$perf_year=$this->getPerformance('month');
		$req->execute(array('id'=>$this->id,'share_value'=>$share_value,'perf'=>$perf));
		return $req;
	}

	public function pay_taxes(){
		$old_date=strtotime('last sunday',time());
		$old_share_value=$this->getValuationByDate($old_date);
		$new_share_value=$this->get_share_value();
		$benefices=round(max($new_share_value-$old_share_value,0)*$this->nb_shares,3);
		if($benefices>0){
			$pct_taxes=Gamemaster::getPctFeesFirmWeekly();
			$taxes=round($benefices*$pct_taxes,2);
			if($taxes>200){
				$this->give_cash($taxes);
			}
			else{
				$this->give_cash(200);
			}
			if($this->save()){
				return $frais;
			}
			else{
				return FALSE;
			}
		}
		return 0;
	}

	public function getWaitingOrders(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM orderbook_stocks WHERE id_firm=:id");
		$req->execute(array('id'=>$this->id));
		$results=array();
		while($row=$req->fetch()){
			$results[]=$row;
		}
		return $results;
	}
}
?>