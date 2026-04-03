<?php
if (! defined("ROOT_PATH")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjJabbApi extends pjAppController
{

    protected function initializePermissions()
    {
        $pair                      = [];
        $pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();
        $cnt                       = $pjAuthUserPermissionModel->where("t1.user_id", $this->getUserId())->findCount()->getData();
        if ($cnt > 0) {
            $pair = $pjAuthUserPermissionModel
                ->reset()
                ->select("t1.*, t2.`key`")
                ->join('pjAuthPermission', 't2.id=t1.permission_id', 'left')
                ->where("t1.user_id", $this->getUserId())
                ->findAll()->getDataPair(null, 'key');
        } else {
            $pair = pjAuthRolePermissionModel::factory()
                ->select("t1.*, t2.`key`")
                ->join('pjAuthPermission', 't2.id=t1.permission_id', 'left')
                ->where("t1.role_id", $this->getRoleId())
                ->findAll()->getDataPair(null, 'key');
        }
        $this->session->setData($this->defaultPermissions, $pair);
    }

    public function pjActionLogin()
    {
          
        header("Content-Type: application/json");

        // Read JSON or POST
        $raw_input = file_get_contents("php://input");
        $input     = json_decode($raw_input, true);

        $email    = isset($input['email']) ? trim($input['email']) : trim($this->_post->toString('email'));
        $password = isset($input['password']) ? $input['password'] : $this->_post->toString('password');

        // ---------------- VALIDATION ----------------
        $errors = [];

        if (!pjValidation::pjActionNotEmpty($email)) {
            $errors[] = "Email is required";
        } elseif (!pjValidation::pjActionEmail($email)) {
            $errors[] = "Invalid email format";
        }

        if (!pjValidation::pjActionNotEmpty($password)) {
            $errors[] = "Password is required";
        }

        if (!empty($errors)) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'Validation failed',
                'errors'  => $errors
            ]);
            exit;
        }

        // ---------------- FIND SUPPLIER ----------------
        $supplier = pjAuthUserModel::factory()
            ->join('pjSupplier', 't2.auth_id = t1.id', 'left')
            ->where('t1.email', $email)
            ->where('t1.role_id', 5)
            ->select("
                t1.id,
                t1.email,
                t1.name,
                t1.phone,
                t1.password,
                t1.status,
                t2.company_name,
                t2.city,
                t2.total_vehicles
            ")
            ->limit(1)
            ->findAll()
            ->getData();

        if (count($supplier) != 1) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 401,
                'message' => 'Invalid credentials',
                'errors'  => []
            ]);
            exit;
        }

        $supplier = $supplier[0];

        // ---------------- PASSWORD CHECK ----------------
        $login = pjAuth::init([
            'login_email'    => $email,
            'login_password' => $password,
            'role_id'        => 5
        ])->doLogin();

        if ($login['status'] != 'OK') {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 401,
                'message' => 'Wrong password',
                'errors'  => []
            ]);
            exit;
        }

        // ---------------- STATUS CHECK ----------------
        if ($supplier['status'] != 'T') {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 403,
                'message' => 'Account not approved by admin',
                'errors'  => []
            ]);
            exit;
        }

        // ---------------- TOKEN GENERATION ----------------
        $api_login_token = bin2hex(random_bytes(32));
        $current_login   = date("Y-m-d H:i:s");

        $pjAuthUserModel = pjAuthUserModel::factory();

        $pjAuthUserModel
            ->reset()
            ->setAttributes(['id' => $supplier['id']])
            ->modify([
                'api_login_token' => $api_login_token,
                'current_login'   => $current_login
            ]);

        unset($supplier['password']);

        // ---------------- SUCCESS RESPONSE ----------------
        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Login successful',
            'supplier' => $supplier,
            'data' => [
                'id'              => $supplier['id'],
                'email'           => $supplier['email'],
                'api_login_token' => $api_login_token,
                'current_login'   => $current_login
            ]
        ]);
        exit;
    }

    public function pjActionRegister()
    {
        header("Content-Type: application/json");

        // Read JSON or POST
        $raw_input = file_get_contents("php://input");
        $input = json_decode($raw_input, true);

        $post = !empty($input) ? $input : $this->_post->raw();

        // ---------------- VALIDATION ----------------
        $errors = [];

        $required = [
            'first_name',
            'last_name',
            'email',
            'password',
            'confirm_password',
            'phone',
            'company_name',
            'city',
            'vehicle_category'
        ];

        foreach ($required as $field) {
            if (!isset($post[$field]) || trim($post[$field]) === '') {
                $errors[] = ucfirst(str_replace('_',' ',$field)) . " is required";
            }
        }

        // Email validation
        if (!empty($post['email']) && !pjValidation::pjActionEmail($post['email'])) {
            $errors[] = "Invalid email format";
        }

        // Password match
        if (isset($post['password'], $post['confirm_password']) && $post['password'] !== $post['confirm_password']) {
            $errors[] = "Passwords do not match";
        }

        // Check email exists
        if (!empty($post['email'])) {
            $exists = pjAuthUserModel::factory()
                ->where('email', $post['email'])
                ->findCount()
                ->getData();

            if ($exists > 0) {
                $errors[] = "Email already exists";
            }
        }

        if (!empty($errors)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 400,
                'message' => 'Validation failed',
                'errors' => $errors
            ]);
            exit;
        }
        $authId = null;
        $supplierId = null;
        try {

            // ---------------- CREATE AUTH USER ----------------
            $userData = [
                'role_id'   => 5,
                'email'     => $post['email'],
                'password'  => $post['password'],
                'name'      => trim($post['first_name'] . ' ' . $post['last_name']),
                'phone'     => $post['phone'],
                'status'    => 'F',
                'is_active' => 'T',
                'ip'        => pjUtil::getClientIp()
            ];

            $authId = pjAuthUserModel::factory($userData)
                ->insert()
                ->getInsertId();

            if (!$authId) {
                throw new Exception('Auth user creation failed');
            }


            // ---------------- CREATE SUPPLIER ----------------
            $supplierData = [
                'auth_id'          => $authId,
                'first_name'       => $post['first_name'],
                'last_name'        => $post['last_name'],
                'phone'            => $post['phone'],
                'company_name'     => $post['company_name'],
                'city'             => $post['city'],
                'vehicle_category' => $post['vehicle_category'],
                'status'           => 'T'
            ];

            $supplierId = pjSupplierModel::factory()
                ->setAttributes($supplierData)
                ->insert()
                ->getInsertId();

            if (!$supplierId) {
                throw new Exception('Supplier creation failed');
            }

        } catch (Exception $e) {

            // ---------------- DELETE AUTH USER IF CREATED ----------------
            if (!empty($authId)) {
                pjAuthUserModel::factory()
                    ->where('id', $authId)
                    ->limit(1)
                    ->eraseAll();
            }

            echo json_encode([
                'status' => 'ERR',
                'code' => 500,
                'message' => $e->getMessage(),
                'errors' => []
            ]);
            exit;
        }

        // ---------------- SUCCESS RESPONSE ----------------
        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'message' => 'Supplier registered successfully. Please wait for admin approval.',
            'data' => [
                'auth_id' => $authId,
                'supplier_id' => $supplierId,
                'email' => $post['email']
            ]
        ]);
        
        $url = PJ_INSTALL_URL . "index.php?controller=pjFrontPublic&action=pjActionSendSupplierEmails&supplier_id=".$supplierId;

        $parts = parse_url($url);

        $fp = fsockopen($parts['host'], 80, $errno, $errstr, 1);

        if ($fp) {
            $out = "GET ".$parts['path']."?".$parts['query']." HTTP/1.1\r\n";
            $out .= "Host: ".$parts['host']."\r\n";
            $out .= "Connection: Close\r\n\r\n";

            fwrite($fp, $out);
            fclose($fp);
        }
        
        exit;
    }

    public function pjActionChangePassword()
    {
        header("Content-Type: application/json");

        $raw_input = file_get_contents("php://input");
        $input = json_decode($raw_input, true);
        $post = !empty($input) ? $input : $this->_post->raw();

        // ---------------- VALIDATION ----------------
        if (
            empty($post['token']) ||
            empty($post['old_password']) ||
            empty($post['new_password']) ||
            empty($post['confirm_password'])
        ) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 400,
                'message' => 'Token, old password, new password and confirm password are required'
            ]);
            exit;
        }

        // ---------------- FIND USER BY TOKEN ----------------
        $user = pjAuthUserModel::factory()
            ->where('api_login_token', $post['token'])
            ->where('role_id', 5)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (!$user) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'Invalid token or user not found'
            ]);
            exit;
        }

        // ---------------- VERIFY OLD PASSWORD ----------------
        if ($user['password'] != $post['old_password']) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'Old password is incorrect'
            ]);
            exit;
        }

        // ---------------- CHECK PASSWORD MATCH ----------------
        if ($post['new_password'] != $post['confirm_password']) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 400,
                'message' => 'New password and confirm password do not match'
            ]);
            exit;
        }

        // ---------------- UPDATE PASSWORD ----------------
        pjAuthUserModel::factory()
            ->where('id', $user['id'])
            ->limit(1)
            ->modifyAll([
                'password'       => $post['new_password'],
                'pswd_modified'  => ':NOW()',
                'ip'             => pjUtil::getClientIp(),
                'api_login_token'=> 'hkjahs'
            ]);

        // ---------------- SUCCESS ----------------
        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'message' => 'Password changed successfully'
        ]);
        exit;
    }

    public function pjActionForgot()
    {
        header("Content-Type: application/json");

        $raw_input = file_get_contents("php://input");
        $input = json_decode($raw_input, true);
        $post = !empty($input) ? $input : $this->_post->raw();

        // ---------------- VALIDATION ----------------
        if (empty($post['email'])) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 400,
                'message' => 'Email is required'
            ]);
            exit;
        }

        if (!pjValidation::pjActionEmail($post['email'])) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 400,
                'message' => 'Invalid email format'
            ]);
            exit;
        }

        // ---------------- CHECK USER ----------------
        $user = pjAuthUserModel::factory()
            ->where('email', $post['email'])
            ->where('role_id', 5)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (!$user) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 404,
                'message' => 'Email not found'
            ]);
            exit;
        }

        // ---------------- GET SUPPLIER ----------------
        $supplier = pjSupplierModel::factory()
            ->where('auth_id', $user['id'])
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (!$supplier) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 404,
                'message' => 'Supplier not found'
            ]);
            exit;
        }

        // ---------------- SEND EMAIL ----------------
        $notification = pjNotificationModel::factory()
            ->where('recipient', 'suppliers')
            ->where('transport', 'email')
            ->where('variant', 'forgot')
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if ((int)$notification['id'] > 0 && $notification['is_active'] == 1) {

            $resp = pjAppController::pjActionGetSubjectMessage(
                $notification,
                $this->getLocaleId(),
                $this->getForeignId()
            );

            $lang_message = $resp['lang_message'];
            $lang_subject = $resp['lang_subject'];

            if (count($lang_message) === 1 && count($lang_subject) === 1) {

                $search = [
                    '{supplierFirstName}',
                    '{supplierLastName}',
                    '{supplierEmail}',
                    '{supplierPassword}',
                    '{supplierPhone}',
                    '{supplierCompany}'
                ];

                $replace = [
                    $supplier['first_name'],
                    $supplier['last_name'],
                    $user['email'],
                    $user['password'],
                    $supplier['phone'],
                    $supplier['company_name']
                ];

                $subject = $lang_subject[0]['content'] ?? '';
                $message = $lang_message[0]['content'] ?? '';

                $subject_client = str_replace($search, $replace, $subject);
                $message_client = str_replace($search, $replace, $message);

                $Email = self::getMailer($this->option_arr);
                $Email->setTo($user['email'])
                    ->setSubject($subject_client)
                    ->send(pjUtil::textToHtml($message_client));
            }
        }

        // ---------------- SUCCESS ----------------
        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'message' => 'Password details sent to your email'
        ]);
        exit;
    }

    protected static function getAdminEmail()
    {
        $arr = pjAuthUserModel::factory()->select('t1.email')->find(1)->getData();
        return $arr ? $arr['email'] : NULL;
    }

    public function logout()
    {
        header("Content-Type: application/json");

        $token = $this->_post->toString('token');
        $email = $this->_post->toString('email') ?? $this->_get->toString('email');

        if (! $token || ! $email) {
            echo json_encode([
                'status'  => 'ERROR',
                'code'    => 400,
                'message' => 'Missing token or email',
            ]);
            exit;
        }

        // Check if user with this token exists
        $user = pjAuthUserModel::factory()
            ->where('t1.email', $email)
            ->where('t1.api_login_token', $token)
            ->limit(1)
            ->findAll()
            ->getData();

        if (count($user) === 1) {
            // Invalidate the token (set to NULL or empty string)
            $data                    = [];
            $data['api_login_token'] = 'sdsd';
            pjAuthUserModel::factory()->reset()->setAttributes(['id' => $user[0]['id']])->modify($data);
            echo json_encode([
                'status'  => 'OK',
                'code'    => 200,
                'message' => 'Logout successful',
            ]);
        } else {
            echo json_encode([
                'status'  => 'ERROR',
                'code'    => 401,
                'message' => 'Invalid token or user not found',
            ]);
        }

        exit;
    }

    public function pjActionGetAvailableBookings()
    {

        header("Content-Type: application/json");
        $params = $this->_post->raw();
        // ---------------- TOKEN ----------------
        $token = $params['api_login_token'] ?? '';

        if (empty($token)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'API token required'
            ]);
            exit;
        }

        // ---------------- FIND SUPPLIER ----------------
        $user = pjAuthUserModel::factory()
        ->select("t1.*")
        ->where('t1.api_login_token', $token)
        ->limit(1)
        ->findAll()
        ->getData();


        if (empty($user)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'Invalid API token'
            ]);
            exit;
        }

        $user = $user[0];
        $role_id = $user['role_id'];
        $supplier_id = $user['id'];

        // ---------------- BOOKINGS QUERY ----------------
        $pjBookingModel = pjBookingModel::factory()
            ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet'", 'left')
            ->join('pjClient', "t3.id=t1.client_id", 'left')
            ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left')
            ->join('taxi_auctions', "taxi_auctions.booking_id = t1.id AND taxi_auctions.status = 'active'", 'inner')
            ->where("t1.is_deleted", 0)
            ->where('t1.is_auction', 1);

        // ---------------- FILTERS ----------------

        if (!empty($params['status'])) {
            $pjBookingModel->where('t1.status', $params['status']);
        }

        if (!empty($params['date'])) {
            $pjBookingModel->where("DATE(t1.booking_date)", $params['date']);
        }

        if (!empty($params['start_date'])) {
            $pjBookingModel->where("DATE(t1.booking_date) >=", $params['start_date']);
        }

        if (!empty($params['end_date'])) {
            $pjBookingModel->where("DATE(t1.booking_date) <=", $params['end_date']);
        }

        // ---------------- PAGINATION ----------------

        $rowCount = isset($params['rowCount']) ? (int) $params['rowCount'] : 20;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        $total = $pjBookingModel->findCount()->getData();
        $pages = ceil($total / $rowCount);
        $offset = ($page - 1) * $rowCount;

        // ---------------- FETCH BOOKINGS ----------------

        $data = $pjBookingModel
            ->select("
                t1.*,
                t2.content AS fleet,
                t4.name,
                t4.email,
                t4.phone,
                CONCAT_WS(' ', t5.first_name, t5.last_name) AS driver_name,
            ")
            ->join('pjDriver', "t1.driver_id=t5.id", 'left')
            // ->join('pjAuthUser', "t6.id=t1.supplier_id", 'left')
            ->limit($rowCount, $offset)
            ->orderBy("t1.created DESC")
            ->findAll()
            ->getData();

        // ---------------- FORMAT DATA ----------------

        foreach ($data as $k => $v) {

            $fullName = trim($v['c_fname'] . ' ' . $v['c_lname']);

            $data[$k]['client'] = $fullName ?: $v['name'];

            $data[$k]['distance'] = (int)$v['distance'] . ' km';
        }

        // ---------------- RESPONSE ----------------

        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'data' => $data,
            'total' => $total,
            'pages' => $pages,
            'page' => $page
        ]);

        exit;
    }

    public function pjActionGetAcceptedBookings()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        // ---------------- TOKEN ----------------
        $token = $params['api_login_token'] ?? '';

        if (empty($token)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'API token required'
            ]);
            exit;
        }

        // ---------------- FIND SUPPLIER ----------------
        $user = pjAuthUserModel::factory()
            ->select("t1.*")
            ->where('t1.api_login_token', $token)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($user)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'Invalid API token'
            ]);
            exit;
        }

        $user = $user[0];
        $role_id = $user['role_id'];
        $supplier_id = $user['id'];

        // ---------------- BOOKINGS QUERY ----------------
        $pjBookingModel = pjBookingModel::factory()
            ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet'", 'left')
            ->join('pjClient', "t3.id=t1.client_id", 'left')
            ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left')
            ->join('auctions', "auctions.booking_id = t1.id AND auctions.supplier_id = $supplier_id AND auctions.status = 'ended'", 'inner')
            ->where("t1.is_deleted", 0)
            ->where('t1.is_auction', 1);

        // ---------------- FILTERS ----------------

        if (!empty($params['status'])) {
            $pjBookingModel->where('t1.status', $params['status']);
        }

        if (!empty($params['date'])) {
            $pjBookingModel->where("DATE(t1.booking_date)", $params['date']);
        }

        if (!empty($params['start_date'])) {
            $pjBookingModel->where("DATE(t1.booking_date) >=", $params['start_date']);
        }

        if (!empty($params['end_date'])) {
            $pjBookingModel->where("DATE(t1.booking_date) <=", $params['end_date']);
        }

        // ---------------- PAGINATION ----------------

        $rowCount = isset($params['rowCount']) ? (int)$params['rowCount'] : 20;
        $page = isset($params['page']) ? (int)$params['page'] : 1;

        $total = $pjBookingModel->findCount()->getData();
        $pages = ceil($total / $rowCount);
        $offset = ($page - 1) * $rowCount;

        // ---------------- FETCH BOOKINGS ----------------

        $data = $pjBookingModel
            ->select("
                t1.*,
                t2.content AS fleet,
                t4.name,
                t4.email,
                t4.phone,
                CONCAT_WS(' ', t5.first_name, t5.last_name) AS driver_name
            ")
            ->join('pjDriver', "t1.driver_id=t5.id", 'left')
            ->limit($rowCount, $offset)
            ->orderBy("t1.created DESC")
            ->findAll()
            ->getData();

        // ---------------- FORMAT DATA ----------------

        foreach ($data as $k => $v) {

            $fullName = trim($v['c_fname'] . ' ' . $v['c_lname']);

            $data[$k]['client'] = $fullName ?: $v['name'];

            $data[$k]['distance'] = (int)$v['distance'] . ' km';
        }

        // ---------------- RESPONSE ----------------

        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'data' => $data,
            'total' => $total,
            'pages' => $pages,
            'page' => $page
        ]);

        exit;
    }

    public function pjActionDashboard()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();
        $token = $params['api_login_token'] ?? '';

        if (empty($token)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'API token required'
            ]);
            exit;
        }

        // ---------------- FIND SUPPLIER ----------------
        $user = pjAuthUserModel::factory()
            ->select("t1.*")
            ->where('t1.api_login_token', $token)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($user)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'Invalid API token'
            ]);
            exit;
        }

        $supplier = $user[0];
        $supplier_id = $supplier['id'];

        $now = date('Y-m-d H:i:s');

        // ---------------- TOTAL RIDES (active auctions) ----------------
        $total_rides = pjBookingModel::factory()
            ->join('taxi_auctions', "taxi_auctions.booking_id = t1.id AND taxi_auctions.status = 'active'", 'inner')
            ->where('t1.is_auction', 1)
            ->where('t1.is_deleted', 0)
            ->findCount()
            ->getData();

        // ---------------- COMPLETED RIDES ----------------
        $completed_rides = pjBookingModel::factory()
            ->join('taxi_auctions', "taxi_auctions.booking_id = t1.id AND taxi_auctions.status = 'ended' AND taxi_auctions.supplier_id = $supplier_id", 'inner')
            ->where('t1.is_auction', 1)
            ->where('t1.is_deleted', 0)
            ->where('t1.status', 'completed')
            ->findCount()
            ->getData();

        // ---------------- TOTAL REVENUE ----------------
        $revenue_row = pjBookingModel::factory()
            ->join('taxi_auctions', "taxi_auctions.booking_id = t1.id AND taxi_auctions.status = 'ended' AND taxi_auctions.supplier_id = $supplier_id", 'inner')
            ->where('t1.is_auction', 1)
            ->where('t1.is_deleted', 0)
            ->where('t1.status', 'completed')
            ->select("ROUND(SUM(t1.total),2) AS total_revenue")
            ->findAll()
            ->getData();

        $total_revenue = $revenue_row[0]['total_revenue'] ?? 0;

        // ---------------- UPCOMING RIDES ----------------
        $upcoming_rides = pjBookingModel::factory()
            ->join('taxi_auctions', "taxi_auctions.booking_id = t1.id AND taxi_auctions.status = 'ended' AND taxi_auctions.supplier_id = $supplier_id", 'inner')
            ->where('t1.is_auction', 1)
            ->where('t1.is_deleted', 0)
            ->where('t1.status <>', 'cancelled')
            // ->where('t1.booking_date >', $now)
            ->findCount()
            ->getData();

        // ---------------- RESPONSE ----------------
        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'data' => [
                'total_rides'     => (int)$total_rides,
                'completed_rides' => (int)$completed_rides,
                'total_revenue'   => $total_revenue,
                'upcoming_rides'  => (int)$upcoming_rides
            ]
        ]);
        exit;
    }

    public function pjActionAcceptAuctionBooking()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();
        $token = $params['api_login_token'] ?? '';
        $booking_id = isset($params['booking_id']) ? (int)$params['booking_id'] : 0;

        if (empty($token)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'API token required'
            ]);
            exit;
        }

        if ($booking_id <= 0) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 402,
                'message' => 'Invalid booking id'
            ]);
            exit;
        }

        /* ================= FIND SUPPLIER ================= */

        $user = pjAuthUserModel::factory()
            ->select("t1.*")
            ->where('t1.api_login_token', $token)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($user)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 403,
                'message' => 'Invalid API token'
            ]);
            exit;
        }

        $supplier_id = $user[0]['id'];

        /* ================= CHECK BOOKING ================= */

        $booking = pjBookingModel::factory()
            ->find($booking_id)
            ->getData();

        if (!$booking) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 404,
                'message' => 'Booking not found'
            ]);
            exit;
        }

        if ($booking['is_auction'] != 1) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 405,
                'message' => 'Booking is not in auction'
            ]);
            exit;
        }

        /* ================= CHECK AUCTION ================= */

        $auction = pjAuctionModel::factory()
            ->where('booking_id', $booking_id)
            ->where('status', 'active')
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($auction)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 406,
                'message' => 'Auction already ended or not available'
            ]);
            exit;
        }

        $auction_id = $auction[0]['id'];

        /* ================= UPDATE AUCTION ================= */

        pjAuctionModel::factory()
            ->where('id', $auction_id)
            ->modifyAll([
                'supplier_id' => $supplier_id,
                'status' => 'ended',
                'accepted_on' => date('Y-m-d H:i:s')
            ]);

        /* ================= UPDATE BOOKING ================= */

        pjBookingModel::factory()
            ->where('id', $booking_id)
            ->modifyAll([
                'supplier_id' => $supplier_id
            ]);

        /* ================= RESPONSE ================= */

        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'message' => 'Booking accepted successfully'
        ]);

        exit;
    }
    
    public function pjActionGetCategories()
    {
        header("Content-Type: application/json");
        
        $pjCategoryModel = pjCategoryModel::factory();

        $categories_arr = $pjCategoryModel
		->join('pjMultiLang', "t2.model='pjCategory' AND t2.foreign_id=t1.id AND t2.field='category' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		->select("t1.id, t2.content as category")
		->findAll()->getData();

        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'data' => $categories_arr
        ]);
        exit;
    }

    public function pjActionUpdateProfile()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        // 🔹 Validate client_id
        if (!isset($params['client_id']) || empty($params['client_id'])) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 400,
                'message' => 'client_id is required.'
            ]);
            exit;
        }

        $client_id = (int)$params['client_id'];

        $pjClientModel = pjClientModel::factory();

        // 🔹 Check client exists
        $client = $pjClientModel
            ->where('id', $client_id)
            ->limit(1)
            ->findAll()
            ->getData();

        if (empty($client)) {
            echo json_encode([
                'status'  => 'ERR',
                'code'    => 404,
                'message' => 'Client not found.'
            ]);
            exit;
        }

        // 🔹 Email duplicate check
        if (isset($params['email']) && !empty($params['email'])) {
            $exists = $pjClientModel
                ->reset()
                ->where('email', $params['email'])
                ->where('id !=', $client_id)
                ->findCount()
                ->getData();

            if ($exists > 0) {
                echo json_encode([
                    'status'  => 'ERR',
                    'code'    => 409,
                    'message' => 'Email already exists.'
                ]);
                exit;
            }
        }

        // 🔹 Update profile
        $update_data = [];
        if (isset($params['client_name'])) $update_data['client_name'] = $params['client_name'];
        if (isset($params['email']))       $update_data['email']       = $params['email'];
        if (isset($params['phone']))       $update_data['phone']       = $params['phone'];

        if (!empty($update_data)) {
            $pjClientModel->reset()->set('id', $client_id)->modify($update_data);
        }

        /* ================= DEFAULT ADDRESS ================= */

        $pjAddressModel = pjAddressModel::factory();

        $defaultAddress = $pjAddressModel
            ->where('client_id', $client_id)
            ->where('is_default_shipping', 1)
            ->limit(1)
            ->findAll()
            ->getData();

        $address_data = [
            'client_id'  => $client_id,
            'country_id' => $params['country_id'] ?? null,
            'state'      => $params['state'] ?? null,
            'city'       => $params['city'] ?? null,
            'zip'        => $params['zip'] ?? null,
            'address_1'  => $params['address_1'] ?? null,
            'address_2'  => $params['address_2'] ?? null,
            'name'       => $params['address_name'] ?? 'Default Address',
            'is_default_shipping' => 1,
            'is_default_billing'  => 1
        ];

        if (!empty($defaultAddress)) {
            $pjAddressModel->reset()->set('id', $defaultAddress[0]['id'])->modify($address_data);
        } else {
            $pjAddressModel->reset()->setAttributes($address_data)->insert();
        }

        /* ================= FETCH UPDATED DATA ================= */

        $updated_client = $pjClientModel
            ->reset()
            ->where('id', $client_id)
            ->limit(1)
            ->findAll()
            ->getData();
        $locale_id = $this->getLocaleId();

        $updated_address = $pjAddressModel
            ->reset()
            ->join('pjBaseCountry', 't2.id = t1.country_id', 'left')
            ->join(
                'pjBaseMultiLang',
                "t3.model='pjBaseCountry' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='$locale_id'",
                'left'
            )
            ->select("
                t1.*,
                t3.content AS country_name
            ")
            ->where('t1.client_id', $client_id)
            ->where('t1.is_default_shipping', 1)
            ->limit(1)
            ->findAll()
            ->getData();


        echo json_encode([
            'status'  => 'OK',
            'code'    => 200,
            'message' => 'Profile updated successfully.',
            'data' => [
                'client'  => $updated_client[0] ?? null,
                'address' => $updated_address[0] ?? null
            ]
        ]);

        exit;
    }

    public function pjActionAddDriver()
    {

        header("Content-Type: application/json");

        $params = $this->_post->raw();
        $token = $params['api_login_token'] ?? '';

        if (empty($token)) {
            echo json_encode(['status'=>'ERR','message'=>'API token required']);
            exit;
        }

        /* ===============================
        GET SUPPLIER
        =============================== */

        $supplier = pjAuthUserModel::factory()
            ->where('api_login_token', $token)
            ->where('role_id', 5)
            ->findAll()
            ->getDataIndex(0);

        if (empty($supplier)) {
            echo json_encode(['status'=>'ERR','message'=>'Invalid supplier token']);
            exit;
        }

        $supplier_id = $supplier['id'];
        $post = $params;

        /* ===============================
        DUPLICATE DRIVER CHECK
        =============================== */

        $exists = pjAuthUserModel::factory()
            ->where('role_id',4)
            ->where('email',$post['email'] ?? '')
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if ($exists) {
            echo json_encode(['status'=>'ERR','message'=>'Driver email already exists']);
            exit;
        }

        $status = (!empty($post['status']) && $post['status']=='T') ? 'T' : 'F';

        /* ===============================
        CREATE AUTH USER
        =============================== */

        $u_data = [
            'is_active' => 'T',
            'role_id'   => 4,
            'email'     => $post['email'] ?? null,
            'password'  => $post['password'] ?? pjAuth::generatePassword($this->option_arr),
            'name'      => trim(($post['first_name'] ?? '').' '.($post['last_name'] ?? '')),
            'phone'     => $post['phone'] ?? null,
            'status'    => $status,
            'ip'        => pjUtil::getClientIp()
        ];

        $auth_id = pjAuthUserModel::factory($u_data)->insert()->getInsertId();

        if (!$auth_id) {
            echo json_encode(['status'=>'ERR','message'=>'Driver user creation failed']);
            exit;
        }

        /* ===============================
        INSERT DRIVER
        =============================== */

        $data = [
            'auth_id' => $auth_id,
            'supplier_id' => $supplier_id,
            'title' => $post['title'] ?? null,
            'first_name' => $post['first_name'] ?? null,
            'last_name' => $post['last_name'] ?? null,
            'email' => $post['email'] ?? null,
            'password' => $post['password'] ?? null,
            'phone' => $post['phone'] ?? null,
            'address' => $post['address'] ?? null,
            'city' => $post['city'] ?? null,
            'zip' => $post['zip'] ?? null,
            'state' => $post['state'] ?? null,
            'license_number' => $post['license_number'] ?? null,
            'license_expiry' => $post['license_expiry'] ?? null,
            'dob' => $post['dob'] ?? null,
            'national_id_number' => $post['national_id_number'] ?? null,
            'vehicle_id' => $post['vehicle_id'] ?? null,
            'notes' => $post['notes'] ?? null,
            'status' => $status
        ];

        $driver_id = pjDriverModel::factory()
            ->setAttributes($data)
            ->insert()
            ->getInsertId();

        if (!$driver_id) {
            echo json_encode(['status'=>'ERR','message'=>'Driver creation failed']);
            exit;
        }

        /* ===============================
        LICENSE UPLOAD
        =============================== */

        if (!empty($_FILES['license_file']['name'])) {

            $uploadDir = PJ_UPLOAD_PATH.'license/';
            $thumbDir  = $uploadDir.'thumb/';

            if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
            if (!is_dir($thumbDir)) mkdir($thumbDir,0777,true);

            $name = $_FILES['license_file']['name'];
            $tmpName = $_FILES['license_file']['tmp_name'];
            $size = $_FILES['license_file']['size'];
            $type = $_FILES['license_file']['type'];

            if (is_uploaded_file($tmpName)) {

                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $firstName = preg_replace('/[^a-z0-9_]/i','',strtolower($post['first_name'] ?? 'driver'));

                $uniqueName = $firstName.'_license_'.uniqid().'.'.$ext;
                $destPath = $uploadDir.$uniqueName;

                if (move_uploaded_file($tmpName,$destPath)) {

                    $thumbRelPath = null;

                    if (in_array($ext,['jpg','jpeg','png']) && function_exists('imagecreatefromjpeg')) {

                        $thumbName = 'thumb_'.$uniqueName;
                        $thumbPathFull = $thumbDir.$thumbName;
                        $thumbRelPath = 'license/thumb/'.$thumbName;

                        switch ($ext) {
                            case 'jpg':
                            case 'jpeg':
                                $sourceImage = imagecreatefromjpeg($destPath);
                            break;

                            case 'png':
                                $sourceImage = imagecreatefrompng($destPath);
                            break;

                            default:
                                $sourceImage = null;
                        }

                        if ($sourceImage) {

                            $width  = imagesx($sourceImage);
                            $height = imagesy($sourceImage);

                            $newWidth = 200;
                            $newHeight = intval($height * ($newWidth/$width));

                            $thumb = imagecreatetruecolor($newWidth,$newHeight);

                            imagecopyresampled(
                                $thumb,$sourceImage,
                                0,0,0,0,
                                $newWidth,$newHeight,
                                $width,$height
                            );

                            ($ext=='png')
                                ? imagepng($thumb,$thumbPathFull)
                                : imagejpeg($thumb,$thumbPathFull);

                            imagedestroy($thumb);
                            imagedestroy($sourceImage);
                        }
                    }

                    pjDriverFileModel::factory()->setAttributes([
                        'driver_id'=>$driver_id,
                        'file_name'=>$uniqueName,
                        'original_name'=>$name,
                        'file_type'=>$type,
                        'file_size'=>$size,
                        'file_category'=>'license',
                        'created'=>date("Y-m-d H:i:s"),
                        'source_path'=>'license/'.$uniqueName,
                        'thumb_path'=>$thumbRelPath
                    ])->insert();

                    pjDriverModel::factory()
                        ->where('id',$driver_id)
                        ->limit(1)
                        ->modifyAll(['license_file'=>$uniqueName]);
                }
            }
        }

        /* ===============================
        DRIVER DOCUMENTS
        =============================== */

        if (!empty($_FILES['driver_documents']['name'][0])) {

            $uploadDir = PJ_UPLOAD_PATH.'driver_documents/';
            if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

            foreach ($_FILES['driver_documents']['name'] as $index=>$name) {

                $tmpName = $_FILES['driver_documents']['tmp_name'][$index];
                $size    = $_FILES['driver_documents']['size'][$index];
                $type    = $_FILES['driver_documents']['type'][$index];

                if (is_uploaded_file($tmpName)) {

                    $category='additional';
                    $lcName=strtolower($name);

                    if (strpos($lcName,'id')!==false) $category='id';
                    elseif (strpos($lcName,'photo')!==false || strpos($lcName,'image')!==false) $category='photo';

                    $ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
                    $doc_prefix=preg_replace('/[^a-z0-9_]/i','',strtolower($post['first_name'] ?? 'driver'));

                    $uniqueName=uniqid($doc_prefix.'_').'.'.$ext;
                    $destPath=$uploadDir.$uniqueName;

                    if (move_uploaded_file($tmpName,$destPath)) {

                        pjDriverFileModel::factory()->setAttributes([
                            'driver_id'=>$driver_id,
                            'file_name'=>$uniqueName,
                            'original_name'=>$name,
                            'file_type'=>$type,
                            'file_size'=>$size,
                            'file_category'=>$category,
                            'created'=>date("Y-m-d H:i:s"),
                            'source_path'=>'driver_documents/'.$uniqueName,
                            'thumb_path'=>null
                        ])->insert();
                    }
                }
            }
        }

        echo json_encode([
            'status'=>'OK',
            'driver_id'=>$driver_id,
            'message'=>'Driver created successfully'
        ]);
        exit;
    }

    public function pjActionUpdateDriver()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();
        $token  = $params['api_login_token'] ?? '';

        if (empty($token)) {
            echo json_encode(['status'=>'ERR','message'=>'API token required']);
            exit;
        }

        /* ===============================
        GET SUPPLIER
        =============================== */

        $supplier = pjAuthUserModel::factory()
            ->where('api_login_token', $token)
            ->where('role_id', 5)
            ->findAll()
            ->getDataIndex(0);

        if (!$supplier) {
            echo json_encode(['status'=>'ERR','message'=>'Invalid supplier token']);
            exit;
        }

        $supplier_id = $supplier['id'];

        $driver_id = $params['driver_id'] ?? 0;

        if (!$driver_id) {
            echo json_encode(['status'=>'ERR','message'=>'Driver ID required']);
            exit;
        }

        /* ===============================
        VERIFY DRIVER BELONGS TO SUPPLIER
        =============================== */

        $driver = pjDriverModel::factory()
            ->where('id',$driver_id)
            ->where('supplier_id',$supplier_id)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (!$driver) {
            echo json_encode(['status'=>'ERR','message'=>'Driver not found']);
            exit;
        }

        $post = $params;

        /* ===============================
        UPDATE DRIVER FIELDS
        =============================== */

        $data = [];

        if(isset($post['address'])) $data['address'] = $post['address'];
        if(isset($post['city'])) $data['city'] = $post['city'];
        if(isset($post['state'])) $data['state'] = $post['state'];
        if(isset($post['zip'])) $data['zip'] = $post['zip'];

        if(isset($post['license_number']))
            $data['license_number'] = $post['license_number'];

        if(isset($post['license_expiry']))
            $data['license_expiry'] = $post['license_expiry'];

        if(isset($post['vehicle_id']))
            $data['vehicle_id'] = $post['vehicle_id'];

        if(isset($post['national_id_number']))
            $data['national_id_number'] = $post['national_id_number'];

        if(isset($post['notes']))
            $data['notes'] = $post['notes'];

        if(!empty($data)){
            pjDriverModel::factory()
                ->where('id',$driver_id)
                ->limit(1)
                ->modifyAll($data);
        }

        /* ===============================
        LICENSE FILE UPLOAD
        =============================== */

        if (!empty($_FILES['license_file']['name'])) {

            $uploadDir = PJ_UPLOAD_PATH.'license/';
            $thumbDir  = $uploadDir.'thumb/';

            if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
            if (!is_dir($thumbDir)) mkdir($thumbDir,0777,true);

            /* DELETE OLD LICENSE */

            $oldFiles = pjDriverFileModel::factory()
                ->where('driver_id',$driver_id)
                ->where('file_category','license')
                ->findAll()
                ->getData();

            foreach ($oldFiles as $file) {

                $filePath  = PJ_UPLOAD_PATH.$file['source_path'];
                $thumbPath = !empty($file['thumb_path'])
                    ? PJ_UPLOAD_PATH.$file['thumb_path']
                    : null;

                if (is_file($filePath)) unlink($filePath);
                if ($thumbPath && is_file($thumbPath)) unlink($thumbPath);
            }

            pjDriverFileModel::factory()
                ->where('driver_id',$driver_id)
                ->where('file_category','license')
                ->eraseAll();

            /* UPLOAD NEW LICENSE */

            $name    = $_FILES['license_file']['name'];
            $tmpName = $_FILES['license_file']['tmp_name'];
            $size    = $_FILES['license_file']['size'];
            $type    = $_FILES['license_file']['type'];

            if (is_uploaded_file($tmpName)) {

                $ext = pathinfo($name, PATHINFO_EXTENSION);

                $firstName = preg_replace('/[^a-z0-9_]/i','',strtolower($driver['first_name']));

                $uniqueName = $firstName.'_license_'.uniqid().'.'.$ext;

                $destPath = $uploadDir.$uniqueName;

                if (move_uploaded_file($tmpName,$destPath)) {

                    /* CREATE THUMBNAIL */

                    $thumbRelPath = null;

                    if (in_array(strtolower($ext),['jpg','jpeg','png'])) {

                        $thumbName = 'thumb_'.$uniqueName;
                        $thumbFull = $thumbDir.$thumbName;
                        $thumbRelPath = 'license/thumb/'.$thumbName;

                        switch(strtolower($ext)){
                            case 'jpg':
                            case 'jpeg':
                                $source = imagecreatefromjpeg($destPath);
                            break;

                            case 'png':
                                $source = imagecreatefrompng($destPath);
                            break;
                        }

                        if($source){

                            $width  = imagesx($source);
                            $height = imagesy($source);

                            $newWidth  = 200;
                            $newHeight = intval($height*($newWidth/$width));

                            $thumb = imagecreatetruecolor($newWidth,$newHeight);

                            imagecopyresampled(
                                $thumb,$source,
                                0,0,0,0,
                                $newWidth,$newHeight,
                                $width,$height
                            );

                            if($ext=='png')
                                imagepng($thumb,$thumbFull);
                            else
                                imagejpeg($thumb,$thumbFull);

                            imagedestroy($thumb);
                            imagedestroy($source);
                        }
                    }

                    pjDriverFileModel::factory()->setAttributes([
                        'driver_id'=>$driver_id,
                        'file_name'=>$uniqueName,
                        'original_name'=>$name,
                        'file_type'=>$type,
                        'file_size'=>$size,
                        'file_category'=>'license',
                        'created'=>date("Y-m-d H:i:s"),
                        'source_path'=>'license/'.$uniqueName,
                        'thumb_path'=>$thumbRelPath
                    ])->insert();

                    pjDriverModel::factory()
                        ->where('id',$driver_id)
                        ->limit(1)
                        ->modifyAll(['license_file'=>$uniqueName]);
                }
            }
        }

        /* ===============================
        DRIVER DOCUMENTS UPLOAD
        =============================== */

        if (!empty($_FILES['driver_documents']['name'][0])) {

            $uploadDir = PJ_UPLOAD_PATH.'driver_documents/';

            if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

            /* DELETE OLD DOCUMENTS */

            $oldFiles = pjDriverFileModel::factory()
                ->where('driver_id',$driver_id)
                ->whereIn('file_category',['photo','id','additional'])
                ->findAll()
                ->getData();

            foreach ($oldFiles as $oldFile) {

                $fullPath = PJ_UPLOAD_PATH.$oldFile['source_path'];

                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            pjDriverFileModel::factory()
                ->where('driver_id',$driver_id)
                ->whereIn('file_category',['photo','id','additional'])
                ->eraseAll();

            /* UPLOAD NEW FILES */

            foreach ($_FILES['driver_documents']['name'] as $index=>$name) {

                $tmpName = $_FILES['driver_documents']['tmp_name'][$index];
                $size    = $_FILES['driver_documents']['size'][$index];
                $type    = $_FILES['driver_documents']['type'][$index];

                if (is_uploaded_file($tmpName)) {

                    $category='additional';

                    $lcName=strtolower($name);

                    if(strpos($lcName,'id')!==false)
                        $category='id';
                    elseif(strpos($lcName,'photo')!==false || strpos($lcName,'image')!==false)
                        $category='photo';

                    $ext=pathinfo($name,PATHINFO_EXTENSION);

                    $doc_prefix=preg_replace('/[^a-z0-9_]/i','',strtolower($driver['first_name']));

                    $uniqueName=uniqid($doc_prefix.'_').'.'.$ext;

                    $destPath=$uploadDir.$uniqueName;

                    if(move_uploaded_file($tmpName,$destPath)){

                        pjDriverFileModel::factory()->setAttributes([
                            'driver_id'=>$driver_id,
                            'file_name'=>$uniqueName,
                            'original_name'=>$name,
                            'file_type'=>$type,
                            'file_size'=>$size,
                            'file_category'=>$category,
                            'created'=>date("Y-m-d H:i:s"),
                            'source_path'=>'driver_documents/'.$uniqueName,
                            'thumb_path'=>null
                        ])->insert();
                    }
                }
            }
        }

        echo json_encode([
            'status'=>'OK',
            'driver_id'=>$driver_id,
            'message'=>'Driver updated successfully'
        ]);

        exit;
    }

    public function pjActionDriversList()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();
        $token = $params['api_login_token'] ?? '';

        if (empty($token)) {
            echo json_encode([
                'status' => 'ERR',
                'message' => 'API token required'
            ]);
            exit;
        }

        // Get supplier using token
        $supplier = pjAuthUserModel::factory()
            ->where('api_login_token', $token)
            ->where('role_id', 5)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (empty($supplier)) {
            echo json_encode([
                'status' => 'ERR',
                'message' => 'Invalid supplier token'
            ]);
            exit;
        }

        $supplier_id = $supplier['id'];

        // Fetch drivers belonging to this supplier
        $drivers = pjDriverModel::factory()
            ->where('supplier_id', $supplier_id)
            ->orderBy('t1.id DESC')
            ->findAll()
            ->getData();

        echo json_encode([
            'status' => 'OK',
            'supplier_id' => $supplier_id,
            'total_drivers' => count($drivers),
            'data' => $drivers
        ]);
        exit;
    }

    public function pjActionAssignDriverToBooking()
    {
        header("Content-Type: application/json");

        $params = $this->_post->raw();

        $token = $params['api_login_token'] ?? '';
        $booking_id = $params['booking_id'] ?? '';
        $driver_id = $params['driver_id'] ?? '';

        if (empty($token) || empty($booking_id) || empty($driver_id)) {
            echo json_encode([
                'status' => 'ERR',
                'message' => 'api_login_token, booking_id and driver_id are required'
            ]);
            exit;
        }

        // Get supplier using token
        $supplier = pjAuthUserModel::factory()
            ->where('api_login_token', $token)
            ->where('role_id', 5)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (empty($supplier)) {
            echo json_encode([
                'status' => 'ERR',
                'message' => 'Invalid supplier token'
            ]);
            exit;
        }

        $supplier_id = $supplier['id'];

        // Check driver belongs to supplier
        $driver = pjDriverModel::factory()
            ->where('id', $driver_id)
            ->where('supplier_id', $supplier_id)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (empty($driver)) {
            echo json_encode([
                'status' => 'ERR',
                'message' => 'Driver not found or not assigned to this supplier'
            ]);
            exit;
        }

        // Check booking exists
        $booking = pjBookingModel::factory()
            ->where('id', $booking_id)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (empty($booking)) {
            echo json_encode([
                'status' => 'ERR',
                'message' => 'Booking not found'
            ]);
            exit;
        }

        // Assign driver
        pjBookingModel::factory()
            ->where('id', $booking_id)
            ->limit(1)
            ->modifyAll([
                'driver_id' => $driver_id
            ]);
        
        $notificationModel = pjNotificationModel::factory()
            ->where('recipient', 'client')
            ->where('transport', 'email')
            ->where('variant', 'driverassingconfirmation');
        
        $notification = $notificationModel
            ->findAll()
            ->getDataIndex(0);
        
        if ((int)$notification['id'] > 0 && $notification['is_active'] == 1) {

            $resp = pjAppController::pjActionGetSubjectMessage($notification, $this->getLocaleId(), $this->getForeignId());

            $lang_message = $resp['lang_message'];
            $lang_subject = $resp['lang_subject'];

            if (count($lang_message) === 1 && count($lang_subject) === 1) {

                $booking_arr = pjBookingModel::factory()->reset()->find($booking_id)->getData();
                $driver_arr = pjDriverModel::factory()->reset()->find($driver_id)->getData();

                $search  = ['{FirstName}','{LastName}','{UniqueID}','{DriverLastName}','{DriverPhone}'];
                $replace = [
                    $booking_arr['c_fname'],
                    $booking_arr['c_lname'],
                    $booking_arr['uuid'],
                    $driver_arr['last_name'],
                    $driver_arr['phone'],
                ];

                $subject_client = str_replace($search, $replace, $lang_subject[0]['content']);
                $message_client = str_replace($search, $replace, $lang_message[0]['content']);

                $Email = self::getMailer($this->option_arr);
                $Email->setTo($arr['email'])
                    ->setSubject($subject_client)
                    ->send(pjUtil::textToHtml($message_client));
            }
        }

        echo json_encode([
            'status' => 'OK',
            'message' => 'Driver assigned successfully',
            'booking_id' => $booking_id,
            'driver_id' => $driver_id
        ]);
        exit;
    }
    
    public function pjActionGetCompletedRides()
    {
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
        header("Content-Type: application/json");

        $params = $this->_post->raw();
        $token = $params['api_login_token'] ?? '';

        if (empty($token)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'API token required'
            ]);
            exit;
        }

        // ---------------- GET SUPPLIER ----------------
        $supplier = pjAuthUserModel::factory()
            ->where('api_login_token', $token)
            ->where('role_id', 5)
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);

        if (empty($supplier)) {
            echo json_encode([
                'status' => 'ERR',
                'code' => 401,
                'message' => 'Invalid supplier token'
            ]);
            exit;
        }

        $supplier_id = $supplier['id'];

        // ---------------- BOOKING QUERY ----------------
        $pjBookingModel = pjBookingModel::factory()
            ->join('pjDriver', "t2.id = t1.driver_id", 'left')
            ->select("t1.*, CONCAT_WS(' ', t2.first_name, t2.last_name) AS driver_name")
            ->where('t1.supplier_id', $supplier_id)   // Only bookings for this supplier
            ->where("t1.is_deleted", 0)
            ->where('t1.status', 'completed');

        // ---------------- FILTERS ----------------
        if (!empty($params['start_date'])) {
            $pjBookingModel->where("DATE(t1.booking_date) >=", $params['start_date']);
        }

        if (!empty($params['end_date'])) {
            $pjBookingModel->where("DATE(t1.booking_date) <=", $params['end_date']);
        }

        if (!empty($params['driver_id'])) {
            $pjBookingModel->where('t1.driver_id', $params['driver_id']);
        }

        // ---------------- PAGINATION ----------------
        $rowCount = isset($params['rowCount']) ? (int) $params['rowCount'] : 20;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        $total = $pjBookingModel->findCount()->getData();
        $pages = ceil($total / $rowCount);
        $offset = ($page - 1) * $rowCount;

        // ---------------- FETCH RIDES ----------------
        $rides = $pjBookingModel
            ->limit($rowCount, $offset)
            ->orderBy('t1.booking_date DESC')
            ->findAll()
            ->getData();

        // ---------------- RESPONSE ----------------
        echo json_encode([
            'status' => 'OK',
            'code' => 200,
            'total' => $total,
            'pages' => $pages,
            'page' => $page,
            'data' => $rides
        ]);

        exit;
    }
}
