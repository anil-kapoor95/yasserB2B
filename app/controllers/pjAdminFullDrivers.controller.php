<?php

if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}

class pjAdminFullDrivers extends pjAdmin
{
 
public function pjActionDriverCalendarEventsOO()
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
           ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
           ->join('pjDriver', "t1.driver_id=t5.id", 'left');

    }

     $data = $pjBookingModel
           ->select("t1.*, t2.content as fleet, t4.name, t4.email,t4.phone, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
            AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`, AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`, AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`, AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`,CONCAT_WS(' ', t5.first_name, t5.last_name) AS driver_name ")  ->findAll() ->getData();

           
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
        $display_date = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'] , strtotime($v['booking_date']));

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
            "display_date" => $display_date,
            "payment_method" => $v['payment_method'],
            "passengers"     => $v['passengers'],
            "driver_name"    => $v['driver_name'],
            "color"       => $color,

            ];
    }

    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
}

public function pjActionDriverCalendarEvents()
{
    $this->setAjax(false); 

    $auth = pjAuth::factory();
    $roleId = $auth->getRoleId();
    $id = $this->getUserId(); // driver auth ID

    // Get start and end from FullCalendar (YYYY-MM-DD)
    $start = $this->_get->toString('start'); 
    $end   = $this->_get->toString('end');

    $start = str_replace('T', ' ', $start);
    $end   = str_replace('T', ' ', $end);


    if ((int)$roleId === 4) {
        // Driver = only his bookings
        $pjBookingModel = pjBookingModel::factory()
            ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
            ->join('pjClient', "t3.id=t1.client_id", 'left outer')
            ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
            ->join('pjDriver', "t5.id=t1.driver_id", 'left outer')
            ->where("t5.auth_id", $id)
            ->where("t1.is_deleted = 0");
    } else {
        // Admin = all bookings
        $pjBookingModel = pjBookingModel::factory()
            ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
            ->join('pjClient', "t3.id=t1.client_id", 'left outer')
            ->join('pjAuthUser', "t4.id=t3.foreign_id", 'left outer')
            ->join('pjDriver', "t1.driver_id=t5.id", 'left')
            ->where("t1.is_deleted = 0");
    }

    // Filter bookings between the start and end dates
    $pjBookingModel->where("DATE(t1.booking_date) >= '$start' AND DATE(t1.booking_date) <= '$end'");

    $data = $pjBookingModel
        ->select("t1.*, t2.content as fleet, t4.name, t4.email, t4.phone, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
                  AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`, AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
                  AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`, AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`, t5.id AS driver_id, CONCAT_WS(' ', t5.first_name, t5.last_name) AS driver_name ")->findAll()->getData();

    $events = [];

    // Optional: Load extras if needed
    $booking_ids = array_column($data, 'id');
    $extras_model = pjBookingExtraModel::factory()
        ->select("t1.*, t2.content AS extra_name")
        ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
        ->whereIn('t1.booking_id', $booking_ids)
        ->where("t1.extra_value > 0")
        ->findAll()
        ->getData();

    $extras_by_booking = [];
    foreach ($extras_model as $ex) {
        $extras_by_booking[$ex['booking_id']][] = $ex;
    }

    foreach ($data as $v) {
        $color = '#3788d8'; // default blue
        if ($v['status'] == 'confirmed') {
            $color = '#28a745';
        } elseif ($v['status'] == 'pending') {
            $color = '#ffc107';
        } elseif ($v['status'] == 'cancelled') {
            $color = '#dc3545';
        }

        $startIso = date('c', strtotime($v['booking_date']));
        $display_date = date($this->option_arr['o_date_format'] . ', ' . $this->option_arr['o_time_format'], strtotime($v['booking_date']));

        $events[] = [
            "id"             => $v['id'],
            "title"          => pjSanitize::clean($v['name']) . "\n" . $v['fleet'],
            "names"           => $v['name'],
            "cars"           => $v['fleet'],
            "start"          => $startIso,
            "end"            => $startIso,
            "pickup"         => $v['pickup_address'],
            "return"         => $v['return_address'],
            "status"         => $v['status'],
            "display_date"   => $display_date,
            "payment_method" => $v['payment_method'],
            "passengers"     => $v['passengers'],
            "color"          => $color,
            "price"          => $v['total'],
            "driver_name"    => $v['driver_name'] ? $v['driver_name'] : 'NA',
            "extras"         => $extras_by_booking[$v['id']] ?? [],
            "driver_id"      => $v['driver_id'],
            "customername" => trim($v['c_fname'] . ' ' . $v['c_lname']),
            "customerphone" => $v['c_phone'],
            "c_departure_terminal"        => !empty($v['c_departure_terminal']) ? $v['c_departure_terminal'] : '',
            "c_terminal"                  => !empty($v['c_terminal']) ? $v['c_terminal'] : 'NA',
            "c_departure_flight_time"     => !empty($v['c_departure_flight_time']) ? $v['c_departure_flight_time'] : '',
            "c_departure_flight_number"   => !empty($v['c_departure_flight_number']) ? $v['c_departure_flight_number'] : '',
            "c_flight_time"               => !empty($v['c_flight_time']) ? $v['c_flight_time'] : '',
            "c_departure_airline_company" => !empty($v['c_departure_airline_company']) ? $v['c_departure_airline_company'] : '',
            "c_airline_company"           => !empty($v['c_airline_company']) ? $v['c_airline_company'] : '',
            "c_flight_number"             => !empty($v['c_flight_number']) ? $v['c_flight_number'] : '',
           
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
}



}
?>