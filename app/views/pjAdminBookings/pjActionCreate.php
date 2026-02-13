<?php

$time_format = 'HH:mm';

if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1))

{

    $time_format = 'hh:mm a';

}

if((strpos($tpl['option_arr']['o_time_format'], 'A') > -1))

{

    $time_format = 'hh:mm A';

}

$months = __('months', true);

ksort($months);

$short_days = __('short_days', true);

$bs = __('booking_statuses', true); 

?>

<div id="dateTimePickerOptions" style="display:none;" data-timeformat="<?php echo $time_format=='HH:mm' ? 'HH:mm' : 'LT'; ?>" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-dateformat="<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?>" data-format="<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?> <?php echo $time_format;?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>

<div class="row wrapper border-bottom white-bg page-heading">

	<div class="col-sm-12">

		<div class="row">

			<div class="col-lg-9 col-md-8 col-sm-6">

				<h2><?php __('infoAddBookingTitle');?></h2>

			</div>

		</div><!-- /.row -->



		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoAddBookingDesc');?></p>

	</div><!-- /.col-md-12 -->

</div>



<div class="wrapper wrapper-content animated fadeInRight">

	<div class="tabs-container tabs-reservations m-b-lg">

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate" method="post" id="frmCreateBooking">
			<input type="hidden" name="booking_create" value="1" />
			<input type="hidden" name="from_city" id="from_city" value="" />
		    <input type="hidden" name="to_city" id="to_city" value="" />

			<ul class="nav nav-tabs" role="tablist">
        		<li role="presentation" class="active"><a class="nav-tab-booking-details" href="#tab-booking-details" aria-controls="booking-details" role="tab" data-toggle="tab"><?php __('lblBookingDetails');?></a></li>
        		<li role="presentation"><a class="nav-tab-client-details" href="#tab-client-details" aria-controls="client-details" role="tab" data-toggle="tab"><?php __('lblClientDetails');?></a></li>

        	</ul>

        	<div class="tab-content">

        		<div role="tabpanel" class="tab-pane active" id="tab-booking-details">

        			<div class="panel-body">

        				<div class="row">

							<div class="col-lg-2 col-md-4 col-sm-6">

								<div class="form-group">

									<label class="control-label">Booking Type</label>

									<select name="return_status" id="return_status" class="form-control required" data-msg-required="<?php __('lblFieldRequired');?>">

										<option value="">-- <?php __('lblChoose'); ?>--</option>

										<option value="0">One Way</option>

										<option value="1">Round-Trip</option>

									</select>

								</div>

							</div><!-- /.col-md-2 -->

        					<div class="col-lg-3 col-md-4 col-sm-6">

								<div class="form-group">

									<label class="control-label"><?php __('lblDateTime'); ?></label>

										<div class="input-group">

										<input type="text" name="booking_date" id="booking_date" class="form-control datetimepick required" data-wt="open" readonly="readonly" data-msg-required="<?php __('tr_field_required');?>">

										<span class="input-group-addon"><i class="fa fa-calendar"></i></span> 

									</div>

								</div>

							</div><!-- /.col-md-3 -->

							<div class="col-lg-3 col-md-4 col-sm-6 returnDateTime">
								<div class="form-group">
									<label class="control-label">Return	Date & time <?php // __('lblreturnbooking'); ?></label>
									<div class="input-group">
										<input type="text" name="return_date" id="return_date" value="<?php echo $return_time; ?>" class="form-control datetimepick" data-wt="open" readonly="readonly" >
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
									</div>
								</div>
							</div> 


							<div class="col-lg-3 col-md-4 col-sm-6">

								<div class="form-group">

									<label class="control-label"><?php __('lblPickupAddress'); ?></label>

									<input type="text" id="pickup_address" name="pickup_address" class="form-control required" data-msg-required="<?php __('tr_field_required'); ?>" />

								</div>

							</div><!-- /.col-md-3 -->

							

							<div class="col-lg-3 col-md-4 col-sm-6">

								<div class="form-group">

									<label class="control-label"><?php __('lblDropoffAddress'); ?></label>

									<input type="text" id="return_address" name="return_address" class="form-control required" data-msg-required="<?php __('tr_field_required'); ?>" />

								</div>

							</div><!-- /.col-md-3 -->

							

							<div class="col-lg-3 col-md-4 col-sm-6">

								<div class="form-group">

									<label class="control-label"><?php __('lblDistance'); ?></label>

									<div class="input-group">

										<input type="text" id="distance" name="distance" class="form-control digits required" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-digits="<?php __('pj_digits_validation');?>"/>

										<input type="hidden" id="pjTbsDurationInMinFiled" name="durationInMin" value=""/>


										<span class="input-group-addon">km</span> 

									</div>

								</div>

							</div><!-- /.col-md-3 -->

        				</div><!-- /.row -->

        				

        				<div class="hr-line-dashed"></div>

        				

        				<div class="row">

        					<div class="col-lg-3 col-md-4 col-sm-6">

								<div class="form-group">

									<label class="control-label"><?php __('lblFleet'); ?></label>

									<select name="fleet_id" id="fleet_id" class="form-control select-item required" data-msg-required="<?php __('tr_field_required'); ?>">

            							<option value="">-- <?php __('lblChoose'); ?>--</option>

            							<?php

            							foreach($tpl['fleet_arr'] as $k => $v)

            							{

            								?><option value="<?php echo $v['id'];?>" data-passengers="<?php echo !empty($v['passengers']) ? $v['passengers'] : null; ?>" data-luggage="<?php echo !empty($v['luggage']) ? $v['luggage'] : null; ?>"><?php echo $v['fleet'];?></option><?php

            							} 

            							?>

            						</select>

								</div>

							</div><!-- /.col-md-3 -->

							

							<div class="col-lg-3 col-md-4 col-sm-6">

								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12">
										<div class="form-group">
                                            <label class="control-label"><?php __('lblPassengers') ?></label>
                                            <input type="text" id="passengers" name="passengers" class="form-control field-int required pj-positive-number" data-value="0" readonly="readonly" data-msg-required="<?php __('tr_field_required'); ?>" />
                                            <span class="small" id="tr_max_passengers"></span>
                                        </div>
									</div>

								<!-- 	<div class="col-lg-6 col-md-6 col-sm-12">
										<div class="form-group">
                                            <label class="control-label"><?php // __('lblLuggage') ?></label>
                                            <input type="text" id="luggage" name="luggage" class="form-control field-int required pj-positive-number" data-value="0" readonly="readonly" data-msg-required="<?php // __('tr_field_required'); ?>" />
                                            <span class="small" id="tr_max_luggage"></span>
                                        </div>
									</div> -->

								</div><!-- /.row -->

							</div><!-- /.col-md-3 -->

							<?php

							$plugins_payment_methods = pjObject::getPlugin('pjPayments') !== NULL? pjPayments::getPaymentMethods(): array();

							$haveOnline = $haveOffline = false;

							foreach ($tpl['payment_titles'] as $k => $v)

							{

							    if( $k != 'cash' && $k != 'bank' )

							    {

							        if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)

							        {

							            $haveOnline = true;

							            break;

							        }

							    }

							}

							foreach ($tpl['payment_titles'] as $k => $v)

							{

							    if( $k == 'cash' || $k == 'bank' )

							    {

							        if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)

							        {

							            $haveOffline = true;

							            break;

							        }

							    }

							}

							?>

							<div class="col-lg-3 col-md-4 col-sm-6">

                                <div class="form-group">

                                    <label class="control-label"><?php __('lblPaymentMethod'); ?></label>



                                    <select name="payment_method" id="payment_method" class="form-control<?php echo $tpl['option_arr']['o_payment_disable'] == 'No' ? ' required' : NULL;?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">

										<option value="">-- <?php __('lblChoose'); ?>--</option>

										<?php

    									if ($haveOnline && $haveOffline)

    									{

    									    ?><optgroup label="<?php __('script_online_payment_gateway', false, true); ?>"><?php

    				                    }

    				                    foreach ($tpl['payment_titles'] as $k => $v)

    				                    {

    				                        if($k == 'cash' || $k == 'bank' ){

    				                            continue;

    				                        }

    				                        if (array_key_exists($k, $plugins_payment_methods))

    				                        {

    				                            if(!isset($tpl['payment_option_arr'][$k]['is_active']) || (isset($tpl['payment_option_arr']) && $tpl['payment_option_arr'][$k]['is_active'] == 0) )

    				                            {

    				                                continue;

    				                            }

    				                        }

    				                        ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php

    				                    }

    				                    if ($haveOnline && $haveOffline)

    				                    {

    				                        ?>

    				                    	</optgroup>

    				                    	<optgroup label="<?php __('script_offline_payment', false, true); ?>">

    				                    	<?php 

    				                    }

    				                    foreach ($tpl['payment_titles'] as $k => $v)

    				                    {

    				                        if( $k == 'cash' || $k == 'bank' )

    				                        {

    				                            if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)

    				                            {

    				                                ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php

    				                            }

    				                        }

    				                    }

    				                    if ($haveOnline && $haveOffline)

    				                    {

    				                        ?></optgroup><?php

    				                    }

    									?>

									</select>

                                </div>

                            </div><!-- /.col-md-3 -->

                            

                            <div class="col-lg-3 col-md-4 col-sm-6">

								<div class="form-group">

									<label class="control-label"><?php __('lblStatus');?></label>

	

									<select name="status" id="status" class="form-control required" data-msg-required="<?php __('lblFieldRequired');?>">

										<option value="">-- <?php __('lblChoose'); ?>--</option>

										<?php

										foreach (__('booking_statuses', true, false) as $k => $v)

										{

											?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php

										}

										?>

									</select>

								</div>

							</div><!-- /.col-md-3 -->

        				</div><!-- /.row -->

        				

        				<div id="extraBox"></div>

        				

        				<div class="hr-line-dashed"></div>

        				

        				<div class="row">
        					<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label"><?php __('lblSubTotal'); ?></label>
									<div class="input-group">
										<input type="text" id="sub_total" name="sub_total" class="form-control">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false);?></span> 
									</div>
								</div>
							</div><!-- /.col-md-3 -->
							<!-- <div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label"><?php // __('lblTax'); ?></label>
									<div class="input-group">
										<input type="text" id="tax" name="tax" class="form-control" data-tax="<?php // echo $tpl['option_arr']['o_tax_payment'];?>">
										<span class="input-group-addon"><?php // echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false);?></span> 
									</div>
								</div>
							</div> /.col-md-3 -->

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label"><?php __('lblTotal'); ?> Balance</label>
									<div class="input-group">
										<input type="text" id="total" name="total" class="form-control">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false);?></span> 
									</div>
								</div>
							</div><!-- /.col-md-3 -->

							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label"><?php __('lblDeposit'); ?></label>
									<div class="input-group">
										<input type="text" id="deposit" name="deposit" class="form-control" data-deposit="<?php echo $tpl['option_arr']['o_deposit_payment'];?>">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false);?></span> 
									</div>
								</div>
							</div><!-- /.col-md-3 -->


							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label"><?php __('lblDeposit'); ?> <?php __('lblStripeLink'); ?> </label>
									<div class="input-group">
										<input type="text" id="d_stripeLink" name="d_stripeLink" class="form-control" placeholder="https://">
									</div>
								</div>
							</div><!-- /.col-md-3 -->


							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label"><?php __('lblRemainingBalance'); ?></label>
									<div class="input-group">
										<input type="text" id="remainingBalance" name="remainingBalance" class="form-control">
										<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false);?></span> 
									</div>
								</div>

							</div><!-- /.col-md-3 -->


							<div class="col-lg-3 col-md-4 col-sm-6">
								<div class="form-group">
									<label class="control-label"><?php __('lblRemainingBalance'); ?> <?php __('lblStripeLink'); ?></label>
									<div class="input-group"> 

										<!-- rb means Remaining Balance -->

										<input type="text" id="rb_stripeLink" name="rb_stripeLink" class="form-control" placeholder="https://">

									</div>

								</div>

							</div><!-- /.col-md-3 -->



        				</div><!-- /.row -->

        				

        				<div class="hr-line-dashed"></div>

        				

        				<div class="clearfix">

							<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">

								<span class="ladda-label"><?php __('btnSave'); ?></span>

								<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>

							</button>
							<button type="button" class="btn btn-white btn-lg pull-right" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';"><?php __('btnCancel'); ?></button>

						</div><!-- /.clearfix -->
        			</div><!-- /.panel-body -->

        		</div><!-- /.tab-pane -->


        		<div role="tabpanel" class="tab-pane" id="tab-client-details">
        			<div class="panel-body">
        				<div class="form-group">
                            <label class="control-label"><?php __('lblClient'); ?></label>
                            <div class="clearfix">
                                <div class="switch onoffswitch-data pull-left">
                                    <div class="onoffswitch onoffswitch-client">
                                        <input type="checkbox" class="onoffswitch-checkbox" id="new_client" name="new_client" checked>
                                        <label class="onoffswitch-label" for="new_client">
                                            <span class="onoffswitch-inner" data-on="<?php __('lblNewClient'); ?>" data-off="<?php __('lblExistingClient'); ?>"></span>
                                            <span class="onoffswitch-switch"></span>

                                        </label>

                                    </div>

                                </div>

                            </div><!-- /.clearfix -->

                        </div><!-- /.form-group -->

                        <div class="current-client-area" style="display:none;">

                			<div class="form-group">

                                <label class="control-label"><?php __('lblExistingClient'); ?></label>

                                <div class="row">

                            		<div class="col-md-10">

                                        <select name="client_id" id="client_id" class="form-control select-item fdRequired" data-msg-required="<?php __('tr_field_required', false, true);?>">

        									<option value="">-- <?php __('lblChoose'); ?>--</option>

        									<?php

        									foreach ($tpl['client_arr'] as $v)

        									{

        										$email_phone = array();

        										if(!empty($v['c_email']))

        										{

        											$email_phone[] = stripslashes($v['c_email']);

        										}

        										if(!empty($v['c_phone']))

        										{

        											$email_phone[] = stripslashes($v['c_phone']);

        										}

        										?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::clean($v['c_name']); ?> (<?php echo join(" | ", $email_phone); ?>)</option><?php

        									}

        									?>

        								</select>

									</div>

        							<div class="col-md-2">

                            			<a id="pjFdEditClient" class="btn btn-primary btn-outline btn-sm m-l-xs" href="#" target="blank" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&id={ID}" style="display:none;"><i class="fa fa-pencil"></i></a>

                            		</div>

                               </div>

                        	</div>

                        </div><!-- /.hidden-area -->

                        

                        <div class="new-client-area">

                        	<div class="hr-line-dashed"></div>

                        	<?php

                        	ob_start();

                        	$field = 0;

                        	if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)))

                        	{

                        	    $title_arr = pjUtil::getTitles();

                        	    $name_titles = __('personal_titles', true, false);

                        	    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingTitle'); ?></label>



                                        <select id="c_title" name="c_title" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_title'] == 3) ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>">

    										<option value="">----</option>

    										<?php

    										$title_arr = pjUtil::getTitles();

    										$name_titles = __('personal_titles', true, false);

    										foreach ($title_arr as $v)

    										{

    											?><option value="<?php echo $v; ?>"><?php echo $name_titles[$v]; ?></option><?php

    										}

    										?>

    									</select>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if (in_array($tpl['option_arr']['o_bf_include_fname'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingFname'); ?></label>



                                        <input type="text" name="c_fname" id="c_fname" class="form-control<?php echo $tpl['option_arr']['o_bf_include_fname'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if (in_array($tpl['option_arr']['o_bf_include_lname'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingLname'); ?></label>



                                        <input type="text" name="c_lname" id="c_lname" class="form-control<?php echo $tpl['option_arr']['o_bf_include_lname'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingEmail'); ?></label>



                                        <input type="text" name="c_email" id="c_email" class="form-control email<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field == 4)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							    <?php

							    ob_start();

							    $field = 0;

							}

							if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6"  style="display:none;">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingPassword'); ?></label>



                                        <input type="password" name="c_password" id="c_password" class="form-control" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field == 4)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							    <?php

							    ob_start();

							    $field = 0;

							}

							if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingPhone'); ?></label>



                                        <input type="text" name="c_phone" id="c_phone" class="form-control<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field == 4)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							
							<?php
							    ob_start();
							    $field = 0;
							}

							if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingCompany'); ?></label>



                                        <input type="text" name="c_company" id="c_company" class="form-control<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field == 4)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							    <?php

							    ob_start();

							    $field = 0;

							}

							if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingAddress'); ?></label>



                                        <input type="text" name="c_address" id="c_address" class="form-control<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field == 4)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							    <?php

							    ob_start();

							    $field = 0;

							}

							if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingCity'); ?></label>



                                        <input type="text" name="c_city" id="c_city" class="form-control<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field == 4)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							    <?php

							    ob_start();

							    $field = 0;

							}

							if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingState'); ?></label>



                                        <input type="text" name="c_state" id="c_state" class="form-control<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field == 4)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							    <?php

							    ob_start();

							    $field = 0;

							}

							if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingZip'); ?></label>



                                        <input type="text" name="c_zip" id="c_zip" class="form-control<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>"/>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field == 4)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							    <?php

							    ob_start();

							    $field = 0;

							}

							if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))

							{

							    ?>

							    <div class="col-lg-3 col-md-4 col-sm-6">

                                    <div class="form-group">

                                        <label class="control-label"><?php __('lblBookingCountry'); ?></label>



                                        <select name="c_country" id="c_country" class="form-control select-item<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' fdRequired required' : NULL; ?>" data-msg-required="<?php __('tr_field_required', false, true);?>">

        									<option value="">-- <?php __('lblChoose'); ?>--</option>

        									<?php

        									foreach ($tpl['country_arr'] as $v)

        									{

        										?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::html($v['name']); ?></option><?php

        									}

        									?>

        								</select>

                                    </div>

                                </div><!-- /.col-md-3 -->

							    <?php

							    $field++;

							}

							if($field > 0)

							{

							    $ob_fields = ob_get_contents();

							    ob_end_clean();

							    ?>

							    <div class="row">

							    	<?php echo $ob_fields;?>

							    </div><!-- /.row -->

							    <?php

							}

                        	?>

                        </div><!-- /.new-client-area -->

                        
                            <?php

							    ob_start();

							    $field = 0;

							
							if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)))
								{
									?>

									<div class="col-lg-3 col-md-4 col-sm-6">
										<div class="form-group">
											<label class="control-label"><?php __('lblBookingNotes'); ?></label>
											<textarea name="c_notes" id="c_notes" class="form-control<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' required' : NULL; ?>"></textarea>
										</div>
									</div><!-- /.col-md-3 -->
									<?php
									$field++;
								}

							if($field == 4)
							{
							    $ob_fields = ob_get_contents();
							    ob_end_clean();
							    ?>
							    <div class="row">
							    	<?php echo $ob_fields;?>
							    </div><!-- /.row -->
                        <?php

                        ob_start();

                        $field = 0;
                  		  }
                     

						if (in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)))

						{

						    ?>

						    <div class="col-lg-3 col-md-4 col-sm-6 airlineIncludedArival">

                                <div class="form-group">

                                    <label class="control-label"><?php __('lblBookingAirlineCompany'); ?></label>



                                    <input type="text" name="c_airline_company" id="c_airline_company" class="form-control<?php echo $tpl['option_arr']['o_bf_include_airline_company'] == 3 ? ' required' : NULL; ?>" />

                                </div>

                            </div><!-- /.col-md-3 -->

						    <?php

						    $field++;

						}

						if (in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)))

						{

						    ?>

						    <div class="col-lg-3 col-md-4 col-sm-6 airlineIncludedArival">

                                <div class="form-group">

                                    <label class="control-label"><?php __('lblArrivalFlightNumber'); ?></label>



                                    <input type="text" name="c_flight_number" id="c_flight_number" class="form-control<?php echo $tpl['option_arr']['o_bf_include_flight_number'] == 3 ? ' required' : NULL; ?>" />

                                </div>

                            </div><!-- /.col-md-3 -->

						    <?php

						    $field++;

						}

						if (in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)))

						{

						    ?>

						    <div class="col-lg-3 col-md-4 col-sm-6 airlineIncludedArival">

                                <div class="form-group">

                                    <label class="control-label"><?php __('lblFlightArrivalTime'); ?></label>

                                    

                                    <div class="input-group pjCrTimePicker pjCrTimePickerFrom" data-rel="from">

										<span class="input-group-addon">

											<span class="glyphicon glyphicon-time" aria-hidden="true"></span>

										</span>

										<input type="text" name="c_flight_time" id="c_flight_time" class="form-control<?php echo $tpl['option_arr']['o_bf_include_flight_time'] == 3 ? ' required' : NULL; ?>" readonly="readonly"/>

									</div>

                                </div>

                            </div><!-- /.col-md-3 -->

						    <?php

						    $field++;

						}

						if($field == 4)

						{

						    $ob_fields = ob_get_contents();

						    ob_end_clean();

						    ?>

						    <div class="row">

						    	<?php echo $ob_fields;?>

						    </div><!-- /.row -->

						    <?php

						    ob_start();

						    $field = 0;

						}

						if (in_array($tpl['option_arr']['o_bf_include_terminal'], array(2, 3)))

						{

						    ?>

						    <div class="col-lg-3 col-md-4 col-sm-6">

                                <div class="form-group">

                                    <label class="control-label"><?php __('lblBookingTerminal'); ?></label>



                                    <input type="text" name="c_terminal" id="c_terminal" class="form-control<?php echo $tpl['option_arr']['o_bf_include_terminal'] == 3 ? ' required' : NULL; ?>" />

                                </div>

                            </div><!-- /.col-md-3 -->

						    <?php

						    $field++;

						}

						if($field > 0)

						{

						    $ob_fields = ob_get_contents();

						    ob_end_clean();

						    ?>

						    <div class="row">

						    	<?php echo $ob_fields;?>

						    </div><!-- /.row -->

						    <?php

						}

                        ?>
                        <div class="row">
	                        <div class="hr-line-dashed"></div>
	                        <div class="col-lg-3 col-md-4 col-sm-6 airlineIncluded">
								<div class="form-group">
									<label class="control-label"><?php __('lblDepartureAirlineCompany'); ?></label>

									<input type="text" name="c_departure_airline_company" id="c_departure_airline_company" class="form-control" />
								</div>
							</div><!-- /.col-md-3 -->

							 <div class="col-lg-3 col-md-4 col-sm-6 airlineIncluded">
								<div class="form-group">
									<label class="control-label"><?php __('lblDepartureFlightNumber'); ?></label>

									<input type="text" name="c_departure_flight_number" id="c_departure_flight_number" class="form-control" />
								</div>
							</div><!-- /.col-md-3 -->

							<div class="col-lg-3 col-md-4 col-sm-6 airlineIncluded">
			                    <div class="form-group">
			                        <label class="control-label"><?php __('lblFlightDepartureTime'); ?></label>
			                        <div class="input-group pjCrTimePicker pjCrTimePickerFrom" data-rel="from">
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
										</span>
										<input type="text" name="c_departure_flight_time" id="c_departure_flight_time" class="form-control" readonly="readonly"/>
									</div>
			                    </div>
			                </div><!-- /.col-md-3 -->
                        </div>
						

                        <div class="hr-line-dashed"></div>

                        <div class="clearfix">

                            <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">

                                <span class="ladda-label"><?php __('btnSave'); ?></span>

                                <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>

                            </button>

                            <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex"><?php __('btnCancel'); ?></a>

                        </div><!-- /.clearfix -->

        			</div><!-- /.panel-body -->

        		</div><!-- /.tab-pane -->

        	</div><!-- /.tab-content -->

		</form>

	</div><!-- /.tabs-container tabs-reservations m-b-lg -->

</div><!-- /.wrapper wrapper-content animated fadeInRight -->



<script type="text/javascript">

var myLabel = myLabel || {};

myLabel.maximum = <?php echo x__encode('lblMaximum', true, false)?>;

myLabel.positive_number = <?php x__encode('lblPositiveNumber'); ?>;

myLabel.max_number = <?php x__encode('lblMaxNumber'); ?>;

myLabel.email_already_exist = <?php x__encode('lblBookingsEmailExist'); ?>;

</script>