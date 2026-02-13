<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoDriversTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoDriversDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<div class="row m-b-md">
					<div class="pjTbs-body">
						<div class="pjTbs-box-title text-center"><?php __('front_your_reservations');?></div><!-- /.pjTbs-box-title -->
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th style="color:black; vertical-align:middle; text-align:center;">AAAaa<?php __('lblFleet', false, true); ?></th>
										<th style="color:black; vertical-align:middle; text-align:center;"><?php __('front_typed', false, true); ?></th>
										<th style="color:black; vertical-align:middle; text-align:center;"><?php __('front_pickup_address', false, true); ?></th>
										<th style="color:black; vertical-align:middle; text-align:center;"><?php __('front_dropoff_address', false, true); ?></th>
										<th style="color:black; vertical-align:middle; text-align:center;"><?php __('lblDistance', false, true); ?></th>
										<th style="color:black; vertical-align:middle; text-align:center;"><?php __('lblDateTime', false, false); ?></th>
										<th style="color:black; vertical-align:middle; text-align:center;"><?php __('front_return_date', false, false); ?></th>
										<th style="color:black; vertical-align:middle; text-align:center;"><?php __('lblStatus'); ?></th>
										<th style="color:black; vertical-align:middle; text-align:center;"><?php __('lblCreatedOn'); ?></th>
										
									</tr>
								</thead>
								<tbody>
									<?php
									
									if(!empty($tpl['arr']))
									{
										$statuses = __('booking_statuses', true, false);
										foreach($tpl['arr'] as $k => $v)
										{
											$seconds = 2 * 60 * 60;
											?>
											<tr>
												<td><?php echo pjSanitize::html($v['fleet']);?></td>
												<td><?php echo pjSanitize::html($v['check_type']);?></td>
												<td><?php echo pjSanitize::html($v['pickup_address']);?></td>
												<td><?php echo pjSanitize::html($v['return_address']);?></td>
												<td><?php echo (int) $v['distance'] . ' km'; ?></td>
												<td><?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($v['booking_date']));?></td>
												<?php if ($v['check_type'] == $front_to_value || $v['check_type'] == $front_from_value ) {?>
													<td>N/A</td>
													<?php } else{ ?>
														<td><?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($v['c_return_date']));?></td>
												<?php }?>
												<td><?php echo $statuses[$v['booking_status']];?></td>
												<td><?php echo date($tpl['option_arr']['o_date_format'] . ', ' . $tpl['option_arr']['o_time_format'], strtotime($v['created']));?></td>

												<!-- <td class="text-center">
													<?php
													// if(time() + $seconds <= strtotime($v['booking_date']) && $v['status'] != 'cancelled')
													// {
														?>
														<a href="#" class="pjTbsCancelBooking" data-id="<?php // echo $v['id'];?>"><?php __('front_btn_cancel')?></a>
														<?php
													// }else{
													// 	echo '&nbsp;';
													// }
													?>
												</td> -->
											</tr>
											<?php
										}
									}else{
										?>
										<tr>
											<td colspan="6"><?php __('lblNoRecordsFound')?></td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>
						<div class="pjTbs-body-actions">
							<div class="row">
								<div class="col-sm-3 col-xs-12">
									<a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminDrivers&action=pjActionIndex" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadSearch"><?php __('front_btn_back');?></a>
								</div><!-- /.col-sm-3 -->
							</div><!-- /.row -->
						</div><!-- /.pjTbs-body-actions -->
					</div>
    			</div><!-- /.col-lg-12 -->
			</div>
		</div>
	</div>
</div>


