<?php

if (!defined("ROOT_PATH"))

{

	header("HTTP/1.1 403 Forbidden");

	exit;

}

class pjFrontEnd extends pjFront

{

	public function __construct()

	{

		parent::__construct();

		$this->setAjax(true);

		$this->setLayout('pjActionEmpty');

	}



	public function pjActionLoad()

	{

		$this->setAjax(false);

		$this->setLayout('pjActionFront');

		

		$_terms_conditions = pjMultiLangModel::factory()->select('t1.*')

			->where('t1.model','pjOption')

			->where('t1.locale', $this->getLocaleId())

			->where('t1.field', 'o_terms')

			->limit(0, 1)

			->findAll()->getData();

		$terms_conditions = '';

		if(!empty($_terms_conditions))

		{

			$terms_conditions = $_terms_conditions[0]['content'];

		}

		$this->set('terms_conditions', $terms_conditions);

		

		ob_start();

		header("Content-Type: text/javascript; charset=utf-8");

	}

	

	public function pjActionLoadCss()

	{

	    $dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);

	    $dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();

	    

		$theme = $this->option_arr['o_theme'];

		$fonts = $this->option_arr['o_theme'];

		if($this->_get->check('theme') && in_array($this->_get->toString('theme'), array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10')))

		{

		    $theme = $this->_get->toString('theme');

		    $fonts = $this->_get->toString('theme');

		}

		$arr = array(

				array('file' => 'bootstrap-datetimepicker.min.css', 'path' => $dm->getPath('pj_bootstrap_datetimepicker')),

				array('file' => "$fonts.css", 'path' => PJ_CSS_PATH . "fonts/"),

				array('file' => 'style.css', 'path' => PJ_CSS_PATH),

				array('file' => "$theme.css", 'path' => PJ_CSS_PATH . "themes/"),

				array('file' => 'transitions.css', 'path' => PJ_CSS_PATH)

		);

		header("Content-Type: text/css; charset=utf-8");

		foreach ($arr as $item)

		{

			ob_start();

			@readfile($item['path'] . $item['file']);

			$string = ob_get_contents();

			ob_end_clean();

				

			if ($string !== FALSE)

			{

				echo str_replace(

						array('../fonts/glyphicons', "pjWrapper"),

						array(

								PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/fonts/glyphicons',

								"pjWrapperTaxiBooking_" . $theme

						),

						$string

				) . "\n";

			}

		}

		exit;

	}

	

	public function pjActionCaptcha()

	{

	    $this->setAjax(true);

	    

	    header("Cache-Control: max-age=3600, private");

	    $rand = $this->_get->toInt('rand') ?: rand(1, 9999);

	    // $patterns = 'app/web/img/button.png';

	    $patterns = PJ_INSTALL_PATH . 'app/web/img/button.png';

	    if(!empty($this->option_arr['o_captcha_background_front']) && $this->option_arr['o_captcha_background_front'] != 'plain')

	    {

	        $patterns = PJ_INSTALL_PATH . $this->getConstant('pjBase', 'PLUGIN_IMG_PATH') . 'captcha_patterns/' . $this->option_arr['o_captcha_background_front'];

	    }

	    $Captcha = new pjCaptcha(PJ_INSTALL_PATH . $this->getConstant('pjBase', 'PLUGIN_WEB_PATH') . 'obj/arialbd.ttf', $this->defaultCaptcha, (int) $this->option_arr['o_captcha_length_front']);

	    $Captcha->setImage($patterns)->setMode($this->option_arr['o_captcha_mode_front'])->init($rand);

	    exit;

	}

	

	public function pjActionCheckCaptcha()

	{

	    $this->setAjax(true);

	    if (!$this->_get->check('captcha') || !$this->_get->toString('captcha') || strtoupper($this->_get->toString('captcha')) != $_SESSION[$this->defaultCaptcha]){

	        echo 'false';

	    }else{

	        echo 'true';

	    }

	    exit;

	}

	

	public function pjActionCheckReCaptcha()

	{

	    $this->setAjax(true);

	    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$this->option_arr['o_captcha_secret_key_front'].'&response='.$this->_get->toString('recaptcha'));

	    $responseData = json_decode($verifyResponse);

	    echo $responseData->success ? 'true': 'false';

	    exit;

	}

	

	public function pjActionSetTaxi()

