<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
		
	public function __construct($requireLogin=null)
	{
	    $this->setLayout('pjActionAdmin');
	    
	    if (!is_null($requireLogin) && is_bool($requireLogin))
	    {
	        $this->requireLogin = $requireLogin;
	    }
	    
	    if ($this->requireLogin)
	    {
	        $_get = pjRegistry::getInstance()->get('_get');
	        if (!$this->isLoged() && !in_array(@$_get->toString('action'), array('pjActionLogin', 'pjActionForgot', 'pjActionValidate', 'pjActionExportFeed')))
	        {
	            if (!$this->isXHR())
	            {
	                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjBase&action=pjActionLogin");
	            } else {
	                header('HTTP/1.1 401 Unauthorized');
	                exit;
	            }
	        }
	    }
	    $ref_inherits_arr = array();
	    if ($this->isXHR() && isset($_SERVER['HTTP_REFERER'])) {
	        $http_refer_arr = parse_url($_SERVER['HTTP_REFERER']);
	        parse_str($http_refer_arr['query'], $arr);
	        if (isset($arr['controller']) && isset($arr['action'])) {
	            parse_str($_SERVER['QUERY_STRING'], $query_string_arr);
	            $key = $query_string_arr['controller'].'_'.$query_string_arr['action'];
	            $cnt = pjAuthPermissionModel::factory()->where('`key`', $key)->findCount()->getData();
	            if ($cnt <= 0) {
	                $ref_inherits_arr[$query_string_arr['controller'].'::'.$query_string_arr['action']] = $arr['controller'].'::'.$arr['action'];
	            }
	        }
	    }
	    $inherits_arr = array(
	        'pjBasePermissions::pjActionResetPermission' => 'pjBasePermissions::pjActionUserPermission',
	        
	        'pjAdminOptions::pjActionPaymentOptions' => 'pjAdminOptions::pjActionPayments',
	        'pjAdminOptions::pjActionNotificationsGetMetaData' => 'pjAdminOptions::pjActionNotifications',
	        'pjAdminOptions::pjActionNotificationsGetContent' => 'pjAdminOptions::pjActionNotifications',
	        'pjAdminOptions::pjActionNotificationsSetContent' => 'pjAdminOptions::pjActionNotifications',
	        
	        'pjAdminBookings::pjActionCheckEmail' => 'pjAdminBookings::pjActionCreate',
	        'pjAdminBookings::pjActionCheckEmail' => 'pjAdminBookings::pjActionUpdate',
	        'pjAdminBookings::pjActionGetExtras' => 'pjAdminBookings::pjActionCreate',
	        'pjAdminBookings::pjActionGetExtras' => 'pjAdminBookings::pjActionUpdate',
	        'pjAdminBookings::pjActionCalPrice' => 'pjAdminBookings::pjActionCreate',
	        'pjAdminBookings::pjActionCalPrice' => 'pjAdminBookings::pjActionUpdate',
	        'pjAdminBookings::pjActionGetBooking' => 'pjAdminBookings::pjActionIndex',
	        'pjAdminBookings::pjActionGetDeletedBooking' => 'pjAdminBookings::pjActionDeleted',
	        'pjAdminBookings::pjActionCancellation' => 'pjAdminBookings::pjActionUpdate',
	        'pjAdminBookings::pjActionConfirmation' => 'pjAdminBookings::pjActionUpdate',
	        'pjAdminBookings::pjActionSmsConfirmation' => 'pjAdminBookings::pjActionUpdate',
	        'pjAdminBookings::pjActionPrint' => 'pjAdminBookings::pjActionPrint',
	        'pjAdminBookings::pjActionAssignDriver' => 'pjAdminBookings::pjActionUpdate',
	        
	        'pjAdminClients::pjActionCheckEmail' => 'pjAdminClients::pjActionCreate',
	        'pjAdminClients::pjActionCheckEmail' => 'pjAdminClients::pjActionUpdate',
	        'pjAdminClients::pjActionGetClient' => 'pjAdminClients::pjActionIndex',
	        'pjAdminClients::pjActionSaveClient' => 'pjAdminClients::pjActionUpdate',
	        'pjAdminClients::pjActionStatusClient' => 'pjAdminClients::pjActionUpdate',
	        
	        'pjAdminExtras::pjActionGetExtra' => 'pjAdminExtras::pjActionIndex',
	        'pjAdminExtras::pjActionCreate' => 'pjAdminExtras::pjActionCreateForm',
	        'pjAdminExtras::pjActionUpdate' => 'pjAdminExtras::pjActionUpdateForm',
	        
	        'pjAdminFleets::pjActionGetFleet' => 'pjAdminFleets::pjActionIndex',
	        'pjAdminFleets::pjActionDeleteImage' => 'pjAdminFleets::pjActionUpdate',
	        'pjAdminFleets::pjActionCheckPrices' => 'pjAdminFleets::pjActionCreate',
	        'pjAdminFleets::pjActionCheckPrices' => 'pjAdminFleets::pjActionUpdate',

        	'pjAdminDrivers::pjActionCheckEmail' => 'pjAdminDrivers::pjActionCreate',
		    'pjAdminDrivers::pjActionCheckEmail' => 'pjAdminDrivers::pjActionUpdate',
		    'pjAdminDrivers::pjActionGetClient' => 'pjAdminDrivers::pjActionIndex',
		    'pjAdminDrivers::pjActionSaveClient' => 'pjAdminDrivers::pjActionUpdate',
		    'pjAdminDrivers::pjActionStatusClient' => 'pjAdminDrivers::pjActionUpdate',
		    'pjAdminFullDrivers::pjActionDriverCalendarEvents' => 'pjAdmin::pjActionIndex',
		    'pjAdmin::pjActionDriverCalendarEvents' => 'pjAdmin::pjActionIndex',
		    'pjAdmin::pjActionDriverViewEvents' => 'pjAdmin::pjActionIndex',
			'pjAdmin::pjActionDriverUpdateEvents' => 'pjAdmin::pjActionIndex',
			'pjAdminBookings::pjActionPrint' => 'pjAdmin::pjActionIndex',

	    );
	    if ($_REQUEST['controller'] == 'pjAdminOptions' && isset($_REQUEST['next_action'])) {
	        $inherits_arr['pjAdminOptions::pjActionUpdate'] = 'pjAdminOptions::'.$_REQUEST['next_action'];
	    }
	    $inherits_arr = array_merge($ref_inherits_arr, $inherits_arr);
	    pjRegistry::getInstance()->set('inherits', $inherits_arr);
	}
	
	public function beforeFilter()
	{
	    parent::beforeFilter();
	    
	    if (!pjAuth::factory()->hasAccess())
	    {
	        if (!$this->isXHR())
	        {
	            $this->sendForbidden();
	            return false;
	        } else {
	            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Access denied.'));
	        }
	    }
	    
	    return true;
	}
	
	public function afterFilter()
	{
	    parent::afterFilter();
	    $this->appendJs('index.php?controller=pjBase&action=pjActionMessages', PJ_INSTALL_URL, true);
	}
	
	public function beforeRender()
	{
		
	}
	
	public function setLocalesData()
	{
	    $locale_arr = pjLocaleModel::factory()
	    ->select('t1.*, t2.file')
	    ->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
	    ->where('t2.file IS NOT NULL')
	    ->orderBy('t1.sort ASC')->findAll()->getData();
	    
	    $lp_arr = array();
	    foreach ($locale_arr as $item)
	    {
	        $lp_arr[$item['id']."_"] = $item['file'];
	    }
	    $this->set('lp_arr', $locale_arr);
	    $this->set('locale_str', pjAppController::jsonEncode($lp_arr));
	    $this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjBaseLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
	}
	
	public function pjActionVerifyAPIKey()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        if (!self::isPost())
	        {
	            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method is not allowed.'));
	        }
	        
	        $option_key = $this->_post->toString('key');
	        if (!array_key_exists($option_key, $this->option_arr))
	        {
	            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Option cannot be found.'));
	        }
	        
	        $option_value = $this->_post->toString('value');
	        if(empty($option_value))
	        {
	            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'API key is empty.'));
	        }
	        
	        $html = '';
	        $isValid = false;
	        switch ($option_key)
	        {
	            case 'o_google_maps_api_key':
	                $address = preg_replace('/\s+/', '+', $this->option_arr['o_timezone']);
	                $api_key_str = $option_value;
	                $gfile = "https://maps.googleapis.com/maps/api/geocode/json?key=".$api_key_str."&address=".$address;
	                $Http = new pjHttp();
	                $response = $Http->request($gfile)->getResponse();
	                $geoObj = pjAppController::jsonDecode($response);
	                $geoArr = (array) $geoObj;
	                if ($geoArr['status'] == 'OK')
	                {
	                    $html = '<img src="' . $url . '" class="img-responsive" />';
	                    $isValid = true;
	                }
	                break;
	            default:
	                // API key for an unknown service. We can't verify it so we assume it's correct.
	                $isValid = true;
	        }
	        
	        if ($isValid)
	        {
	            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Key is correct!', 'html' => $html));
	        }
	        else
	        {
	            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Key is not correct!', 'html' => $html));
	        }
	    }
	    exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();

		if (!pjAuth::factory()->hasAccess()) {
			$this->sendForbidden();
			return;
		}

		$pjBookingModel = pjBookingModel::factory();
		$pjAuthUserModel = pjAuthUserModel::factory();

		$authUser = $_SESSION[$this->defaultUser];

		//supplier code
		$isSupplier = ($authUser['role_id'] == 5);
		if($isSupplier){
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminSuppliers&action=pjActionIndex");
			exit();
		}


		$isDriver = ($authUser['role_id'] == 4);
		$driverId = null;

		/* ================= DRIVER FILTER ================= */
		if ($isDriver) {
			$driver = pjDriverModel::factory()
				->where('auth_id', $authUser['id'])
				->findAll()
				->getData();
			if (!empty($driver)) {
				$driverId = $driver[0]['id'];
			}
		}

		/* ================= DATE FILTER ================= */
		$from_input = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : null;
		$to_input   = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : null;
		/* ================= OTHER FILTERS ================= */
		$booking_status = isset($_REQUEST['booking_status']) ? $_REQUEST['booking_status'] : null;
		$payment_status = isset($_REQUEST['payment_status']) ? $_REQUEST['payment_status'] : null;
		$time_type = isset($_REQUEST['time_type']) ? $_REQUEST['time_type'] : null;
		$city   = isset($_REQUEST['city']) ? $_REQUEST['city'] : null;
		$fleet_id  = isset($_REQUEST['fleet_id']) ? $_REQUEST['fleet_id'] : null;

		if (empty($from_input) || empty($to_input)) {
			$dateFrom = date('Y-m-01 00:00:00');
			$dateTo   = date('Y-m-t 23:59:59');
			$filter_from = date('Y-m-01');
			$filter_to   = date('Y-m-t');
		} else {
			$dateFrom = date('Y-m-d 00:00:00', strtotime($from_input));
			$dateTo   = date('Y-m-d 23:59:59', strtotime($to_input));
			$filter_from = $from_input;
			$filter_to   = $to_input;
		}

		/* ================= COMMON FILTER FUNCTION ================= */
		$applyBookingFilters = function($model) use (
			$driverId, $booking_status, $payment_status, $dateFrom, $dateTo, $time_type, $city, $fleet_id
		) {
			if ($driverId) {
				$model->where("t1.driver_id", $driverId);
			}

			if (!empty($booking_status)) {
				$model->where("t1.status", $booking_status);
			}

			if (!empty($payment_status)) {
				$model->where("t1.payment_method", $payment_status);
			}

			if (!empty($fleet_id)) {
				$model->where("t1.fleet_id", $fleet_id);
			}

			  // CITY filter using escaped value
			if (!empty($city)) {
				$cityEscaped = pjSanitize::clean($city);
				$model->where("(t1.pickup_address LIKE '%$cityEscaped%' OR t1.return_address LIKE '%$cityEscaped%')");
			}

			if ($time_type == "past") {
				$model->where("t1.booking_date <", date('Y-m-d H:i:s'));
			} elseif ($time_type == "future") {
				$model->where("t1.booking_date >", date('Y-m-d H:i:s'));
			} elseif ($time_type == "present") {
				$model->where("DATE(t1.booking_date)", date('Y-m-d'));
			}

			$model->where("t1.booking_date >=", $dateFrom)
				->where("t1.booking_date <=", $dateTo);

			return $model;
		};

		/* ================= CARDS ================= */
		// Total Reservations
		$total_reservations = $applyBookingFilters(
			$pjBookingModel->reset()
		)->findCount()->getData();

		// Completed
		$completed_bookings = $applyBookingFilters(
			$pjBookingModel->reset()->where("t1.status", "completed")
		)->findCount()->getData();

		// Cancelled
		$cancelled_bookings = $applyBookingFilters(
			$pjBookingModel->reset()->where("t1.status", "cancelled")
		)->findCount()->getData();

		// Revenue
		$revenue_row = $applyBookingFilters(
			$pjBookingModel->reset()->select("ROUND(SUM(t1.total),0) as total_revenue")
		)->findAll()->getData();

		$total_revenue = $revenue_row[0]['total_revenue'] ?? 0;

		/* ================= CUSTOMERS ================= */

		$new_customers = $pjAuthUserModel
			->reset()
			->select("COUNT(t1.id) as cnt")
			->where("t1.role_id", 3)
			->where("t1.created >=", $dateFrom)
			->where("t1.created <=", $dateTo)
			->findAll()->getData();

		$new_customers = $new_customers[0]['cnt'] ?? 0;

		$total_customers = $pjAuthUserModel
			->reset()
			->select("COUNT(t1.id) as cnt")
			->where("t1.role_id", 3)
			->where("t1.created <=", $dateTo)
			->findAll()->getData();

		$total_customers = $total_customers[0]['cnt'] ?? 0;

		/* ================= REVENUE TREND ================= */

		$groupType = isset($_REQUEST['group']) ? $_REQUEST['group'] : 'daily';
		$this->set('groupType', $groupType);

		// $trendModel = $applyBookingFilters(
		// 	$pjBookingModel->reset()->where("t1.status <>", "cancelled")
		// );
		if (empty($booking_status)) {
			$trendModel = $pjBookingModel->reset()->where("t1.status <>", "cancelled");
		} else {
			$trendModel = $pjBookingModel->reset();
		}

		// Apply all filters including status filter
		$trendModel = $applyBookingFilters($trendModel);

		switch ($groupType) {
			case 'weekly':
				$trendModel->select('
					YEARWEEK(t1.created,1) AS period,
					CONCAT("Week ", WEEK(t1.created,1)) AS label,
					SUM(t1.total) AS total,
					STR_TO_DATE(CONCAT(YEAR(t1.created), WEEK(t1.created,1), " Monday"), "%X%V %W") AS week_start
				', false)
				->groupBy('YEARWEEK(t1.created,1)', false)
				->orderBy('week_start ASC'); // <- ensures proper chronological order
				break;

			case 'monthly':
				$trendModel->select('
					DATE_FORMAT(t1.created, "%Y-%m") AS period,
					DATE_FORMAT(t1.created, "%b %Y") AS label,
					SUM(t1.total) AS total
				', false)
				->groupBy('DATE_FORMAT(t1.created, "%Y-%m")', false)
				->orderBy('period ASC');
				break;

			default: // daily
				$trendModel->select('
					DATE(t1.created) AS period,
					DATE_FORMAT(t1.created,"%d %b") AS label,
					SUM(t1.total) AS total
				', false)
				->groupBy('DATE(t1.created)', false)
				->orderBy('period ASC');
		}

		$revenue_trend = $trendModel->findAll()->getData();

		/* ================= STATUS CHART ================= */

		$status_chart = $applyBookingFilters(
		$pjBookingModel->reset()
		->select("t1.status, COUNT(*) as total")
		->groupBy("t1.status")
		)->findAll()->getData();

		/* ================= PAYMENT CHART ================= */

		// $payment_chart = $applyBookingFilters(
		// 	$pjBookingModel->reset()
		// 	->select("t1.payment_method, COUNT(*) as total")
		// 	->groupBy("t1.payment_method")
		// )->findAll()->getData();
		$payment_chart = $applyBookingFilters(
			$pjBookingModel->reset()
			->select("t1.payment_method, COUNT(*) as total_count, SUM(t1.total) as total_amount")
			->groupBy("t1.payment_method")
		)->findAll()->getData();

		/* ================= BOOKINGS PER DAY ================= */

		$bookings_per_day = $applyBookingFilters(
			$pjBookingModel->reset()
			->select("DATE(t1.booking_date) as date, COUNT(*) as total")
			->groupBy("DATE(t1.booking_date)")
			->orderBy("DATE(t1.booking_date) ASC")
		)->findAll()->getData();

		/* ================= BOOKING TREND ================= */

		$bookingAnalysisType = isset($_REQUEST['analysis']) ? $_REQUEST['analysis'] : 'date';

		$this->set('bookingAnalysisType', $bookingAnalysisType);

		$bookingAnalysisModel = $applyBookingFilters(
			$pjBookingModel->reset()
		);
		switch ($bookingAnalysisType) {

			case 'hour':

				$bookingAnalysisModel->select('
					HOUR(t1.booking_date) AS period,
					CONCAT(LPAD(HOUR(t1.booking_date),2,"0"), ":00") AS label,
					COUNT(*) AS total
				', false)
				->groupBy('HOUR(t1.booking_date)', false)
				->orderBy('period ASC');

				break;

			case 'date':
			default:

				$bookingAnalysisModel->select('
					DATE(t1.booking_date) AS period,
					DATE_FORMAT(t1.booking_date,"%d %b %Y") AS label,
					COUNT(*) AS total
				', false)
				->groupBy('DATE(t1.booking_date)', false)
				->orderBy('period ASC');

				break;
		}
		$booking_analysis = $bookingAnalysisModel->findAll()->getData();

		// ================= REVENUE BY VEHICLE =================
		// $revenue_by_fleet = $applyBookingFilters(
		// 	$pjBookingModel->reset()
		// 		->join('pjFleet', 't2.id = t1.fleet_id', 'left')
		// 		->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t2.id AND t3.field='fleet' AND t3.locale='".$this->getLocaleId()."'", 'left')
		// 		->select("t1.fleet_id, t3.content AS fleet_name, SUM(t1.total) AS total_revenue")
		// 		->where("t1.status <>", "cancelled")
		// 		->groupBy("t1.fleet_id")
		// 		->orderBy("total_revenue DESC")
		// )->findAll()->getData();

		if (empty($booking_status)) {
			// No status filter set: exclude cancelled
			$fleetModel = $pjBookingModel->reset()
				->join('pjFleet', 't2.id = t1.fleet_id', 'left')
				->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t2.id AND t3.field='fleet' AND t3.locale='".$this->getLocaleId()."'", 'left')
				->where("t1.status <>", "cancelled");
		} else {
			// Status filter set: include all statuses (including cancelled)
			$fleetModel = $pjBookingModel->reset()
				->join('pjFleet', 't2.id = t1.fleet_id', 'left')
				->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t2.id AND t3.field='fleet' AND t3.locale='".$this->getLocaleId()."'", 'left');
		}

		$revenue_by_fleet = $applyBookingFilters(
			$fleetModel->select("t1.fleet_id, t3.content AS fleet_name, SUM(t1.total) AS total_revenue")
				->groupBy("t1.fleet_id")
				->orderBy("total_revenue DESC")
		)->findAll()->getData();

		$chart_labels = [];
		$chart_data = [];
		foreach ($revenue_by_fleet as $row) {
			// Take the fleet name or fallback
			$label = $row['fleet_name'] ?: "Fleet #" . $row['fleet_id'];

			// Remove everything after ' -- '
			if (strpos($label, '--') !== false) {
				$label = trim(explode('--', $label)[0]);
			}

			// Shorten the label if it's still long (optional: max 20 chars)
			if (strlen($label) > 20) {
				$label = substr($label, 0, 20) . '...';
			}

			$chart_labels[] = $label;
			$chart_data[] = (float) $row['total_revenue'];
		}


		/* ================= PASS DATA TO VIEW ================= */
		$pjFleetModel = pjFleetModel::factory();

		$fleet_arr = $pjFleetModel
		->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		->select("t1.id, t2.content as fleet")
		->where('t1.status', 'T')
		->findAll()->getData();
		$this->set('fleets', $fleet_arr);

		$column = 'name';
		$direction = 'ASC';
		

		$pjCityModel = pjCityModel::factory();
		$city_array = $pjCityModel
		->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCity' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
		->select('t1.id, t2.content AS name')
		->orderBy("$column $direction")
		->findAll()
		->getData();
		$this->set('cities', $city_array);

		// echo'<pre>';print_r($payment_chart);die;

		$this->set('filter_from', $filter_from);
		$this->set('filter_to', $filter_to);
		$this->set('total_reservations', $total_reservations);
		$this->set('completed_bookings', $completed_bookings);
		$this->set('cancelled_bookings', $cancelled_bookings);
		$this->set('total_revenue', $total_revenue);
		$this->set('new_customers', $new_customers);
		$this->set('total_customers', $total_customers);
		$this->set('revenue_trend', $revenue_trend);
		$this->set('status_chart', $status_chart);
		$this->set('payment_chart', $payment_chart);
		$this->set('bookings_per_day', $bookings_per_day);
		$this->set('booking_analysis', $booking_analysis);
		$this->set('revenue_by_vehicle', [
					'labels' => $chart_labels,
					'data'   => $chart_data
				]);
		// -------------------------------
		// 2️⃣1️⃣ Append JS/CSS files for dashboard
		// -------------------------------
		$version = rand(0,9) . '.' . rand(0,9) . '.' . rand(0,9);
		$this->appendJs('index.global.js', PJ_THIRD_PARTY_PATH . 'fullcalendar/');
		$this->appendJs('index.global.min.js', PJ_THIRD_PARTY_PATH . 'fullcalendar/');
		$this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
		$this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
		$this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs("pjAdmin.js?v=1");
	}

     public function pjActionCalendar()
		{
			$this->checkLogin();
			// if (!$this->isAdmin())
			// {
			// 	pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdmin&action=pjActionIndex");
			// }

			$pjDriverModel = pjDriverModel::factory();
            $deriver_ids =  $pjDriverModel->findAll()->getData();
            
			$this->set('has_update', pjAuth::factory('pjAdminBookings', 'pjActionUpdate')->hasAccess());

            $this->set('deriver_ids', $deriver_ids);

			$this->setLayout('pjActionAdmin'); // Admin layout

			// Load FullCalendar assets (CSS + JS)
			$this->appendJs('index.global.js', PJ_THIRD_PARTY_PATH . 'fullcalendar/');
			$this->appendJs('index.global.min.js', PJ_THIRD_PARTY_PATH . 'fullcalendar/');
		}
        
	// public function pjActionIndex()
	// {
	//     $this->checkLogin();
	//     if (!pjAuth::factory()->hasAccess())
	//     {
	//         $this->sendForbidden();
	//         return;
	//     }
	    
	//     $pjBookingModel = pjBookingModel::factory();
	    
	//     $enquiries_received_today = $pjBookingModel->where("(DATE_FORMAT(t1.created, '%Y-%m-%d')='".date('Y-m-d')."')")->findCount()->getData();
	//     $reservations_today = $pjBookingModel->reset()->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='".date('Y-m-d')."')")->where("t1.status <>", 'cancelled')->findCount()->getData();
	//     $total_reservations = $pjBookingModel->reset()->findCount()->getData();
	    
	//     $latest_enquiries = $pjBookingModel
	//     ->reset()
	//     ->select("t1.*, t2.content as fleet, t4.name, t4.email, t4.phone")
	//     ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	//     ->join('pjClient', "t3.id=t1.client_id", 'left outer')
	//     ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
	//     ->orderBy("t1.created DESC")
	//     ->limit(4)
	//     ->findAll()->getData();
	    
	//     $reservations_today_arr = $pjBookingModel
	//     ->reset()
	//     ->select("t1.*, t2.content as fleet, t4.name, t4.email, t4.phone")
	//     ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	//     ->join('pjClient', "t3.id=t1.client_id", 'left outer')
	//     ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
	//     ->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='".date('Y-m-d')."')")
	//     ->where("t1.status <>", 'cancelled')
	//     ->orderBy("t1.booking_date ASC")
	//     ->limit(4)
	//     ->findAll()->getData();
	    
	//     $this->set('enquiries_received_today', $enquiries_received_today);
	//     $this->set('reservations_today', $reservations_today);
	//     $this->set('total_reservations', $total_reservations);
	    
	//     $this->set('latest_enquiries', $latest_enquiries);
	//     $this->set('reservations_today_arr', $reservations_today_arr);
	// }
	
	public function rebuildPermissions()
	{
	    $this->setLayout('pjActionEmpty');
	    
	    $pjAuthRolePermissionModel = pjAuthRolePermissionModel::factory();
	    $pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();
	    
	    $permissions = pjAuthPermissionModel::factory()->findAll()->getDataPair('key', 'id');
	    
	    $roles = array(1 => 'admin', 2 => 'editor');
	    foreach ($roles as $role_id => $role)
	    {
	        if (isset($GLOBALS['CONFIG'], $GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && is_array($GLOBALS['CONFIG']["role_permissions_{$role}"])
	        && !empty($GLOBALS['CONFIG']["role_permissions_{$role}"]))
	        {
	            $pjAuthRolePermissionModel->reset()->where('role_id', $role_id)->eraseAll();
	            
	            foreach ($GLOBALS['CONFIG']["role_permissions_{$role}"] as $role_permission)
	            {
	                if($role_permission == '*')
	                {
	                    // Grant full permissions for the role
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                    }
	                    break;
	                }
	                else
	                {
	                    $hasAsterix = strpos($role_permission, '*') !== false;
	                    if($hasAsterix)
	                    {
	                        $role_permission = str_replace('*', '', $role_permission);
	                    }
	                    
	                    foreach($permissions as $key => $permission_id)
	                    {
	                        if($role_permission == $key || ($hasAsterix && strpos($key, $role_permission) !== false))
	                        {
	                            $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
	                        }
	                    }
	                }
	            }
	        }
	    }
	    echo 'DONE!';
	    exit;
	}

	public function pjActionDriverCalendarEvents()
	{
	    // Required for FullCalendar (it does not send XHR)
	    $this->setAjax(false); 

	    $auth = pjAuth::factory();
	    $roleId = $auth->getRoleId();
	    $id = $this->getUserId(); // driver auth ID

	    if ((int)$roleId === 4) {
	        // Driver = only his bookings
	        $pjBookingModel = pjBookingModel::factory()
	             ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	            ->join('pjClient', "t3.id=t1.client_id", 'left outer')
	            ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
	            ->join('pjDriver', "t5.id=t1.driver_id", 'left outer')
	            ->where("t5.auth_id", $id);
	    } else {
	        // Admin = all bookings
	        // $pjBookingModel = pjBookingModel::factory();
	        $pjBookingModel = pjBookingModel::factory()
	           ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	           ->join('pjClient', "t3.id=t1.client_id", 'left outer')
	           ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer');

	    }

	     $data = $pjBookingModel
	           ->select("t1.*, t2.content as fleet, t4.name, t4.email,t4.phone, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
	            AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`, AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`, AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`, AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")  ->findAll() ->getData();
	    // echo '<pre>'; print_r($data); echo '</pre>'; die();// debugging
	    $events = [];

	    foreach ($data as $v)
	    {
	        $color = '#3788d8'; // default blue

	            if ($v['status'] == 'confirmed') {
	                $color = '#28a745'; // green
	            } elseif ($v['status'] == 'pending') {
	                $color = '#ffc107'; // yellow
	            } elseif ($v['status'] == 'cancelled') {
	                $color = '#dc3545'; // red
	            }

	        $start = date('c', strtotime($v['booking_date']));
	        // date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['booking_date']));
	       
	        // $events[] = [
	        //     "id"    => $v['id'],
	        //     "title" => $v['pickup_address'] . ' - ' . $v['return_address'],
	        //     "start" => $start,
	        //     "end" => $start,
	        //     "allDay" => false
	        // ];
	               
	        $events[] = [
	        "id"          => $v['id'],
	        "title" => pjSanitize::clean($v['name']) . "\n" . $v['fleet'],
	        "start"       => $start,
	        "end"         => $start,
	        "pickup"      => $v['pickup_address'],
	        "return"      => $v['return_address'],
	        "status"      => $v['status'],
	        "passengers"      => $v['passengers'],
	        "color"       => $color
	        ];
	    }

	    header('Content-Type: application/json');
	    echo json_encode($events);
	    exit;
	}

	public function pjActionDriverViewEvents()
    {
       $this->setLayout('pjActionPrint');
		$id = $this->_get->toInt('id');
		
		$booking = pjBookingModel::factory()
		->select("t1.*, t2.*, t3.email AS c_email, t3.name AS c_name, t3.phone AS c_phone")
		->join("pjClient", "t2.id = t1.client_id", 'left outer')
		->join("pjAuthUser", "t3.id = t2.foreign_id", 'left outer')
		->find($id)
		->getData();

		// Build name from client record if available
		$clientFullName = trim(($booking['c_fname'] ?? '') . ' ' . ($booking['c_lname'] ?? ''));

		// If first+last not available, fall back to auth user name
		$bookedName = !empty($clientFullName) ? $clientFullName : $booking['c_name'];
		$this->set('bookedName', $bookedName);
    }

	public function pjActionDriverUpdateEvents()
		{
			$id = $this->_get->toInt('id');

			pjBookingModel::factory()->set('id', $id)->modify(array('status' => 'completed'));
			 $err = 'Booking is completed successfully.';
			pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdmin&action=pjActionIndex&err=$err");

		}

}
?>