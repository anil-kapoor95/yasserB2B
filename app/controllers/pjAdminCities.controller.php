<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCities extends pjAdmin
{
	public function pjActionCreate()
	{
	
		$this->checkLogin();
		if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
	
		// if (isset($_POST['item_create']))
		if (self::isPost() && $this->_post->toInt('item_create'))

		{ 
			$data = array();
            $post = $this->_post->raw();

			$id = pjCityModel::factory(array_merge($post, $data))->insert()->getInsertId();
			
			if ($id !== false && (int) $id > 0)
			{
				$err = 'ACI03';
				// if (isset($_POST['i18n']))
				// {  
				// 	//$post['i18n'][1]['name']
				// 	pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjCity', 'data');
					
				// }

				if (isset($post['i18n']))
                {
                    pjMultiLangModel::factory()->saveMultiLang($post['i18n'], $id, 'pjCity', 'data');
                }


			} else {
				$err = 'ACI04';
			}
			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCities&action=pjActionIndex&err=$err");
		} else {
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
					
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file']; 
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
	
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminCities.js');
		}
		 
	}
		
	public function pjActionDeleteCity()

	{
        $this->setAjax(true);
        
		$pjCityModel = pjCityModel::factory();
        $arr = $pjCityModel->find($this->_get->toInt('id'))->getData();
        if (!$arr)
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Cities not found.'));
        }
        $id = $this->_get->toInt('id');
		
        if ($pjCityModel->setAttributes(array('id' => $id))->erase()->getAffectedRows() == 1)
        {
            
            pjMultiLangModel::factory()->where('model', 'pjCity')->where('foreign_id', $id)->eraseAll();

            // pjFleetPriceModel::factory()->where('from_city', $id)->eraseAll();
            // pjFleetPriceModel::factory()->where('to_city', $id)->eraseAll();

            pjFleetPriceModel::factory()
							    ->where('from_city', $id)
							    ->orWhere('to_city', $id)
							    ->eraseAll();

		             
				           
            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Cities has been deleted'));
        }else{
            self::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => 'Cities has not been deleted.'));
        }
        exit;
    }
	
	public function pjActionDeleteCityBulk()
	{
		$this->setAjax(true);
		if ($this->isXHR())
		{ 
			$record = $this->_post->toArray('record');
		    $pjCityModel = pjCityModel::factory();
		    $arr = $pjCityModel->whereIn('id', $record)->findAll()->getData();
		
			if ($arr && $arr > 0)
			{ 	
				pjCityModel::factory()->whereIn('id', $record)->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjCity')->whereIn('foreign_id', $record)->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetCity()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCityModel = pjCityModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCity' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjCityModel->where('t2.content LIKE', "%$q%");
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjCityModel->findCount()->getData();
			
			// $rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;

			$rowCount = $this->_get->toInt('rowCount') ?: 20;
			$pages = ceil($total / $rowCount);

			// $page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;

			$page = $this->_get->toInt('page') ?: 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjCityModel
				->select('t1.*, t2.content AS name')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
		
	public function pjActionIndex()
	// {
	// 	$this->checkLogin();
		
	// 	if ($this->isAdmin())
	// 	{
	// 		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
	// 		$this->appendJs('pjAdminCities.js');
	// 	} else {
	// 		$this->set('status', 2);
	// 	}
	// }
	{
        $this->set('has_access_create', pjAuth::factory('pjAdminCities', 'pjActionCreate')->hasAccess());
        $this->set('has_access_update', pjAuth::factory('pjAdminCities', 'pjActionUpdate')->hasAccess());
        
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs('pjAdminCities.js');
    }
	
	public function pjActionSaveCity()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCityModel = pjCityModel::factory();
			if (!in_array($_POST['column'], $pjCityModel->getI18n()))
			{
				$pjCityModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjCity', 'data');
			}
		}
		exit;
	}
	
	// public function pjActionUpdate()
	// {
	// 	$this->checkLogin();

	// 	if ($this->isAdmin())
	// 	{
	// 		if (isset($_POST['item_update']))
	// 		{
	// 			$data = array();
	// 			$data['per'] = isset($_POST['price_per_person']) ? 'person' : 'total';
	// 			pjCityModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
	// 			if (isset($_POST['i18n']))
	// 			{
	// 				pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjCity', 'data');
	// 			}
	// 			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCities&action=pjActionIndex&err=ACI01");
				
	// 		} else {
	// 			$arr = pjCityModel::factory()->find($_GET['id'])->getData();
	// 			if (count($arr) === 0)
	// 			{
	// 				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCities&action=pjActionIndex&err=ACI08");
	// 			}
	// 			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCity');
	// 			$this->set('arr', $arr);
				
	// 			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
	// 				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
	// 				->where('t2.file IS NOT NULL')
	// 				->orderBy('t1.sort ASC')->findAll()->getData();
				
	// 			$lp_arr = array();
	// 			foreach ($locale_arr as $item)
	// 			{
	// 				$lp_arr[$item['id']."_"] = $item['file']; 
	// 			}
	// 			$this->set('lp_arr', $locale_arr);
	// 			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
	// 			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
	// 			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
	// 			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
	// 			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
	// 			$this->appendJs('pjAdminCities.js');
	// 		}
	// 	} else {
	// 		$this->set('status', 2);
	// 	}
	// }
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			//if (isset($_POST['item_update']))
			if (self::isPost() && $this->_post->toInt('item_update'))
			{
				
				$data = array();
				$data['per'] = isset($_POST['price_per_person']) ? 'person' : 'total';
				$post = $this->_post->raw();
								
				$pasd = pjCityModel::factory()->where('id', $post['id'])->limit(1)->modifyAll(array_merge($post, $data));
				
				if (isset($post['i18n']))
				{  
					pjMultiLangModel::factory()->updateMultiLang($post['i18n'], $post['id'], 'pjCity', 'data');
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCities&action=pjActionIndex&err=ACI01");
				
			} else {
				$pjCityModel = pjCityModel::factory();
				$arr = $pjCityModel->find($this->_get->toInt('id'))->getData();
				// $arr = pjCityModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCities&action=pjActionIndex&err=ACI08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCity');
				$this->set('arr', $arr);
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file']; 
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCities.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>