	{

		if($this->isXHR())

		{

			if($this->_is('fleet_id'))

			{

				$this->_unset('fleet_id');

			}

			if($this->_get->toInt('fleet_id') > 0)

			{

			    $this->_set('fleet_id', $this->_get->toInt('fleet_id'));

			}

			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));

		}

	}

	public function pjActionCheckLogin()

	{

		if($this->isXHR())

		{

			if($this->_post->check('lbs_login'))

			{

			    $data = array();

			    $data['login_email'] = $this->_post->toString('login_email');

			    $data['login_password'] = $this->_post->toString('login_password');

			    $response = pjFrontClient::init($data)->doClientLogin();			    

			    $code = (int) $response['code'];

			    $login_err = __('plugin_base_login_err', true);

			    $text = '';

			    if(isset($login_err[$code]))

			    {

			        if($code == 5)

			        {

			            $text = sprintf($login_err[$code], (int)$this->option_arr['o_failed_login_lock_after']);

			        }else{

			            $text = $login_err[$code];

			        }

			    }

			    

			    if($response['status'] == 'OK')

			    {

			        $client = $this->session->getData($this->defaultClient);

			        

			        $name_arr = pjUtil::splitName($client['name']);

			        

			        $_SESSION[$this->defaultForm]['c_email'] = $client['email'];

			        $_SESSION[$this->defaultForm]['c_password'] = $client['password'];

			        $_SESSION[$this->defaultForm]['c_fname'] = $name_arr[0];

			        $_SESSION[$this->defaultForm]['c_lname'] = $name_arr[1];

			        $_SESSION[$this->defaultForm]['c_phone'] = $client['phone'];

			        

			        $_SESSION[$this->defaultForm]['c_company'] = $client['company'];

			        $_SESSION[$this->defaultForm]['c_address'] = $client['address'];

			        $_SESSION[$this->defaultForm]['c_city'] = $client['city'];

			        $_SESSION[$this->defaultForm]['c_state'] = $client['state'];

			        $_SESSION[$this->defaultForm]['c_zip'] = $client['zip'];

			        $_SESSION[$this->defaultForm]['c_country'] = $client['country_id'];

			    }

			    

			    pjAppController::jsonResponse(array('status' => $response['status'], 'code' => $code, 'text' => $text));

			}else{

				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => ''));

			}

		}

	}

	

	public function pjActionSendPassword()

	{

	    $this->setAjax(true);

	    

	    if ($this->isXHR())

	    {

	        $params = array();

	        $params['locale_id'] = $this->getLocaleId();

	        $params['email'] = $this->_post->toString('email');

	        $resp = pjFrontClient::init($params)->doSendPassword();

	        pjAppController::jsonResponse($resp);

	    }

	    exit;

	}

	

	public function pjActionLogout()

	{

		if($this->isXHR())

		{

			if(isset($_SESSION[$this->defaultClient]))

			{

				unset($_SESSION[$this->defaultClient]);

			}

			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));

		}

	}

	

	// public function pjActionSaveBooking()
	// { 
	// 	// error_reporting(E_ALL);
 	// 	// ini_set("display_errors", 1);
	// 	if ($this->isXHR())
	// 	{
	// 		if (!$this->_post->check('tbs_preview') || !isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm]) || !isset($_SESSION[$this->defaultStore]) || empty($_SESSION[$this->defaultStore]))
	// 		{
	// 			pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109));
	// 		}
	// 		if($this->option_arr['o_captcha_type_front'] == 'system')
	// 		{
    // 			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_SESSION[$this->defaultForm]['captcha']) ||
    // 					!pjCaptcha::validate($_SESSION[$this->defaultForm]['captcha'], $_SESSION[$this->defaultCaptcha]) ))
    // 			{
    // 				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110));
    // 			}
	// 		}

	// 		$STORE = @$_SESSION[$this->defaultStore];
	// 		$SEARCH = @$_SESSION[$this->defaultStore]['search'];
	// 		$FORM = @$_SESSION[$this->defaultForm];
	// 		$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
	// 		$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
	// 		$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
	// 		$durationInMin = !empty($SEARCH['durationInMin']) ? $SEARCH['durationInMin'] : 0;
	// 		$koffers = 0;
	// 		$trolleys = 0;



	// 		$data = array();
	// 		/* Client section */
	// 		$is_new_client = false;
	// 		$update_client = false;
	// 		$pjClientModel = pjClientModel::factory();
	// 		$data['client_id'] = ':NULL';
	// 		if($this->isFrontLogged())
	// 		{
	// 		    $cnt = $pjClientModel->where('t1.foreign_id', $this->getClientId())->findCount()->getData();
	// 		    if($cnt == 0)
	// 		    {
	// 		        $is_new_client = true;
	// 		    }else{
	// 		        $update_client = true;
	// 		    }
	// 		}else{
	// 		    $is_new_client = true;
	// 		}
	// 		if($is_new_client == true)
	// 		{
	// 		    $client_by_email = pjFrontClient::init(array('email' => $FORM['c_email']))->getClientByEmail();
	// 		    if($client_by_email == false)
	// 		    {
	// 		        $client_data['email'] = isset($FORM['c_email']) ? $FORM['c_email'] : ':NULL';
	// 		        $client_data['password'] = isset($FORM['c_password']) ? $FORM['c_password'] : pjUtil::getRandomPassword(6);
	// 		        $client_data['email'] = isset($FORM['c_email']) ? $FORM['c_email'] : ':NULL';
	// 		        $client_data['title'] = isset($FORM['c_title']) ? $FORM['c_title'] : ':NULL';
	// 		        $client_data['fname'] = isset($FORM['c_fname']) ? $FORM['c_fname'] : ':NULL';
	// 		        $client_data['lname'] = isset($FORM['c_lname']) ? $FORM['c_lname'] : ':NULL';
	// 		        $client_data['phone'] = isset($FORM['c_phone']) ? $FORM['c_phone'] : ':NULL';
	// 		        $client_data['company'] = isset($FORM['c_company']) ? $FORM['c_company'] : ':NULL';
	// 		        $client_data['address'] = isset($FORM['c_address']) ? $FORM['c_address'] : ':NULL';
	// 		        $client_data['city'] = isset($FORM['c_city']) ? $FORM['c_city'] : ':NULL';
	// 		        $client_data['state'] = isset($FORM['c_state']) ? $FORM['c_state'] : ':NULL';
	// 		        $client_data['zip'] = isset($FORM['c_zip']) ? $FORM['c_zip'] : ':NULL';
	// 		        $client_data['country_id'] = isset($FORM['c_country']) ? $FORM['c_country'] : ':NULL';
	// 		        $client_data['status'] = 'T';			        
	// 		        $response = pjFrontClient::init($client_data)->createClient();
	// 		        if(isset($response['client_id']) && (int) $response['client_id'] > 0)
	// 		        {
	// 		            $data['client_id'] = $response['client_id'];
	// 		        }
	// 		    }else{
	// 		        $update_client = true;
	// 		    }
	// 		}else{
	// 		    $client = $pjClientModel->reset()->where('foreign_id', $this->getClientId())->findAll()->getDataIndex(0);
	// 		    $data['client_id'] = $client['id'];
	// 		}
	// 		if($update_client == true)
	// 		{
	// 		    $c_data = array();
	// 		    $auth_data = array();
	// 		    if(isset($FORM['c_address']))
	// 		    {
	// 		        $c_data['c_address'] = $FORM['c_address'];
	// 		    }
	// 		    if(isset($FORM['c_country']))
	// 		    {
	// 		        $c_data['country_id'] = $FORM['c_country'];
	// 		    }
	// 		    if(isset($FORM['c_state']))
	// 		    {
	// 		        $c_data['c_state'] = $FORM['c_state'];
	// 		    }
	// 		    if(isset($FORM['c_city']))
	// 		    {
	// 		        $c_data['c_city'] = $FORM['c_city'];
	// 		    }
	// 		    if(isset($FORM['c_zip']))
	// 		    {
	// 		        $c_data['c_zip'] = $FORM['c_zip'];
	// 		    }
	// 		    if(isset($FORM['c_title']))
	// 		    {
	// 		        $c_data['c_title'] = $FORM['c_title'];
	// 		    }
	// 		    if(isset($FORM['c_company']))
	// 		    {
	// 		        $c_data['c_company'] = $FORM['c_company'];
	// 		    }

	// 		    $name_arr = array();
	// 		    if(isset($FORM['c_fname']) && !empty($FORM['c_fname']))
	// 		    {
	// 		        $name_arr[] = $FORM['c_fname'];
	// 		    }
	// 		    if(isset($FORM['c_lname']) && !empty($FORM['c_lname']))
	// 		    {
	// 		        $name_arr[] = $FORM['c_lname'];
	// 		    }
	// 		    $auth_data['name'] = join(" ", $name_arr);
	// 		    if(isset($FORM['c_email']))
	// 		    {
	// 		        $auth_data['email'] = $FORM['c_email'];
	// 		    }
	// 		    if(isset($FORM['c_phone']))
	// 		    {
	// 		        $auth_data['phone'] = $FORM['c_phone'];
	// 		    }
	// 		    $foreign_id = $this->getClientId();
	// 		    if(isset($client_by_email))
	// 		    {
	// 		        $foreign_id = $client_by_email['id'];
	// 		    }
	// 		    if(!empty($auth_data))
	// 		    {
	// 		        pjAuthUserModel::factory()->set('id', $foreign_id)->modify($auth_data);
	// 		    }
	// 		    $pjClientModel->reset()->where('foreign_id', $foreign_id)->limit(1)->modifyAll($c_data);
	// 		    $client = $pjClientModel->reset()->where('foreign_id', $foreign_id)->findAll()->getDataIndex(0);
	// 		    pjFrontClient::init(array('id' => $foreign_id))->setClientSession();
	// 		    $data['client_id'] = $client['id'];
	// 		}
	// 		/* End client section */
				

	// 		$data['uuid'] = pjUtil::uuid();

	// 		$data['locale_id'] = $this->getLocaleId();

	// 		$data['fleet_id'] = $STORE['fleet_id'];

	// 		$data['pickup_address'] = $SEARCH['pickup_address'];

	// 		$data['return_address'] = $SEARCH['return_address'];

	// 		$data['distance'] = $distance;

	// 		$data['ip'] = pjUtil::getClientIp();

	// 		$data['status'] = $this->option_arr['o_booking_status'];

	// 		$data['passengers'] = $passengers;

	// 		$data['luggage'] = $luggage;

	// 		$data['created'] = date('Y-m-d H:i:s');

	// 		$extra_id_arr = isset($FORM['extra_id']) && is_array($FORM['extra_id']) ? array_keys($FORM['extra_id']) : array();



	// 		$fixed_price = 0;

	// 		$fleet_arr = pjFleetModel::factory()

	// 					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

	// 					->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')

	// 					->select("t1.*, t2.content as fleet, t3.content as description,  (SELECT ($distance * `TP`.price) FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`start` <= $distance AND $distance <= `TP`.`end`) LIMIT 1 ) AS price")

	// 					->find($_SESSION[$this->defaultStore]['fleet_id'])->getData();

						

	// 				$this->set('fleet_arr', $fleet_arr);



	// 		if($fixed_price === 0)

	// 			{

	// 				$pjCityModel = pjCityModel::factory();

	// 				$from_city_arr = $pjCityModel

	// 				->join('pjMultiLang', "t2.model='pjCity' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

	// 				->where('LOWER(TRIM(t2.content))', strtolower(trim(@$SEARCH['from_city'])))

	// 				->limit(1)->findAll()->getDataIndex(0);

			

	// 			   $to_city_arr = $pjCityModel->reset()

	// 				->join('pjMultiLang', "t2.model='pjCity' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

	// 				->where('LOWER(TRIM(t2.content))', strtolower(trim(@$SEARCH['to_city'])))

	// 				->limit(1)->findAll()->getDataIndex(0);

				

	// 				if ($fleet_arr && $from_city_arr && $to_city_arr) {

	// 					$fleet_price_arr = pjFleetPriceModel::factory()

	// 						->where('t1.from_city', $from_city_arr['id'])

	// 						->where('t1.to_city', $to_city_arr['id'])

	// 						->where('t1.fleet_id', $fleet_arr['id'])

	// 						->limit(1)->findAll()->getDataIndex(0);

	// 					if ($fleet_price_arr && (float)$fleet_price_arr['price'] > 0) {

	// 						$fixed_price += $fleet_price_arr['price'];

	// 						$fleet_arr['price'] = $fleet_price_arr['price'];

	// 					}

	// 				}

						

	// 			}

	// 		$price_arr = pjAppController::calPrice($STORE['fleet_id'], $distance, $passengers, $extra_id_arr, $this->option_arr, $durationInMin, $SEARCH['return_status'], $fixed_price);

			

	// 		$data['price_id'] = $price_arr['price_id'];

	// 		$data['sub_total'] = $price_arr['subtotal'];

	// 		$data['tax'] = $price_arr['tax'];

	// 		$data['total'] = $price_arr['total'];

	// 		$data['deposit'] = $price_arr['deposit'];

	// 		$data['booking_date'] = pjDateTime::formatDate($SEARCH['booking_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($SEARCH['booking_time']));

	// 		$data['return_date'] = pjDateTime::formatDate($SEARCH['return_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($SEARCH['return_time']));

	// 		$data['c_flight_time'] = isset($FORM['c_flight_time']) ? date("H:i:s", strtotime($FORM['c_flight_time'])) : ':NULL';

	// 		$data['c_departure_airline_company'] = ':NULL';

	// 		$data['c_departure_flight_number'] = ':NULL';

	// 		$data['c_departure_flight_time'] = ':NULL';

	// 		$data['c_departure_terminal'] = ':NULL';

	// 		$data['return_status'] = isset($SEARCH['return_status']) ? $SEARCH['return_status'] : '0';

	// 		$payment = ':NULL';

	// 		if(isset($FORM['payment_method'])) 

	// 		{

	// 			if (isset($FORM['payment_method'])){

	// 				$payment = $FORM['payment_method'];

	// 			}

	// 		}

				

	// 		$pjBookingModel = pjBookingModel::factory();

	// 		$id = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();



	// 		/** Return data save starting here */

	// 		if (isset($SEARCH['return_status']) && $SEARCH['return_status'] == 1 && isset($SEARCH['return_date'])) 

	// 		{

				

	// 			$data['uuid'] = pjUtil::uuid();

	// 			$data['locale_id'] = $this->getLocaleId();

	// 			$data['fleet_id'] = $STORE['fleet_id'];

	// 			$data['pickup_address'] = $SEARCH['return_address']; // its for return case

	// 			$data['return_address'] = $SEARCH['pickup_address']; // its for return case

	// 			$data['distance'] = $distance;

	// 			$data['ip'] = pjUtil::getClientIp();

	// 			$data['status'] = $this->option_arr['o_booking_status'];

	// 			$data['passengers'] = $passengers;

	// 			$data['luggage'] = $luggage;

	// 			$data['luggage_trolleys'] = $trolleys;

	// 			$data['luggage_koffers'] = $koffers;

	// 			$data['created'] = date('Y-m-d H:i:s');

	// 			$data['from_city'] = $SEARCH['from_city'];

	// 			$data['to_city'] = $SEARCH['to_city'];

	// 			$data['price_id'] = $price_arr['price_id'];

	// 			$data['sub_total'] = $price_arr['subtotal'];

	// 			$data['tax'] = $price_arr['tax'];

	// 			$data['total'] = $price_arr['total'];

	// 			$data['deposit'] = $price_arr['deposit'];

	// 			$data['booking_date'] = pjDateTime::formatDate($SEARCH['return_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($SEARCH['return_time']));



	// 			$data['c_departure_flight_time'] = isset($FORM['c_departure_flight_time']) ? date("H:i:s", strtotime($FORM['c_departure_flight_time'])) : ':NULL';

	// 			$data['return_status'] = $SEARCH['return_status'];

	// 			$data['parent_id'] = $id;

	// 			$data['c_airline_company'] = ':NULL';
	// 			$data['c_flight_number'] = ':NULL';
	// 			$data['c_flight_time'] = ':NULL';


	// 			$data['c_departure_airline_company'] = $FORM['c_departure_airline_company'] ? $FORM['c_departure_airline_company'] : '';
	// 			$data['c_departure_flight_number'] = $FORM['c_departure_flight_number'] ? $FORM['c_departure_flight_number'] : '';
	// 			$data['c_departure_flight_time'] = $FORM['c_departure_flight_time'] ? $FORM['c_departure_flight_time'] : '';

	// 			if($SEARCH['extra_stop_address'] > 0){$data['extra_stop'] = 1;}else {$data['extra_stop'] = 0;}



	// 			$ids = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();



	// 			if ($ids !== false && (int) $ids > 0)

	// 				{

	// 					// $extrastops = pjBookingExtraStop::factory();

	// 					// 	if (isset($SEARCH['extra_stop_address']) && is_array($SEARCH['extra_stop_address']) && count($SEARCH['extra_stop_address']) > 0)

	// 					// 	{

	// 					// 		$extrastops->begin();

	// 					// 		$extra_stop_addresses = array_reverse($SEARCH['extra_stop_address']);

	// 					// 		foreach ($extra_stop_addresses as $extra_stop)

	// 					// 		{ 

	// 					// 			$extrastops

	// 					// 			->reset()

	// 					// 			->set('booking_id', $ids)

	// 					// 			->set('extras_stops', $extra_stop)

	// 					// 			->insert();

	// 					// 		}

	// 					// 		$extrastops->commit();

	// 					// 	}

							

	// 					$arr = $pjBookingModel

	// 						->reset()

	// 						->select("

	// 							t1.*, 

	// 							AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,

	// 							AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,

	// 							AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,

	// 							AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,

	// 							AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code,

	// 							t2.content as fleet")

	// 						->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

	// 						->find($ids)
	// 						->getData();

	// 					$pjBookingExtraModel = pjBookingExtraModel::factory();
	// 					if (isset($FORM['extra_id']) && is_array($FORM['extra_id']) && count($FORM['extra_id']) > 0)
	// 					{
	// 						$pjBookingExtraModel->begin();
	// 						foreach ($FORM['extra_id'] as $extra_id => $price)
	// 						{
	// 							$pjBookingExtraModel
	// 							->reset()
	// 							->set('booking_id', $ids)
	// 							->set('extra_value', $price)
	// 							->set('extra_id', $extra_id)
	// 							->insert();
	// 						}
	// 						$pjBookingExtraModel->commit();
	// 					}


	// 					$pdata = array();
	// 					$pdata['booking_id'] = $ids;
	// 					$pdata['payment_method'] = $payment;
	// 					$pdata['payment_type'] = 'online';
	// 					$pdata['amount'] = $arr['deposit'];
	// 					$pdata['status'] = 'notpaid';
	// 					pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
	// 					pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirmation', $this->getLocaleId());
	// 				}
	// 		}


	// 		if ($id !== false && (int) $id > 0)
	// 		{
	// 			$arr = $pjBookingModel
	// 				->reset()
	// 				->select("
	// 					t1.*, 
	// 				 	AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,
	// 				 	AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,
	// 				 	AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,
	// 				 	AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,
	// 				 	AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code,
	// 					t2.content as fleet")
	// 				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	// 				->find($id)
	// 				->getData();

	

	// 			$pjBookingExtraModel = pjBookingExtraModel::factory();
	// 			if (isset($FORM['extra_id']) && is_array($FORM['extra_id']) && count($FORM['extra_id']) > 0)
	// 			{
	// 				$pjBookingExtraModel->begin();
	// 				foreach ($FORM['extra_id'] as $extra_id => $price)
	// 				{
	// 					$pjBookingExtraModel
	// 					->reset()
	// 					->set('booking_id', $id)
	// 					->set('extra_value', $price)
	// 					->set('extra_id', $extra_id)
	// 					->insert();
	// 				}
	// 				$pjBookingExtraModel->commit();
	// 			}

				

	// 			$pdata = array();
	// 			$pdata['booking_id'] = $id;
	// 			$pdata['payment_method'] = $payment;
	// 			$pdata['payment_type'] = 'online';
	// 			$pdata['amount'] = $arr['deposit'];
	// 			$pdata['status'] = 'notpaid';
	// 			pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();

	

	// 			pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirmation', $this->getLocaleId());
	// 			unset($_SESSION[$this->defaultStore]);
	// 			unset($_SESSION[$this->defaultForm]);
	// 			unset($_SESSION[$this->defaultCaptcha]);

					

	// 			$json = array('code' => 200, 'text' => '', 'booking_id' => $id, 'payment' => $payment);
	// 			pjAppController::jsonResponse($json);


	// 			//echo "jjjjjjj"; die('ooo');
	// 		}else {
	// 			pjAppController::jsonResponse(array('code' => 'ERR', 'code' => 119));
	// 		}
	// 	}
	// }

