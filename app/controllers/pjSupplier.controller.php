<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjSupplier extends pjAdmin{

	public function pjActionIndex(){

		$this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }

        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs('pjSupplier.js');
	}

	public function pjActionCreate(){
		$this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }

        if (self::isPost() && $this->_post->toInt('supplier_create'))
        {
        	$post = $this->_post->raw();
        	if($this->_post->check('status'))
            {
                $post['status'] = 'T';
            }else{
                $post['status'] = 'F';
            }
            $post['locale_id'] = $this->getLocaleId();
            $response = pjFrontClient::init($post)->createSupplier();
            if($response['status'] == 'OK')
            {
                $err = 'AC03';
            }else{
                $err = 'AC04';
            }
            pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjSupplier&action=pjActionIndex&err=$err");
			
		}

		if (self::isGet())
        {
        	$country_arr = pjBaseCountryModel::factory()
            ->select('t1.id, t2.content AS country_title')
            ->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
            ->where('status', 'T')
            ->orderBy('`country_title` ASC')->findAll()->getData();
            $this->set('country_arr', $country_arr);

            $this->set('v_cats', pjCategoryModel::factory()
            ->select('t1.*')
            ->orderBy('category ASC')
            ->findAll()
            ->getData());
            
            //$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
            //$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
            $this->appendCss('bootstrap-chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
            $this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
            $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
            $this->appendJs('pjSupplier.js');
        }
	}

	public function pjActionUpdate(){
		$this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
       if (self::isPost() && $this->_post->toInt('supplier_update') && $this->_post->toInt('id'))
        {
            $pjSupplierModel = pjSupplierModel::factory();
            $id = $this->_post->toInt('id');

            /* ===== GET OLD SUPPLIER + USER ===== */
            $old_supplier = $pjSupplierModel->find($id)->getData();
            if (empty($old_supplier)) {
                pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjSupplier&action=pjActionIndex&err=AC08");
            }

            $auth_id = $old_supplier['auth_id'];

            $old_user = pjAuthUserModel::factory()->find($auth_id)->getData();
            $old_status = $old_user['status'];

            /* ===== SUPPLIER DATA ===== */
            $supplier_data = array();
            $supplier_data['first_name'] = $this->_post->toString('fname');
            $supplier_data['last_name'] = $this->_post->toString('lname');
            $supplier_data['company_name'] = $this->_post->toString('company');
            $supplier_data['phone'] = $this->_post->toString('phone');
            $supplier_data['city'] = $this->_post->toString('city');

            $categories = $this->_post->toArray('category');
            if (!empty($categories)) {
                $supplier_data['vehicle_category'] = implode(',', $categories);
            }

            /* ===== UPDATE SUPPLIER TABLE ===== */
            $pjSupplierModel
                ->reset()
                ->where('id', $id)
                ->limit(1)
                ->modifyAll($supplier_data);
            /* ===== USER DATA ===== */
            $data = array();
            $data['id'] = $auth_id;
            $data['email'] = $this->_post->toString('email');
            $data['password'] = $this->_post->toString('password');
            $data['phone'] = $this->_post->toString('phone');

            $name_arr = array();
            $name_arr[] = $this->_post->toString('fname');
            $name_arr[] = $this->_post->toString('lname');
            $data['name'] = join(" ", $name_arr);

            if ($this->_post->check('status')) {
                $data['status'] = 'T';
            } else {
                $data['status'] = 'F';
            }

            /* ===== UPDATE USER ===== */
            pjAuth::init($data)->updateUser();

            /* ===== SEND EMAIL ONLY IF STATUS CHANGED F -> T ===== */
            if ($old_status == 'F' && $data['status'] == 'T') {

                $option_arr = $this->option_arr;
                $locale_id = $this->getLocaleId();

                register_shutdown_function(function() use ($id, $option_arr, $locale_id) {
                    pjAppController::pjActionSupplierAccountSend(
                        $option_arr,
                        $id,
                        PJ_SALT,
                        'activeaccount',
                        $locale_id
                    );
                });
            }

            /* ===== REDIRECT ===== */
            pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjSupplier&action=pjActionUpdate&id=".$id."&err=AC01");
        }

        if (self::isGet() && $this->_get->toInt('id'))
        {
            $id = $this->_get->toInt('id');
            $order_table = pjBookingModel::factory()->getTable();
            $arr = pjSupplierModel::factory()
            ->select("t1.*, t2.email, t2.name, t2.phone, t2.status as status, AES_DECRYPT(t2.password, '".PJ_SALT."') AS password,
							  (SELECT COUNT(TB.id) FROM `".$order_table."` AS TB WHERE TB.client_id = t1.id) AS cnt_orders,
							  (SELECT SUM(TB.total) FROM `".$order_table."` AS TB WHERE TB.client_id = t1.id) AS total_amount,
							  (SELECT CONCAT(TB.created, '~:~', TB.id) FROM `".$order_table."` AS TB WHERE TB.client_id = t1.id ORDER BY TB.created DESC LIMIT 1) AS last_order")
    		  ->join('pjAuthUser', 't2.id=t1.auth_id', 'left outer')
    		  ->find($id)
    		  ->toArray("last_order", "~:~")
    		  ->getData();
    		  
    		  if (count($arr) === 0)
    		  {
    		      pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjSupplier&action=pjActionIndex&err=AC08");
    		  }
    		  $this->set('arr', $arr);

              $this->set('v_cats', pjCategoryModel::factory()
                ->select('t1.*')
                ->orderBy('category ASC')
                ->findAll()
                ->getData());
    		  
    		  $country_arr = pjBaseCountryModel::factory()
    		  ->select('t1.id, t2.content AS country_title')
    		  ->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
    		  ->where('status', 'T')
    		  ->orderBy('`country_title` ASC')->findAll()->getData();
    		  
    		  $this->set('country_arr', $country_arr);
    		  
    		  //$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
    		  //$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
              $this->appendCss('bootstrap-chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
            $this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
    		  $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
    		  $this->appendJs('pjSupplier.js');
        }
	}

	public function pjActionGetSupplier()
    {

        $this->setAjax(true);
        
        if ($this->isXHR())
        {
        	
            $pjSupplierModel = pjSupplierModel::factory()
            ->join('pjAuthUser', 't2.id=t1.auth_id', 'left outer');
            
            if ($q = $this->_get->toString('q'))
            {
                $pjSupplierModel->where("(t2.email LIKE '%$q%' OR t2.name LIKE '%$q%')");
            }
            if ($this->_get->toString('status'))
            {
                $status = $this->_get->toString('status');
                if(in_array($status, array('T', 'F')))
                {
                    $pjSupplierModel->where('t2.status', $status);
                }
            }
            $column = 'name';
            $direction = 'ASC';
            if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
            {
                $column = $this->_get->toString('column');
                $direction = strtoupper($this->_get->toString('direction'));
            }
            
            $total = $pjSupplierModel->findCount()->getData();
            
            $rowCount = $this->_get->toInt('rowCount') ?: 10;
            $pages = ceil($total / $rowCount);
            $page = $this->_get->toInt('page') ?: 1;
            $offset = ((int) $page - 1) * $rowCount;
            if ($page > $pages)
            {
                $page = $pages;
            }
            
            $data = $pjSupplierModel
            ->select("t1.id, t2.email, t2.phone, t2.name, t2.status, t2.locked, (SELECT COUNT(TO.client_id) FROM `".pjBookingModel::factory()->getTable()."` AS `TO` WHERE `TO`.client_id=t1.id) AS cnt_orders")
            ->orderBy("$column $direction")
            ->limit($rowCount, $offset)
            ->findAll()
            ->getData();
            foreach($data as $k => $v)
            {
                $v['name'] = pjSanitize::stripScripts($v['name']);
                $v['email'] = pjSanitize::stripScripts($v['email']);
                $data[$k] = $v;
            }
            
            pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
        }
        exit;
    }

    public function pjActionDeleteSupplier(){
    	$this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        if (!pjAuth::factory()->hasAccess())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
        }
        if (!($this->_get->toInt('id')))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }

        $pjSupplierModel = pjSupplierModel::factory();
        $client = $pjSupplierModel->find($this->_get->toInt('id'))->getData();
        if (!$pjSupplierModel->reset()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Supplier has not been deleted.'));
        }
        pjAuthUserModel::factory()->set('id', $client['auth_id'])->erase();
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Supplier has been deleted'));
        exit;
    }

    public function pjActionSaveSupplier()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        $params = array(
            'id' => $this->_get->toInt('id'),
            'column' => $this->_post->toString('column'),
            'value' => $this->_post->toString('value'),
        );
        if (!(isset($params['id'], $params['column'], $params['value'])
            && pjValidation::pjActionNumeric($params['id'])
            && pjValidation::pjActionNotEmpty($params['column'])))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing, empty or invalid parameters.'));
        }
        
        if ($params['column'] == 'locked')
        {
            $result = pjAuth::init()->unlockAccount($params['id']);
            if ($result['status'] == 'OK') {
                $client = pjClientModel::factory()->find($params['id'])->getData();
                pjAuthUserModel::factory()->set('id', $client['foreign_id'])->modify(array('locked' => $params['value']));
            }
        } else {
            pjSupplierModel::factory()->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
            if(in_array($params['column'], array('status', 'email', 'name','phone')))
            {
                $client = pjSupplierModel::factory()->reset()->find($params['id'])->getData();
                $params['column'] = pjMultibyte::str_replace('c_', '', $params['column']);
                $params['id'] = $client['auth_id'];
                pjAuth::init($params)->updateUser();
            }
        }
        // self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Supplier has been updated!'));
         /* ================= SEND EMAIL IF STATUS = T ================= */

        if ($params['column'] == 'status' && $params['value'] == 'T') {

            $supplier = pjSupplierModel::factory()
                ->find($this->_get->toInt('id'))
                ->getData();

            if (!empty($supplier)) {
                pjAppController::pjActionSupplierAccountSend(
                    $this->option_arr,
                    $this->_get->toInt('id'),
                    PJ_SALT,
                    'activeaccount',
                    $this->getLocaleId()
                );
            }
        }

        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Supplier has been updated!'));
        exit;
    }

    public function pjActionDeleteSupplierBulk()
    {
        $this->setAjax(true);
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        if (!pjAuth::factory()->hasAccess())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
        }
        if (!$this->_post->has('record'))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $record = $this->_post->toArray('record');
        if (empty($record))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $pjSupplierModel = pjSupplierModel::factory();
        $foreign_ids = $pjSupplierModel->whereIn('id', $record)->findAll()->getDataPair(null, 'auth_id');
        $pjSupplierModel->reset()->whereIn('id', $record)->eraseAll();
        if(!empty($foreign_ids))
        {
            pjAuthUserModel::factory()
            ->where('role_id', 5)
            ->whereIn('id', $foreign_ids)
            ->eraseAll();
        }
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Supplier(s) has been deleted.'));
        exit;
    }

    public function pjActionStatusSupplier()
    {
        $this->setAjax(true);
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        if (!pjAuth::factory()->hasAccess())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
        }
        $record = $this->_post->toArray('record');
        if (empty($record))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $foreign_ids = pjSupplierModel::factory()->whereIn('id', $record)->findAll()->getDataPair(null, 'auth_id');
        if(!empty($foreign_ids))
        {
            pjAuthUserModel::factory()
            ->whereIn('id', $foreign_ids)
            ->where('id !=', 1)
            ->modifyAll(array(
                'status' => ":IF(`status`='F','T','F')"
            ));
        }
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Supplier status has been updated.'));
        exit;
    }

    public function pjActionExportSupplier()
    {
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        
        $record = $this->_post->toArray('record');
        if (count($record))
        {
            $arr = pjSupplierModel::factory()
            ->select("t1.*, t2.email as email, t2.name as name, t2.phone as phone")
            ->join("pjAuthUser", 't2.id=t1.auth_id', 'left outer')
            ->whereIn('t1.id', $record)->findAll()->getData();
            $csv = new pjCSV();
            $csv
            ->setHeader(true)
            ->setName("Suppliers-".time().".csv")
            ->process($arr)
            ->download();
        }
        exit;
    }
}