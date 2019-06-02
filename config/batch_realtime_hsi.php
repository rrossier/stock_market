<?php

//include('../header.php');
date_default_timezone_set ('America/New_York');
$val=array(
"Symbol"=>                           "s",
"Name"=>                             "n",
"Last Trade (With Time)"=>           "l",
"Last Trade (Price Only)"=>          "l1",
"Last Trade Date"=>                  "d1",
"Last Trade Time"=>                  "t1",
"Last Trade Size"=>                  "k3",
"Change and Percent Change"=>        "c",
"Change"=>                           "c1",
"Change in Percent"=>                "p2",
"Ticker Trend"=>                     "t7",
"Volume"=>                           "v",
"Average Daily Volume"=>             "a2",
"More Info"=>                        "i",
"Trade Links"=>                      "t6",
"Bid"=>                              "b",
"Bid Size"=>                         "b6",
"Ask"=>                              "a",
"Ask Size"=>                         "a5",
"Previous Close"=>                   "p",
"Open"=>                             "o",
"Day's Range"=>                      "m",
"52-week Range"=>                    "w",
"Change From 52-wk Low"=>            "j5",
"Pct Chg From 52-wk Low"=>           "j6",
"Change From 52-wk High"=>           "k4",
"Pct Chg From 52-wk High"=>          "k5",
"Earnings/Share"=>                   "e",
"P/E Ratio"=>                        "r",
"Short Ratio"=>                      "s7",
"Dividend Pay Date"=>                "r1",
"Ex-Dividend Date"=>                 "q",
"Dividend/Share"=>                   "d",
"Dividend Yield"=>                   "y",
"Float Shares"=>                     "f6",
"Market Capitalization"=>            "j1",
"1yr Target Price"=>                 "t8",
"EPS Est. Current Yr"=>              "e7",
"EPS Est. Next Year"=>               "e8",
"EPS Est. Next Quarter"=>            "e9",
"Price/EPS Est. Current Yr"=>        "r6",
"Price/EPS Est. Next Yr"=>           "r7",
"PEG Ratio"=>                        "r5",
"Book Value"=>                       "b4",
"Price/Book"=>                       "p6",
"Price/Sales"=>                      "p5",
"EBITDA"=>                           "j4",
"50-day Moving Avg"=>                "m3",
"Change From 50-day Moving Avg"=>    "m7",
"Pct Chg From 50-day Moving Avg"=>   "m8",
"200-day Moving Avg"=>               "m4",
"Change From 200-day Moving Avg"=>   "m5",
"Pct Chg From 200-day Moving Avg"=>  "m6",
"Shares Owned"=>                     "s1",
"Price Paid"=>                       "p1",
"Commission"=>                       "c3",
"Holdings Value"=>                   "v1",
"Day's Value Change"=>               "w1",
"Holdings Gain Percent"=>            "g1",
"Holdings Gain"=>                    "g4",
"Trade Date"=>                       "d2",
"Annualized Gain"=>                  "g3",
"High Limit"=>                       "l2",
"Low Limit"=>                        "l3",
"Notes"=>                            "n4",
"Last Trade (Real-time) with Time"=> "k1",
"Bid (Real-time)"=>                  "b3",
"Ask (Real-time)"=>                  "b2",
"Change Percent (Real-time)"=>       "k2",
"Change (Real-time)"=>               "c6",
"Holdings Value (Real-time)"=>       "v7",
"Day's Value Change (Real-time)"=>   "w4",
"Holdings Gain Pct (Real-time)"=>    "g5",
"Holdings Gain (Real-time)"=>        "g6",
"Day's Range (Real-time)"=>          "m2",
"Market Cap (Real-time)"=>           "j3",
"P/E (Real-time)"=>                  "r2",
"After Hours Change (Real-time)"=>   "c8",
"Order Book (Real-time)"=>           "i5",
"Stock Exchange"=>                   "x");

$tickers=Gamemaster::getTickers('hsi');

$start=microtime();
$str="'".implode("','", $tickers)."'";
$columns="snl1b3b2bad1t1c6vp2";
$file="http://download.finance.yahoo.com/d/quotes.csv?f=".$columns."&s=".$str;

$stocks=array();
$handle = fopen($file, "r");
$sql="INSERT INTO hsi_temp (symbol, name, last_trade, bid_realtime, ask_realtime, bid, ask, date, time, change_realtime, volume, change_day) VALUES ";

$i=0;
while($data = fgetcsv($handle, 4096, ',')){
    
     $stocks[$i]['symbol']=$data[0];
     $stocks[$i]['name']=$data[1];
     $stocks[$i]['last_trade']=$data[2];
     $stocks[$i]['bid_realtime']=$data[3];
     $stocks[$i]['ask_realtime']=$data[4];
     $stocks[$i]['bid']=$data[5];
     $stocks[$i]['ask']=$data[6];
     $stocks[$i]['date']=$data[7];
     $stocks[$i]['time']=$data[8];
     $stocks[$i]['change_realtime']=$data[9];
     $stocks[$i]['volume']=$data[10];
         $stocks[$i]['change_day']=substr($data[11],0,-1);
     $i++;
     
     //$stocks[]=$data;
}
fclose($handle);
//var_dump($stocks);
$connexion = new Connexion_db();
$connexion->exec("TRUNCATE TABLE hsi_temp");
$connexion->beginTransaction();
for($i=0;$i<count($stocks);$i++){
$sql.="(:symbol".$i.", :name".$i.", :last_trade".$i.", :bid_realtime".$i.", :ask_realtime".$i.", :bid".$i.", :ask".$i.", :date".$i.", :time".$i.", :change_realtime".$i.", :volume".$i.", :change_day".$i."), ";
}
$sql=substr($sql,0,-2);
$req=$connexion->prepare($sql);
foreach($stocks as $i=>$line){
$req->bindValue("symbol".$i,$line['symbol']);
$req->bindValue("name".$i,$line['name']);
$req->bindValue("last_trade".$i,$line['last_trade']);
$req->bindValue("bid_realtime".$i,$line['bid_realtime']);
$req->bindValue("ask_realtime".$i,$line['ask_realtime']);
$req->bindValue("bid".$i,$line['bid']);
$req->bindValue("ask".$i,$line['ask']);
$req->bindValue("date".$i,$line['date']);
$req->bindValue("time".$i,$line['time']);
$req->bindValue("change_realtime".$i,$line['change_realtime']);
$req->bindValue("volume".$i,$line['volume']);
$req->bindValue("change_day".$i,$line['change_day']);
}

try {
    $req->execute();
} catch (PDOException $e){
    echo $e->getMessage();
    $connexion->rollBack();
}
$connexion->commit();
$final=number_format(microtime()-$start,3);
