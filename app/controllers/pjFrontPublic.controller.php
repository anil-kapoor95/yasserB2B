<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
// ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);

class pjFrontPublic extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setAjax(true);
		
		$this->setLayout('pjActionEmpty');
	}
	public function pjActionSearch()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() || $this->_get->check('_escaped_fragment_'))
		{
		    if($this->_post->check('tbs_search'))
			{
			    $date_time = pjDateTime::formatDate($this->_post->toString('booking_date'), $this->option_arr['o_date_format']) . ' ' . date("H:i:s", strtotime($this->_post->toString('booking_time')));
				$date_time_ts = strtotime($date_time);
				if(time() + $this->option_arr['o_hour_earlier'] * 3600 > $date_time_ts)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 120));
				}
					
				if($this->_is('search'))
				{
					$this->_unset('search');
				}
				$this->_set("search", $this->_post->raw());
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
			}
		}
	}
	public function pjActionFleets()
	{ 
		
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['search']))
			{
				$SEARCH = $this->_get('search');
				
				$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
				$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
				$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
				$date_time = pjDateTime::formatDate($SEARCH['booking_date'], $this->option_arr['o_date_format']);

				$returndate_time = pjDateTime::formatDate($SEARCH['return_date'], $this->option_arr['o_date_format']);

				$pjFleetModel = pjFleetModel::factory();
				$fleet_arr = $pjFleetModel
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as fleet, t3.content as description, (SELECT `TP`.price FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`start` <= $distance AND $distance <= `TP`.`end`) LIMIT 1 ) AS price, (
						SELECT TP.start_fee_r
						FROM `".pjPriceModel::factory()->getTable()."` AS `TP`
						WHERE TP.fleet_id = t1.id 
						AND (TP.start <= $distance AND $distance <= TP.end)
						LIMIT 1
					) AS start_fee_r, (
						SELECT TP.time_rate_per_minute_r
						FROM `".pjPriceModel::factory()->getTable()."` AS `TP`
						WHERE TP.fleet_id = t1.id 
						AND (TP.start <= $distance AND $distance <= TP.end)
						LIMIT 1
					) AS time_rate_per_minute_r")
					->where('t1.status', 'T')
					->where('t1.passengers >=', $passengers)
					->where('t1.luggage >=', $luggage)
					->orderBy("start_fee_r ASC")
					->findAll()->getData();
				
				$this->set('fleet_arr', $fleet_arr);
				$fleet_price_arr = 0;

				if(empty($fleet_price_arr)){
					
					$pjCityModel = pjCityModel::factory();

					$from_city_arr = $pjCityModel
					->join('pjMultiLang', "t2.model='pjCity' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('LOWER(TRIM(t2.content))', strtolower(trim(@$SEARCH['from_city'])))
					->limit(1)->findAll()->getDataIndex(0);
				
					$to_city_arr = $pjCityModel->reset()
					->join('pjMultiLang', "t2.model='pjCity' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('LOWER(TRIM(t2.content))', strtolower(trim(@$SEARCH['to_city'])))
					->limit(1)->findAll()->getDataIndex(0);
				
					if ($from_city_arr && $to_city_arr) {
						$fleet_price_arr = pjFleetPriceModel::factory()
							->where('t1.from_city', $from_city_arr['id'])
							->where('t1.to_city', $to_city_arr['id'])
							->findAll()->getDataPair('fleet_id', 'price');
						$this->set('fleet_price_arr', $fleet_price_arr);
					}
				}

				$from_daterange = pjDateRangeModel::factory()
					->where('DATE(from_date) <=', $date_time)
					->where('DATE(to_date) >=', $date_time)
					->findAll()->getDataPair('fleet_id', 'price');
				$this->set('from_daterange', $from_daterange);

				$return_daterange = pjDateRangeModel::factory()
			    ->where('DATE(from_date) <=', $returndate_time)
			    ->where('DATE(to_date) >=', $returndate_time)
			    ->findAll()->getDataPair('fleet_id', 'price');
			    $this->set('return_daterange', $return_daterange);

				$checkDate = $date_time; // e.g. '2026-02-04'

				$start = $checkDate . ' 00:00:00';
				$end   = $checkDate . ' 23:59:59';

				$pjTotalBookingDay = pjBookingModel::factory()
				    ->select('fleet_id, COUNT(id) AS total_booking')
				    ->where('booking_date >=', $start)
				    ->where('booking_date <=', $end)
				    ->groupBy('fleet_id')
				    ->findAll()
				    ->getData();
					
					$totalBooking = [];
					foreach ($pjTotalBookingDay as $row) {
					    $totalBooking[$row['fleet_id']] = (int)$row['total_booking'];
					}
				
				$this->set('totalBooking', $totalBooking);

			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}				
		}
	}
	
	public function pjActionCheckout()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['fleet_id']))
			{
				if($this->_post->check('lbs_checkout'))
				{
					$_SESSION[$this->defaultForm] = $this->_post->raw();
						
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
				}else{
					$SEARCH = $this->_get('search');
					$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
					$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
					$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
					$durationInMin = !empty($SEARCH['durationInMin']) ? $SEARCH['durationInMin'] : 0;
					$date_time = pjDateTime::formatDate($SEARCH['booking_date'], $this->option_arr['o_date_format']);
					$returndate_time = pjDateTime::formatDate($SEARCH['return_date'], $this->option_arr['o_date_format']);

					$return_status = isset($SEARCH['return_status']) ? $SEARCH['return_status'] : 0;
					$fleet_arr = pjFleetModel::factory()
						->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer')->select("t1.*, t2.content as fleet, t3.content as description,  (SELECT ($distance * `TP`.price) FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`start` <= $distance AND $distance <= `TP`.`end`) LIMIT 1 ) AS price, (
						SELECT TP.start_fee_r
						FROM `".pjPriceModel::factory()->getTable()."` AS `TP`
						WHERE TP.fleet_id = t1.id 
						AND (TP.start <= $distance AND $distance <= TP.end)
						LIMIT 1
					) AS start_fee_r, (
						SELECT TP.time_rate_per_minute_r
						FROM `".pjPriceModel::factory()->getTable()."` AS `TP`
						WHERE TP.fleet_id = t1.id 
						AND (TP.start <= $distance AND $distance <= TP.end)
						LIMIT 1
					) AS time_rate_per_minute_r")
						->find($_SESSION[$this->defaultStore]['fleet_id'])->getData();
					$this->set('fleet_arr', $fleet_arr);
					
					$fixed_price = 0;

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

					$this->set('country_arr', pjBaseCountryModel::factory()
					    ->select('t1.*, t2.content AS country_title')
					    ->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					    ->where('t1.status', 'T')
					    ->orderBy('`country_title` ASC')
					    ->findAll()
					    ->getData()
					    );
					
					$pjFleetExtraModel = pjFleetExtraModel::factory()
						->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->join('pjExtra', "t1.extra_id=t3.id", 'left')
						->select("t1.*, t2.content as name, t3.price, t3.per")
						->where('t1.fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
						->orderBy("name ASC");
					$avail_extra_arr = $pjFleetExtraModel->findAll()->getData();
					$this->set('avail_extra_arr', $avail_extra_arr);
					
					$extra_id_arr = isset($_SESSION[$this->defaultForm]['extra_id']) && is_array($_SESSION[$this->defaultForm]['extra_id']) ? array_keys($_SESSION[$this->defaultForm]['extra_id']) : array();

					$price_arr = pjAppController::calPrice($_SESSION[$this->defaultStore]['fleet_id'], $distance, $passengers, $extra_id_arr, $this->option_arr, $durationInMin, $return_status, $fixed_price);

					// $price_arr = pjAppController::calPrice($_SESSION[$this->defaultStore]['fleet_id'], $distance, $passengers, $extra_id_arr, $this->option_arr, $fixed_price);

					$from_daterange = pjDateRangeModel::factory()
					->where('DATE(from_date) <=', $date_time)
					->where('DATE(to_date) >=', $date_time)
					->where('fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
					->limit(1)
					->findAll()
					->getDataIndex(0);
					$this->set('from_daterange', $from_daterange);

					$return_daterange = pjDateRangeModel::factory()
					->where('DATE(from_date) <=', $returndate_time)
					->where('DATE(to_date) >=', $returndate_time)
					->where('fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
					->limit(1)
					->findAll()
					->getDataIndex(0);
			       $this->set('return_daterange', $return_daterange);

					$checkDate = $date_time; // e.g. '2026-02-04'

					$start = $checkDate . ' 00:00:00';
					$end   = $checkDate . ' 23:59:59';
					$pjTotalBookingDay = pjBookingModel::factory()
						->where('booking_date >=', $start)
						->where('booking_date <=', $end)
						->where('fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
						->findAll()
						->getData();

					$totalBooking = count($pjTotalBookingDay);
					$this->set('totalBooking', $totalBooking);


					$this->set('price_arr', $price_arr);
					$this->set('passengers', $passengers);
					$this->set('extra_id_arr', $extra_id_arr);
					$this->set('fleet_price_arr', $fleet_price_arr);
					
					$bank_account = pjMultiLangModel::factory()
					->select('t1.content')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_bank_account')
					->limit(1)
					->findAll()->getDataIndex(0);
					$this->set('bank_account', $bank_account ? $bank_account['content'] : '');
					
					if(pjObject::getPlugin('pjPayments') !== NULL)
					{
					    $this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($this->getForeignId()));
					    $this->set('payment_titles', pjPayments::getPaymentTitles($this->getForeignId(), $this->getLocaleId()));
					}else{
					    $this->set('payment_titles', __('payment_methods', true));
					}
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	
	public function pjActionGetPrices()
	{	
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['fleet_id']))
			{
				$SEARCH = $this->_get('search');
			
				$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
				$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
				$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
				$durationInMin = !empty($SEARCH['durationInMin']) ? $SEARCH['durationInMin'] : 0;
				$return_status = isset($SEARCH['return_status']) ? $SEARCH['return_status'] : 0;
				$pjFleetExtraModel = pjFleetExtraModel::factory()
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjExtra', "t1.extra_id=t3.id", 'left')
					->select("t1.*, t2.content as name, t3.price, t3.per")
					->where('t1.fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
					->orderBy("name ASC");
				$avail_extra_arr = $pjFleetExtraModel->findAll()->getData();
				$this->set('avail_extra_arr', $avail_extra_arr);
	
				$extra_id_arr = $this->_post->check('extra_id') && is_array($this->_post->toArray('extra_id')) ? array_keys($this->_post->toArray('extra_id')) : array();
				
				$fixed_price = 0;

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
					if ($from_city_arr && $to_city_arr) 
					{
						$fleet_price_arr = pjFleetPriceModel::factory()
							->where('t1.from_city', $from_city_arr['id'])
							->where('t1.to_city', $to_city_arr['id'])
							->where('t1.fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
							->limit(1)->findAll()->getDataIndex(0);
						if ($fleet_price_arr && (float)$fleet_price_arr['price'] > 0)
						{
							$fixed_price = $fleet_price_arr['price'];
						}
					}
				}

				$price_arr = pjAppController::calPrice($_SESSION[$this->defaultStore]['fleet_id'], $distance, $passengers, $extra_id_arr, $this->option_arr, $durationInMin, $return_status, $fixed_price);
	
				$this->set('price_arr', $price_arr);
				$this->set('passengers', $passengers);
				$this->set('extra_id_arr', $extra_id_arr);
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}

	
	public function pjActionPreview()
	{

		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['fleet_id']))
			{
				$SEARCH = $this->_get('search');
				$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
				$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
				$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
				$durationInMin = !empty($SEARCH['durationInMin']) ? $SEARCH['durationInMin'] : 0;
				$return_status = isset($SEARCH['return_status']) ? $SEARCH['return_status'] : 0;
				$date_time = pjDateTime::formatDate($SEARCH['booking_date'], $this->option_arr['o_date_format']);
				$returndate_time = pjDateTime::formatDate($SEARCH['return_date'], $this->option_arr['o_date_format']);
				
				$fleet_arr = pjFleetModel::factory()
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->select("t1.*, t2.content as fleet, (SELECT ($distance * `TP`.price) FROM `".pjPriceModel::factory()->getTable()."` AS `TP` WHERE `TP`.fleet_id=t1.id AND (`TP`.`start` <= $distance AND $distance <= `TP`.`end`) LIMIT 1 ) AS price, (
						SELECT TP.start_fee_r
						FROM `".pjPriceModel::factory()->getTable()."` AS `TP`
						WHERE TP.fleet_id = t1.id 
						AND (TP.start <= $distance AND $distance <= TP.end)
						LIMIT 1
					) AS start_fee_r, (
						SELECT TP.time_rate_per_minute_r
						FROM `".pjPriceModel::factory()->getTable()."` AS `TP`
						WHERE TP.fleet_id = t1.id 
						AND (TP.start <= $distance AND $distance <= TP.end)
						LIMIT 1
					) AS time_rate_per_minute_r")
					->find($_SESSION[$this->defaultStore]['fleet_id'])->getData();
				$this->set('fleet_arr', $fleet_arr);
				
				$fixed_price = 0;

				if($fixed_price === 0){

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
				
				$extra_id_arr = isset($_SESSION[$this->defaultForm]['extra_id']) && is_array($_SESSION[$this->defaultForm]['extra_id']) ? array_keys($_SESSION[$this->defaultForm]['extra_id']) : array();
				$price_arr = pjAppController::calPrice($_SESSION[$this->defaultStore]['fleet_id'], $distance, $passengers, $extra_id_arr, $this->option_arr, $durationInMin, $return_status, $fixed_price);
				
				$this->set('fleet_price_arr', $fleet_price_arr);
				$this->set('price_arr', $price_arr);
				$this->set('passengers', $passengers);
				
				$pjFleetExtraModel = pjFleetExtraModel::factory()
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjExtra', "t1.extra_id=t3.id", 'left')
					->select("t1.*, t2.content as name, t3.price, t3.per")
					->where('t1.fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
					->orderBy("name ASC");
				$avail_extra_arr = $pjFleetExtraModel->findAll()->getData();
				$this->set('avail_extra_arr', $avail_extra_arr);

				
				$this->set('country_arr', pjBaseCountryModel::factory()
				    ->select('t1.*, t2.content AS country_title')
				    ->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				    ->find($_SESSION[$this->defaultForm]['c_country'])
				    ->getData()
				    );
				
				$bank_account = pjMultiLangModel::factory()
				->select('t1.content')
				->where('t1.model','pjOption')
				->where('t1.locale', $this->getLocaleId())
				->where('t1.field', 'o_bank_account')
				->limit(1)
				->findAll()->getDataIndex(0);
				$this->set('bank_account', $bank_account ? $bank_account['content'] : '');

				$from_daterange = pjDateRangeModel::factory()
					->where('DATE(from_date) <=', $date_time)
					->where('DATE(to_date) >=', $date_time)
					->where('fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
					->limit(1)
					->findAll()
					->getDataIndex(0);
					$this->set('from_daterange', $from_daterange);

				$return_daterange = pjDateRangeModel::factory()
					->where('DATE(from_date) <=', $returndate_time)
					->where('DATE(to_date) >=', $returndate_time)
					->where('fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
					->limit(1)
					->findAll()
					->getDataIndex(0);
			       $this->set('return_daterange', $return_daterange);

					$checkDate = $date_time; // e.g. '2026-02-04'

					$start = $checkDate . ' 00:00:00';
					$end   = $checkDate . ' 23:59:59';
					$pjTotalBookingDay = pjBookingModel::factory()
						->where('booking_date >=', $start)
						->where('booking_date <=', $end)
						->where('fleet_id', $_SESSION[$this->defaultStore]['fleet_id'])
						->findAll()
						->getData();
					$totalBooking = count($pjTotalBookingDay);
					$this->set('totalBooking', $totalBooking);

				
				if(pjObject::getPlugin('pjPayments') !== NULL)
				{
				    $this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($this->getForeignId()));
				    $this->set('payment_titles', pjPayments::getPaymentTitles($this->getForeignId(), $this->getLocaleId()));
				}else{
				    $this->set('payment_titles', __('payment_methods', true));
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	
	public function pjActionGetPaymentForm()
	{
	    if ($this->isXHR())
	    {
	        $arr = pjBookingModel::factory()->find($this->_get->toInt('booking_id'))->getData();

	        //echo "<pre>"; print_r($arr); echo "</pre>"; die('okk');
	      	
	      	if (!empty($arr['net_total'])) {
			    // Highest priority: explicit net_total value
			    $d_amount = $arr['net_total'];

			} elseif (isset($arr['net_total']) && $arr['net_total'] == '1') {
			    // Special flag case
			    $d_amount = $arr['deposit'] * 2;

			} else {
			    // Default
			    $d_amount = $arr['deposit'];
			}
			
	        if(pjObject::getPlugin('pjPayments') !== NULL)
	        {
	            $pjPlugin = pjPayments::getPluginName($arr['payment_method']);
	            if(pjObject::getPlugin($pjPlugin) !== NULL)
	            {
	                $client = pjClientModel::factory()
	                ->select("t1.*, t2.email as c_email, t2.name as c_name, t2.phone as c_phone")
	                ->join('pjAuthUser', "t1.foreign_id=t2.id", 'left outer')
	                ->find($arr['client_id'])->getData();
	                
	                $this->set('params', $pjPlugin::getFormParams(array('payment_method' => $arr['payment_method']), array(
	                    'locale_id'	 => $this->getLocaleId(),
	                    'return_url'	=> $this->option_arr['o_thankyou_page'],
	                    'id'			=> $arr['id'],
	                    'foreign_id'	=> $this->getForeignId(),
	                    'uuid'		  => $arr['uuid'],
	                    'name'		  => @$client['c_name'],
	                    'email'		 => @$client['c_email'],
	                    'phone'		 => @$client['c_phone'],
	                    'amount'		=> $d_amount,
	                    'cancel_hash'   => sha1($arr['uuid'].strtotime($arr['created']).PJ_SALT),
	                    'currency_code' => $this->option_arr['o_currency'],
	                )));
	            }
	            
	            if ($arr['payment_method'] == 'bank')
	            {
	                $bank_account = pjMultiLangModel::factory()
	                ->select('t1.content')
	                ->where('t1.model','pjOption')
	                ->where('t1.locale', $this->getLocaleId())
	                ->where('t1.field', 'o_bank_account')
	                ->limit(1)
	                ->findAll()->getDataIndex(0);
	                $this->set('bank_account', $bank_account ? $bank_account['content'] : '');
	            }
	        }
	        
	        $this->set('arr', $arr);
	        $this->set('get', $this->_get->raw());
	    }
	}	

	public function pjActionSupplierLogin()
	{
		$this->setLayout('pjActionSupplierLogin');

		if ($this->isXHR())
		{
			if ($this->_post->toInt('supplier_login') === 1)
			{
				$email = $this->_post->toString('email');
				$password = $this->_post->toString('password');

				$user = pjAuthUserModel::factory()
					->where('email', $email)
					->where('role_id', 5)
					->limit(1)
					->findAll()
					->getData();

				if (empty($user))
				{
					pjAppController::jsonResponse(array(
						'status' => 'ERR',
						'msg' => 'Invalid email or supplier account not found'
					));
				}

				$user = $user[0];

				if ($user['status'] != 'T')
				{
					pjAppController::jsonResponse(array(
						'status' => 'ERR',
						'msg' => 'Your account is not active. Please wait for admin approval.'
					));
				}

				$data = array(
					'login_email' => $email,
					'login_password' => $password,
					'role_id' => 5
				);

				$response = pjAuth::init($data)->doLogin();

				if ($response['status'] == 'OK')
				{
					pjAppController::jsonResponse(array(
						'status' => 'OK',
						'msg' => 'Login successful',
						'redirect' => PJ_INSTALL_URL . 'index.php?controller=pjAdminSuppliers&action=pjActionIndex'
					));
				}
				else
				{
					pjAppController::jsonResponse(array(
						'status' => 'ERR',
						'msg' => 'Wrong password'
					));
				}
			}
		}
	}

	public function pjActionSupplierRegister()
    {
        // Initialize variables for the view
        $this->set('post', array());
        $this->set('errors', array());

        if (self::isPost() && $this->_post->toInt('supplier_register'))
        {
            $post = $this->_post->raw();
            $errors = array();

            // Required fields
            $required = array(
                'first_name',
                'last_name',
                'email',
                'password',
                'confirm_password',
                'phone',
                'company_name',
                'city',
                //'total_vehicles'
            );

            foreach ($required as $field)
            {
                if (!isset($post[$field]) || trim($post[$field]) === '')
                {
                    $errors[] = ucfirst(str_replace('_',' ',$field)) . " is required";
                }
            }

            // Email validation
            if (!empty($post['email']) && !filter_var($post['email'], FILTER_VALIDATE_EMAIL))
            {
                $errors[] = "Invalid email format";
            }

            // Check email already exists
            $exists = pjAuthUserModel::factory()
                        ->where('email', $post['email'])
                        ->findCount()
                        ->getData();

            if ($exists > 0)
            {
                $errors[] = "Email already exists";
            }

            // Password match
            if ($post['password'] !== $post['confirm_password'])
            {
                $errors[] = "Passwords do not match";
            }

            if (!empty($errors))
			{
				echo json_encode(array(
					'status' => 'ERR',
					'errors' => $errors
				));
				exit;
			}

            // Create Auth User
            $userData = array(
                'role_id'   => 5,
                'email'     => $post['email'],
                'password'  => $post['password'],
                'name'      => trim($post['first_name'] . ' ' . $post['last_name']),
                'phone'     => $post['phone'],
                'status'    => 'F',
                'is_active' => 'T',
                'ip'        => pjUtil::getClientIp()
            );

            $authId = pjAuthUserModel::factory($userData)->insert()->getInsertId();
            if ($authId!== false && (int) $authId > 0)
				{
				
                $supplierData = array(
                    'auth_id'        => $authId,
                    'first_name'     => $post['first_name'],
                    'last_name'      => $post['last_name'],
                    'phone'          => $post['phone'],
                    'company_name'   => $post['company_name'],
                    'city'           => $post['city'],
                    // 'total_vehicles' => $post['total_vehicles'],
                    'status'         => 'T',
                );

                $supplierId = pjSupplierModel::factory()
								->setAttributes($supplierData)
                                ->insert()
                                ->getInsertId();

                if ($supplierId)
                {
					echo json_encode(array(
					'status' => 'OK',
					'supplier_id' => $supplierId,
					'message' => 'You have successfully registered. Please wait for admin approval.'
					));
					exit;
                }
            }
			echo json_encode(array(
				'status' => 'ERR',
				'errors' => $errors
			));
			exit;
        }
    }

	public function pjActionSendSupplierEmails()
	{
		$supplierId = $this->_get->toInt('supplier_id');

		if ($supplierId > 0) {

			pjAppController::pjActionSupplierAccountSend(
				$this->option_arr,
				$supplierId,
				PJ_SALT,
				"account",
				$this->getLocaleId()
			);

			pjAppController::pjActionAccountActiveSend(
				$this->option_arr,
				$supplierId,
				PJ_SALT,
				"accountactive",
				$this->getLocaleId()
			);
		}

		exit;
	}
}
?>