<?php

//require('config/password.php');
/*
$password='isfa';
$hash = password_hash($password.PA','PASSWORD_BCRYPT);
echo $hash;
*/
ini_set('display_errors','1');
error_reporting(E_ALL | E_STRICT);
//var_dump($_SERVER);
include ('../main/header.php');
//include('../config/batch_realtime_indexes.php');
// var_dump($tickers);
// foreach ($tickers as $stock) {
// 	$req=$connexion->prepare("INSERT INTO stocks (ticker,market,currency) VALUES (:ticker,'sbf120','euro')");
// 	$req->execute(array('ticker'=>$stock));
// }
// var_dump($req);



/*
$jour_debut=1;
$jour_fin=28;
$tickers=Gamemaster::getTickers();

$nb_traders=2;
for($i=$jour_debut;$i<=$jour_fin;$i++){
    $nb_new_traders=50;
    for($j=1;$j<=$nb_new_traders;$j++){
        $str="trader_".$i."_".$j;
        Gamemaster::createTrader($str);
    }
    $nb_traders+=$nb_new_traders;
    for($j=3;$j<=$nb_traders;$j++){
        $trader=new Trader($j);

        for($k=1;$k<=25;$k++){
            $ticker=$tickers[array_rand($tickers)];
            $stock=new Stock();
            $stock->__set('uuid',$ticker);
            $qty=10;
            if($i%2 && $j%2){
                $trader->buy($stock,$qty);
            }
            else{
                $trader->sell($stock,$qty);
            }
        }        
    }
}


//var_dump(Gamemaster::updateClassementTradersMonthly());
/*
$trader=new Trader(1);
var_dump($trader->historique);
var_dump($trader->getPerformance('year'));

/*
echo strtotime(date('Y-m-d',time()))."<br/>";
echo mktime(0,0,0,8,27,2013)."<br/>";

$array=array();
for($i=1;$i<27;$i++){
    $date=date('Y-m-d',mktime(0,0,0,8,$i,2013));
    $valuation=round(100000-$i*100,2);
    $array[]=array('date'=>$date,'valuation'=>$valuation);
}
echo serialize($array);
/*
$connexion= new Connexion_db();
$req=$connexion->prepare("SELECT * FROM indexes");
$req->execute();
echo $req->rowCount();

/*
foreach ($tickers as $stock) {
	Gamemaster::getHistorique($stock);
}
*/
/*
$connexion=new Connexion_db();
$req=$connexion->prepare("SELECT * FROM stocks ORDER BY id");
$req->execute();
$all_tickers=array();
while($row=$req->fetch(PDO::FETCH_ASSOC)){
	$all_tickers[]=$row['ticker'];
}
$tickers=array();
$tickers[]=array_slice($all_tickers,0,150);
$tickers[]=array_slice($all_tickers,150,150);
$tickers[]=array_slice($all_tickers,300,150);
$tickers[]=array_slice($all_tickers,450,150);
$tickers[]=array_slice($all_tickers,600,150);
$tickers[]=array_slice($all_tickers,750,150);
$tickers[]=array_slice($all_tickers,900);

$stocks=array();
$i=0;
foreach($tickers as $list_ticker){
    $str="'".implode("','", $list_ticker)."'";
    $file="http://download.finance.yahoo.com/d/quotes.csv?f=sn&s=".$str;
    $handle = fopen($file, "r");
    while($data = fgetcsv($handle, 4096, ',')){
        
         $stocks[$i]['ticker']=$data[0];
         $stocks[$i]['name']=$data[1];
         $i++;
         
         //$stocks[]=$data;
    }
    fclose($handle);
}

for($i=0;$i<count($stocks);$i++){
	$req=$connexion->prepare("UPDATE stocks SET name=:name WHERE ticker=:ticker");
	$req->execute(array('name'=>$stocks[$i]['name'],'ticker'=>$stocks[$i]['ticker']));
}
*/

/*
var_dump(Gamemaster::getPctFeesFirmCreation());
var_dump(Gamemaster::getPctFeesBrokerWeekly());
var_dump(Gamemaster::getPctFeesFirmWeekly());
var_dump(Gamemaster::getFeesFirms());
var_dump(Gamemaster::getFeesBrokers());
Gamemaster::collect_fees('firm',100);


$trader=new Trader();
$trader->fill(1);
var_dump($trader);
$trader->create_firm('Goldman Sachs',200);
$firm=new Firm(1);
var_dump($firm);
var_dump($trader);
*/

// $trader= new Trader();
// $trader->fill(2);
// var_dump($trader);
// $trader->join_firm(1,100);
// $firm=new Firm(1);
// var_dump($firm);
// var_dump($trader);

// $firm=new Firm(1);
// var_dump($firm->historique);
// $firm->saveHistorique('titi');
// var_dump($firm->historique);

?>