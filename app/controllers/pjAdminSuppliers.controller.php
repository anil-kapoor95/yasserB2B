<?php
if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjAdminSuppliers extends pjAdmin
{
    public function pjActionIndex(){
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        
        //total available rides count
        $pjBookingModel = pjBookingModel::factory();
        $today = date('Y-m-d 00:00:00');
        $avail_rides = $pjBookingModel
            ->where('is_auction', 1)
            ->where('t1.supplier_id IS NULL')
            ->where('status', 'confirmed')
            ->where('is_deleted', 0)
            ->where('booking_date >=', $today)
            ->findCount()
            ->getData();
        $this->set('avail_rides', $avail_rides);

        //total supplier drivers
        $pjDriverModel = pjDriverModel::factory();
        $login_id = $this->getUserId();
        $total_drivers = $pjDriverModel
            ->where('supplier_id', $login_id)
            ->findCount()
            ->getData();
        $this->set('total_drivers', $total_drivers);

        
    }

    public function pjActionAcceptRide(){
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }

        $booking_id = $this->_post->toInt('booking_id');
        if ($booking_id <= 0) {
            self::jsonResponse([
                'status' => 'ERR',
                'code' => $booking_id,
                'text' => 'Invalid booking ID'
            ]);
        }   

        $login_id = $this->getUserId();
        $pjBookingModel = pjBookingModel::factory();

        $pjBookingModel
            ->reset()
            ->where('id', $booking_id)
            ->modifyAll([
                'supplier_id'   => $login_id
            ]);


        $pjAuctionModel = pjAuctionModel::factory();
        $pjAuctionModel
            ->reset()
            ->where('booking_id', $booking_id)
            ->modifyAll([
                'supplier_id' => $login_id,
                'status' => 'ended',
                'accepted_on' => date('Y-m-d H:i:s')
            ]);
        
        /* ================= GET BOOKING DATA ================= */
        $booking = pjBookingModel::factory()
            ->find($booking_id)
            ->getData();

        $response = [
            'status' => 'OK',
            'code'   => 200,
            'text'   => 'Booking accepted'
        ];

        $json = json_encode($response);

        header("Content-Type: application/json");
        header("Connection: close");
        header("Content-Length: " . strlen($json));

        echo $json;

        ob_flush();
        flush();
        ignore_user_abort(true);

        /* ================= SEND EMAIL AFTER RESPONSE ================= */

        pjAppController::pjActionBookingAcceptBySupplierSend(
            $this->option_arr,
            $booking,
            $login_id,
            PJ_SALT,
            'bookingaccept',
            $this->getLocaleId()
        );

        exit;
    }

    public function pjActionAvailableRides()
    {
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        //get login supplier vehicle categories
        $login_id = $this->getUserId();
        $supplierVehicleCategories = [];
        $pjSupplierModel = pjSupplierModel::factory();
        $supdata = $pjSupplierModel
            ->select('vehicle_category')
            ->where('auth_id', $login_id)
            ->findAll()
            ->getData();

        if (
            !empty($supdata) &&
            isset($supdata[0]['vehicle_category']) &&
            trim($supdata[0]['vehicle_category']) !== ''
        ) {
            // vehicle_category is NOT blank
            $supplierVehicleCategories = array_map(
                'intval',
                explode(',', $supdata[0]['vehicle_category'])
            );
        }

        $this->set('supplierVehicleCategories', $supplierVehicleCategories);
        

        $version = rand(0,9) . '.' . rand(0,9) . '.' . rand(0,9);
        $this->set('date_format', pjUtil::toBootstrapDate($this->option_arr['o_date_format']));

        $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
        $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
        $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs("pjSupplierRides.js?v={$version}");

        $this->set('has_update', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_create', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_delete', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_restore', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_delete_bulk', pjAuth::factory('pjAdminSuppliers')->hasAccess());
    }

    public function pjActionRideDetails()
    {
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
          $this->sendForbidden();
          return;
        }

        if (self::isGet() && $this->_get->toInt('id'))
        {
           $id = $this->_get->toInt('id');
           
           $arr = pjBookingModel::factory()->find($id)->getData();
           if(count($arr) <= 0)
           {
               pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminSuppliers&action=pjActionAvailableRides&err=ABB08");
           }
           
           $country_arr = pjBaseCountryModel::factory()
           ->select('t1.id, t2.content AS name')
           ->join('pjMultiLang', "t2.model='pjBaseCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
           ->where('t1.status', 'T')
           ->orderBy('`name` ASC')->findAll()->getData();
           $this->set('country_arr', $country_arr);
           
           $client_arr = pjClientModel::factory()
           ->select("t1.*, t2.email as c_email, t2.name as c_name, t2.phone as c_phone")
           ->join("pjAuthUser", "t2.id=t1.foreign_id", 'left outer')
           ->where('t2.status', 'T')
           ->orderBy('t2.name ASC')
           ->findAll()
           ->getData();
           $this->set('client_arr', $client_arr);
           
           $fleet_arr = pjFleetModel::factory()
           ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
           ->select("t1.*, t2.content as fleet")
           ->where('t1.status', 'T')
           ->orderBy("fleet ASC")
           ->findAll()->getData();
           $this->set('fleet_arr', $fleet_arr);
           
           $pjFleetExtraModel = pjFleetExtraModel::factory()
           ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')

            ->join('pjExtra', "t1.extra_id=t3.id", 'left')

            ->select("t1.*, t2.content as name, t3.price, t3.per")

            ->where('t1.fleet_id', $arr['fleet_id'])

            ->orderBy("name ASC");
           $avail_extra_arr = $pjFleetExtraModel->findAll()->getData();

            $this->set('avail_extra_arr', $avail_extra_arr);
           
           $extra_id_arr = pjBookingExtraModel::factory()->where('booking_id', $id)->findAll()->getDataPair('extra_id', 'extra_value');

            $this->set('extra_id_arr', $extra_id_arr);
           $pjDriverModel = pjDriverModel::factory();

                $deriver_ids =  $pjDriverModel->where('supplier_id',719)->findAll()->getData();
               $this->set('deriver_ids', $deriver_ids);
           if(pjObject::getPlugin('pjPayments') !== NULL)
           {
               $this->set('payment_option_arr', pjPaymentOptionModel::factory()->getOptions($this->getForeignId()));
               $this->set('payment_titles', pjPayments::getPaymentTitles($this->getForeignId(), $this->getLocaleId()));
           }else{
               $this->set('payment_titles', __('payment_methods', true));
           }
           
           $version = rand(0,9) . '.' . rand(0,9) . '.' . rand(0,9);

           $this->set('arr', $arr);
           $this->set('date_format', pjUtil::toBootstrapDate($this->option_arr['o_date_format']));
           $api_key = isset($this->option_arr['o_google_maps_api_key']) && !empty($this->option_arr['o_google_maps_api_key']) ? '&key=' . $this->option_arr['o_google_maps_api_key'] : '';
           $this->appendJs('https://maps.googleapis.com/maps/api/js?libraries=places' . $api_key, null, true);
           $this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
           $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
           $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
           $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
           $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
           $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
           $this->appendJs("pjActionRideDetails.js?v={$version}");
       }
    }

    public function pjActionUpcomingRides(){
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        

        $version = rand(0,9) . '.' . rand(0,9) . '.' . rand(0,9);
        $this->set('date_format', pjUtil::toBootstrapDate($this->option_arr['o_date_format']));

        $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
        $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
        $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs("pjSupplierUpRides.js?v={$version}");

        $this->set('has_update', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_create', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_delete', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_restore', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_delete_bulk', pjAuth::factory('pjAdminSuppliers')->hasAccess());
    }

    public function pjActionPastRides(){
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        

        $version = rand(0,9) . '.' . rand(0,9) . '.' . rand(0,9);
        $this->set('date_format', pjUtil::toBootstrapDate($this->option_arr['o_date_format']));

        $this->appendJs('moment-with-locales.min.js', PJ_THIRD_PARTY_PATH . 'moment/');
        $this->appendCss('build/css/bootstrap-datetimepicker.min.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
        $this->appendJs('build/js/bootstrap-datetimepicker.min.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datetimepicker/');
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs("pjSupplierPastRides.js?v={$version}");

        $this->set('has_update', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_create', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_delete', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_restore', pjAuth::factory('pjAdminSuppliers')->hasAccess());
        $this->set('has_delete_bulk', pjAuth::factory('pjAdminSuppliers')->hasAccess());
    }

    public function pjActionDrivers(){
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        
        $this->appendJs('pjSupplierDrivers.js?ver=1.2.69');
    }

    public function pjActionDriverCreate(){
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }

        if (self::isPost() && $this->_post->toInt('driver_create'))
        {
            $post = $this->_post->raw();
            $login_id = $this->getUserId();

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
                $data['auth_id'] = $driverAuthIds['id'];;
                $data['supplier_id'] = $login_id;
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
                pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSuppliers&action=pjActionDrivers&err=$err");
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
            $this->appendJs('pjSupplierDrivers.js?ver=1.2.69');
        }
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

            //new code
            $login_id = $this->getUserId();

            $pjDriverModel
            ->where('t1.supplier_id',$login_id);
            //->where('t1.auth_id', 0);
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
    
    public function pjActionDriverUpdate(){
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
            pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSuppliers&action=pjActionDrivers&err=$err");
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
        
        $this->appendJs('pjSupplierDrivers.js?ver=1.2.69');
    }

    public function pjActionGetPastBooking(){
         $this->checkLogin();
       $this->setAjax(true);
       
       if ($this->isXHR())
       {
            $supplier_id = $this->getUserId();
           $pjBookingModel = pjBookingModel::factory()
           ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
           ->join('pjClient', "t3.id=t1.client_id", 'left outer')
           ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
           ->where("t1.is_deleted = 0");
           //->where("t1.supplier_id",$supplier_id);

            
            $role_id = $this->getRoleId();

            if ((int) $role_id === 5) {
                $today = date('Y-m-d 00:00:00');

                $pjBookingModel = $pjBookingModel
                    ->select('t1.*')
                    ->join(
                        'taxi_auctions',
                        "taxi_auctions.booking_id = t1.id AND taxi_auctions.status = 'active'",
                        'inner'
                    )
                    ->where('t1.is_auction', 1)
                    ->where('t1.supplier_id',$supplier_id)
                    //->where('t1.status', 'confirmed')
                    ->where('t1.is_deleted', 0)
                    ->where("t1.booking_date < '{$today}'");
            }
         
           if ($this->_get->has('q') && !$this->_get->isEmpty('q'))
           {
               $q = $this->_get->toString('q');

               // $pjBookingModel->where("(t4.name LIKE '%$q%' OR t4.email LIKE '%$q%' OR t2.content LIKE '%$q%')");
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
           $extras_by_booking = [];

           if (!empty($booking_ids)) {
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

                
                foreach ($extras_model as $ex) {
                    $extras_by_booking[$ex['booking_id']][] = $ex;
                }
            }

            foreach($data as $k => $v)
            {
               // echo "<pre>"; print_r($v); echo "</pre>";

                $v['client'] = $fullName = trim($v['c_fname'] . ' ' . $v['c_lname']);

                $v['client'] = pjSanitize::clean($fullName !== '' ? $fullName : $v['name']); // pjSanitize::clean($v['name']);

                $v['date_time']  = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['booking_date']));
                $v['distance'] = (int) $v['distance'] . ' km';
                $v['driver_name'] = pjSanitize::clean($v['driver_name'] ? $v['driver_name'] : 'NA');
                $data[$k] = $v;
                $data[$k]['is_auction'] = $v['is_auction'] == 1 ? 'Yes' : 'No';
                $data[$k]['extras'] = $extras_by_booking[$v['id']] ?? [];
            }
            // echo "<pre>"; print_r($data); 
            self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
        }
       exit;
    }

    public function pjActionGetUpcomingBooking(){
        $this->checkLogin();
       $this->setAjax(true);
       
       if ($this->isXHR())
       {
            $supplier_id = $this->getUserId();
           $pjBookingModel = pjBookingModel::factory()
           ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
           ->join('pjClient', "t3.id=t1.client_id", 'left outer')
           ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
           ->where("t1.is_deleted = 0");
           //->where("t1.supplier_id",$supplier_id);

            
            $role_id = $this->getRoleId();

            if ((int) $role_id === 5) {
                $today = date('Y-m-d 00:00:00');

                $pjBookingModel = $pjBookingModel
                    ->select('t1.*')
                    ->join(
                        'taxi_auctions',
                        "taxi_auctions.booking_id = t1.id AND taxi_auctions.status = 'active'",
                        'inner'
                    )
                    ->where('t1.is_auction', 1)
                    ->where('t1.supplier_id',$supplier_id)
                    //->where('t1.status', 'confirmed')
                    ->where('t1.is_deleted', 0)
                    ->where("t1.booking_date >= '{$today}'");
            }
         
           if ($this->_get->has('q') && !$this->_get->isEmpty('q'))
           {
               $q = $this->_get->toString('q');

               // $pjBookingModel->where("(t4.name LIKE '%$q%' OR t4.email LIKE '%$q%' OR t2.content LIKE '%$q%')");
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
           $extras_by_booking = [];

           if (!empty($booking_ids)) {
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

                
                foreach ($extras_model as $ex) {
                    $extras_by_booking[$ex['booking_id']][] = $ex;
                }
            }

            foreach($data as $k => $v)
            {
               // echo "<pre>"; print_r($v); echo "</pre>";

                $v['client'] = $fullName = trim($v['c_fname'] . ' ' . $v['c_lname']);

                $v['client'] = pjSanitize::clean($fullName !== '' ? $fullName : $v['name']); // pjSanitize::clean($v['name']);

                $v['date_time']  = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['booking_date']));
                $v['distance'] = (int) $v['distance'] . ' km';
                $v['driver_name'] = pjSanitize::clean($v['driver_name'] ? $v['driver_name'] : 'NA');
                $data[$k] = $v;
                $data[$k]['is_auction'] = $v['is_auction'] == 1 ? 'Yes' : 'No';
                $data[$k]['extras'] = $extras_by_booking[$v['id']] ?? [];
            }
            // echo "<pre>"; print_r($data); 
            self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
        }
       exit;
    }

    public function pjActionGetAvailableBooking()
   {
       $this->checkLogin();
       $this->setAjax(true);
       
       if ($this->isXHR())
       {
           $pjBookingModel = pjBookingModel::factory()
           ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
           ->join('pjClient', "t3.id=t1.client_id", 'left outer')
           ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
           ->where("t1.is_deleted = 0");

            $supplier_id = $this->getUserId();
            $role_id = $this->getRoleId();

            if ((int) $role_id === 5) {
                $today = date('Y-m-d 00:00:00');

                $pjBookingModel = $pjBookingModel
                    ->select('t1.*')
                    ->join(
                        'taxi_auctions',
                        "taxi_auctions.booking_id = t1.id AND taxi_auctions.status = 'active'",
                        'inner'
                    )
                    ->where('t1.is_auction', 1)
                    ->where('t1.supplier_id IS NULL')
                    ->where('t1.status', 'confirmed')
                    ->where('t1.is_deleted', 0)
                    ->where("t1.booking_date >= '{$today}'");
            }
         
           if ($this->_get->has('q') && !$this->_get->isEmpty('q'))
           {
               $q = $this->_get->toString('q');

               // $pjBookingModel->where("(t4.name LIKE '%$q%' OR t4.email LIKE '%$q%' OR t2.content LIKE '%$q%')");
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
           $extras_by_booking = [];

           if (!empty($booking_ids)) {
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

                
                foreach ($extras_model as $ex) {
                    $extras_by_booking[$ex['booking_id']][] = $ex;
                }
            }

            foreach($data as $k => $v)
            {
               // echo "<pre>"; print_r($v); echo "</pre>";

                $v['client'] = $fullName = trim($v['c_fname'] . ' ' . $v['c_lname']);

                $v['client'] = pjSanitize::clean($fullName !== '' ? $fullName : $v['name']); // pjSanitize::clean($v['name']);

                $v['date_time']  = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['booking_date']));
                $v['distance'] = (int) $v['distance'] . ' km';
                $v['driver_name'] = pjSanitize::clean($v['driver_name'] ? $v['driver_name'] : 'NA');
                $data[$k] = $v;
                $data[$k]['is_auction'] = $v['is_auction'] == 1 ? 'Yes' : 'No';
                $data[$k]['extras'] = $extras_by_booking[$v['id']] ?? [];
            }
            // echo "<pre>"; print_r($data); 
            self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
        }
       exit;
   }

   public function pjActionDriverConfirmation()
    {
        $this->setAjax(true);
        if ($this->isXHR())
        {
            if ($this->_post->check('send_confirmation') && $this->_post->toString('to') && $this->_post->toString('from') && $this->_post->toInt('locale_id'))
            {
                $pjEmail = self::getMailer($this->option_arr);
                $locale_id = $this->_post->toInt('locale_id');
                $subject = $this->_post->toString('subject');
                $message = $this->_post->toString('message');
                $r = $pjEmail
                ->setTo($this->_post->toString('to'))
                ->setSubject($subject)
                ->send($message);
                if ($r)
                {
                    self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
                }
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
            }

            
            if ($this->_get->toInt('booking_id'))
            {
                $booking_arr = pjBookingModel::factory()
                ->select("t1.*, t3.email AS c_email")
                ->join('pjClient', 't1.client_id=t2.id', 'left')
                ->join('pjAuthUser', 't3.id=t2.foreign_id', 'left')
                ->find($this->_get->toInt('booking_id'))->getData();
               $pjMultiLangModel = pjMultiLangModel::factory();

                $pjNotificationModel = pjNotificationModel::factory();
               $tokens = pjAppController::getTokens($this->option_arr, $booking_arr, PJ_SALT, $booking_arr['locale_id']);
               $notification = $pjNotificationModel->reset()->where('recipient', 'drivers')->where('transport', 'email')->where('variant', "driverconfirmation")->findAll()->getDataIndex(0);

                if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
                {
                    $resp = pjAppController::pjActionGetSubjectMessage($notification, $booking_arr['locale_id'], $this->getForeignId());
                    $lang_message = $resp['lang_message'];
                    $lang_subject = $resp['lang_subject'];
                    if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']))
                    {
                        $subject = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
                        $message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
                       $this->set('arr', array(
                            'locale_id' => $booking_arr['locale_id'],
                            'to' => $booking_arr['c_email'],
                            'from' => self::getAdminEmail(),
                            'subject' => $subject,
                            'message' => $message,
                        ));
                    }
                }
            } else {
                exit;
            }
        }
    }

    public function pjActionConfirmation()
    {

        $this->setAjax(true);
       
       if ($this->isXHR())
       {
           if ($this->_post->check('send_confirmation') && $this->_post->toString('to') && $this->_post->toString('from') && $this->_post->toInt('locale_id'))
           {
               $pjEmail = self::getMailer($this->option_arr);
               
               $locale_id = $this->_post->toInt('locale_id');
               
               $subject = $this->_post->toString('subject');
               $message = $this->_post->toString('message');
               
               $r = $pjEmail
               ->setTo($this->_post->toString('to'))
               ->setSubject($subject)
               ->send($message);
               
               if ($r)
               {
                   self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
               }
               self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
           }
           
           if ($this->_get->toInt('booking_id'))
           {
               $booking_arr = pjBookingModel::factory()
               ->select("t1.*, t3.email AS c_email")
               ->join('pjClient', 't1.client_id=t2.id', 'left')
               ->join('pjAuthUser', 't3.id=t2.foreign_id', 'left')
               ->find($this->_get->toInt('booking_id'))->getData();
               
               $pjMultiLangModel = pjMultiLangModel::factory();
               $pjNotificationModel = pjNotificationModel::factory();
               
               $tokens = pjAppController::getTokens($this->option_arr, $booking_arr, PJ_SALT, $booking_arr['locale_id']);
               
               $notification = $pjNotificationModel->reset()->where('recipient', 'client')->where('transport', 'email')->where('variant', "confirmation")->findAll()->getDataIndex(0);
               if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
               {
                   $resp = pjAppController::pjActionGetSubjectMessage($notification, $booking_arr['locale_id'], $this->getForeignId());
                   $lang_message = $resp['lang_message'];
                   $lang_subject = $resp['lang_subject'];
                   if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content']))
                   {
                       $subject = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
                       $message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
                       
                       $this->set('arr', array(
                           'locale_id' => $booking_arr['locale_id'],
                           'to' => $booking_arr['c_email'],
                           'from' => self::getAdminEmail(),
                           'subject' => $subject,
                           'message' => $message,
                       ));
                   }
               }
           } else {
               exit;
           }
       }
   }

}
?>