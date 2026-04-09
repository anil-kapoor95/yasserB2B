<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$bs = __('booking_statuses', true);
$get = $controller->_get->raw();
?>

<style>
	.auction_links{
		text-decoration: underline;
	cursor: pointer;
	color: #0a5114;
	}
	.bg-completed {
		background-color: #8E44AD !important; /* Purple (example) */
		color: #fff !important;
	}

	/* Mobile / iPhone */
	@media (max-width: 768px) {
	.list-view {
		width: 56%;
		float: left;
	}
	.cal-view {
		width: 44%;
		float: left;
	}
	}

</style>
<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo $tpl['date_format']; ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12 bookings-page-heading">
		<div class="row">
			<div class="col-sm-10 list-view">
				<h2><?php __('infoReservationListTitle');?></h2>
			</div>
			<div class="col-sm-2 align-items-end cal-view" style="padding-top: 10px; text-align: right;">
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionDeleted" class="btn btn-primary">
					<i class="fa fa-trash m-r-xs"></i></a> &nbsp;
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&action=pjActionCalendar" class="btn btn-primary">Calendar View</a>
			</div>
			
		</div><!-- /.row -->

		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoReservationListDesc');?></p>
	</div><!-- /.col-md-12 -->
	
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<?php
	$error_code = $controller->_get->toString('err');
	if (!empty($error_code))
	{
		switch (true)
		{
			case in_array($error_code, array('ABB01', 'ABB03')):
				?>
				<div class="alert alert-success">
					<i class="fa fa-check m-r-xs"></i>
					<strong><?php echo @$titles[$error_code]; ?></strong>
					<?php echo @$bodies[$error_code];?>
				</div>
				<?php 
				break;
			case in_array($error_code, array('ABB04', 'ABB08', 'ABB09', 'ABB10')):	
				?>
				<div class="alert alert-danger">
					<i class="fa fa-exclamation-triangle m-r-xs"></i>
					<strong><?php echo @$titles[$error_code]; ?></strong>
					<?php echo @$bodies[$error_code];?>
				</div>
				<?php
				break;
		}
	} 
	?>
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content cardealer-no-border">
					<form action="" method="get" class="form-horizontal frm-filter">
    					<div class="row m-b-md">
    						<div class="col-lg-2 col-md-2 col-sm-12 m-b-sm">
    						<?php 
                            if ($tpl['has_create'])
                            {
                            	?>
    							<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate" class="btn btn-primary"><i class="fa fa-plus m-r-xs"></i> <?php __('btnAddEnquiry'); ?></a>
    							<?php 
                            }
                            ?>
    						</div>
    						<div class="col-md-2 col-sm-5">
    							
								<div class="input-group">
									<input type="text" name="q" placeholder="<?php __('btnSearch', false, true); ?>" class="form-control">
									
								</div>
                            </div>
							
							<div class="col-md-2 col-sm-5">
								<div class="input-group">
									<input type="text" name="from_date" id="from_date" class="form-control datetimepick_from required" data-wt="open" readonly="readonly"placeholder="From" data-msg-required="<?php __('tr_field_required'); ?>">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
								</div>
							</div><!-- /.col-md-2 -->
							
							<div class="col-md-2 col-sm-5">
								<div class="input-group">
									<input type="text" name="to_date" id="to_date" class="form-control datetimepick_to required" data-wt="open" readonly="readonly"  placeholder="To" data-msg-required="<?php __('tr_field_required');?>">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
								</div>
							</div><!-- /.col-md-2 -->

							<div class="col-md-1 col-sm-5">
								<div class="input-group-btn">
									<button class="btn btn-primary" type="submit">
										<i class="fa fa-search"></i>
									</button>
								</div>
							</div>

    						<div class="col-lg-2 col-md-2 col-sm-6 m-b-sm">
    							<select class="form-control pj-filter-status text-center" name="status">
    								<option value="">-- <?php __('lblAll');?> --</option>
    								<?php foreach ($bs as $k => $v) { ?>
    									<option value="<?php echo $k;?>"><?php echo pjSanitize::html($v);?></option>
    								<?php } ?>
    							</select>
    						</div>
    					</div>				
					</form>
					<div id="grid"></div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$commission_type = isset($tpl['option_arr']['o_commission_type']) 
		? $tpl['option_arr']['o_commission_type'] 
		: 'percent';

	$commission_amount = isset($tpl['option_arr']['o_commission_amount']) 
		? $tpl['option_arr']['o_commission_amount'] 
		: '0';
	?>
	<!-- Commission Modal -->
	<div class="modal fade" id="commissionModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Enter Commission</h4>
				</div>

				<div class="modal-body">

					<!-- Commission Type -->
					<div class="form-group">
						<label>Commission Type</label>

						<select id="commissionTypeSelect" class="form-control">

							<option value="percent"
							<?php echo $commission_type == 'percent' ? 'selected' : ''; ?>>
							Percent
							</option>

							<option value="fixed"
							<?php echo $commission_type == 'fixed' ? 'selected' : ''; ?>>
							Fixed
							</option>

						</select>
					</div>

					<!-- Commission Value -->
					<div class="form-group">

						<label id="commissionLabel">
							<?php echo $commission_type == 'percent' ? 'Enter Percentage' : 'Commission Amount'; ?>
						</label>

						<div class="input-group">

							<input
								type="number"
								class="form-control"
								id="commissionInput"
								value="<?php echo $commission_amount; ?>"
								min="0"
								step="0.01"
							>

							<span class="input-group-addon" id="commissionSymbol">
								<?php echo $commission_type == 'percent' ? '%' : '<i class="fa fa-money"></i>'; ?>
							</span>

						</div>

					</div>

				</div>

				<div class="modal-footer">

					<input type="hidden" class="currentBookingId" value="">

					<button type="button" class="btn btn-default" data-dismiss="modal">
						Cancel
					</button>

					<button type="button" class="btn btn-primary" id="saveCommissionBtn">
						Save
					</button>

				</div>

			</div>
		</div>
	</div>
