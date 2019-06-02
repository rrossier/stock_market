<?php

function racine(){
	return DIRECTORY."/";
}

function get_last_quotes(){
	$handle = fopen("temp/last_quotes.csv", "r");
	$stocks=array();
	while($data = fgetcsv($handle, 4096, ',')){
		$stocks[]['symbol']=$data[0];
		$stocks[]['name']=$data[1];
		$stocks[]['last_trade']=$data[2];
		$stocks[]['bid_realtime']=$data[3];
		$stocks[]['ask_realtime']=$data[4];
		$stocks[]['bid']=$data[5];
		$stocks[]['ask']=$data[6];
		$stocks[]['date']=$data[7];
		$stocks[]['time']=$data[8];
		$stocks[]['change_realtime']=$data[9];
	}
	fclose($handle);
	return $stocks;
}

function get_list_brokers(){
	$connexion=new Connexion_db();
	//$req=$connexion->prepare("SELECT brokers.* FROM brokers LEFT JOIN users ON users.id_user=brokers.id AND users.actif=1 AND users.type='broker' ORDER BY brokers.date_inscription DESC");
	$req=$connexion->prepare("SELECT id FROM brokers ORDER BY date_inscription DESC");
	$req->execute();
	$tab=array();
	while($row=$req->fetch()){
		$broker=new Broker();
		$broker->fill($row['id']);
		$tab[]=$broker;
	}
	return $tab;
}

function get_list_firms(){
	$connexion=new Connexion_db();
	$req=$connexion->prepare("SELECT id FROM firms ORDER BY datetime DESC LIMIT 0,50");
	$req->execute();
	$tab=array();
	while($row=$req->fetch()){
		$firm=new Firm($row['id']);
		$tab[]=$firm;
	}
	return $tab;
}

function display_news($ticker,$number=10){
	$url="http://finance.yahoo.com/rss/headline?s=".strtolower($ticker);
	$dom = new DOMDocument;
    $dom->load($url);
	$items=$dom->getElementsByTagName('item');
	$data=array();
	$i=0;
	foreach ($items as $item)
	{
		$titles = $item->getElementsByTagName( "title" );
		$data[$i]['title'] = $titles->item(0)->nodeValue;
		$links = $item->getElementsByTagName( "link" );
		$data[$i]['link'] = $links->item(0)->nodeValue;
		$descriptions = $item->getElementsByTagName( "description" );
		$data[$i]['description'] = $descriptions->item(0)->nodeValue;
		$pubdates = $item->getElementsByTagName( "pubDate" );
		$data[$i]['pubdate'] = $pubdates->item(0)->nodeValue;
		$i++;
	}
	return array_splice($data,0,$number);
}

function is_incorrect($val){
	if(is_null($val)){
		return 0;
	}
	elseif(is_nan($val)){
		return 0;
	}
	elseif(empty($val)){
		return 0;
	}
	else{
		return 1;
	}
}

function get_best_daily_quotes($market){
	$sql='SELECT * FROM '.safe($market).'_temp ORDER BY change_day DESC LIMIT 0,3';
	$connexion= new Connexion_db();
	$req=$connexion->prepare($sql);
	$req->execute();
	$data=array();
	while($row=$req->fetch()){
		$data[]=$row;
	}
	$sql='SELECT * FROM '.safe($market).'_temp ORDER BY change_day ASC LIMIT 0,3';
	$connexion= new Connexion_db();
	$req=$connexion->prepare($sql);
	$req->execute();
	$data2=array();
	while($row=$req->fetch()){
		$data2[]=$row;
	}
	$results=array_merge($data,array_reverse($data2));
	return $results;
}

function domain_exists( $email, $record = 'MX' ) {
	list( $user, $domain ) = explode( '@', $email );
	return checkdnsrr( $domain, $record );
}

function get_change($currency){
	$connexion=new Connexion_db();
	//$req=$connexion->prepare("SELECT brokers.* FROM brokers LEFT JOIN users ON users.id_user=brokers.id AND users.actif=1 AND users.type='broker' ORDER BY brokers.date_inscription DESC");
	$req=$connexion->prepare("SELECT * FROM forex_temp WHERE symbol=:currency");
	$req->execute(array('currency'=>$currency));
	$tab=array();
	while($row=$req->fetch()){
		$last_trade=$row['last_trade'];
		$bid_realtime=$row['bid_realtime'];
		$ask_realtime=$row['ask_realtime'];
		$bid=$row['bid'];
		$ask=$row['ask'];
		if($ask_realtime==0){
			if($ask==0){
				if($last_trade==0){
					if($bid_realtime==0){
						if($bid==0){
							$change = 1;
						}
						else{
							$change = $bid;
						}
					}
					else{
						$change = $bid_realtime;
					}
				}
				else{
					$change = $last_trade;
				}
			}
			else{
				$change = $ask;
			}
		}
		else{
			$change = $ask_realtime;
		}
	}
	return $change;
}

function create_listener($id_user,$uuid_stock,$clause,$qty,$price,$datetime){
	$connexion=new Connexion_db();
	//$req=$connexion->prepare("SELECT brokers.* FROM brokers LEFT JOIN users ON users.id_user=brokers.id AND users.actif=1 AND users.type='broker' ORDER BY brokers.date_inscription DESC");
	$req=$connexion->prepare("INSERT INTO listeners(id_user,ticker,clause,quantity,value,datetime) VALUES (:id_user,:ticker,:clause,:qty,:price,:datetime)");
	$req->execute(array('id_user'=>$id_user,'ticker'=>$ticker,'clause'=>$clause,'quantity'=>$quantity,'price'=>$price,'datetime'=>$datetime));

}

function write_log($message){
	$message.="\r\n";
	$file='../logs/log.txt';
	$monfichier = fopen($file, 'a+');
	fputs($monfichier, $message);
	fclose($monfichier);
}
function safe($var)
{
	//$var = mysql_real_escape_string($var);
	$var = trim($var);
	$var = addcslashes($var, '%_');
	$var = htmlspecialchars($var);
	return $var;
}

?>