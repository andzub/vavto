<?php

$extra = extra::getInstance();

class extra{

	protected static $_instance;
	private function __clone() {}
	private function __wakeup() {}
	private function __construct() {}

	public static function getInstance() {
		if (self::$_instance === null) { self::$_instance = new self; }
		return self::$_instance;
	}


	/**
	 * @param $status
	 * @param $code
	 * @param $message
	 *
	 * status code:
	 *      100 - info
	 *
	 *      200 - success
	 *
	 *      300 - permission error
	 *          301 - non authorized
	 *          302 - permission denied
	 *
	 *      400 - data error
	 *          401 - not all data got
	 *          402 - invalid data
	 *
	 *      500 - server error
	 *          501 - database error
	 *
	 * @return array
	 */
	public function notice($status, $code, $message=null){

		$notice = array(
			'status'=>$status,
			'code'=>$code
		);

		if($message !== null)
			$notice['message'] = $message;

		return $notice;
	}


	public function electricity_price(){

		$db = db::getInstance();

		$electricity_price = $db->getOne('SELECT `value` FROM `settings` WHERE `name`="electricity"');

		return $electricity_price;

	}

	// returning $currency in dollars
	public function exchange_rate($currency){

		$asic = json_decode(file_get_contents('https://whattomine.com/asic.json'),1);

		if(!$reward = $asic['coins'][ucfirst($currency)]['exchange_rate']){
			$gpu = json_decode(file_get_contents('https://whattomine.com/coins.json'),1);
			$reward = $gpu['coins'][ucfirst($currency)]['exchange_rate'];
		}

		return $reward;
	}




	public function profitability($th, $wh, $currency){

		$db = db::getInstance();
		$settings = settings::getInstance();

		$currencies = $settings->currency_list();
		$electricity_price = $this->electricity_price();
		$income_admin = $db->getOne('SELECT `value` FROM `settings` WHERE `name`="income_admin"');
		$exchange_rate = $this->exchange_rate($currency);

		$rate = $db->getAll('SELECT `name`,`value` FROM `settings` WHERE `name` IN (?a)', $currencies);
		$rate = array_combine(array_column($rate, 'name'), array_column($rate, 'value'));

		if($income_admin) $reward_btc = $th*$rate[$currency."_admin"]*30; // биткойнов в месяц
		else $reward_btc = $th*$rate[$currency]*30;


		$reward_usd = $exchange_rate*$reward_btc; // долларов в месяц

		$electricity = $wh*$electricity_price*24*30;

		$commission = $reward_usd/10;

		$profit = $reward_usd - $commission - $electricity;

		return array(
			'reward_btc'=>$reward_btc,
			'reward_usd'=>$reward_usd,
			'electricity'=>$electricity,
			'commission'=>$commission,
			'profit'=>$profit
		);
	}

	/**
	 * @param $options (array)
	 * get pagination html code
	 * @return string
	 */
	function pagination($options){
		if($options['notes_in_page'] >= $options['total_count']) return false;
		$page= (!empty($_GET['page'])) ? (int)$_GET['page'] : 1;
		$default_options=['notes_in_page'=>12];
		array_merge($options,$default_options);
		if(!is_numeric($options['notes_in_page'])) return false;
		$total_count=$options['total_count'];
		$notes_in_page=$options['notes_in_page'];
		$base_url=explode('?',$_SERVER['REQUEST_URI'])[0];
		$max_pages=ceil($total_count/$notes_in_page);
		$pgn_s='<ul class="pagination vavto_pgn">';
		$pgn_e='</ul>';
		$start=1;
		if($page>1){
			$pgn_s.='<li class="prev adds"><a href="'.$base_url.'?page='.($page-1).'"><span>←</span> '._('Предыдущая').'</a></li>';
		}
		if($max_pages-$page>0){
			$pgn_e='<li class="next adds"><a href="'.$base_url.'?page='.($page+1).'">'._('Следующая').' <span>→</span></a></li>'.$pgn_e;
		}
		for($i=$start;$i<($max_pages+$start);$i++){
			if($i==$page)$li_class=" class=\"pgn_active\"";
			else $li_class='';
			$pgn_s.='<li'.$li_class.'><a href="'.$base_url.'?page='.$i.'">'.$i.'</a></li>';
		}
		$pgn_s.=$pgn_e;
		return $pgn_s;
	}

}