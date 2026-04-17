<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminReport extends pjAdmin
{
    public function pjActionIndex()
    {
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        $this->setLocalesData();

        // ✅ GET SUPPLIERS
        $pjUserModel = pjAuthUserModel::factory();

        $suppliers = $pjUserModel
            ->where('t1.role_id', 5)
            ->orderBy('t1.name ASC')
            ->findAll()
            ->getData();
            
        $this->set('suppliers', $suppliers);
        
        $version = rand(0,9) . '.' . rand(0,9) . '.' . rand(0,9);
        $this->set('date_format', pjUtil::toBootstrapDate($this->option_arr['o_date_format']));
        $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
        $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
        $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs("pjAdminReport.js?v={$version}");
    }
    public function pjActionGetBooking()
    {
        $this->checkLogin();
        $this->setAjax(true);
        
        if ($this->isXHR())
        {
            $pjBookingModel = pjBookingModel::factory()
            ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
            ->join('pjClient', "t3.id=t1.client_id", 'left outer')
            ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
            ->where("t1.is_auction = 1")
            ->where("t1.is_deleted = 0");

            if ($this->_get->has('q') && !$this->_get->isEmpty('q'))
            {
                $q = $this->_get->toString('q');

                $pjBookingModel->where("(t4.name LIKE '%$q%' OR t4.email LIKE '%$q%' OR t2.content LIKE '%$q%' OR t1.uuid LIKE '%$q%' OR t1.c_fname LIKE '%$q%' OR t1.c_lname LIKE '%$q%' OR TRIM(CONCAT(t1.c_fname, ' ', t1.c_lname)) LIKE '%$q%' OR t1.c_phone LIKE '%$q%' OR t1.c_company LIKE '%$q%')");
            }
            
            if ($this->_get->toInt('fleet_id') > 0)
            {
                $fleet_id = $this->_get->toInt('fleet_id');
                $pjBookingModel->where("(t1.fleet_id='".$fleet_id."')");
            }
            if ($this->_get->toInt('client_id') > 0)
            {
                $client_id = $this->_get->toInt('client_id');
                $pjBookingModel->where("(t1.client_id='".$client_id."')");
            }
            if (!$this->_get->isEmpty('status') && in_array($this->_get->toString('status'), array('confirmed','cancelled','pending', 'completed')))
            {
                $pjBookingModel->where('t1.status', $this->_get->toString('status'));
            }
            if ($this->_get->toInt('supplier') > 0) {
                $pjBookingModel->where('t1.supplier_id', $this->_get->toInt('supplier'));
            }
            if (!$this->_get->isEmpty('name'))
            {
                $q = $this->_get->toString('name');
                $pjBookingModel->where("(t4.name LIKE '%$q%')");
            }
            if (!$this->_get->isEmpty('email'))
            {
                $q = $this->_get->toString('email');
                $pjBookingModel->where('t4.email LIKE', "%$q%");
            }
            if (!$this->_get->isEmpty('phone'))
            {
                $q = $this->_get->toString('phone');
                $pjBookingModel->where('t4.phone LIKE', "%$q%");
            }
            if (!$this->_get->isEmpty('date'))
            {
                $pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='".$this->_get->toString('date')."')");
            }
            if (!$this->_get->isEmpty('start_date'))
            {
                $start_date = $this->_get->toString('start_date');
                $pjBookingModel->where("DATE(t1.booking_date) >=", $start_date);
            }

            // TO DATE
            if (!$this->_get->isEmpty('end_date'))
            {
                $end_date = $this->_get->toString('end_date');
                $pjBookingModel->where("DATE(t1.booking_date) <=", $end_date);
            }
            $column = 'created';
            $direction = 'DESC';
            if ($this->_get->check('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
            {
                $column = $this->_get->toString('column');
                $direction = strtoupper($this->_get->toString('direction'));
            }
            
            $total = $pjBookingModel->findCount()->getData();
            
            $rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 20;
            $pages = ceil($total / $rowCount);
            $page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
            $offset = ((int) $page - 1) * $rowCount;
            if ($page > $pages)
            {
                $page = $pages;
            }
            
            $data = array();
            
            $data = $pjBookingModel
            ->select("t1.*, t2.content as fleet, t4.name, t4.email,t4.phone, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
            AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`, AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`, AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`, AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`,CONCAT_WS(' ', t5.first_name, t5.last_name) AS driver_name,t6.name AS supplier_name ")
            ->join('pjDriver', "t1.driver_id=t5.id", 'left')
            ->join('pjAuthUser', "t6.id=t1.supplier_id", 'left')
            ->orderBy("$column $direction")
            ->limit($rowCount, $offset)
            ->findAll()
            ->getData();

            $booking_ids = array_column($data, 'id');


            $extras_model = pjBookingExtraModel::factory()
            ->select("t1.*, t2.content AS extra_name")
            ->join(
                'pjMultiLang',
                "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'",
                'left'
            )
            ->whereIn('t1.booking_id', $booking_ids)
            ->where("t1.extra_value > 0")
            ->findAll()
            ->getData();

            $extras_by_booking = [];
            foreach ($extras_model as $ex) {
                $extras_by_booking[$ex['booking_id']][] = $ex;
            }

            foreach($data as $k => $v)
            {
                // echo "<pre>"; print_r($v); echo "</pre>";

                $v['client'] = $fullName = trim($v['c_fname'] . ' ' . $v['c_lname']);

                $v['client'] = pjSanitize::clean($fullName !== '' ? $fullName : $v['name']); // pjSanitize::clean($v['name']);

                $v['date_time']  = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['booking_date']));
                $v['distance'] = (int) $v['distance'] . ' km';
                $v['driver_name'] = pjSanitize::clean($v['driver_name'] ? $v['driver_name'] : 'NA');
                 // ✅ SUPPLIER AMOUNT CALCULATION
                $total_amount = (float) $v['total'];
                $commission = (float) $v['commission_amount'];

                $v['supplier_amount'] = number_format($total_amount - $commission, 2, '.', '');                
                $data[$k] = $v;
            }
            $total_price = 0;
            $total_commission = 0;
            $total_supplier_amount = 0;

            foreach ($data as $k => $v)
            {
                $total_price += (float)$v['total'];
                $total_commission += (float)$v['commission_amount'];
                $total_supplier_amount += (float)$v['supplier_amount'];
            }
            self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction','total_price','total_commission',   'total_supplier_amount'));
        }
        exit;
    }

    public function pjActionExport()
    {
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        
        $record = $this->_post->toArray('record');
        if (!empty($record))
        {
            $arr = pjBookingModel::factory()->whereIn('id', $record)->findAll()->getData();
            $csv = new pjCSV();
            $csv
            ->setHeader(true)
            ->setName("PayoutReport-".time().".csv")
            ->process($arr)
            ->download();
        }
        exit;
    }

    public function pjActionExportForSupplier()
    {
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        
        $record = $this->_post->toArray('record');

        if (!empty($record))
        {
            $data = pjBookingModel::factory()
                ->select("t1.*")
                ->whereIn('t1.id', $record)
                ->findAll()
                ->getData();

            $export = array();

            foreach ($data as $item)
            {
                $export[] = array(
                    'Booking ID'  => $item['uuid'],
                    'Pickup'      => $item['pickup_address'],
                    'Dropoff'     => $item['return_address'],
                    'Date Time'   => date('d-m-Y H:i', strtotime($item['booking_date'])),
                    'Distance'       => $item['distance'],
                    'Total'       => $item['total'],
                    'Commission'  => !empty($item['commission_amount']) ? $item['commission_amount'] : 0,
                    'Status'      => $item['status']
                );
            }

            $csv = new pjCSV();
            $csv
                ->setHeader(true)
                ->setName("PayoutReport-" . time() . ".csv")
                ->process($export)
                ->download();
        }
        exit;
    }
    
}
?>