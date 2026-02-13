<?php 
$today = pjDateTime::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$bs = __('booking_statuses', true); 
?>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5><?php __('dash_today');?></h5>
		</div>
		<div class="ibox-content">
			<div class="row">
				<div class="col-xs-4">
					<p class="h1 no-margins"><?php echo $tpl['enquiries_received_today'];?></p>
					<small class="text-info"><?php $tpl['enquiries_received_today'] != 1 ? __('dash_enquiries_received_today') : __('dash_enquiry_received_today');?></small>        
				</div><!-- /.col-xs-6 -->
	
				<div class="col-xs-4">
					<p class="h1 no-margins"><?php echo $tpl['reservations_today'];?></p>
					<small class="text-info"><?php $tpl['reservations_today'] != 1 ? __('dash_reservations_today') : __('dash_reservation_today');?></small>        
				</div><!-- /.col-xs-6 -->

				<div class="col-xs-4">
					<p class="h1 no-margins"><?php echo $tpl['total_reservations'];?></p>
					<small class="text-info"><?php $tpl['total_reservations'] != 1 ? __('dash_total_reservations') : __('dash_reservation');?></small>        
				</div><!-- /.col-xs-6 -->
			</div><!-- /.row -->
		</div>
	</div><!-- /.row -->

	<div class="row">
		<div class="col-lg-4">
			<div class="ibox float-e-margins">
				<div class="ibox-content ibox-heading clearfix">
					<div class="pull-left">
						<h3><?php __('dash_latest_enquiries');?></h3>
					</div><!-- /.pull-left -->

					<div class="pull-right m-t-md">
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="btn btn-primary btn-sm btn-outline m-n"><?php __('lblDashViewAll');?></a>
					</div><!-- /.pull-right -->
				</div>

				<div class="ibox-content inspinia-timeline">
					<?php if (count($tpl['latest_enquiries']) > 0) { ?>
						<?php foreach ($tpl['latest_enquiries'] as $k => $v) { ?>
						<div class="timeline-item">
							<div class="row">
								<div class="col-xs-3 date">
									<i class="fa fa-calendar"></i>
									<?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?>
								</div>
								<div class="col-xs-7 content">
									<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']; ?>">
										<p class="m-b-xs"><strong><?php echo pjSanitize::html($v['name']);?></strong></p>
	
										<p class="m-n"><em><?php echo pjSanitize::html($v['fleet']);?></em></p>
									</a>
								</div>
	
								<div class="badge bg-<?php echo $v['status'];?> b-r-sm pull-right m-t-md m-r-sm"><?php echo @$bs[$v['status']];?></div>
							</div>
						</div>
						<?php } ?>
					<?php } else { ?>
						<p><?php __('dash_no_enquiries');?></p>
					<?php } ?>
				</div>
			</div>
		</div><!-- /.col-lg-4 -->

		<div class="col-lg-4">
			<div class="ibox float-e-margins">
				<div class="ibox-content ibox-heading clearfix">
					<div class="pull-left">
						<h3><?php __('dash_title_reservations_today');?></h3>
					</div><!-- /.pull-left -->

				</div>

				<div class="ibox-content inspinia-timeline">
					<?php if (count($tpl['reservations_today_arr']) > 0) { ?>
						<?php foreach ($tpl['reservations_today_arr'] as $k => $v) { ?>
							<div class="timeline-item">
								<div class="row">
    								<div class="col-xs-3 date">
    									<i class="fa fa-calendar"></i>
    									<?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?>
    								</div>
    								<div class="col-xs-7 content">
    									<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']; ?>">
    										<p class="m-b-xs"><strong><?php echo pjSanitize::html($v['name']);?></strong></p>
    	
    										<p class="m-n"><em><?php echo pjSanitize::html($v['fleet']);?></em></p>
    									</a>
    								</div>
    	
    								<div class="badge bg-<?php echo $v['status'];?> b-r-sm pull-right m-t-md m-r-sm"><?php echo @$bs[$v['status']];?></div>
    							</div>
							</div>
						<?php } ?>
					<?php } else { ?>
						<p><?php __('dash_no_enquiries');?></p>
					<?php } ?>
				</div>
			</div>
		</div><!-- /.col-lg-4 -->

		<div class="col-lg-4">
			<div class="ibox float-e-margins">
				<div class="ibox-content ibox-heading clearfix">
					<h3><?php __('dash_quick_links');?></h3>
				</div>

				<div class="ibox-content inspinia-timeline">
					<?php
					if(pjAuth::factory('pjAdminBookings', 'pjActionIndex')->hasAccess())
					{
						?>
						<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('dash_view_enquiries'); ?></a></p>
						<?php
					}
					if(pjAuth::factory('pjAdminBookings', 'pjActionIndex')->hasAccess())
					{
					    ?>
						<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex&amp;date=<?php echo date("Y-m-d");?>"><?php __('dash_link_reservations_today'); ?></a></p>
						<?php
					}
					if(pjAuth::factory('pjAdminBookings', 'pjActionCreate')->hasAccess())
					{
					    ?>
						<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate"><?php __('dash_add_enquiry'); ?></a></p>
						<?php
					}
					?>
					<p><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><?php __('dash_open_frontend'); ?></a></p>
				</div>
			</div>
		</div><!-- /.col-lg-4 -->
	</div><!-- /.row -->
</div><!-- /.wrapper wrapper-content -->