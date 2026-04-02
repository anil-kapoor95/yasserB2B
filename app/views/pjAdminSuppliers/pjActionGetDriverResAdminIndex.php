<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php echo $tpl['driver_name']['first_name']; ?> <?php __('front_your_reservations', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <!-- <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php // __('infoDriversDesc', false, true);?></p> -->
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">	
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<div class="row m-b-md">
                </div><!-- /.row -->
				<div id="grid-reservations-admin"></div>
				<!-- <div class="col-sm-3 col-xs-12">
					<a href="<?php // echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminDrivers&action=pjActionIndex" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadSearch"><?php __('front_btn_back');?></a>
				</div> -->
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var pjGrid = pjGrid || {};
pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
pjGrid.currentUserId = <?php echo (int) $_SESSION[$controller->defaultUser]['id']; ?>;
pjGrid.hasUpdate = <?php echo pjAuth::factory('pjAdminDrivers', 'pjActionUpdate')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasDeleteSingle = <?php echo pjAuth::factory('pjAdminDrivers', 'pjActionDeleteDriver')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasDeleteMulti = <?php echo pjAuth::factory('pjAdminDrivers', 'pjActionDeleteDriverBulk')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasRevertStatus = <?php echo pjAuth::factory('pjAdminDrivers', 'pjActionStatusDriver')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasExport = <?php echo pjAuth::factory('pjAdminDrivers', 'pjActionExportDriver')->hasAccess() ? 'true' : 'false';?>;

var myLabel = myLabel || {};
myLabel.fleet = <?php x__encode('lblFleet'); ?>;
myLabel.pickup_address = <?php x__encode('front_pickup_address'); ?>;
myLabel.return_address = <?php x__encode('front_dropoff_address'); ?>;
myLabel.distance = <?php x__encode('lblDistance'); ?>;
myLabel.booking_date = <?php x__encode('lblDateTime'); ?>;
myLabel.booking_status = <?php x__encode('lblStatus'); ?>;
myLabel.pending = <?php echo x__encode('booking_statuses_ARRAY_pending'); ?>;
myLabel.confirmed = <?php echo x__encode('booking_statuses_ARRAY_confirmed'); ?>;
myLabel.cancelled = <?php echo x__encode('booking_statuses_ARRAY_cancelled'); ?>;
myLabel.completed = "Completed"; <?php // echo x__encode('booking_statuses_ARRAY_cancelled'); ?>;

myLabel.created = <?php x__encode('lblCreatedOn'); ?>;

</script>


