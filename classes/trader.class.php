<?php
class Trader{
	private $id;
	private $name;
	private $cash;
	private $portfolio;
	private $notifications;
	private $id_broker;
	private $VAD;
	private $shares_firms;

	public function __construct($id=null)
	{
		if(isset($id)){
			$this->fill($id);
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
	
	public function all_object_properties() {
        return get_object_vars($this);
    }

    public function all_class_properties(){
    	return get_class_vars(__CLASS__);
    }

	public function reset(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE traders SET cash=100000, portfolio='', notifications='', id_broker=1 WHERE id=:id");
		$req->execute(array('id'=>$this->id));
		if($req){
			return 1;
		}
		return 0;
	}

	public function fill($id){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM traders WHERE id=:id");
		$req->execute(array("id"=>$id));
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$this->id=$row['id'];
			$this->name=$row['name'];
			$this->cash=$row['cash'];
			$this->id_broker=$row['id_broker'];
			$this->portfolio=(!empty($row['portfolio'])) ? unserialize($row['portfolio']) : array();
			$this->notifications=(!empty($row['notifications'])) ? unserialize($row['notifications']) : array();
			$this->shares_firms=(!empty($row['shares_firms'])) ? unserialize($row['shares_firms']) : array();
			$this->VAD=$row['VAD'];
		}
		$this->retrieveValuation();
	}

	public function save(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE traders SET name=:name, cash=:cash, portfolio=:portfolio,
			notifications=:notifications, id_broker=:id_broker, VAD=:VAD, shares_firms=:shares_firms WHERE id=:id");
		$res=$req->execute(array(
					'name'=>$this->name,
					'cash'=>round($this->cash,2),
					'portfolio'=>serialize($this->portfolio),
					'notifications'=>serialize($this->notifications),
					'id_broker'=>$this->id_broker,
					'VAD'=>$this->VAD,
					'shares_firms'=>serialize($this->shares_firms),
					'id'=>$this->id)
				);
		if($res){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function get_type(){
		return 'trader';
	}

	public function get_navbar(){
		include('../trader/navbar.php');
	}

	public function get_navbar_bottom(){
		include('../trader/navbar-bottom.php');
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
		$transaction->set_user($this);
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
		$transaction->set_user($this);
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
			if($this->VAD){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			if($this->portfolio[$key]['stock']->get_uuid()==$stock->get_uuid()){
				if($this->portfolio[$key]['quantity']>=$quantity){
					return true;
				}
				else{
					if($this->VAD){
						return true;
					}
					else{
						return false;
					}
				}
			}
		}
	}
	
	public function get_cash(){
		return $this->cash;
	}
	
	public function get_name(){
		return $this->name;
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

	public function receive_shares($quantity, $price, $uuid){
		$bool=false;
		if(!empty($this->shares)){
			foreach($this->shares as $i=>$line){
				if($line['uuid']==$uuid){
					$bool=true;
					if($this->shares[$i]['quantity']+$quantity==0){
						unset($this->shares[$i]);
						$this->shares=array_values($this->shares);
					}
					else{
						$nv_prix=($line['price']*$line['quantity']+($price*$quantity))/($line['quantity']+$quantity);
						$nv_prix=round($nv_prix,2);
						$this->shares[$i]['price']=$nv_prix;
						$this->shares[$i]['quantity']+=$quantity;
					}
				}
			}
			if(!$bool){
				$this->shares[]=array('uuid'=>$uuid,'quantity'=>$quantity,'price'=>$price);
			}
		}
		else{
			$this->shares[]=array('uuid'=>$uuid,'quantity'=>$quantity,'price'=>$price);
		}
	}

	public function add_waiting(Stock &$stock, $quantity, $mode){
		$this->waitings[]=array('mode'=>$mode,'stock'=>$stock,'quantity'=>$quantity);
	}

	public function process_waitings(){
		if(!empty($this->waitings)){
			$bool=true;
			foreach($this->waitings as $waiting){
				$transaction= new Transaction();
				$transaction->set_mode($waiting['mode']);
				$transaction->set_user($this);
				$transaction->set_stock($waiting['stock']);
				$transaction->set_quantity($waiting['quantity']);
				if(!$transaction->execute()){
					$bool=false;
				}
			}
			return $bool;
		}
		else{
			return 0;
		}
	}

	public function record_transaction(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("UPDATE traders SET cash=:cash, portfolio=:portfolio WHERE id=:id");
		$res=$req->execute(array('cash'=>round($this->cash,2),'portfolio'=>serialize($this->portfolio),'id'=>$this->id));
		if($res){
			return 1;
		}
		else{
			return 0;
		}
	}

	public function resume(){
		echo "Name : ".$this->name."<br/>";
		echo "Wallet : \$".$this->cash."<br/>";
		echo "Portfolio : <br/>";
		if(!empty($this->portfolio)){
			echo "<ul>";
			foreach($this->portfolio as $line){
				echo "<ol>".$line['stock']->get_name()." (".$line['quantity']."): \$".$line['stock']->get_price()."</ol>";
			}
			echo "</ul>";
		}
		else{
			echo "\t\tportfolio empty<br/>";
		}
		echo "<br/><br/>";
	}

	public function get_portfolio($hash=null){
		$str=null;
		$portfolio=$this->portfolio;
		$cash=$this->get_cash();
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
				$str.="<td><button>Sell</button></td></tr>";
			}
			$str.="<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
			$evolution=(round(100*$val_sum/$amt_sum-100,2));
			$class=($evolution>=0) ? 'up' : 'down';
			$class_globale=($global_evolution>=0) ? 'up' : 'down';
			$margin=round($val_sum-$amt_sum,2);
			$str.="<tr><td></td><td>".$qty_sum."</td><td></td>";
			$str.="<td></td><td>\$".number_format($val_sum,2,'.',' ')."</td><td>\$".$margin."</td><td class='".$class."'>".$evolution." %</td><td></td><td></td></tr>";
			$str.="<tr><td></td><td></td><td></td><td><strong>Cash:</strong></td><td><strong>\$".number_format($cash,2,'.',' ')."</strong></td><td></td><td></td><td></td><td></td></tr>";
			$str.="<tr><td></td><td></td><td></td><td><strong>Valuation:</strong></td><td><strong>\$".number_format(($cash+$val_sum),2,'.',' ')."</strong></td><td></td><td class='".$class_globale."'>".$global_evolution." %</td><td></td><td></td></tr>";
			$str.= "</table>";
		}
		else{
			$str.= "\t\tno portfolio<br/>";
		}
		return $str;
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

	public function precise_save($key,$value){
		$connexion=new Connexion_db();
		$properties=array_keys($this->all_object_properties());
		if(!in_array($key, $properties)){
			return 0;
		}
		else{
			$this->$key=$value;
			$sql="UPDATE traders SET ".$key."=:".$key." WHERE id=:id";
			$req=$connexion->prepare($sql);
			$res=$req->execute(array($key=>$value,'id'=>$this->id));
			return $res;
		}
	}

	public function get_historique(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM trades WHERE id_user=:id ORDER BY datetime DESC LIMIT 0,50");
		$req->execute(array('id'=>$this->id));
		$tab=array();
		while($row=$req->fetch(PDO::FETCH_ASSOC)){
			$tab[]=$row;
		}
		return $tab;
	}

	public function get_valuation(){
		$somme=$this->cash;
		$somme +=$this->portfolio_valuation();
		$somme +=$this->portfolio_firms_valuation();
		return $somme;
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

	public function create_firm($name,$nb_shares){
		$firm = new Firm();
		$result=$firm->register_new($name,$nb_shares,$this->id);
		if($result!==FALSE){
			if(array_sum($result) > $this->cash){
				return FALSE;
			}
			else{
				$fees=$result['fees'];
				Gamemaster::collect_fees('firm',$fees);
				$id_firm=$firm->register();
				$this->cash -= array_sum($result);
				$this->shares_firms[]=array('id_firm'=>$id_firm,'nb_shares'=>$nb_shares);
				if($this->save()){
					return $id_firm;
				}
				else{
					return FALSE;
				}
			}
		}
		else{
			return FALSE;
		}
	}

	public function join_firm($id,$nb_shares){ // !! different from buy_private_shares() !!
		$firm=new Firm($id);
		$amount= round($nb_shares*$firm->__get('share_value'),2);
		if($amount>$this->cash){
			return FALSE;
		}
		else{
			$this->cash -=$amount;
			$firm->add_private_shareholder($this->id,$nb_shares);
			$firm->save();
			$this->shares_firms[]=array('id_firm'=>$id,'nb_shares'=>$nb_shares);
			return $this->save();
		}
	}

	public function buy_private_shares($id_firm,$qty){
		if($this->has_firm($id_firm)){
			$firm=new Firm($id_firm);
			$amount= round($nb_shares*$firm->__get('share_value'),2);
			if($amount>$this->cash){
				return FALSE;
			}
			else{
				$this->cash -=$amount;
				foreach($this->shares_firms as $key=>$line){
					if($line['id_firm']==$id_firm){
						$this->shares_firms[$key]['nb_shares']+=$qty;
						$firm->add_private_shareholder($this->id,$nb_shares);
						$firm->save();
						return $this->save();
					}
				}
			}
		}
		else{
			return FALSE;
		}
	}

	public function sell_private_shares($id_firm,$qty){
		if($this->has_firm($id_firm)){
			foreach($this->shares_firms as $key=>$line){
				if($line['id_firm']==$id_firm){
					if($qty<$line['nb_shares']){
						$this->shares_firms[$key]['nb_shares'] -=$qty;
						$firm=new Firm($id_firm);
						$firm->shareholder_sells($this->id,$qty);
					}
					else{
						if($this->isUnique($id_firm)){
							// close the firm
							$firm=new Firm($id_firm);
							$amount= round($firm->share_value * $qty,2);
							$fees= round($amout * Gamemaster::getPctFeesSellShares(),2);
							$this->cash += ($amount - $fees);
							Gamemaster::collect_fees('firm',$fees);
							unset($this->shares_firms[$key]);
							$this->shares_firms=array_values($this->shares_firms);
							Gamemaster::deleteFirm($id_firm);
						}
						if($this->isManager($id_firm)){
							// must assign a new Manager before getting out
							return FALSE;
						}
						$qty=min($qty,$line['nb_shares']);
						unset($this->shares_firms[$key]);
						$this->shares_firms=array_values($this->shares_firms);
					}
					return $this->save();
				}
			}
			return FALSE;
		}
		else{
			return FALSE;
		}
	}

	public function isUnique($id_firm){
		if($this->has_firm($id_firm)){
			$firm=new Firm($id_firm);
			return (count($firm->id_private_shareholders)==1);
		}
		return FALSE;
	}

	public function portfolio_firms_valuation(){
		if(!empty($this->shares_firms)){
			$somme=0;
			$portfolio=$this->shares_firms;
			foreach($portfolio as $line){
				$firm=new Firm($line['id_firm']);
				$nb_shares=$line['nb_shares'];
				$share_value=$firm->get_share_value();
				$amount=$nb_shares*$share_value;
				$somme+=$amount;
			}
			return $somme;
		}else{
			return 0;
		}
	}

	public function display_portfolio_firms(){
		$str=null;
		$portfolio=$this->shares_firms;
		$cash=$this->get_cash();
		if(!empty($portfolio)){
			$str.= '<table class="table table-condensed">';
			$str.='<tr>';
			$str.='<th>Firm</th>';
			$str.='<th>Nb Shares</th>';
			$str.='<th>Share Value</th>';
			$str.='<th>Amount</th>';
			$str.='<th></th>';
			$str.='</tr>';
			$nb_sum=0;
			$amt_sum=0;
			foreach($portfolio as $line){
				$firm=new Firm($line['id_firm']);
				$nb_shares=$line['nb_shares'];
				$total_shares=$firm->__get('number_shares');
				$percent_shares=round($nb_shares/$total_shares*100,2);
				$share_value=$firm->get_share_value();
				$amount=$nb_shares*$share_value;
				$str.='<tr>';
				$str.='<td><a href="../firm/?id='.$firm->__get('id').'">'.$firm->get_name().'</a></td>';
				$str.='<td>'.number_format($nb_shares,0,'.',' ').' ('.number_format($percent_shares,'2','.',' ').'%)</td>';
				$str.='<td>'.number_format($share_value,2,'.',' ').'</td>';
				$str.='<td>'.number_format($amount,2,'.',' ').'</td>';
				$str.='<td><a href="#myModal" role="button" class="btn" data-toggle="modal" data-id="" data-name="">Place Order</a></td>';
				$str.='</tr>';
			}
			$str.= '</table>';
		}
		else{
			$str.= "\t\tNo investment made in firms<br/>";
		}
		return $str;
	}

	public function has_firm($id_firm){
		foreach ($this->shares_firms as $value) {
			if($id_firm==$value['id_firm']){
				return 1;
			}
		}
		return 0;
	}

	public function is_private_shareholder($id_firm){
		$firm=new Firm($id_firm);
		foreach ($firm->__get('id_private_shareholders') as $value) {
			if($this->id==$value['id']){
				return TRUE;
			}
		}
		return FALSE;
	}

	public function has_any_firm(){
		return (!empty($this->shares_firms));
	}

	public function has_power_from_firm($id_firm){
		return $this->isManager($id_firm);
		//return $tthis->hasMajority($id_firm);
	}

	public function hasMajority($id_firm){
		if($this->has_firm($id_firm)){
			$firm = new Firm($id_firm);
			return ($firm->has_majority($this->id));
		}
		return 0;
	}

	public function buy_public_share($id_firm,$nb_shares,$price){
		$orderbook=Orderbook::getInstance();
		$orders=$orderbook->getSellOrdersForFirm($id_firm);
		$completed=FALSE;
		if(!empty($orders)){
			foreach($orders as $order){
				$sell_price=$order['price'];
				$qty=$order['quantity'];
				$datetime=$order['datetime'];
				$expired = (($datetime+3600*24)>time()) ? true : false; 
				if($sell_price<=$price){
					if($qty>=$nb_shares){
						if(!expired){
							// execute whole trade
							$amount = $nb_shares*$sell_price;
							$broker=$this->get_broker();
							$fees=$broker->get_frais($amount);
							$total_amount=$amount+$fees;
							if($this->cash>=$total_amount){
								$this->cash -= $total_amount;
								$this->shares_firms[]=array('id_firm'=>$id,'nb_shares'=>$nb_shares);
								$this->save();
								return 1;
							}
							else{
								return 0;
							}
						}
						else{
							// delete expired trade
						}
					}
					else{
						// execute partially the trade
						$amount = $qty*$sell_price;
						$broker = $this->get_broker();
						$fees = $broker->get_frais($amount);
						$total_amount = $amount+$fees;
						if($this->cash>=$total_amount){
							$this->cash -= $total_amount;
							$this->shares_firms[]=array('id_firm'=>$id,'nb_shares'=>$nb_shares);
							$this->save();
							$nb_shares -=$qty;
						}
						else{
							return 0;
						}
					}
				}
			}
			if(!$completed){
				// insert order bdd
			}
		}
		else{
			// insert order bdd
		}
	}

	public function sell_shares($id_firm,$nb_shares,$price){

	}

	public function get_list_firms(){
		if(!empty($this->shares_firms)){
			$ids=array();
			foreach ($this->shares_firms as $line) {
				$firm=new Firm($line['id_firm']);
				$name=$firm->get_name();
				$ids[]=array('id_firm'=>$line['id_firm'],'name'=>$name);
			}
			return $ids;
		}
		else{
			return array();
		}
	}

	public function isManager($id_firm){
		$firm=new Firm($id_firm);
		$id_manager=$firm->id_manager;
		return ($this->id==$id_manager);
	}

	public function getNbNotifs(){
		if(!empty($this->notifications)){
			return count($this->notifications);
		}
		else{
			return FALSE;
		}
	}

	public function retrieve_email(){
		$connexion=new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM users WHERE id_user=:id AND type='trader' AND actif=1");
		$req->execute(array('id'=>$this->id));
		$row=$req->fetch();
		$email=$row['email'];
		return $email;
	}

	public function get_gravatar($s = 160, $d = 'identicon', $r = 'g', $img = false, $atts = array() ) {
		$email=$this->retrieve_email();
	    $url = 'http://www.gravatar.com/avatar/';
	    $url .= md5( strtolower( trim( $email ) ) );
	    $url .= "?s=$s&d=$d&r=$r";
	    if ( $img ) {
	        $url = '<img src="' . $url . '"';
	        foreach ( $atts as $key => $val )
	            $url .= ' ' . $key . '="' . $val . '"';
	        $url .= ' />';
	    }
	    return $url;
	}

	public function retrieve_classement(){
		$connexion = new Connexion_db();
		$req=$connexion->prepare("SELECT * FROM classement_traders WHERE id_trader=:id");
		$req->execute(array('id'=>$this->id));
		if($req->rowCount()>0){
			$results = array();
			$row = $req->fetch();
			return $row;
		}
		else{
			return FALSE;
		}
	}

	public function retrieveValuation(){
		$file="../data/traders/".$this->id.".txt";
		$handle=@file_get_contents($file);
		if($handle!==FALSE){
			$this->historique=unserialize($handle);
		}
		else{
			$this->historique=null;
		}
	}

	public function registerValuation(){
		$file="../data/traders/".$this->id.".txt";
		$historique=$this->retrieveValuation();
		$valuation=$this->get_valuation();
		$date=strtotime('Y-m-d',time());
		$new_historique = array('date'=>$date,'valuation'=>$valuation);
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
				return $this->historique[0]['valuation'];
			}
			else{
				foreach ($this->historique as $line) {
					$date_historique=strtotime($line['date']);
					if($date_historique==$date){
						return $line['valuation'];
					}
				}
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}

	public function computePerformance($date){
		if(!empty($this->historique)){
			$old_valuation=$this->getValuationByDate($date);
			if($old_valuation!==FALSE){
				$new_valuation=$this->get_valuation();
				$perf=round((($new_valuation-$old_valuation)/$old_valuation)*100,2);
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
		$valuation=$this->get_valuation();
		$perf_year=$this->getPerformance('year');
		$req=$connexion->prepare("UPDATE classement_traders SET last_valuation=:valuation, performance_year=:perf WHERE id_trader=:id");
		return $req->execute(array('id'=>$this->id,'valuation'=>$valuation,'perf'=>$perf));
	}

	public function updateClassementWeekly(){
		$connexion=new Connexion_db();
		$valuation=$this->get_valuation();
		$perf_year=$this->getPerformance('week');
		$req=$connexion->prepare("UPDATE classement_traders SET last_valuation=:valuation, performance_week=:perf WHERE id_trader=:id");
		return $req->execute(array('id'=>$this->id,'valuation'=>$valuation,'perf'=>$perf));
	}

	public function updateClassementMonthly(){
		$connexion=new Connexion_db();
		$valuation=$this->get_valuation();
		$perf_year=$this->getPerformance('month');
		$req=$connexion->prepare("UPDATE classement_traders SET last_valuation=:valuation, performance_month=:perf WHERE id_trader=:id");
		return $req->execute(array('id'=>$this->id,'valuation'=>$valuation,'perf'=>$perf));
	}
}