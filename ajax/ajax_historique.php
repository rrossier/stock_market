<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && parse_url($_SERVER['HTTP_REFERER'])['host']==='localhost') {
	include('../main/header.php');

	$id=(isset($_GET['id'])) ? htmlspecialchars($_GET['id']) : 'BNP.PA';

	$file="../data/markets/".$id.".csv";
	$results=array();
	$firstline = true;
	if (($handle = fopen($file, "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    	if(!$firstline){
	    		$tm=strtotime($data['0'])*1000;
	        	$results[]=array($tm,
	        					floatval($data['1']),
	        					floatval($data['2']),
	        					floatval($data['3']),
	        					floatval($data['4']),
	        					floatval($data['5']));
	    	}
	    	$firstline=false;
	    }
	    fclose($handle);
	}
	$results=array_reverse($results);
	echo json_encode($results);
}

?>