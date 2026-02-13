<?php

if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjAdminDrivers extends pjAdmin
{
    public function pjActionCheckEmail()
    {
        $this->setAjax(true);
        
        if ($this->isXHR())
        {
            if (!$this->_get->toString('email')) {
                echo 'false';
                exit;
            }
            
            $pjAuthUserModel = pjAuthUserModel::factory()
            ->join('pjClient', 't2.foreign_id = t1.id', 'left')
            ->where('t1.email', $this->_get->toString('email'));
            
            if ($this->_get->toInt('id')) {
                $pjAuthUserModel->where('t2.id !=', $this->_get->toInt('id'));
            }
            
            echo $pjAuthUserModel->findCount()->getData() == 0 ? 'true' : 'false';
        }
        exit;
    }
    
    public function pjActionCreate()
        {
            $this->checkLogin();
            if (!pjAuth::factory()->hasAccess())
            {
                $this->sendForbidden();
                return;
            }
            if (self::isPost() && $this->_post->toInt('driver_create'))
            {
                $post = $this->_post->raw();

                $date = $this->_post->toString('license_expiry');
                $bob_date = $this->_post->toString('dob');
                if($this->_post->check('status'))
                {
                    $post['status'] = 'T';
                }else{
                    $post['status'] = 'F';
                }
                $u_data = array();
                $u_data['is_active'] = 'T';
                $u_data['role_id'] = 4;
                $u_data['email'] = $post['email'];
                $u_data['password'] = isset($post['password']) ? $post['password'] : pjAuth::generatePassword($this->option_arr);
                $name_arr = array();
                if(isset($post['first_name']) && !empty($post['first_name']))
                {
                    $name_arr[] = $post['first_name'];
                }
                if(isset($post['first_name']) && !empty($post['first_name']))
                {
                    $name_arr[] = $post['last_name'];
                }
                $u_data['name'] = join(" ", $name_arr);
                $u_data['phone'] = isset($post['phone']) ? $post['phone'] : ":NULL";
                $u_data['status'] = isset($post['status']) ? $post['status'] : ":NULL";
                $u_data['ip'] = pjUtil::getClientIp();
                $u_data['is_active'] = 'T';
                $id = pjAuthUserModel::factory($u_data)->insert()->getInsertId();

                 if($id !== false && (int) $id > 0)
                     { 
                        $driverAuthIds = pjAuthUserModel::factory()->where('email', $post['email'])->where('role_id', 4)->findAll()->getDataIndex(0);
                        $post['locale_id'] = $this->getLocaleId();
                        $data = array();
                        $data['title'] = isset($post['title']) ? $post['title'] : ":NULL";
                        $data['auth_id'] = $driverAuthIds['id'];
                        $data['first_name'] = isset($post['first_name']) ? $post['first_name'] : ":NULL";
                        $data['last_name'] = isset($post['last_name']) ? $post['last_name'] : ":NULL";
                        $data['email'] = isset($post['email']) ? $post['email'] : ":NULL";
                        $data['password'] = isset($post['password']) ? $post['password'] : ":NULL";
                        $data['phone'] = isset($post['phone']) ? $post['phone'] : ":NULL";
                        $data['address'] = isset($post['address']) ? $post['address'] : ":NULL";
                        $data['city'] = isset($post['city']) ? $post['city'] : ":NULL";
                        $data['zip'] = isset($post['zip']) ? $post['zip'] : ":NULL";
                        $data['state'] = isset($post['state']) ? $post['state'] : ":NULL";
                        $data['license_number'] = isset($post['license_number']) ? $post['license_number'] : ":NULL";
                        $data['license_expiry'] = $date;
                        $data['dob'] = $bob_date;
                        $data['national_id_number'] = isset($post['national_id_number']) ? $post['national_id_number'] : ":NULL";
                        $data['vehicle_id'] = isset($post['vehicle_id']) ? $post['vehicle_id'] : ":NULL";
                        $data['notes'] = isset($post['notes']) ? $post['notes'] : ":NULL";
                        $data['status'] = $post['status'];
                        $response = pjDriverModel::factory()->setAttributes($data)->insert()->getInsertId();
                     }
                     
            if (!empty($response))
                {
                    if (!empty($_FILES['license_file']['name'])) 
                    {
                        $uploadDir = PJ_UPLOAD_PATH . 'license/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $thumbDir = $uploadDir . 'thumb/';
                        if (!is_dir($thumbDir)) {
                            mkdir($thumbDir, 0777, true);
                        }

                        $name    = $_FILES['license_file']['name'];
                        $tmpName = $_FILES['license_file']['tmp_name'];
                        $error   = $_FILES['license_file']['error'];
                        $size    = $_FILES['license_file']['size'];
                        $type    = $_FILES['license_file']['type'];

                        if ($error === 0 && is_uploaded_file($tmpName)) 
                            {
                                $ext = pathinfo($name, PATHINFO_EXTENSION);
                                // $uniqueName = uniqid('license_') . '.' . $ext;
                                $firstName = preg_replace('/[^a-z0-9_]/i', '', strtolower($post['first_name']));
                                $uniqueName = $firstName . '_license_' . uniqid() . '.' . $ext;
                                $destPath = $uploadDir . $uniqueName;
                                if (move_uploaded_file($tmpName, $destPath)) 
                                    {
                                    $thumbRelPath = null;
                                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
                                        $thumbName = 'thumb_' . $uniqueName;
                                        $thumbPathFull = $thumbDir . $thumbName;
                                        $thumbRelPath  = 'license/thumb/' . $thumbName;
                                        $sourceImage = null;
                                        switch (strtolower($ext)) {
                                            case 'jpg':
                                            case 'jpeg':
                                                $sourceImage = imagecreatefromjpeg($destPath);
                                                break;
                                            case 'png':
                                                $sourceImage = imagecreatefrompng($destPath);
                                                break;
                                        }
                                        if ($sourceImage) {
                                            $width  = imagesx($sourceImage);
                                            $height = imagesy($sourceImage);
                                            $newWidth = 200;
                                            $newHeight = intval($height * ($newWidth / $width));
                                            $thumb = imagecreatetruecolor($newWidth, $newHeight);
                                            imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                            switch (strtolower($ext)) {
                                                case 'jpg':
                                                case 'jpeg':
                                                    imagejpeg($thumb, $thumbPathFull);
                                                    break;
                                                case 'png':
                                                    imagepng($thumb, $thumbPathFull);
                                                    break;
                                            }
                                            imagedestroy($thumb);
                                            imagedestroy($sourceImage);
                                        }
                                    }

                                    pjDriverFileModel::factory()->setAttributes([
                                        'driver_id'     => $response,
                                        'file_name'     => $uniqueName,
                                        'original_name' => $name,
                                        'file_type'     => $type,
                                        'file_size'     => $size,
                                        'file_category' => 'license',
                                        'created'       => date("Y-m-d H:i:s"),
                                        'source_path'   => 'license/' . $uniqueName,
                                        'thumb_path'    => $thumbRelPath
                                    ])->insert();

                                    pjDriverModel::factory()
                                        ->where('id', $response)
                                        ->limit(1)
                                        ->modifyAll(['license_file' => $uniqueName]);
                                }
                            }
                    }

                
                    if (!empty($_FILES['driver_documents']['name'][0])) 
                    {
                        $uploadDir = PJ_UPLOAD_PATH . 'driver_documents/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        foreach ($_FILES['driver_documents']['name'] as $index => $name) {
                            $tmpName = $_FILES['driver_documents']['tmp_name'][$index];
                            $error = $_FILES['driver_documents']['error'][$index];
                            $size = $_FILES['driver_documents']['size'][$index];
                            $type = $_FILES['driver_documents']['type'][$index];

                            if ($error === 0 && is_uploaded_file($tmpName)) {
                                // Determine file category from filename or default
                            $category = 'additional';
                                $lcName = strtolower($name);
                                if (strpos($lcName, 'id') !== false) {
                                    $category = 'id';
                                } elseif (strpos($lcName, 'photo') !== false || strpos($lcName, 'image') !== false) {
                                    $category = 'photo';
                                }
                                // Generate unique file name
                                $ext = pathinfo($name, PATHINFO_EXTENSION);
                                // $uniqueName = uniqid('doc_') . '.' . $ext;
                                $doc_prefix = preg_replace('/[^a-z0-9_]/i', '', strtolower($post['first_name']));
                                $uniqueName = uniqid($doc_prefix . '_') .'.' . $ext;
                                $destPath = $uploadDir . $uniqueName;

                                if (move_uploaded_file($tmpName, $destPath)) {
                                    $file_data = array();
                                    $file_data['driver_id']     = $response;
                                    $file_data['file_name']     = $uniqueName;
                                    $file_data['original_name'] = $name;
                                    $file_data['file_type']    = $type;
                                    $file_data['file_size']     = $size;
                                    $file_data['file_category'] = $category;
                                    $file_data['created']    = date("Y-m-d H:i:s");
                                    $file_data['source_path']   = 'driver_documents/' . $uniqueName;
                                    $file_data['thumb_path']    = $thumbPath ?? null;
                                    pjDriverFileModel::factory()->setAttributes($file_data)->insert()->getInsertId();
                                }
                            }
                        }
                    }
                }
   
                if (!empty($response))
                {
                    $err = 'ADR03';
                }else{
                    $err = 'ADR04';
                }
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminDrivers&action=pjActionIndex&err=$err");
            }
            if (self::isGet())
            {
                $country_arr = pjBaseCountryModel::factory()
                ->select('t1.id, t2.content AS country_title')
                ->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->where('status', 'T')
                ->orderBy('`country_title` ASC')->findAll()->getData();
                $this->set('country_arr', $country_arr);
                            
                $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
                $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
                $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
                $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
                $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
                $this->appendJs('pjAdminDrivers.js?ver=1.2.69');
            }
        }
    
    public function pjActionDeleteDriver()
        {
            $this->setAjax(true);

            if (!$this->isXHR()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
            }

            if (!self::isPost()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
            }

            if (!pjAuth::factory()->hasAccess()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
            }

            $driverId = $this->_get->toInt('id') ?: $this->_post->toInt('id');
            if (!$driverId) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing or invalid driver ID.'));
            }

            $db = pjAppModel::factory(); // ✅ correct for transactions
            $db->begin();

            try {
                // Step 1: Delete driver
                $deleted = pjDriverModel::factory()
                    ->reset()
                    ->set('id', $driverId)
                    ->erase()
                    ->getAffectedRows();

                if (!$deleted) {
                    throw new Exception("Driver could not be deleted.");
                }

                // Step 2: Delete driver files
                pjDriverFileModel::factory()
                    ->where('driver_id', $driverId)
                    ->eraseAll();

                $db->commit();

                self::jsonResponse(array(
                    'status' => 'OK',
                    'code' => 200,
                    'text' => 'Driver and related files deleted successfully.'
                ));
            } catch (Exception $e) {
                $db->rollBack();

                self::jsonResponse(array(
                    'status' => 'ERR',
                    'code' => 500,
                    'text' => 'Deletion failed and rolled back.',
                    'error' => $e->getMessage()
                ));
            }

            exit;
        }


    public function pjActionDeleteDriverFiles()
        {
            $this->setAjax(true);

            if (!$this->isXHR()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
            }
            if (!self::isPost()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
            }
            if (!pjAuth::factory()->hasAccess()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
            }

            $fileId = $this->_post->toInt('id');
            if (!$fileId) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing or invalid file ID.'));
            }

            // ✅ Begin DB transaction
            $pjFileModel = pjDriverFileModel::factory();
            $pjFileModel->begin();

            try {
                
                // Optionally get file info before deletion if you want to delete the actual file from disk
                $file = $pjFileModel->find($fileId)->getData();
                // Delete DB record
                $deleted = $pjFileModel
                    ->reset()
                    ->set('id', $fileId)
                    ->erase()
                    ->getAffectedRows();

                if (!$deleted) {
                    throw new Exception("Document has not been deleted.");
                }

                // ✅ Optional: delete file from disk
                if (!empty($file['file_name'])) {
                    $filePath = PJ_UPLOAD_PATH . $file['file_name']; // adjust path constant if needed
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $pjFileModel->commit();

                self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Document has been deleted'));
            } catch (Exception $e) {
                
                $pjFileModel->rollBack();

                self::jsonResponse(array(
                    'status' => 'ERR',
                    'code' => 500,
                    'text' => 'Delete failed. Rolled back.',
                    'error' => $e->getMessage()
                ));
            }

            exit;
        }


    public function pjActionDeleteDriverBulk()
        {
            $this->setAjax(true);

            if (!$this->isXHR()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
            }

            if (!self::isPost()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
            }

            if (!pjAuth::factory()->hasAccess()) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
            }

            if (!$this->_post->has('record')) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
            }

            $record = $this->_post->toArray('record');
            if (empty($record)) {
                self::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Missing, empty or invalid parameters.'));
            }

            $db = pjAppModel::factory();
            $db->begin();

            try {
                // Step 1: Delete drivers
                pjDriverModel::factory()
                    ->whereIn('id', $record)
                    ->eraseAll();

                // Step 2: Delete driver files
                pjDriverFileModel::factory()
                    ->whereIn('driver_id', $record)
                    ->eraseAll();

                $db->commit();

                self::jsonResponse(array(
                    'status' => 'OK',
                    'code' => 200,
                    'text' => 'Drivers and related files deleted successfully.'
                ));
            } catch (Exception $e) {
                $db->rollBack();

                self::jsonResponse(array(
                    'status' => 'ERR',
                    'code' => 500,
                    'text' => 'Bulk deletion failed and rolled back.',
                    'error' => $e->getMessage()
                ));
            }

            exit;
        }

    
    public function pjActionExportDriver()
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
                $arr = pjDriverModel::factory()
                ->select("t1.*, t2.email as email, t2.name as name, t2.phone as phone")
                ->join("pjAuthUser", 't2.id=t1.foreign_id', 'left outer')
                ->whereIn('t1.id', $record)->findAll()->getData();
                $csv = new pjCSV();
                $csv
                ->setHeader(true)
                ->setName("Clients-".time().".csv")
                ->process($arr)
                ->download();
            }
            exit;
        }
    
    public function pjActionGetDriver()
        {
            $this->setAjax(true);
            if ($this->isXHR())
            {
                $pjDriverModel = pjDriverModel::factory();

                if ($q = $this->_get->toString('q'))
                    { 
                        $pjDriverModel->where("(t1.email LIKE '%$q%' OR t1.first_name LIKE '%$q%')");
                    }
                    if ($this->_get->toString('status'))
                    {
                        $status = $this->_get->toString('status');

                        if(in_array($status, array('T', 'F')))
                        { 
                           $pjDriverModel->where('t1.status', $status);
                          
                        }
                    } 

                 $column = $this->_get->toString('column');

                 if ($column == 'name' || empty($column)) {
                        $column = 'first_name';
                    }
                $direction = 'ASC';
                if ($column && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
                {
                    $column = $column;
                    $direction = strtoupper($this->_get->toString('direction'));
                }

                 $total = $pjDriverModel->findCount()->getData();
                $rowCount = $this->_get->toInt('rowCount') ?: 10;
                $pages = ceil($total / $rowCount);
                $page = $this->_get->toInt('page') ?: 1;
                $offset = ((int) $page - 1) * $rowCount;
                if ($page > $pages)
                {
                    $page = $pages;
                }

                $data = $pjDriverModel
                ->join('pjDriverFile', 't1.id=t2.driver_id AND t2.file_category="license"', 'left outer')
                ->select("t1.id, CONCAT_WS(' ', t1.first_name, t1.last_name) AS name, t1.email, t1.phone, 
                        t1.license_number, t1.license_expiry, t1.vehicle_id, t1.status,
                        t2.thumb_path as thumb_path")
                ->orderBy("t1.$column $direction")
                ->limit($rowCount, $offset)
                ->findAll()
                ->getData();

                foreach ($data as $k => $v)
                { 
                    $v['name'] = pjSanitize::stripScripts($v['name']);
                    $v['email'] = pjSanitize::stripScripts($v['email']);
                    $v['phone'] = pjSanitize::stripScripts($v['phone']);
                    $v['license_number'] = pjSanitize::stripScripts($v['license_number']);
                    $v['vehicle_id'] = pjSanitize::stripScripts($v['vehicle_id']);
                    $v['files'] = isset($groupedFiles[$v['id']]) ? $groupedFiles[$v['id']] : [];
                    $v['thumb_path'] = !empty($v['thumb_path']) ? PJ_UPLOAD_PATH . $v['thumb_path'] : null;
                    $data[$k] = $v;
                }
                pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
            }
            exit;
        }

    public function pjActionIndex()
        {
            $this->checkLogin();
            if (!pjAuth::factory()->hasAccess())
            {
                $this->sendForbidden();
                return;
            }
            $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
            
            $this->appendJs('pjAdminDrivers.js?ver=1.2.69');
        }
    
    public function pjActionSaveDriver()
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
                    $client = pjDriverModel::factory()->find($params['id'])->getData();
                    pjAuthUserModel::factory()->set('id', $client['foreign_id'])->modify(array('locked' => $params['value']));
                }
            } else {
                pjDriverModel::factory()->where('id', $params['id'])->limit(1)->modifyAll(array($params['column'] => $params['value']));
                if(in_array($params['column'], array('status', 'email', 'name')))
                {
                    $client = pjDriverModel::factory()->reset()->find($params['id'])->getData();
                    $params['column'] = pjMultibyte::str_replace('c_', '', $params['column']);
                    $params['id'] = $client['foreign_id'];
                    pjAuth::init($params)->updateUser();
                }
            }
            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Client has been updated!'));
            exit;
        }
        
    public function pjActionStatusDriver()
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
            $foreign_ids = pjDriverModel::factory()->whereIn('id', $record)->findAll()->getDataPair(null, 'foreign_id');
            if(!empty($foreign_ids))
            {
                pjAuthUserModel::factory()
                ->whereIn('id', $foreign_ids)
                ->where('id !=', 1)
                ->modifyAll(array(
                    'status' => ":IF(`status`='F','T','F')"
                ));
            }
            self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Client status has been updated.'));
            exit;
        }

    public function pjActionUpdate()
        {
        
            $this->checkLogin();
            if (!pjAuth::factory()->hasAccess())
            {
                $this->sendForbidden();
                return;
            }
            if (self::isPost() && $this->_post->toInt('driver_update') && $this->_post->toInt('id'))
            {
                $id = $this->_post->toInt('id');
                $post = $this->_post->raw();
               
                // Status
                $post['status'] = $this->_post->check('status') ? 'T' : 'F';

                // Locale
                $post['locale_id'] = $this->getLocaleId();
                
                $date = $this->_post->toString('license_expiry');
                $bob_date = $this->_post->toString('dob');
                // Format fields
                $post['license_expiry'] = $date; //pjDateTime::formatDate($post['license_expiry'], $this->option_arr['o_date_format']);
                $post['dob'] = $bob_date; // pjDateTime::formatDate($post['dob'], $this->option_arr['o_date_format']);

                // Update main driver record
                $respons = pjDriverModel::factory()
                    ->where('id', $id)
                    ->limit(1)
                    ->modifyAll($post);
              

                $driver = pjDriverModel::factory()->find($id)->getData();

                $u_data = array();
                $u_data['is_active'] = 'T';
                $u_data['role_id'] = 4;
                $u_data['email'] = $post['email'];
                $u_data['password'] = isset($post['password']) ? $post['password'] : pjAuth::generatePassword($this->option_arr);
                $name_arr = array();
                if(isset($post['first_name']) && !empty($post['first_name']))
                {
                    $name_arr[] = $post['first_name'];
                }
                if(isset($post['first_name']) && !empty($post['first_name']))
                {
                    $name_arr[] = $post['last_name'];
                }
                $u_data['name'] = join(" ", $name_arr);
                $u_data['phone'] = isset($post['phone']) ? $post['phone'] : ":NULL";
                $u_data['status'] = isset($post['status']) ? $post['status'] : ":NULL";
                $u_data['ip'] = pjUtil::getClientIp();
                $u_data['is_active'] = 'T';

                pjAuthUserModel::factory()->where('id', $driver['auth_id'])->limit(1)
                    ->modifyAll($u_data);

             if(!empty($respons))
                    { 
                    if (!empty($_FILES['license_file']['name'])) 
                        { 
                            $uploadDir = PJ_UPLOAD_PATH . 'license/';
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0777, true);
                            }

                            $thumbDir = $uploadDir . 'thumb/';
                            if (!is_dir($thumbDir)) {
                                mkdir($thumbDir, 0777, true);
                            }

                            // STEP 1: Delete old license files from DB and disk
                            $oldFiles = pjDriverFileModel::factory()
                                ->where('driver_id', $id)
                                ->where('file_category', 'license')
                                ->findAll()
                                ->getData();

                            foreach ($oldFiles as $file) {
                                $filePath = PJ_UPLOAD_PATH . $file['source_path'];
                                $thumbPath = !empty($file['thumb_path']) ? PJ_UPLOAD_PATH . $file['thumb_path'] : null;
                                if (is_file($filePath)) unlink($filePath);
                                if ($thumbPath && is_file($thumbPath)) unlink($thumbPath);
                            }
                            pjDriverFileModel::factory()
                                ->where('driver_id', $id)
                                ->where('file_category', 'license')
                                ->eraseAll();

                            // STEP 2: Process new license file upload
                            $name    = $_FILES['license_file']['name'];
                            $tmpName = $_FILES['license_file']['tmp_name'];
                            $error   = $_FILES['license_file']['error'];
                            $size    = $_FILES['license_file']['size'];
                            $type    = $_FILES['license_file']['type'];

                            if ($error === 0 && is_uploaded_file($tmpName)) 
                            {
                                $ext = pathinfo($name, PATHINFO_EXTENSION);
                                // $uniqueName = uniqid('license_') . '.' . $ext;
                                $firstName = preg_replace('/[^a-z0-9_]/i', '', strtolower($post['first_name']));
                                $uniqueName = $firstName . '_license_' . uniqid() . '.' . $ext;
                                $destPath = $uploadDir . $uniqueName;

                                if (move_uploaded_file($tmpName, $destPath)) 
                                {
                                    // STEP 3: Generate thumbnail (if image)
                                    $thumbRelPath = null;
                                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
                                        $thumbName = 'thumb_' . $uniqueName;
                                        $thumbPathFull = $thumbDir . $thumbName;
                                        $thumbRelPath  = 'license/thumb/' . $thumbName;

                                        $sourceImage = null;
                                        switch (strtolower($ext)) {
                                            case 'jpg':
                                            case 'jpeg':
                                                $sourceImage = imagecreatefromjpeg($destPath);
                                                break;
                                            case 'png':
                                                $sourceImage = imagecreatefrompng($destPath);
                                                break;
                                        }
                                        if ($sourceImage) {
                                            $width  = imagesx($sourceImage);
                                            $height = imagesy($sourceImage);
                                            $newWidth = 200;
                                            $newHeight = intval($height * ($newWidth / $width));
                                            $thumb = imagecreatetruecolor($newWidth, $newHeight);
                                            imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                            switch (strtolower($ext)) {
                                                case 'jpg':
                                                case 'jpeg':
                                                    imagejpeg($thumb, $thumbPathFull);
                                                    break;
                                                case 'png':
                                                    imagepng($thumb, $thumbPathFull);
                                                    break;
                                            }
                                            imagedestroy($thumb);
                                            imagedestroy($sourceImage);
                                        }
                                    }
                                    // STEP 4: Insert new file record
                                    pjDriverFileModel::factory()->setAttributes([
                                        'driver_id'     => $id,
                                        'file_name'     => $uniqueName,
                                        'original_name' => $name,
                                        'file_type'     => $type,
                                        'file_size'     => $size,
                                        'file_category' => 'license',
                                        'created'       => date("Y-m-d H:i:s"),
                                        'source_path'   => 'license/' . $uniqueName,
                                        'thumb_path'    => $thumbRelPath
                                    ])->insert();

                                    // STEP 5: Update driver record with license_file name
                                    pjDriverModel::factory()
                                        ->where('id', $id)
                                        ->limit(1)
                                        ->modifyAll(['license_file' => $uniqueName]);
                                }
                            }
                        }
                    if (!empty($_FILES['driver_documents']['name'][0])) 
                        {
                            $uploadDir = PJ_UPLOAD_PATH . 'driver_documents/';
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0777, true);
                            }
                            // Step 1: Delete old files (DB + folder)
                            $oldFiles = pjDriverFileModel::factory()
                                ->where('driver_id', $id)
                                ->whereIn('file_category', array('photo', 'id', 'additional'))
                                ->findAll()
                                ->getData();
                            foreach ($oldFiles as $oldFile) {
                                $fullPath = PJ_UPLOAD_PATH . $oldFile['source_path'];
                                if (file_exists($fullPath)) {
                                    unlink($fullPath);
                                }
                            }
                            pjDriverFileModel::factory()
                                ->where('driver_id', $id)
                                ->whereIn('file_category', array('photo', 'id', 'additional'))
                                ->eraseAll();

                            // Step 2: Process new uploads
                            foreach ($_FILES['driver_documents']['name'] as $index => $name) {
                                $tmpName = $_FILES['driver_documents']['tmp_name'][$index] ?? null;
                                $error   = $_FILES['driver_documents']['error'][$index] ?? 1;
                                $size    = $_FILES['driver_documents']['size'][$index] ?? 0;
                                $type    = $_FILES['driver_documents']['type'][$index] ?? '';

                                if ($error === 0 && is_uploaded_file($tmpName)) {
                                    // Detect category
                                    $category = 'additional';
                                    $lcName = strtolower($name);
                                    if (strpos($lcName, 'id') !== false) {
                                        $category = 'id';
                                    } elseif (strpos($lcName, 'photo') !== false || strpos($lcName, 'image') !== false) {
                                        $category = 'photo';
                                    }
                                    // Save file
                                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                                    // $uniqueName = uniqid('doc_') . '.' . $ext;
                                    $doc_prefix = preg_replace('/[^a-z0-9_]/i', '', strtolower($post['first_name']));
                                    $uniqueName = uniqid($doc_prefix . '_') . '.' . $ext;
                                    $destPath = $uploadDir . $uniqueName;

                                    if (move_uploaded_file($tmpName, $destPath)) {
                                        pjDriverFileModel::factory()->setAttributes([
                                            'driver_id'     => $id,
                                            'file_name'     => $uniqueName,
                                            'original_name' => $name,
                                            'file_type'     => $type,
                                            'file_size'     => $size,
                                            'file_category' => $category,
                                            'created'       => date("Y-m-d H:i:s"),
                                            'source_path'   => 'driver_documents/' . $uniqueName,
                                            'thumb_path'    => null
                                        ])->insert();
                                    }
                                }
                            }
                        }
                }
                if (!empty($respons))
                {
                    $err = 'ADR03';
                }else{
                    $err = 'ADR04';
                }
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminDrivers&action=pjActionIndex&err=$err");
            }
            if (self::isGet() && $this->_get->toInt('id'))
            { 
                $id = $this->_get->toInt('id');
                // $arr = pjDriverModel::factory() 
                //     ->find($id)
                //     ->getData();
                $driverId = (int) $id;

                // Get driver with all related files
                $driver = pjDriverModel::factory()
                    ->find($driverId)
                    ->getData();

                if ($driver) {
                    $driver['files'] = pjDriverFileModel::factory()
                        ->where('driver_id', $driverId)
                        ->findAll()
                        ->getData();
                }
                $arr = $driver;

                if (count($arr) === 0)
                {
                    pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminDrivers&action=pjActionIndex&err=AC08");
                }
                $this->set('arr', $arr);
                $country_arr = pjBaseCountryModel::factory()
                ->select('t1.id, t2.content AS country_title')
                ->join('pjBaseMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->where('status', 'T')
                ->orderBy('`country_title` ASC')->findAll()->getData();
                $this->set('country_arr', $country_arr);
                
                $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
                $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
                $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
                $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
                $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
                $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
                $this->appendJs('pjAdminDrivers.js?ver=1.2.69');
            }
        }

    private function deleteDriverFiles($driverId, $category = null)
        {
            $query = pjDriverFileModel::factory()->where('driver_id', $driverId);
            if ($category) {
                $query->where('file_category', $category);
            }
            $files = $query->findAll()->getData();

            foreach ($files as $file) {
                if (file_exists(PJ_UPLOAD_PATH . $file['source_path'])) {
                    unlink(PJ_UPLOAD_PATH . $file['source_path']);
                }
                if (!empty($file['thumb_path']) && file_exists(PJ_UPLOAD_PATH . $file['thumb_path'])) {
                    unlink(PJ_UPLOAD_PATH . $file['thumb_path']);
                }
            }

            $query->eraseAll();
        }

   
    public function pjActionDriverReservations()
        {  
            $id = $this->_get->toInt('id'); // this is driver_id

            $pjBookingModel = pjBookingModel::factory()
                ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjDriver', "t3.id=t1.driver_id", 'left outer')
                ->join('pjAuthUser', "t4.id=t3.auth_id", 'left outer');
            $pjBookingModel->where("t3.id", $id);
            $arr = $pjBookingModel
                ->select("
                    t1.*,
                    t1.status AS booking_status,
                    t2.content as fleet, 
                    t3.*, 
                    AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS cc_type,
                    AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS cc_num,
                    AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS cc_exp_month,
                    AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS cc_exp_year,
                    AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS cc_code,
                    t4.name AS driver_name
                ")
                ->orderBy("t1.created DESC")
                ->findAll()
                ->getData();

            $this->set('arr', $arr);
        }

    public function pjActionGetDriverResAdminIndex()
        {  
          
            $this->checkLogin();
              $id = $this->_get->toInt('id'); // driver_id
             $pjDriverModel = pjDriverModel::factory();
             $driver_name = $pjDriverModel->
                    where("id", $id)
                    ->findAll()
                    ->getDataIndex(0);
                  
            $this->set('driver_name', $driver_name);
            if (!pjAuth::factory()->hasAccess())
            {
                $this->sendForbidden();
                return;
            }
            $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
            
            $this->appendJs('pjAdminDrivers.js?ver=1.2.69');
        }
    
    public function pjActionDriverResAdminList()
        {
            $this->setAjax(true);
            if ($this->isXHR())
            {
                $id = $this->_get->toInt('id'); // driver_id
                $pjBookingModel = pjBookingModel::factory()
                ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjDriver', "t3.id=t1.driver_id", 'left outer')
                ->join('pjAuthUser', "t4.id=t3.auth_id", 'left outer');
                $pjBookingModel->where("t3.id", $id);

                $total = $pjBookingModel->findCount()->getData();

                $rowCount = $this->_get->toInt('rowCount') ?: 10;
                $pages    = ceil($total / $rowCount);
                $page     = $this->_get->toInt('page') ?: 1;
                $offset   = ((int) $page - 1) * $rowCount;
                if ($page > $pages) {
                    $page = $pages;
                }

                $column    = $this->_get->toString('column') ?: 'created';
                if($column == 'booking_status' ){
                    $column = 'status';
                }
                
                $direction = strtoupper($this->_get->toString('direction'));
                if (!in_array($direction, array('ASC','DESC'))) {
                    $direction = 'DESC';
                }

                $data = $pjBookingModel
                    ->select("
                        t1.*,
                        t1.status AS booking_status,
                        t2.content as fleet,
                        t3.*,
                        AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS cc_type,
                        AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS cc_num,
                        AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS cc_exp_month,
                        AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS cc_exp_year,
                        AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS cc_code,
                        t4.name AS driver_name
                    ")
                    ->orderBy("t1.$column $direction")
                    ->limit($rowCount, $offset)
                    ->findAll()
                    ->getData();

                   

                    foreach ($data as $k => $v) {
                    $v['fleet'] = pjSanitize::stripScripts($v['fleet']);
                    $v['booking_date']  = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['booking_date']));
                   
                    $v['pickup_address'] = pjSanitize::stripScripts($v['pickup_address']);
                    $v['driver_name'] = pjSanitize::stripScripts($v['driver_name']);
                    $v['created']  = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['created']));
                    $data[$k] = $v;
                }
                pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
            }
       
            exit;
        }

    public function pjActionGetDriverReservationIndex()
        {
            $this->checkLogin();
            if (!pjAuth::factory()->hasAccess())
            {
                $this->sendForbidden();
                return;
            }
            $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
            
            $this->appendJs('pjAdminDrivers.js?ver=1.2.69');
        }

    public function pjActionDriverReservationsList()
        {
            $this->setAjax(true);
            if ($this->isXHR())
            {
                $id = $this->getUserId(); //$this->_get->toInt('id'); // driver_id
              
                $pjBookingModel = pjBookingModel::factory()
                ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjDriver', "t3.id=t1.driver_id", 'left outer')
                ->join('pjAuthUser', "t4.id=t3.auth_id", 'left outer');
                $pjBookingModel->where("t3.auth_id", $id);

                $total = $pjBookingModel->findCount()->getData();

                $rowCount = $this->_get->toInt('rowCount') ?: 10;
                $pages    = ceil($total / $rowCount);
                $page     = $this->_get->toInt('page') ?: 1;
                $offset   = ((int) $page - 1) * $rowCount;
                if ($page > $pages) {
                    $page = $pages;
                }

                $column    = $this->_get->toString('column') ?: 'created';
                if($column == 'booking_status' ){
                    $column = 'status';
                }
                
                $direction = strtoupper($this->_get->toString('direction'));
                if (!in_array($direction, array('ASC','DESC'))) {
                    $direction = 'DESC';
                }

                $data = $pjBookingModel
                    ->select("
                        t1.*,
                        t1.status AS booking_status,
                        t2.content as fleet,
                        t3.*,
                        AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS cc_type,
                        AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS cc_num,
                        AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS cc_exp_month,
                        AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS cc_exp_year,
                        AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS cc_code,
                        t4.name AS driver_name
                    ")
                    ->orderBy("t1.$column $direction")
                    ->limit($rowCount, $offset)
                    ->findAll()
                    ->getData();
                    foreach ($data as $k => $v) {
                    $v['fleet'] = pjSanitize::stripScripts($v['fleet']);
                    $v['booking_date']  = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['booking_date']));
                   
                    $v['pickup_address'] = pjSanitize::stripScripts($v['pickup_address']);
                    $v['driver_name'] = pjSanitize::stripScripts($v['driver_name']);
                    $v['created']  = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['created']));
                    $data[$k] = $v;
                }
                pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
            }
            exit;
        }
}
?>