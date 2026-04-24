<?php
$titles = __("error_titles", true);
$bodies = __("error_bodies", true);
$today = pjDateTime::formatDate(
    date("Y-m-d"),
    "Y-m-d",
    $tpl["option_arr"]["o_date_format"]
);
$months = __("months", true);
ksort($months);
$short_days = __("short_days", true);
$bs = __("booking_statuses", true);
$get = $controller->_get->raw();
$set = isset($get["group"]) && !empty($get["group"]) ? $get["group"] : "daily";
$auth = pjAuth::factory();
$roleId = $auth->getRoleId();
?>
<div id="datePickerOptions" style="display:none;" 
		data-wstart="<?php echo (int) $tpl["option_arr"]["o_week_start"]; ?>" 
		data-format="<?php echo $tpl["date_format"]; ?>" 
		data-months="<?php echo implode("_", $months); ?>" 
		data-days="<?php echo implode("_", $short_days); ?>">
	</div>
<!-- ================= FILTERS ================= -->
<form method="get" action="" class="form-horizontal supplier-frm-filter" 
      style="display:flex; flex-wrap:wrap; gap:20px; margin:20px 0;">

    <input type="hidden" name="group" value="<?= htmlspecialchars($set ?? '') ?>">

    <div class="input-group">
        <input type="text" name="from_date" id="from_date" class="form-control datetimepick_from required" value="<?php echo isset($ge["from_date"]) ? htmlspecialchars($get["from_date"]) : $tpl["filter_from"]; ?>" data-wt="open" readonly="readonly"placeholder="From" data-msg-required="<?php __('tr_field_required'); ?>">
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
    </div>
    
    <div class="input-group">
        <input type="text" name="to_date" id="to_date" class="form-control datetimepick_to required" value="<?php echo isset($get["to_date"]) ? htmlspecialchars($get["to_date"]): $tpl["filter_to"]; ?>" data-wt="open" readonly="readonly"  placeholder="To" data-msg-required="<?php __('tr_field_required');?>" >
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
    </div>

    <!-- BOOKING STATUS -->
    <div style="flex: 0 0 200px;">
        <select name="booking_status" class="form-control">
            <option value="">-- All Status --</option>
            <?php foreach ($bs as $k => $v) { ?>
                <option value="<?= $k; ?>"
                    <?= isset($get['booking_status']) && $get['booking_status'] === $k ? 'selected' : '' ?>>
                    <?= pjSanitize::html($v); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <!-- CITY -->
    <div style="flex: 0 0 200px;">
        <select name="city" class="form-control">
            <option value="">-- City --</option>
            <?php foreach($tpl['cities'] as $city){ ?>
                <option value="<?= pjSanitize::html($city['name']) ?>"
                    <?= isset($get['city']) && $get['city'] == $city['name'] ? 'selected' : '' ?>>
                    <?= pjSanitize::html($city['name']) ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <!-- SUBMIT -->
    <div>
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-search"></i>
        </button>
    </div>
</form>
<div id="dashboardContent">
    <div class="kpi-row">

        <!-- TOTAL BOOKINGS -->
        <div class="kpi-col">
            <div class="kpi-card kpi-total-rides">
                <div class="kpi-top">
                    <i class="fa fa-taxi"></i>
					<h4><?php __('plugin_base_menu_available_rides');?></h4>
                </div>
                <h2><?php echo $tpl['avail_rides']; ?></h2>
            </div>
        </div>

        <!-- TOTAL upcomming -->
        <div class="kpi-col">
            <div class="kpi-card kpi-total-rides">
                <div class="kpi-top">
					<i class="fa fa-calendar"></i>
					<h4><?php __('plugin_base_menu_upcoming_rides');?></h4>
                </div>
                <h2><?php echo $tpl['upcoming_rides']; ?></h2>
            </div>
        </div>
        <!-- TOTAL Completed -->
        <div class="kpi-col">
            <div class="kpi-card kpi-total-rides">
                <div class="kpi-top">
                    <i class="fa fa-check-circle"></i>
					<h4><?php __('plugin_base_lbl_completed_rides');?></h4>
                </div>
                <h2><?php echo $tpl['completed_rides']; ?></h2>
            </div>
        </div>

        <!-- TOTAL EARNINGS -->
        <div class="kpi-col">
            <div class="kpi-card kpi-earnings">
                <div class="kpi-top">
                    <i class="fa fa-money"></i>
                    <h4>Total Earnings</h4>
                </div>
                <h2><?php echo pjCurrency::formatPrice($tpl['supplier_earning'] ?? 0); ?>
</h2>
            </div>
        </div>

        <!-- DRIVERS -->
        <div class="kpi-col">
            <div class="kpi-card kpi-total-drivers">
                <div class="kpi-top">
                    <i class="fa fa-users"></i>
                    <h4>Total Drivers</h4>
                </div>
                <h2><?php echo $tpl['total_drivers']; ?></h2>
            </div>
        </div>

    </div>

    <div class="row m-t-lg">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h4><?php __("supplier_dash_rides_overview"); ?></h4>
                </div>
                <div class="ibox-content">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h4><?php __("supplier_dash_trend_analysis"); ?></h4>
                </div>
                <div class="ibox-content">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h4><?php __("supplier_dash_earning_overview"); ?></h4>
                </div>
                <div class="ibox-content">
                    <canvas id="earningChart"></canvas>
                </div>
            </div>
        </div>

    </div>
    
</div>
<style>
    .ibox {
        background: #fff;
        border: 1px solid #e7eaec;
        border-radius: 8px;
        padding: 15px;
    }

    .ibox-title {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .ibox-content {
        position: relative;
        height: 300px;
    }
    .chart-box {
        height: 320px;
        position: relative;
        margin-bottom: 20px;
        margin-top: 20px;
    }
    .kpi-row{
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom:20px
    }

    .kpi-col{
        flex: 1 1 200px;
    }

    .kpi-card{
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: 0.2s;
    }

    .kpi-top {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .kpi-top i {
        font-size: 28px;
    }

    .kpi-card h4 {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: #555;
    }

    /* Colors */
    .kpi-total-rides i { color: #3498db; }
    .kpi-completed-rides i { color: #1abc9c; }
    .kpi-total-drivers i { color: #9b59b6; }
    .kpi-earnings i { color: #2ecc71; }
    .kpi-commission i { color: #e74c3c; }
    .kpi-net i { color: #27ae60; }

</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
const bookingData = <?= json_encode($tpl['chart_booking_counts']); ?>;
const earningData = <?= json_encode($tpl['chart_earnings']); ?>;
const trendData   = <?= json_encode($tpl['chart_trend']); ?>;
</script>