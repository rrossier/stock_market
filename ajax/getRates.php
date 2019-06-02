<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');

	$rate=(isset($_GET['rate'])) ? strtolower(htmlspecialchars($_GET['rate'])) : 'Overnight';
	$rates_available=array('overnight','1-week','2-weeks','1-month');
	$rates_files=array('overnight.csv','1-week.csv','2-weeks.csv','1-month.csv');
	$key=array_search($rate, $rates_available);
	if($key!==FALSE){
		$rate=$rates_files[$key];
	}
	$file="../data/rates/".$rate;
	$results=array();
	$firstline = true;
	if (($handle = fopen($file, "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    	if(!$firstline){
	    		$tm=strtotime($data['0'])*1000;
	        	$results[]=array($tm, floatval($data['1']));
	    	}
	    	$firstline=false;
	    }
	    fclose($handle);
	}
	//$results=array_reverse($results);
	echo json_encode($results);
}

?>