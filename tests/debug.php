<?php
include('header.php');

Class Firm{

    private $id;
    private $name;
    private $capital;
    private $portfolio;
    private $junior_associates;
    private $senior_associates;
    private $junior_partners;
    private $senior_partners;
    private $managing_partner;
    private $rules;

    public function __construct(){
      $this->use_classic_rules();
    }

    public function __set($name,$value){
      $this->$name=$value;
    }

    public function __get($name){
      return $this->$name;
    }

    public function nominate($rank,$id_user){
      switch($rank){
        case 'junior-associate':
        $this->junior_associates[]=$id_user;
        break;
        
        case 'senior-associate':
        $this->senior_associates[]=$id_user;
        break;
        
        case 'junior-partner':
        $this->junior_partners[]=$id_user;
        break;
        
        case 'senior-partner':
        $this->senior_partners[]=$id_user;
        break;
        
        case 'managing_partner':
        $this->managing_partner[]=$id_user;
        break;

        default:
      }
      return 1;
    }

    public function demote($id_user){
      $done=false;
      switch($this->get_rank($id_user)){

        case 'junior-associate':
        $i=array_search($id_user, $this->junior_associates);
        if($i!==FALSE){
          unset($this->junior_associates[$i]);
          $this->junior_associates=array_values($this->junior_associates);
          $done=true;
        }
        break;
        
        case 'senior-associate':
        $i=array_search($id_user, $this->senior_associates);
        if($i!==FALSE){
          unset($this->senior_associates[$i]);
          $this->senior_associates=array_values($this->senior_associates);
          $done=true;
        }
        break;

        case 'junior-partner':
        $i=array_search($id_user, $this->junior_partners);
        if($i!==FALSE){
          unset($this->junior_partners[$i]);
          $this->junior_partners=array_values($this->junior_partners);
          $done=true;
        }
        break;
        
        case 'senior-partner':
        $i=array_search($id_user, $this->senior_partners);
        if($i!==FALSE){
          unset($this->senior_partners[$i]);
          $this->senior_partners=array_values($this->senior_partners);
          $done=true;
        }
        break;

        case 'managing-partner':
        if($this->managing_partner==$id_user){
          $this->managing_partner=null;
          $done=true;
        }
        break;
      }

      return $done;
    }

    public function hire($rank='junior-associate',$id_user){
      if($this->nominate($rank,$id_user)){
        return 1;
      }
      else{
        return 0;
      }
    }

    public function fire($id_user){
      $this->demote($id_user);
    }

    public function get_rank($id_user){
      if(!isset($this->managing_partner)){
        return FALSE;
      }
      else{
        $found=FALSE;
        if(in_array($id_user, $junior_associates)){
          $found=TRUE;
          $rank='junior-associate';
        }
        elseif(in_array($id_user, $senior_associates)){
          $found=TRUE;
          $rank='senior-associate';
        }
        elseif(in_array($id_user, $junior_partners)){
          $found=TRUE;
          $rank='junior-partner';
        }
        elseif(in_array($id_user, $senior_partners)){
          $found=TRUE;
          $rank='senior-partner';
        }
        elseif(in_array($id_user, $senior_associates)){
          $found=TRUE;
          $rank='managing-partner';
        }
        if($found){
          return $rank;
        }
        else{
          return FALSE;
        }
      }
    }

    public function get_position($id_user){
      $tab=explode('-',$this->get_rank($id_user));
      return $tab[1];
    }

    public function get_number_associates($rank='all'){
      switch($rank){
        case 'junior':
        return count($this->junior_associates);
        break;

        case 'senior':
        return count($this->senior_associates);
        break;
        
        case 'all':
        $juniors=count($this->junior_associates);
        $seniors=count($this->senior_associates);
        return $juniors+$seniors;
        break;
      }
    }

    public function get_number_partners($rank='all'){
      switch($rank){
        case 'junior':
        return count($this->junior_partners);
        break;

        case 'senior':
        return count($this->senior_partners);
        break;
        
        case 'all':
        $juniors=count($this->junior_partners);
        $seniors=count($this->senior_partners);
        return $juniors+$seniors;
        break;
      }
    }

    public function get_funds_authorized($id_user){
      if(!isset($this->managing_partner)){
        return FALSE;
      }
      else{
        switch($this->get_position($id_user)){
          case 'partner':
          return $this->get_all_funds();
          break;

          case 'associate':
          $funds_allowed=$this->get_remaining_funds_associates();
          if($this->get_rank($id_user)=='junior-associate'){

          }
          break;
        }
      }
    }

    public function retrieve_last_valuation(){
      $connexion=new Connexion_db();
      $req=$connexion->prepare("SELECT * FROM firms WHERE id=:id");
      $req->execute(array('id'=>$this->id));
      $row=$req->fetch(PDO::FETCH_ASSOC);
      return $row['last_valuation'];
    }

    public function get_all_funds(){
      $this->retrieve_last_valuation();
    }

    public function get_funds_allowed_associates(){
      $percentage=$this->get_rule('percentage_associates');
      return $percentage*$this->get_all_funds();
    }

    public function get_remaining_funds_associates(){
      return $this->get_funds_allowed_associates()-$this->get_funds_engaged_associates();
    }

    public function get_funds_engaged($id_user){
      $amount=0;
      foreach($this->portfolio as $line){
        if($line->get_trader->get_id()==$id_user){
          $amount+=$line->get_amount();
        }
      }
      return $amount;
    }

    public function get_funds_engaged_associates(){
      $amnt=0;
      foreach($this->junior_associates as $associate){
        $amnt+=$this->get_funds_engaged($associate);
      }
      foreach($this->senior_associates as $associate){
        $amnt+=$this->get_funds_engaged($associate);
      }
      return $amnt;
    }

    public function get_funds_engaged_partners(){
      $amnt=0;
      foreach($this->junior_partners as $partner){
        $amnt+=$this->get_funds_engaged($partner);
      }
      foreach($this->senior_partners as $partner){
        $amnt+=$this->get_funds_engaged($associate);
      }
      return $amnt;
    }

    public function use_classic_rules(){
      $this->rules['percentage_associates']=0.3;
      $this->rules['percentage_juniors']=0.3;
    }

    public function set_rule($rule,$value){
      if($rule=='percentage_associates'){
        if($value<=1 && $value>=0){
          $this->rules['percentage_associates']=$value;
          return TRUE;
        }
      }
      elseif($rule=='percentage_juniors'){
        if($value<=1 && $value>=0){
          $this->rules['percentage_juniors']=$value;
          return TRUE;
        }
      }
      else{
        return FALSE;
      }
    }

    public function get_rule($rule){
      if($rule=='percentage_associates'){
        return $this->rules['percentage_associates'];
      }
      elseif($rule=='percentage_juniors'){
        return $this->rules['percentage_juniors'];
      }
      else{
        return FALSE;
      }
    }

    public function get_number_shares(){
      return $this->number_shares;
    }

    public function __shares($nb){
      $this->number_shares+=$nb;
    }

    public function get_price_share(){
      return $this->price_share;
    }

    public function reevaluate_price_share($new_price){
      $this->price_share=$new_price;
    }

    public function number_shares_for_price($amount){
      return ceil($amount/$this->price_share);
    }
}