public function pjActionSaveBooking()
	{ 
		// error_reporting(E_ALL);
 		// ini_set("display_errors", 1);
		if ($this->isXHR())
		{
			if (!$this->_post->check('tbs_preview') || !isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm]) || !isset($_SESSION[$this->defaultStore]) || empty($_SESSION[$this->defaultStore]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109));
			}
			if($this->option_arr['o_captcha_type_front'] == 'system')
			{
    			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_SESSION[$this->defaultForm]['captcha']) ||
    					!pjCaptcha::validate($_SESSION[$this->defaultForm]['captcha'], $_SESSION[$this->defaultCaptcha]) ))
    			{
    				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110));
    			}
			}
	
			$STORE = @$_SESSION[$this->defaultStore];
			$SEARCH = @$_SESSION[$this->defaultStore]['search'];
			$FORM = @$_SESSION[$this->defaultForm];
			$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
			$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
			$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
			$durationInMin = !empty($SEARCH['durationInMin']) ? $SEARCH['durationInMin'] : 0;

			 // echo "<pre>"; print_r($SEARCH); echo "</pre>";
			 // echo "<pre>"; print_r($FORM); echo "</pre>"; die('okk');
			$data = array();
						
			/* Client section */
			$is_new_client = false;
			$update_client = false;
			$pjClientModel = pjClientModel::factory();
			$data['client_id'] = ':NULL';
			if($this->isFrontLogged())
			{
			    $cnt = $pjClientModel->where('t1.foreign_id', $this->getClientId())->findCount()->getData();


			    if($cnt == 0)
			    { 
			        $is_new_client = true;
			    }else{
			    	
			        $update_client = true;
			    }
			}else{
			    $is_new_client = true;
			}
			if($is_new_client == true)
			{
			    $client_by_email = pjFrontClient::init(array('email' => $FORM['c_email']))->getClientByEmail();
			    
			    if($client_by_email == false)
			    {
			        $client_data['email'] = isset($FORM['c_email']) ? $FORM['c_email'] : ':NULL';
			        $client_data['password'] = isset($FORM['c_password']) ? $FORM['c_password'] : pjUtil::getRandomPassword(6);
			        $client_data['email'] = isset($FORM['c_email']) ? $FORM['c_email'] : ':NULL';
			        $client_data['title'] = isset($FORM['c_title']) ? $FORM['c_title'] : ':NULL';
			        $client_data['fname'] = isset($FORM['c_fname']) ? $FORM['c_fname'] : ':NULL';
			        $client_data['lname'] = isset($FORM['c_lname']) ? $FORM['c_lname'] : ':NULL';
			        $client_data['phone'] = isset($FORM['c_phone']) ? $FORM['c_phone'] : ':NULL';
			        $client_data['company'] = isset($FORM['c_company']) ? $FORM['c_company'] : ':NULL';
			        $client_data['address'] = isset($FORM['c_address']) ? $FORM['c_address'] : ':NULL';
			        $client_data['city'] = isset($FORM['c_city']) ? $FORM['c_city'] : ':NULL';
			        $client_data['state'] = isset($FORM['c_state']) ? $FORM['c_state'] : ':NULL';
			        $client_data['zip'] = isset($FORM['c_zip']) ? $FORM['c_zip'] : ':NULL';
			        $client_data['country_id'] = isset($FORM['c_country']) ? $FORM['c_country'] : ':NULL';
			        $client_data['status'] = 'T';			        
			        $response = pjFrontClient::init($client_data)->createClient();
			        if(isset($response['client_id']) && (int) $response['client_id'] > 0)
			        {
			            $data['client_id'] = $response['client_id'];
			        }

			        if (empty($data['client_id']))
			        {
                    	$pjClientID = pjAuthUserModel::factory()
                        ->select('t1.*, t2.id as c_id,t2.foreign_id as foreign_id')
                        ->join('pjClient', 't2.foreign_id = t1.id', 'left')
                        ->where('t1.role_id', 3)
                        ->where('t1.email', $FORM['c_email'])
                        ->findAll()
                        ->getDataIndex(0);

                    	$data['client_id'] = $pjClientID['c_id'];
               		}

			    }else{
			        $update_client = true;
			    }
			}else{
			    $client = $pjClientModel->reset()->where('foreign_id', $this->getClientId())->findAll()->getDataIndex(0);
			    $data['client_id'] = $client['id'];
			}
			if($update_client == true)
			{ 
				$pjClientID = pjAuthUserModel::factory()
                        ->select('t1.*, t2.id as c_id,t2.foreign_id as foreign_id')
                        ->join('pjClient', 't2.foreign_id = t1.id', 'left')
                        ->where('t1.role_id', 3)
                        ->where('t1.email', $FORM['c_email'])
                        ->findAll()
                        ->getDataIndex(0);
                       
                    	$data['client_id'] = $pjClientID['c_id'];
                    	/**
			    $c_data = array();
			    $auth_data = array();
			    if(isset($FORM['c_address']))
			    {
			        $c_data['c_address'] = $FORM['c_address'];
			    }
			    if(isset($FORM['c_country']))
			    {
			        $c_data['country_id'] = $FORM['c_country'];
			    }
			    if(isset($FORM['c_state']))
			    {
			        $c_data['c_state'] = $FORM['c_state'];
			    }
			    if(isset($FORM['c_city']))
			    {
			        $c_data['c_city'] = $FORM['c_city'];
			    }
			    if(isset($FORM['c_zip']))
			    {
			        $c_data['c_zip'] = $FORM['c_zip'];
			    }
			    if(isset($FORM['c_title']))
			    {
			        $c_data['c_title'] = $FORM['c_title'];
			    }
			    if(isset($FORM['c_company']))
			    {
			        $c_data['c_company'] = $FORM['c_company'];
			    }
			    
			    $name_arr = array();
			    if(isset($FORM['c_fname']) && !empty($FORM['c_fname']))
			    {
			        $name_arr[] = $FORM['c_fname'];
			    }
			    if(isset($FORM['c_lname']) && !empty($FORM['c_lname']))
			    {
			        $name_arr[] = $FORM['c_lname'];
			    }
			    $auth_data['name'] = join(" ", $name_arr);
			    if(isset($FORM['c_email']))
			    {
			        $auth_data['email'] = $FORM['c_email'];
			    }
			    if(isset($FORM['c_phone']))
			    {
			        $auth_data['phone'] = $FORM['c_phone'];
			    }
			    $foreign_id = $this->getClientId();
			    if(isset($client_by_email))
			    {
			        $foreign_id = $client_by_email['id'];
			    }
			    if(!empty($auth_data))
			    {
			        pjAuthUserModel::factory()->set('id', $foreign_id)->modify($auth_data);
			    }
			    $pjClientModel->reset()->where('foreign_id', $foreign_id)->limit(1)->modifyAll($c_data);
			    $client = $pjClientModel->reset()->where('foreign_id', $foreign_id)->findAll()->getDataIndex(0);
			    pjFrontClient::init(array('id' => $foreign_id))->setClientSession();
			    $data['client_id'] = $client['id']; */
			} 
			/* End client section */
				
			$data['uuid'] = pjUtil::uuid();
			$data['locale_id'] = $this->getLocaleId();
			$data['fleet_id'] = $STORE['fleet_id'];
			$data['pickup_address'] = $SEARCH['pickup_address'];
			$data['return_address'] = $SEARCH['return_address'];
			$data['distance'] = $distance;
			$data['ip'] = pjUtil::getClientIp();
			$data['status'] = $this->option_arr['o_booking_status'];
			$data['passengers'] = $passengers;
			$data['luggage'] = $luggage;
			$data['created'] = date('Y-m-d H:i:s');
			$extra_id_arr = isset($FORM['extra_id']) && is_array($FORM['extra_id']) ? array_keys($FORM['extra_id']) : array();

			$fixed_price = 0;
			$fleet_arr = pjFleetModel::factory()
						->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
						->select("t1.*, t2.content as fleet, t3.content as description,  (SELECT ($distance * `TP`.price) FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`start` <= $distance AND $distance <= `TP`.`end`) LIMIT 1 ) AS price")
						->find($_SESSION[$this->defaultStore]['fleet_id'])->getData();
						
					$this->set('fleet_arr', $fleet_arr);

			if($fixed_price === 0)
				{
					$pjCityModel = pjCityModel::factory();
					$from_city_arr = $pjCityModel
					->join('pjMultiLang', "t2.model='pjCity' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('LOWER(TRIM(t2.content))', strtolower(trim(@$SEARCH['from_city'])))
					->limit(1)->findAll()->getDataIndex(0);
			
				   $to_city_arr = $pjCityModel->reset()
					->join('pjMultiLang', "t2.model='pjCity' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('LOWER(TRIM(t2.content))', strtolower(trim(@$SEARCH['to_city'])))
					->limit(1)->findAll()->getDataIndex(0);
				
					if ($fleet_arr && $from_city_arr && $to_city_arr) {
						$fleet_price_arr = pjFleetPriceModel::factory()
							->where('t1.from_city', $from_city_arr['id'])
							->where('t1.to_city', $to_city_arr['id'])
							->where('t1.fleet_id', $fleet_arr['id'])
							->limit(1)->findAll()->getDataIndex(0);
						if ($fleet_price_arr && (float)$fleet_price_arr['price'] > 0) {
							$fixed_price += $fleet_price_arr['price'];
							$fleet_arr['price'] = $fleet_price_arr['price'];
						}
					}
						
				}
			$price_arr = pjAppController::calPrice($STORE['fleet_id'], $distance, $passengers, $extra_id_arr, $this->option_arr, $durationInMin, $SEARCH['return_status'], $fixed_price);
			
			$data['price_id'] = $price_arr['price_id'];
			$data['sub_total'] = $price_arr['subtotal'] + $price_arr['daterange_price'];
			$data['tax'] = $price_arr['tax'];
			$data['total'] = $price_arr['total'] + $price_arr['daterange_price'];
			$data['net_total'] = $FORM['net_total'];

			if ($FORM['payment_method'] == 'cash' || $FORM['payment_method'] == 'bank') {
			    $data['remainingBalance'] = $price_arr['deposit'] + $price_arr['daterange_price'];
			    $data['deposit'] = 0;
			} else {
			    $data['deposit'] = $price_arr['deposit'] + $price_arr['daterange_price'];
			}

			$data['booking_date'] = pjDateTime::formatDate($SEARCH['booking_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($SEARCH['booking_time']));
			$data['return_date'] = pjDateTime::formatDate($SEARCH['return_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($SEARCH['return_time']));
			
			
			$data['c_email'] = isset($FORM['c_email']) ? $FORM['c_email'] : '';
			$data['c_fname'] = isset($FORM['c_fname']) ? $FORM['c_fname'] : '';
			$data['c_lname'] = isset($FORM['c_lname']) ? $FORM['c_lname'] : '';
			$data['c_phone'] = isset($FORM['c_phone']) ? $FORM['c_phone'] : '';

			$data['c_flight_time'] = !empty($FORM['c_flight_time']) ? date("H:i:s", strtotime($FORM['c_flight_time'])) : null;
			$data['c_airline_company'] = !empty($FORM['c_airline_company']) ? $FORM['c_airline_company'] : null;
			$data['c_flight_number'] = !empty($FORM['c_flight_number']) ? $FORM['c_flight_number'] : null;

			// DEPARTURE airline company
			$data['c_departure_airline_company'] = !empty($FORM['c_departure_airline_company']) ? $FORM['c_departure_airline_company'] : null;
			$data['c_departure_flight_number'] = !empty($FORM['c_departure_flight_number']) ? $FORM['c_departure_flight_number'] : null;
			$data['c_departure_flight_time'] = !empty($FORM['c_departure_flight_time']) ? date("H:i:s", strtotime($FORM['c_departure_flight_time'])) : null;

			$data['return_status'] = '0'; // isset($SEARCH['return_status']) ? $SEARCH['return_status'] : '0';

			if (isset($SEARCH['return_status']) && $SEARCH['return_status'] == 1) {
			    $data['return_status_f'] = 1;
			}
						
			$payment = ':NULL';
			if(isset($FORM['payment_method'])) 
			{
				if (isset($FORM['payment_method'])){
					$payment = $FORM['payment_method'];
				}
			}
			$data['booking_type'] = '1';

			$pjBookingModel = pjBookingModel::factory();
			$id = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();

			/** Return data save starting here */
			if (isset($SEARCH['return_status']) && $SEARCH['return_status'] == 1 && isset($SEARCH['return_date'])) 
			{
				
				$data['uuid'] = pjUtil::uuid();
				$data['locale_id'] = $this->getLocaleId();
				$data['fleet_id'] = $STORE['fleet_id'];
				$data['pickup_address'] = $SEARCH['return_address']; // its for return case
				$data['return_address'] = $SEARCH['pickup_address']; // its for return case
				$data['distance'] = $distance;
				$data['ip'] = pjUtil::getClientIp();
				$data['status'] = $this->option_arr['o_booking_status'];
				$data['passengers'] = $passengers;
				$data['luggage'] = $luggage;
				$data['luggage_trolleys'] = $trolleys;
				$data['luggage_koffers'] = $koffers;
				$data['created'] = date('Y-m-d H:i:s');
				$data['from_city'] = $SEARCH['from_city'];
				$data['to_city'] = $SEARCH['to_city'];
				$data['price_id'] = $price_arr['price_id'];
				$data['sub_total'] = $price_arr['subtotal'] + $price_arr['returndate_rangePrice'];
				$data['tax'] = $price_arr['tax'];
				$data['total'] = $price_arr['total'] + $price_arr['returndate_rangePrice'];

				if ($FORM['payment_method'] == 'cash' || $FORM['payment_method'] == 'bank') {
					    $data['remainingBalance'] = $price_arr['deposit'] + $price_arr['returndate_rangePrice'];
					    $data['deposit'] = 0;
					} else {
					    $data['deposit'] = $price_arr['deposit'] + $price_arr['returndate_rangePrice'];
					}

				$data['booking_date'] = pjDateTime::formatDate($SEARCH['return_date'], $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($SEARCH['return_time']));

				$data['return_status'] = $SEARCH['return_status'];
			    $data['return_status_f'] = 1;
			    
				$data['parent_id'] = $id;

				// Default values as actual NULL
				$data['c_airline_company'] = null;
				$data['c_flight_number']   = null;
				$data['c_flight_time']     = null;

				$data['c_email'] = isset($FORM['c_email']) ? $FORM['c_email'] : '';
				$data['c_fname'] = isset($FORM['c_fname']) ? $FORM['c_fname'] : '';
				$data['c_lname'] = isset($FORM['c_lname']) ? $FORM['c_lname'] : '';
				$data['c_phone'] = isset($FORM['c_phone']) ? $FORM['c_phone'] : '';

				// DEPARTURE airline company
				$data['c_departure_airline_company'] = !empty($FORM['c_departure_airline_company'])
				    ? $FORM['c_departure_airline_company']
				    : null;

				// DEPARTURE flight number
				$data['c_departure_flight_number'] = !empty($FORM['c_departure_flight_number'])
				    ? $FORM['c_departure_flight_number']
				    : null;

				// DEPARTURE flight time
				$data['c_departure_flight_time'] = !empty($FORM['c_departure_flight_time'])
				    ? date("H:i:s", strtotime($FORM['c_departure_flight_time']))
				    : null;
				if($SEARCH['extra_stop_address'] > 0){$data['extra_stop'] = 1;}else {$data['extra_stop'] = 0;}
				$data['booking_type'] = '1';
				
				$ids = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();

				if ($ids !== false && (int) $ids > 0)
					{
						// $extrastops = pjBookingExtraStop::factory();

						// 	if (isset($SEARCH['extra_stop_address']) && is_array($SEARCH['extra_stop_address']) && count($SEARCH['extra_stop_address']) > 0)
						// 	{

						// 		$extrastops->begin();

						// 		$extra_stop_addresses = array_reverse($SEARCH['extra_stop_address']);

						// 		foreach ($extra_stop_addresses as $extra_stop)
						// 		{ 
						// 			$extrastops
						// 			->reset()
						// 			->set('booking_id', $ids)
						// 			->set('extras_stops', $extra_stop)
						// 			->insert();
						// 		}
						// 		$extrastops->commit();
						// 	}
							
						$arr = $pjBookingModel
							->reset()
							->select("
								t1.*, 
								AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code,
								t2.content as fleet")
							->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->find($ids)
							->getData();
							
			
						$pjBookingExtraModel = pjBookingExtraModel::factory();
						if (isset($FORM['extra_id']) && is_array($FORM['extra_id']) && count($FORM['extra_id']) > 0)
						{
							$pjBookingExtraModel->begin();
							foreach ($FORM['extra_id'] as $extra_id => $price)
							{
								$pjBookingExtraModel
								->reset()
								->set('booking_id', $ids)
								->set('extra_value', $price)
								->set('extra_id', $extra_id)
								->insert();
							}
							$pjBookingExtraModel->commit();
						}
						
						$pdata = array();
						$pdata['booking_id'] = $ids;
						$pdata['payment_method'] = $payment;
						$pdata['payment_type'] = 'online';
						$pdata['amount'] = $arr['deposit'];
						$pdata['status'] = 'notpaid';
						pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
			
						pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirmation', $this->getLocaleId());

					}
			}

			if ($id !== false && (int) $id > 0)
			{
				$arr = $pjBookingModel
					->reset()
					->select("
						t1.*, 
					 	AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,
					 	AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,
					 	AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,
					 	AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,
					 	AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code,
						t2.content as fleet")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->find($id)
					->getData();
	
				$pjBookingExtraModel = pjBookingExtraModel::factory();
				if (isset($FORM['extra_id']) && is_array($FORM['extra_id']) && count($FORM['extra_id']) > 0)
				{
					$pjBookingExtraModel->begin();
					foreach ($FORM['extra_id'] as $extra_id => $price)
					{

						$pjBookingExtraModel
						->reset()
						->set('booking_id', $id)
						->set('extra_value', $price)
						->set('extra_id', $extra_id)
						->insert();
					}
					$pjBookingExtraModel->commit();
				}
				
				$pdata = array();
				$pdata['booking_id'] = $id;
				$pdata['payment_method'] = $payment;
				$pdata['payment_type'] = 'online';
				$pdata['amount'] = $arr['deposit'];
				$pdata['status'] = 'notpaid';
				pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
	
				pjAppController::pjActionConfirmSend($this->option_arr, $arr, PJ_SALT, 'confirmation', $this->getLocaleId());
	
				unset($_SESSION[$this->defaultStore]);
				unset($_SESSION[$this->defaultForm]);
				unset($_SESSION[$this->defaultCaptcha]);
					
				$json = array('code' => 200, 'text' => '', 'booking_id' => $id, 'payment' => $payment);
				pjAppController::jsonResponse($json);

			}else {
				pjAppController::jsonResponse(array('code' => 'ERR', 'code' => 119));
			}
		}
	}
	

	public function pjActionConfirm()

	{

	    $this->setAjax(true);

	    

	    if (pjObject::getPlugin('pjPayments') === NULL)

	    {

	        $this->log('pjPayments plugin not installed');

	        exit;

	    }

	    

	    $pjPayments = new pjPayments();

	    $post = $this->_post->raw();

	    $get = $this->_get->raw();

	    $request = array();

	    if(isset($get['payment_method']))

	    {

	        $request = $get;

	    }

	    if(isset($post['payment_method']))

	    {

	        $request = $post;

	    }

	    if($pjPlugin = $pjPayments->getPaymentPlugin($request))

	    {

	        if($uuid = $this->requestAction(array('controller' => $pjPlugin, 'action' => 'pjActionGetCustom', 'params' => $request), array('return')))

	        {

				$pjBookingModel = pjBookingModel::factory();

				

				$booking_arr = $pjBookingModel

				->select("

    				t1.*,

    				AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,

    				AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,

    				AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,

    				AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,

    				AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code,

    				t2.content as fleet,

    				t4.name,

    				t4.email,

    				AES_DECRYPT(t4.password, '".PJ_SALT."') as password,

    				t4.phone

    			")

    			->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

    			->join('pjClient', "t2.id=t1.client_id", 'left outer')

    			->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')

    			->where('t1.uuid', $uuid)

    			->limit(1)

    			->findAll()->getDataIndex(0);

				

				// $childBookings = $pjBookingModel->where('t1.parent_id', $booking_arr['id'])->limit(1)->findAll()->getDataIndex(0);



    			if (!empty($booking_arr))

				{

				    $params = array(

				        'request'		=> $request,

				        'payment_method' => $request['payment_method'],

				        'foreign_id'	 => $this->getForeignId(),

				        'amount'		 => $booking_arr['deposit'],

				        'txn_id'		 => $booking_arr['txn_id'],

				        'order_id'	   => $booking_arr['id'],

				        'cancel_hash'	=> sha1($booking_arr['uuid'].strtotime($booking_arr['created']).PJ_SALT),

				        'key'			=> md5($this->option_arr['private_key'] . PJ_SALT)

				    );

				    $response = $this->requestAction(array('controller' => $pjPlugin, 'action' => 'pjActionConfirm', 'params' => $params), array('return'));

				    if($response['status'] == 'OK')

				    {

				        $this->log("Payments | {$pjPlugin} plugin<br>Order was confirmed. UUID: {$uuid}");

				        

				        $pjBookingModel->reset()

				        ->setAttributes(array('id' => $booking_arr['id']))

				        ->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));



				        // if (!empty($childBookings)) {

						// $pjBookingModel

						// 	->reset()

						// 	->setAttributes(array('id' => $childBookings['id']))

						// 	->modify(array(

						// 		'status' => $this->option_arr['o_payment_status'],

						// 		'processed_on' => ':NOW()'

						// 	));

						//  pjBookingPaymentModel::factory()

				        // ->setAttributes(array('booking_id' => $childBookings['id'], 'payment_type' => 'online'))

				        // ->limit(1)->modifyAll(array('status' => 'paid'));

						// }

				        

				        pjBookingPaymentModel::factory()

				        ->setAttributes(array('booking_id' => $booking_arr['id'], 'payment_type' => 'online'))

				        ->limit(1)->modifyAll(array('status' => 'paid'));

				        

				        pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment', $this->getLocaleId());

				        

				        echo $this->option_arr['o_thankyou_page'];

				        exit;

				    }elseif($response['status'] == 'CANCEL'){

				        $this->log("Payments | {$pjPlugin} plugin<br>Payment was cancelled. UUID: {$uuid}");

				        

				        $pjBookingModel->reset()

				        ->setAttributes(array('id' => $booking_arr['id']))

				        ->modify(array('status' => 'cancelled', 'processed_on' => ':NOW()'));

				        

				        pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'cancel', $this->getLocaleId());

				        

				        echo $this->option_arr['o_thankyou_page'];

				        exit;

				    }else{

				        $this->log("Payments | {$pjPlugin} plugin<br>Order confirmation was failed. UUID: {$uuid}");

				    }

				    

				    if(isset($response['redirect']) && $response['redirect'] == true)

				    {

				        echo $this->option_arr['o_thankyou_page'];

				        exit;

				    }

				}else{

				    $this->log("Payments | {$pjPlugin} plugin<br>Booking with UUID {$uuid} not found.");

				}

				echo $this->option_arr['o_thankyou_page'];

				exit;

	        }

	    }

	    echo $this->option_arr['o_thankyou_page'];

	    exit;

	}

		

	public function pjActionCancel()

	{

		$this->setAjax(false);

		$this->setLayout('pjActionCancel');

	

		$pjBookingModel = pjBookingModel::factory();

	

		if (self::isPost() && $this->_post->check('booking_cancel'))

		{

			$booking_arr = $pjBookingModel

				->select("

					t1.*,

					AES_DECRYPT(t1.cc_type, '".PJ_SALT."') as cc_type,

					AES_DECRYPT(t1.cc_num, '".PJ_SALT."') as cc_num,

					AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') as cc_exp_month,

					AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') as cc_exp_year,

					AES_DECRYPT(t1.cc_code, '".PJ_SALT."') as cc_code, 

					t2.content as fleet, 

					t4.name, 

					t4.email,

					AES_DECRYPT(t4.password, '".PJ_SALT."') as password,

					t4.phone

				")

				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

				->join('pjClient', "t3.id=t1.client_id", 'left outer')

				->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')

				->find($this->_post->toInt('id'))

				->getData();

			if (count($booking_arr) > 0)

			{

			    $pjBookingModel

			    ->reset()

			    ->setAttributes(array("id" => $booking_arr['id']))

			    ->modify(array('status' => 'cancelled'));


				pjAppController::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'cancel', $this->getLocaleId());

				pjAppController::pjActionDriverConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'drivercancel', $this->getLocaleId());

				pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjFrontEnd&action=pjActionCancel&err=200');

			}

		}else{

		    if ($this->_get->check('hash') && $this->_get->check('id'))

			{

				$arr = $pjBookingModel

					->reset()

					->select("t1.*, t2.content as fleet, t4.name, t4.email, AES_DECRYPT(t4.password, '".PJ_SALT."') as password, t4.phone,

					            AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,

								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,

								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,

								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,

								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")

					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

					->join('pjClient', "t3.id=t1.client_id", 'left outer')

					->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')

					->find($this->_get->toInt('id'))

					->getData();

				if (count($arr) == 0)

				{

					$this->set('status', 2);

				}else{

					if ($arr['status'] == 'cancelled')

					{

						$this->set('status', 4);

					}else{

						$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);

						if ($this->_get->toString('hash') != $hash)

						{

							$this->set('status', 3);

						}else{

							$client = pjClientModel::factory()

								->select("t1.*, t2.content as country_title, t3.name, t3.email, t3.phone")

								->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

								->join('pjAuthUser', "t3.id=t1.foreign_id", 'left outer')

								->find($arr['client_id'])->getData();

							$this->set('arr', $arr);

							$this->set('client', $client);

						}

					}

				}

			}else if (!$this->_get->check('err')) {

				$this->set('status', 1);

			}

		}

	}



}

?>