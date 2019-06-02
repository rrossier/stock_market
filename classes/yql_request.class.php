<?php

class yql_request{
	private $base_url;
	private $yql_query;
	private $tickers;
	public $results;

	public function __construct(){
		$this->base_url="http://query.yahooapis.com/v1/public/yql";
		$this->yql_query = "select symbol, Name, Bid, Ask, ChangeinPercent, BidRealtime, AskRealtime from yahoo.finance.quotes where symbol in ('";
	}

	public function add_ticker($ticker){
		if(!is_array($ticker)){
			$this->tickers[]=$ticker;
		}
	}

	public function execute(){
		$this->yql_query.=implode("', '", $this->tickers)."') | sort(field='Ask', descending='false')";
		$yql_query_url=$this->base_url . "?q=" . urlencode($this->yql_query) . "&env=store://datatables.org/alltableswithkeys&format=json";
		$session = curl_init($yql_query_url);
		curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
		$json = curl_exec($session);
		$phpObj =  json_decode($json);
		$results=array();
		foreach($phpObj->query->results->quote as $stock)
		{
			$results[]=$stock;
		}
		$this->results=$results;
		return $results;
	}

}