class Employee{
  private $id;
  private $id_trader;
  private $firm;
  private $shares;
  private $rank;

  public function __set($name,$value){
    $this->$name=$value;
  }

  public function __get($name){
    return $this->$name;
  }

  public function construct_trader($id_trader){
    $trader=new Trader();
    $trader->fill($id_trader);
    return $trader;
  }

  public function create_firm($name,$amount){
    $temp_Trader=$this->construct_trader();
    if($temp_Trader->get_cash()<$amount){
      return FALSE;
    }
    else{
      if($amount<15000){
        return FALSE;
      }
      else{
        $firm=new Firm();
        $firm->__set('name',$name);
        $price=10;
        $firm->__set('price_share',$price);
        $quantity=$firm->number_shares_for_price($amount);
        $new_amount=$quantity*$price;
        $uuid=$firm->get_uuid();
        $temp_Trader->give_cash($new_amount);
        $temp_Trader->receive_shares($quantity, $price, $uuid);
        $temp_Trader->save();
        $firm->save();
        $this->save();
      }
    }
  }

  public function join($uuid){
    $this->firm=$uuid;
  }

  public function leave($uuid){
    $this->firm=null;
  }

  public function __cash($amount){
    $this->cash+=$amount;
  }

  public function __shares($nb){
    $this->nb+=$shares;
  }