</div>
<script>
var defaultCommissionType = "<?php echo $tpl['option_arr']['o_commission_type']; ?>";
var defaultCommissionAmount = "<?php echo $tpl['option_arr']['o_commission_amount']; ?>";
</script>
<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if ($controller->_get->toInt('fleet_id') > 0)
	{
		?>pjGrid.queryString += "&fleet_id=<?php echo $controller->_get->toInt('fleet_id'); ?>";<?php
	}
	if ($controller->_get->toInt('client_id') > 0)
	{
		?>pjGrid.queryString += "&client_id=<?php echo $controller->_get->toInt('client_id'); ?>";<?php
	}
	if ($controller->_get->has('date'))
	{
		?>pjGrid.queryString += "&date=<?php echo $controller->_get->toString('date'); ?>";<?php
	}
	if ($controller->_get->has('date_from') && $controller->_get->toString('date_from') != '')
	{
		?>pjGrid.queryString += "&date_from=<?php echo $controller->_get->toString('date_from'); ?>";<?php
	}
	if ($controller->_get->has('date_to') && $controller->_get->toString('date_to') != '')
	{
		?>pjGrid.queryString += "&date_to=<?php echo $controller->_get->toString('date_to'); ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.client = <?php x__encode('lblClient', false, true); ?>;
	myLabel.fleet = <?php x__encode('lblFleet', false, true); ?>;
	myLabel.pickup_address = <?php x__encode('plugin_base_lbl_pickup', false, true); ?>;
	myLabel.return_address = <?php x__encode('plugin_base_lbl_dropoff', false, true); ?>;
	myLabel.passengers = <?php x__encode('lblPassengers', false, true); ?>;
	myLabel.extras = <?php x__encode('plugin_base_lbl_extras', false, true); ?>;

	myLabel.payment_method = <?php x__encode('lblPaymentMethod', false, true); ?>;
	myLabel.total = <?php x__encode('plugin_base_lbl_price', false, true); ?>;
	myLabel.distance = <?php x__encode('lblDistance', false, true); ?>;
	myLabel.date_time = <?php x__encode('lblDateTime', false, false); ?>;
	myLabel.email = <?php x__encode('email', false, true); ?>;
	myLabel.driver_name = <?php x__encode('plugin_base_lbl_driver', false, true); ?>;
	myLabel.supplier_name = <?php x__encode('plugin_base_lbl_supplier_name', false, true); ?>;
	myLabel.is_auction = <?php x__encode('plugin_base_lbl_in_auction', false, true); ?>;
	myLabel.status = <?php x__encode('lblStatus'); ?>;
	myLabel.exported = <?php x__encode('lblExport', false, true); ?>;
	myLabel.print = <?php x__encode('lblPrint', false, true); ?>;
	myLabel.delete_selected = <?php x__encode('delete_selected', false, true); ?>;
	myLabel.delete_confirmation = <?php x__encode('delete_confirmation', false, true); ?>;
	myLabel.pending = <?php echo x__encode('booking_statuses_ARRAY_pending'); ?>;
	myLabel.confirmed = <?php echo x__encode('booking_statuses_ARRAY_confirmed'); ?>;
	myLabel.cancelled = <?php echo x__encode('booking_statuses_ARRAY_cancelled'); ?>;
	myLabel.completed = <?php echo x__encode('plugin_base_lbl_completed'); ?>;
</script>