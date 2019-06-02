<?php

class waiting_order{
	private $list;

	public function __construct(){
	}

	public function add_to_list(Item &$item, Trader &$user){
		if($user->has_item($item)){
			$this->list[]=array('item'=>$item,'owner'=>$user);
			return 1;
		}
		else{
			return 0;
		}
	}

	public function remove_from_list(Item &$item){
		foreach($this->list as $i=>$product){
			if($product['item']->get_uuid()==$item->get_uuid()){
				unset($this->list[$i]);
			}
		}
	}

	public function get_list(){
		return $this->list;
	}
}