  public function make_order($stock, $type, $nb, $options)
  {
    $order=new Order();
    $order->__set('stock',$stock);
    $order->__set('type',$type);
    $order->__set('nb',$nb);
    $order->__set('options',$options);
    return $order->save();
  }
/*
  public function vote(Vote &$vote,$opinion)
  {
    $vote->bulletin($this->id,$opinion);
  }
*/
}

class Order{
  private $stock;
  private $type;
  private $nb;
  private $options;

  public function __set($name,$value){
    $this->$name=$value;
  }

  public function __get($name){
    return $this->$name;
  }

  public function xmlify(){
    $data=null;
    $data.="\t<order>\n";
    $data.="\t\t<stock>".$this->stock."</stock>\n";
    $data.="\t\t<type>".$this->type."</type>\n";
    $data.="\t\t<nb>".$this->nb."</nb>\n";
    $data.="\t\t<options>".serialize($this->options)."</options>\n";
    $data.="\t</order>\n";

    return $data;
  }

  public function save(){
    $xml_data=$this->xmlify();
    $file="orders.xml";
    $handle=fopen($file,'a+');
    if($handle){
      if (fwrite($handle, $xml_data) === FALSE) {
        return FALSE;

      fclose($handle);
      return TRUE;
    }
    }
    else{
      return FALSE;
    }
  }
}

$Goldman= new Firm();

//http://finance.yahoo.com/webservice/v1/symbols/allcurrencies/quote
/*
display_stock();

function display_stock($ticker='^DJI'){
	_grab_yahoo_stock_index_streamerapi_str($ticker);

	$STR = file_get_contents('tmp.txt');

	$matchArr = NULL;

	preg_match('/parent.yfs_u1f\((.*)\);/', $STR, $matchArr);

	if (!empty($matchArr[1])) {
	  $ARR = json_decode_v2($matchArr[1]);

	  echo '<pre>';
	  print_r($ARR);
	  echo '</pre>';
	}

}


function _grab_yahoo_stock_index_streamerapi_str($symbol) {
  $URL = 'http://streamerapi.finance.yahoo.com/streamer/1.0?s=' . $symbol;

  // l10 // index value
  // c10 // change value
  // p20 // change percentage
  // g00 // day range low
  // h00 // day rang high
  // v00 // volume
  $URL_field = '&k=c10,g00,h00,l10,p20,v00';

  $URL_postfix = '&callback=parent.yfs_u1f&mktmcb=parent.yfs_mktmcb&gencallback=parent.yfs_gencb';

  $URL = $URL . $URL_field . $URL_postfix;

  # When using CURLOPT_FILE, pass it the file handle that is open 
  # for write only (eg fopen('blahblah', 'w')). If you also open 
  # the file for reading (eg fopen('blahblah', 'w+')), curl will 
  # fail with error 23. 
  $fp = fopen('tmp.txt', 'w');

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $URL);

  # When you are using CURLOPT_FILE to download directly into a file 
  # you must close the file handler after the curl_close() otherwise 
  # the file will be incomplete and you will not be able to use it 
  # until the end of the execution of the php process.
  curl_setopt($ch, CURLOPT_FILE, $fp);

  curl_setopt($ch, CURLOPT_TIMEOUT, 3);

  //curl_setopt($ch, CURLOPT_VERBOSE, 1);

  curl_exec($ch);

  curl_close($ch);

  # at this point your file is not complete and corrupted.
  fclose($fp);

  return;
}

function json_decode_v2($json, $assoc = FALSE){
  $json = str_replace(array("\n","\r"), "", $json);

  //$str = preg_replace("/([a-zA-Z0-9_]+?):/" , "\"$1\":", $str); // fix variable names

  $json = preg_replace('/([{,])(\s*)([^"]+?)\s*:/', '$1"$3":', $json);

  return json_decode($json, $assoc);
}

*/
?>