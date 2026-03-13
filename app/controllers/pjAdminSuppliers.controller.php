<?php

if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjAdminSuppliers extends pjAdmin
{
    public function pjActionIndex()
    {die('kkkkkkk');
        $response = pjAuth::init()->getPermissions();
        echo'<pre>';print_R($response);die;

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
        $this->appendJs("pjAdminSuppliers1.js?v={$version}");
    }
        
}
?>