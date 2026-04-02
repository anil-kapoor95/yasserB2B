<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjAdminFleets extends pjAdmin
{
    public function pjActionCheckPrices()
    {
        $this->setAjax(true);
        
        if ($this->isXHR())
        {
            if($this->_post->check('index_arr') && !$this->_post->isEmpty('index_arr'))
            {
                $post = $this->_post->raw();
                $index_arr = explode("|", $this->_post->toString('index_arr'));
                if(count($index_arr) > 1)
                {
                    foreach($index_arr as $k => $v)
                    {
                        $start = $post['start'][$v];
                        $end = $post['end'][$v];
                        if(isset($index_arr[$k + 1]))
                        {
                            for($i = $k + 1; $i < count($index_arr); $i++)
                            {
                                $tmp_start = $post['start'][$index_arr[$i]];
                                $tmp_end = $post['end'][$index_arr[$i]];
                                
                                if($start <= $tmp_end && $end >= $tmp_start)
                                {
                                    self::jsonResponse(array('status' => "ERR", 'text' => __('lblPricesDuplicated', true)));
                                }
                            }
                        }
                    }
                }
            }
            self::jsonResponse(array('status' => "OK"));
        }
    }
    
    public function pjActionIndex()
    {
        $this->set('has_access_create', pjAuth::factory('pjAdminFleets', 'pjActionCreate')->hasAccess());
        $this->set('has_access_update', pjAuth::factory('pjAdminFleets', 'pjActionUpdate')->hasAccess());
        
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
       $this->appendJs('pjAdminFleets.js?v=' . $this->generateVersion());
    }

    protected function generateVersion()
        {
            return rand(1, 9) . '.' . rand(0, 9) . '.' . rand(0, 9) . '.' . rand(0, 9);
        }

    public function pjActionGetFleet()
    {
        $this->setAjax(true);
        
        if ($this->isXHR())
        {
            $pjFleetModel = pjFleetModel::factory();
            
            $pjFleetModel
            ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
            ->join('pjMultiLang', "t3.model='pjFleet' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='".$this->getLocaleId()."'", 'left outer');
            
            
            if ($q = $this->_get->toString('q'))
            {
                $pjFleetModel->where(
                    "(t2.content LIKE '%$q%'
                      OR t3.content LIKE '%$q%'
                      OR EXISTS (
                            SELECT 1
                            FROM taxi_plugin_base_multi_lang ml
                            WHERE ml.model='pjCategory'
                              AND ml.field='category'
                              AND ml.foreign_id=t1.category
                              AND ml.locale='".$this->getLocaleId()."'
                              AND ml.content LIKE '%$q%'
                      ))"
                );
            }
            
            if ($this->_get->toString('status') && !$this->_get->isEmpty('status') && in_array($this->_get->toString('status'), array('T', 'F')))
            {
                $pjFleetModel->where('t1.status', $this->_get->toString('status'));
            }
            
            $column = 't2.content';
            $direction = 'ASC';
            if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
            {
                //$column = $this->_get->toString('column');
                switch ($this->_get->toString('column')) {
                    case 'category':
                        $column = "(SELECT ml.content
                            FROM taxi_plugin_base_multi_lang ml
                            WHERE ml.model='pjCategory'
                              AND ml.field='category'
                              AND ml.foreign_id=t1.category
                              AND ml.locale='".$this->getLocaleId()."'
                            LIMIT 1)";
                        break;
                    case 'fleet':
                        $column = 't2.content';
                        break;
                    default:
                        $column = 't1.' . $this->_get->toString('column');
                }
                $direction = strtoupper($this->_get->toString('direction'));
            }
            
            $pjFleetCount = clone $pjFleetModel;
            $total = $pjFleetModel->findCount()->getData();
            $rowCount = $this->_get->toInt('rowCount') ?: 10;
            $pages = ceil($total / $rowCount);
            $page = $this->_get->toInt('page') ?: 1;
            $offset = ((int) $page - 1) * $rowCount;
            if ($page > $pages)
            {
                $page = $pages;
            }
            
            $data = array();
            
            $data = $pjFleetModel
            
            ->select(
            "t1.id,
             t1.thumb_path,
             t2.content AS fleet,
             t1.passengers,
             t1.luggage,
             (
                SELECT ml.content
                FROM taxi_plugin_base_multi_lang ml
                WHERE ml.model='pjCategory'
                  AND ml.field='category'
                  AND ml.foreign_id=t1.category
                  AND ml.locale='".$this->getLocaleId()."'
                LIMIT 1
             ) AS category,
             t1.status"
        )
            ->orderBy("$column $direction")
            ->limit($rowCount, $offset)
            ->findAll()->getData();
            
            self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
        }
        exit;
    }
    
    public function pjActionDeleteFleet()
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
        if (!($this->_get->toInt('id')))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $pjFleetModel = pjFleetModel::factory();
        $arr = $pjFleetModel->find($this->_get->toInt('id'))->getData();
        if (!$arr)
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Fleet not found.'));
        }
        $id = $this->_get->toInt('id');
        if ($pjFleetModel->setAttributes(array('id' => $id))->erase()->getAffectedRows() == 1)
        {
            if(file_exists(PJ_INSTALL_PATH . $arr['source_path']))
            {
                @unlink(PJ_INSTALL_PATH . $arr['source_path']);
            }
            if(file_exists(PJ_INSTALL_PATH . $arr['thumb_path']))
            {
                @unlink(PJ_INSTALL_PATH . $arr['thumb_path']);
            }
            pjMultiLangModel::factory()->where('model', 'pjFleet')->where('foreign_id', $id)->eraseAll();
            pjFleetExtraModel::factory()->where('fleet_id', $id)->eraseAll();
            pjPriceModel::factory()->where('fleet_id', $id)->eraseAll();
            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Fleet has been deleted'));
        }else{
            self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Fleet has not been deleted.'));
        }
        exit;
    }
    
    public function pjActionDeleteFleetBulk()
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
        $pjFleetModel = pjFleetModel::factory();
        $arr = $pjFleetModel->whereIn('id', $record)->findAll()->getData();
        foreach($arr as $v)
        {
            if(file_exists(PJ_INSTALL_PATH . $v['source_path']))
            {
                @unlink(PJ_INSTALL_PATH . $v['source_path']);
            }
            if(file_exists(PJ_INSTALL_PATH . $v['thumb_path']))
            {
                @unlink(PJ_INSTALL_PATH . $v['thumb_path']);
            }
        }
        $pjFleetModel->reset()->whereIn('id', $record)->eraseAll();
        pjMultiLangModel::factory()->where('model', 'pjFleet')->whereIn('foreign_id', $record)->eraseAll();
        pjFleetExtraModel::factory()->whereIn('fleet_id', $record)->eraseAll();
        pjPriceModel::factory()->whereIn('fleet_id', $record)->eraseAll();
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Fleet(s) has been deleted.'));
        exit;
    }
    
    public function pjActionCreate()
    {
        // echo "<pre>"; print_r($this->_post); echo "</pre>"; die('kkkkk');
        $post_max_size = pjUtil::getPostMaxSize();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
        {
            pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF05");
        }
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        if (self::isPost() && $this->_post->toInt('fleet_create'))
        {
            $data = array();
            $post = $this->_post->raw();
           
            $data['passengers'] = $this->_post->toInt('passengers');
            $data['luggage'] = $this->_post->toInt('luggage');
            $data['category'] = $this->_post->toInt('category');
            $id = pjFleetModel::factory(array_merge($post, $data))->insert()->getInsertId();
            if ($id !== false && (int) $id > 0)
            {
                if (isset($post['i18n']))
                {
                    pjMultiLangModel::factory()->saveMultiLang($post['i18n'], $id, 'pjFleet', 'data');
                }
                
                $pjFleetExtraModel = pjFleetExtraModel::factory();
                $extra_id_arr = $this->_post->toArray('extra_id');
                if (count($extra_id_arr) > 0)
                {
                    $pjFleetExtraModel->begin();
                    foreach ($extra_id_arr as $extra_id)
                    {
                        $pjFleetExtraModel
                        ->reset()
                        ->set('fleet_id', $id)
                        ->set('extra_id', $extra_id)
                        ->insert();
                    }
                    $pjFleetExtraModel->commit();
                }
                
                if($this->_post->check('index_arr') && !$this->_post->isEmpty('index_arr'))
                {
                    $index_arr = explode("|", $this->_post->toString('index_arr'));
                    $pjPriceModel = pjPriceModel::factory();
                    foreach($index_arr as $k => $v)
                    {
                        $p_data = array();
                        $p_data['fleet_id'] = $id;
                        $p_data['start'] = $post['start'][$v];
                        $p_data['end'] = $post['end'][$v];
                        $p_data['price'] = $post['price'][$v];
                        $p_data['start_fee_r'] = $post['start_fee_r'][$v];
                        $p_data['time_rate_per_minute_r'] = $post['time_rate_per_minute_r'][$v];
                        $pjPriceModel->reset()->setAttributes($p_data)->insert();
                    }
                }

                if($this->_post->check('index_city_price_arr') && !$this->_post->isEmpty('index_city_price_arr'))
                {  
                    $index_arr = explode("|", $this->_post->toString('index_city_price_arr'));
                    $pjFleetPriceModel = pjFleetPriceModel::factory();
                    foreach($index_arr as $k => $v)
                    {
                        $p_data = array();
                        $p_data['fleet_id'] = $id;
                        $p_data['from_city'] = $post['from_city'][$v];
                        $p_data['to_city'] = $post['to_city'][$v];
                        $p_data['price'] = $post['price'][$v]; 
                        
                        $pjFleetPriceModel->reset()->setAttributes($p_data)->insert();
                    }
                }

                 if($this->_post->check('index_daterange_price_arr') && !$this->_post->isEmpty('index_daterange_price_arr'))
                    {   
                        $pjDateRangeModel = pjDateRangeModel::factory();
                        $index_daterange_price_arr = explode("|", $this->_post->toString('index_daterange_price_arr'));

                        foreach($index_daterange_price_arr as $k => $v)
                        {
                            $p_data = array();
                            $p_data['fleet_id'] = $id;
                            $p_data['from_date'] = $post['from_date'][$v];
                            $p_data['to_date'] = $post['to_date'][$v];
                            $p_data['price'] = $post['price_date'][$v];
                            $pjDateRangeModel->reset()->setAttributes($p_data)->insert();
                        }
                    }
                
                if (isset($_FILES['image']))
                {
                    if($_FILES['image']['error'] == 0)
                    {
                        $image_size = getimagesize($_FILES['image']['tmp_name']);
                        if(!empty($image_size))
                        {
                            $pjFleetModel = pjFleetModel::factory();
                            $Image = new pjImage();
                            if ($Image->getErrorCode() !== 200)
                            {
                                $Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
                                if ($Image->load($_FILES['image']))
                                {
                                    $resp = $Image->isConvertPossible();
                                    if ($resp['status'] === true)
                                    {
                                        $hash = md5(uniqid(rand(), true));
                                        $source_path = PJ_UPLOAD_PATH . 'fleets/source/' . $id . '_' . $hash . '.' . $Image->getExtension();
                                        $thumb_path = PJ_UPLOAD_PATH . 'fleets/thumb/' . $id . '_' . $hash . '.' . $Image->getExtension();
                                        if ($Image->save($source_path))
                                        {
                                            $Image->loadImage($source_path);
                                            $Image->resizeSmart(250, 130);
                                            $Image->saveImage($thumb_path);
                                            
                                            $data['source_path'] = $source_path;
                                            $data['thumb_path'] = $thumb_path;
                                            $data['image_name'] = $_FILES['image']['name'];
                                            $pjFleetModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
                                        }
                                    }
                                }
                            }
                        }else{
                            pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=$id&err=AF11");
                        }
                    }else if($_FILES['image']['error'] != 4){
                        pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=$id&err=AF09");
                    }
                }
                
                $err = 'AF03';
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionIndex");
            }
        }else{
            
            $this->setLocalesData();

            $this->set('categories_arr', pjCategoryModel::factory()
                ->select('t1.id, t1.category')
                ->orderBy('t1.category ASC')
                ->findAll()
                ->getData()
            );
            
            $this->set('extra_arr', pjExtraModel::factory()
            ->select('t1.*, t2.content AS name')
            ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
            ->orderBy('name ASC')
            ->findAll()
            ->getData());
            
             $this->set('city_arr', pjCityModel::factory()
                ->select('t1.*, t2.content AS name')
                ->join('pjMultiLang', "t2.model='pjCity' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->orderBy('name ASC')
                ->findAll()
                ->getData());

            $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
            $this->appendJs('additional-methods.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
            $this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
            $this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
            $this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
            $this->appendCss('bootstrap-chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
            $this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
            $this->appendCss('jasny-bootstrap.min.css', PJ_THIRD_PARTY_PATH . 'jasny/');
            $this->appendJs('jasny-bootstrap.min.js',  PJ_THIRD_PARTY_PATH . 'jasny/');

            $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
            $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
            $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');

            $this->appendJs('pjAdminFleets.js?v=' . $this->generateVersion());
        }
    }
    
    public function pjActionUpdate()
    {
       
        $post_max_size = pjUtil::getPostMaxSize();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
        {
            pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF06");
        }
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        if (self::isPost() && $this->_post->toInt('fleet_update'))
        {
            $post = $this->_post->raw();
           
            $id = $this->_post->toInt('id');
            $pjFleetModel = pjFleetModel::factory();
            
            $arr = $pjFleetModel->find($id)->getData();
            if (empty($arr))
            {
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionIndex&err=AF08");
            }
            
            $data = array();
            if (isset($_FILES['image']))
            {
                if($_FILES['image']['error'] == 0)
                {
                    $image_size = getimagesize($_FILES['image']['tmp_name']);
                    if(!empty($image_size))
                    {
                        if(!empty($arr['source_path']))
                        {
                            $source_path = PJ_INSTALL_PATH . $arr['source_path'];
                            $thumb_path = PJ_INSTALL_PATH . $arr['thumb_path'];
                            @unlink($source_path);
                            @unlink($thumb_path);
                        }
                        
                        $Image = new pjImage();
                        if ($Image->getErrorCode() !== 200)
                        {
                            $Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
                            if ($Image->load($_FILES['image']))
                            {
                                $resp = $Image->isConvertPossible();
                                if ($resp['status'] === true)
                                {
                                    $hash = md5(uniqid(rand(), true));
                                    $source_path = PJ_UPLOAD_PATH . 'fleets/source/' . $id . '_' . $hash . '.' . $Image->getExtension();
                                    $thumb_path = PJ_UPLOAD_PATH . 'fleets/thumb/' . $id . '_' . $hash . '.' . $Image->getExtension();
                                    if ($Image->save($source_path))
                                    {
                                        $Image->loadImage($source_path);
                                        $Image->resizeSmart(250, 130);
                                        $Image->saveImage($thumb_path);
                                        
                                        $data['source_path'] = $source_path;
                                        $data['thumb_path'] = $thumb_path;
                                        $data['image_name'] = $_FILES['image']['name'];
                                    }
                                }
                            }
                        }
                    }else{
                        pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=".$this->_post->toInt('id')."&err=AF11");
                    }
                }else if($_FILES['image']['error'] != 4){
                    pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminFleets&action=pjActionUpdate&id=".$this->_post->toInt('id')."&err=AF10");
                }
            }
            $data['passengers'] = $this->_post->toInt('passengers');
            $data['luggage'] = $this->_post->toInt('luggage');
            $data['category'] = $this->_post->toInt('category');
            $pjFleetModel->reset()->where('id', $id)->limit(1)->modifyAll(array_merge($this->_post->raw(), $data));
            
            $pjFleetExtraModel = pjFleetExtraModel::factory();
            $pjFleetExtraModel->where('fleet_id', $id)->eraseAll();
            $extra_id_arr = $this->_post->toArray('extra_id');
            if (count($extra_id_arr) > 0)
            {
                $pjFleetExtraModel->begin();
                foreach ($extra_id_arr as $extra_id)
                {
                    $pjFleetExtraModel
                    ->reset()
                    ->set('fleet_id', $id)
                    ->set('extra_id', $extra_id)
                    ->insert();
                }
                $pjFleetExtraModel->commit();
            }
            
            if (isset($post['i18n']))
            {
                pjMultiLangModel::factory()->updateMultiLang($post['i18n'], $id, 'pjFleet', 'data');
            }
            
            $pjPriceModel = pjPriceModel::factory();
            if($this->_post->check('index_arr') && !$this->_post->isEmpty('index_arr'))
            {
                $index_arr = explode("|", $this->_post->toString('index_arr'));
                foreach($index_arr as $k => $v)
                {
                    if(strpos($v, 'new') !== false)
                    {
                        $p_data = array();
                        $p_data['fleet_id'] = $id;
                        $p_data['start'] = $post['start'][$v];
                        $p_data['end'] = $post['end'][$v];
                        $p_data['price'] = $post['price'][$v];
                        $p_data['start_fee_r'] = $post['start_fee_r'][$v];
                        $p_data['time_rate_per_minute_r'] = $post['time_rate_per_minute_r'][$v];
                        $pjPriceModel->reset()->setAttributes($p_data)->insert();
                        
                    }else{
                        $p_data = array();
                        $p_data['fleet_id'] = $id;
                        $p_data['start'] = $post['start'][$v];
                        $p_data['end'] = $post['end'][$v];
                        $p_data['price'] = $post['price'][$v];
                        $p_data['start_fee_r'] = $post['start_fee_r'][$v];
                        $p_data['time_rate_per_minute_r'] = $post['time_rate_per_minute_r'][$v];
                        $pjPriceModel->reset()->where('id', $v)->limit(1)->modifyAll($p_data);
                    }
                }
            }
            if($this->_post->check('remove_arr') && !$this->_post->isEmpty('remove_arr'))
            {
                $remove_arr = explode("|", $this->_post->toString('remove_arr'));
                $pjPriceModel->reset()->whereIn('id', $remove_arr)->eraseAll();
            }

            $pjFleetPriceModel = pjFleetPriceModel::factory();

            if($this->_post->check('index_city_price_arr') && !$this->_post->isEmpty('index_city_price_arr'))
            {
                $index_city_price_arr = explode("|", $this->_post->toString('index_city_price_arr'));

                foreach($index_city_price_arr as $k => $v)
                {
                    if(strpos($v, 'new') !== false)
                    {
                        $p_data = array();
                        $p_data['fleet_id'] = $id;
                        $p_data['from_city'] = $post['from_city'][$v];
                        $p_data['to_city'] = $post['to_city'][$v];
                        $p_data['price'] = $post['price_'][$v];
                        $pjFleetPriceModel->reset()->setAttributes($p_data)->insert();
                        
                    }else{
                        $p_data = array();
                        $p_data['fleet_id'] = $id;
                        $p_data['from_city'] = $post['from_city'][$v];
                        $p_data['to_city'] = $post['to_city'][$v];
                        $p_data['price'] = $post['price_'][$v];
                        $pjFleetPriceModel->reset()->where('id', $v)->limit(1)->modifyAll($p_data);
                    }
                }
            }
            if($this->_post->check('remove_city_price_arr') && !$this->_post->isEmpty('remove_city_price_arr'))
            {
                $remove_arr = explode("|", $this->_post->toString('remove_city_price_arr'));

                $pjFleetPriceModel->reset()->whereIn('id', $remove_arr)->eraseAll();
            }
             $pjDateRangeModel = pjDateRangeModel::factory();
            if($this->_post->check('index_daterange_price_arr') && !$this->_post->isEmpty('index_daterange_price_arr'))
            {  
                $index_daterange_price_arr = explode("|", $this->_post->toString('index_daterange_price_arr'));

                foreach($index_daterange_price_arr as $k => $v)
                {
                    if(strpos($v, 'new') !== false)
                    {
                        $p_data = array();
                        $p_data['fleet_id'] = $id;
                        $p_data['from_date'] = $post['from_date'][$v];
                        $p_data['to_date'] = $post['to_date'][$v];
                        $p_data['price'] = $post['price_date'][$v];
                        $pjDateRangeModel->reset()->setAttributes($p_data)->insert();
                        
                    }else{
                        $p_data = array();
                        $p_data['fleet_id'] = $id;
                        $p_data['from_date'] = $post['from_date'][$v];
                        $p_data['to_date'] = $post['to_date'][$v];
                        $p_data['price'] = $post['price_date'][$v];
                        $pjDateRangeModel->reset()->where('id', $v)->limit(1)->modifyAll($p_data);
                    }
                }
            }
            if($this->_post->check('remove_daterange_price_arr') && !$this->_post->isEmpty('remove_daterange_price_arr'))
            {
                $remove_arr = explode("|", $this->_post->toString('remove_daterange_price_arr'));

                $pjDateRangeModel->reset()->whereIn('id', $remove_arr)->eraseAll();
            }
            
            pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AF01");
        }
        if (self::isGet() && $this->_get->toInt('id'))
        {
            $id = $this->_get->toInt('id');
            $pjMultiLangModel = pjMultiLangModel::factory();
            
            $arr = pjFleetModel::factory()->find($id)->getData();
            if (count($arr) === 0)
            {
                pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminFleets&action=pjActionIndex&err=AP08");
            }
            $arr['i18n'] = $pjMultiLangModel->getMultiLang($arr['id'], 'pjFleet');
            $this->set('arr', $arr);
            
            $this->setLocalesData();
            
            $price_arr = pjPriceModel::factory()->where('fleet_id', $id)->findAll()->getData();
            $this->set('price_arr', $price_arr);

            $this->set('categories_arr', pjCategoryModel::factory()
                ->select('t1.id, t1.category')
                ->orderBy('t1.category ASC')
                ->findAll()
                ->getData()
            );
            
            $this->set('extra_arr', pjExtraModel::factory()
                ->select('t1.*, t2.content AS name')
                ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->orderBy('name ASC')
                ->findAll()
                ->getData());
            $extra_id_arr = pjFleetExtraModel::factory()->where('fleet_id', $id)->findAll()->getDataPair(null, 'extra_id');
            $this->set('extra_id_arr', $extra_id_arr);
            
            $this->set('city_arr', pjCityModel::factory()
                    ->select('t1.*, t2.content AS name')
                    ->join('pjMultiLang', "t2.model='pjCity' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                    ->orderBy('name ASC')
                    ->findAll()
                    ->getData());
            $this->set('date_format', pjUtil::toBootstrapDate($this->option_arr['o_date_format']));

            $city_price_arr = pjFleetPriceModel::factory()->where('fleet_id', $id)->findAll()->getData();
            $this->set('city_price_arr', $city_price_arr);

            $daterange_price_arr = pjDateRangeModel::factory()->where('fleet_id', $id)->findAll()->getData();
            $this->set('daterange_price_arr', $daterange_price_arr);

            $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
            $this->appendJs('additional-methods.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
            $this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
            $this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
            $this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
            $this->appendCss('bootstrap-chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
            $this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
            $this->appendCss('jasny-bootstrap.min.css', PJ_THIRD_PARTY_PATH . 'jasny/');
            $this->appendJs('jasny-bootstrap.min.js',  PJ_THIRD_PARTY_PATH . 'jasny/');

            $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
            $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
            $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
            
            $this->appendJs('pjAdminFleets.js?v=' . $this->generateVersion());
            
    
        }
    }
    
    public function pjActionDeleteImage()
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
        if (!($this->_get->toInt('id')))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $id = $this->_get->toInt('id');
        $pjFleetModel = pjFleetModel::factory();
        $arr = $pjFleetModel->find($id)->getData();
        if(empty($arr))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Fleet not found.'));
        }
        if(!empty($arr['source_path']))
        {
            $source_path = PJ_INSTALL_PATH . $arr['source_path'];
            $thumb_path = PJ_INSTALL_PATH . $arr['thumb_path'];
            @unlink($source_path);
            @unlink($thumb_path);
        }
        
        $data = array();
        $data['source_path'] = ':NULL';
        $data['thumb_path'] = ':NULL';
        $data['image_name'] = ':NULL';
        $pjFleetModel->reset()->where(array('id' => $id))->limit(1)->modifyAll($data);
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Fleet image has been deleted.'));
    }
}
?>