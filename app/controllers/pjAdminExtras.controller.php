<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminExtras extends pjAdmin
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
        
        $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
        $this->appendJs('jquery.multilang.js', $this->getConstant('pjBase', 'PLUGIN_JS_PATH'), false, false);
        $this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
        $this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs('pjAdminExtras.js');
    }
    
    public function pjActionGetExtra()
    {
        $this->setAjax(true);
        
        if ($this->isXHR())
        {
            $pjExtraModel = pjExtraModel::factory()->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left');
            if ($q = $this->_get->toString('q'))
            {
                $pjExtraModel->where("(t2.content LIKE '%$q%')");
            }
            $column = 'name';
            $direction = 'ASC';
            if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
            {
                $column = $this->_get->toString('column');
                $direction = strtoupper($this->_get->toString('direction'));
            }
            
            $total = $pjExtraModel->findCount()->getData();
            $rowCount = $this->_get->toInt('rowCount') ?: 10;
            $pages = ceil($total / $rowCount);
            $page = $this->_get->toInt('page') ?: 1;
            $offset = ((int) $page - 1) * $rowCount;
            if ($page > $pages)
            {
                $page = $pages;
            }
            
            $data = $pjExtraModel
            ->select("t1.*, t2.content AS name")
            ->orderBy("$column $direction")
            ->limit($rowCount, $offset)
            ->findAll()
            ->getData();
            foreach($data as $k => $v)
            {
                $v['price'] = pjCurrency::formatPrice($v['price']);
                $data[$k] = $v;
            }
            pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
        }
        exit;
    }
    public function pjActionCreate()
    {
        $this->setAjax(true);
        if (!pjAuth::factory()->hasAccess())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
        }
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        if (!$this->_post->toInt('extra_create'))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $post = $this->_post->raw();
        $data = array();
        $data['per'] = $this->_post->check('price_per_person') ? 'person' : 'total';
        $pjExtraModel = pjExtraModel::factory();
        $id = $pjExtraModel->setAttributes(array_merge($post, $data))->insert()->getInsertId();
        if ($id !== false && (int) $id > 0)
        {
            if (isset($post['i18n']))
            {
                pjMultiLangModel::factory()->saveMultiLang($post['i18n'], $id, 'pjExtra', 'data');
            }
            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Extra has been added!'));
        } else {
            self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Extra could not be added!'));
        }
        exit;
    }
    public function pjActionCreateForm()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isGet())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        $this->setLocalesData();
    }
    
    public function pjActionUpdate()
    {
        $this->setAjax(true);
        if (!pjAuth::factory()->hasAccess())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
        }
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        if (!$this->_post->toInt('extra_update'))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
        }
        if (!$this->_post->toInt('id'))
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.'));
        }
        $post = $this->_post->raw();
        $data = array();
        $data['per'] = $this->_post->check('price_per_person') ? 'person' : 'total';
        $id = $this->_post->toInt('id');
        $pjExtraModel = pjExtraModel::factory();
        $pjExtraModel->reset()->where('id', $id)->limit(1)->modifyAll(array_merge($post, $data));
        if (isset($post['i18n']))
        {
            pjMultiLangModel::factory()->updateMultiLang($post['i18n'], $post['id'], 'pjExtra', 'data');
        }
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Category has been updated!'));
        exit;
    }
    public function pjActionUpdateForm()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        if (!self::isGet())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
        }
        if ($this->_get->toInt('id'))
        {
            $id = $this->_get->toInt('id');
            $arr = pjExtraModel::factory()->find($id)->getData();
            if (count($arr) === 0)
            {
                self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Extra is not found.'));
            }
            $arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjExtra');
            $this->set('arr', $arr);
            
            $this->setLocalesData();
        }else{
            self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Missing parameters.'));
        }
    }
    
    public function pjActionDeleteExtra()
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
        if (!pjExtraModel::factory()->set('id', $this->_get->toInt('id'))->erase()->getAffectedRows())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Extra has not been deleted.'));
        }
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Extra has been deleted'));
        exit;
    }
    
    public function pjActionDeleteExtraBulk()
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
        pjMultiLangModel::factory()->where('model', 'pjExtra')->whereIn('foreign_id', $record)->eraseAll();
        pjExtraModel::factory()->whereIn('id', $record)->eraseAll();
        self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Extra(s) has been deleted.'));
        exit;
    }
}
?>