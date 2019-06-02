<?php

include('header.php');

include('downjones_tickers.php');
$tick=$tab;
include('nasdaq_tickers.php');
echo count(array_unique(array_merge($tick,$tab)));
/*
//$tab=array("AAPL","MSFT");
$start_year=2000;
$start_month=0;
$start_day=1;
$end_year=2012;
$end_month=6;
$end_day=29;
$url="http://ichart.finance.yahoo.com/table.csv?g=d&f=".$end_year."&e=".$end_day."&c=".$start_year."&b=".$start_day."&a=".$start_month."&d=".$end_month."&s=";
foreach($tab as $line){
	$target=$url.$line;
	$handle = fopen($target, "r");
	$val=array();
	while($data = fgetcsv($handle, 4096, ',')){
	    //var_dump($data);
	    $val[]=$data;
	}
	fclose($handle);
	$fp = fopen("historicaldata/".$line.".csv", 'w+');

	foreach ($val as $fields) {
	    fputcsv($fp, $fields);
	}
	fclose($fp);
}
*/