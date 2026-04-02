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
	?><div id="dateTimePickerOptionss" style="display:none;" data-timeformat="<?php echo $time_format=='HH:mm' ? 'HH:mm' : 'LT'; ?>" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-dateformat="<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?>" data-format="<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?> <?php echo $time_format;?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>">
	</div>
<style>
	.table-responsive.abc {
		overflow: visible !important;
	}
</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-6">
                <h2><?php __('infoAddFleetTitle');?></h2>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                <?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<?php endif; ?>
        	</div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoAddFleetDesc');?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
               <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminFleets&amp;action=pjActionCreate" method="post" id="frmCreateFleet" autocomplete="off" enctype="multipart/form-data">
               		<input type="hidden" name="fleet_create" value="1" />
               		<input type="hidden" id="index_arr" name="index_arr" value="<?php echo $index;?>" />
               		<input type="hidden" id="index_city_price_arr" name="index_city_price_arr" value="<?php echo $index;?>" />
					<input type="hidden" id="index_daterange_price_arr" name="index_daterange_price_arr" value="<?php echo $index;?>" />

                    <div class="row">
                        <div class="col-sm-6">
                        	<?php
                        	foreach ($tpl['lp_arr'] as $v)
                        	{
                            	?>
                                <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                                    <label class="control-label"><?php __('lblFleet');?></label>
                                                            
                                    <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
										<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][fleet]" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">	
										<?php if ($tpl['is_flag_ready']) : ?>
										<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="row">
                            	<div class="col-lg-8 col-md-7">
									<div class="form-group">
		                                <label class="control-label"><?php __('lblImage', false, true); ?></label>
		                                <div>
		                                    <div class="fileinput fileinput-new" data-provides="fileinput">
		                                        <span class="btn btn-primary btn-outline btn-file"><span class="fileinput-new"><i class="fa fa-upload"></i> <?php __('lblSelectImage');?></span>
		                                        <span class="fileinput-exists"><?php __('lblChangeImage');?></span><input name="image" type="file"></span>
		                                        <span class="fileinput-filename"></span>
		
		                                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
		                                    </div>
		                                </div>
		                            </div><!-- /.form-group -->
								</div>
							</div>	
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label">Choose Category</label>
    
                                        <select name="category" id="category" class="form-control" data-placeholder="-- <?php __('lblChoose'); ?> --">
	                						<?php
	                						foreach ($tpl['categories_arr'] as $v)
	                						{
	                							?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['category']); ?></option><?php
	                						}
	                						?>
	                					</select>
                                    </div><!-- /.form-group -->
								</div>
								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label"><?php __('lblPassengers'); ?></label>
    
                                        <input type="text" name="passengers" id="passengers" class="form-control pj-field-count digits" data-msg-digits="<?php __('pj_digits_validation');?>"/>
                                    </div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->
								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label"><?php __('lblLuggage'); ?></label>
    
                                        <input type="text" name="luggage" id="luggage" class="form-control pj-field-count digits" data-msg-digits="<?php __('pj_digits_validation');?>"/>
                                    </div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->
							</div><!-- /.row -->
                        </div><!-- /.col-sm-6 -->

                        <div class="col-sm-6">
                            <?php
                        	foreach ($tpl['lp_arr'] as $v)
                        	{
                            	?>
                                <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                                    <label class="control-label"><?php __('lblDescription');?></label>
                                                            
                                    <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
                                    	<textarea class="form-control form-control-lg" name="i18n[<?php echo $v['id']; ?>][description]" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"></textarea>
										<?php if ($tpl['is_flag_ready']) : ?>
										<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
                                </div>
                                <?php
                            }
                            ?>
                        </div><!-- /.col-sm-6 -->
                    </div><!-- /.row -->

                    <div class="hr-line-dashed"></div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                        	<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label"><?php __('lblStartFee'); ?></label>
    
                                        <div class="input-group">
                                            <input type="text" id="start_fee" name="start_fee" class="form-control pj-field-price required number" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-number="<?php __('pj_number_validation');?>"/>
        
                                            <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                                        </div>
                                    </div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->
								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label"><?php __('lblFeePerPerson'); ?></label>
    
                                        <div class="input-group">
                                            <input type="text" id="fee_per_person" name="fee_per_person" class="form-control pj-field-price required number" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-number="<?php __('pj_number_validation');?>"/>
        
                                            <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                                        </div>
                                    </div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->
							</div><!-- /.row -->
                        </div><!-- /.col-sm-6 -->
						
                        <div class="col-sm-6">
                        	<?php
                        	if(!empty($tpl['extra_arr']))
                        	{
                            	?>
                                <div class="form-group">
                                    <label class="control-label"><?php __('lblExtras'); ?></label>
    
                                    <select name="extra_id[]" id="extra_id" multiple="multiple" size="5" class="form-control" data-placeholder="-- <?php __('lblChoose'); ?> --">
                						<?php
                						foreach ($tpl['extra_arr'] as $v)
                						{
                							?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
                						}
                						?>
                					</select>
                                </div><!-- /.form-group -->
                                <?php
                        	}else{
                        	    ?>
                        	    <div class="form-group">
                        	    	<label class="control-label"><?php __('lblExtras'); ?></label>
                                    <p class="form-control-static"><?php echo __('lblNoExtrasFound', true, false);?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex&create=1"><?php __('lblHere');?></a></p>
                                </div>
                        	    <?php
                        	}
                            ?> 
                        </div><!-- /.col-sm-6 -->
                        
                    </div><!-- /.row -->

					<div class="hr-line-dashed"></div>
					<div class="row">
						<div class="col-sm-6">
                        	<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label"><?php __('lblFeePerMints'); ?></label>
                                        <div class="input-group">
                                            <input type="text" id="time_rate_per_minute" name="time_rate_per_minute" class="form-control pj-field-price  number" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-number="<?php __('pj_number_validation');?>"/>
                                            <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                                        </div>
                                    </div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->

								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label"><?php __('lblOverbookingCost'); ?></label>
                                        <div class="input-group">
                                            <input type="text" id="overbooking_cost" name="overbooking_cost" class="form-control pj-field-price  number" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-number="<?php __('pj_number_validation');?>"/>

                                            <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                                        </div>
                                    </div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->

								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label"><?php __('lblPriceHikeCost'); ?></label>
                                        <div class="input-group">
                                            <input type="text" id="price_hike" name="price_hike" class="form-control pj-field-price number" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-number="<?php __('pj_number_validation');?>"/>

                                            <span class="input-group-addon">%</span> 
                                        </div>
                                    </div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->

								<div class="col-sm-6">
									<div class="form-group">
                                        <label class="control-label">Number of booking per day<?php // __('lblPriceHikeCost'); ?></label>
                                        <div class="input-group">
                                            <input type="text" id="numberof_booking" name="numberof_booking" class="form-control pj-field-price number" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-number="<?php __('pj_number_validation');?>"/>

                                            <span class="input-group-addon"><i class="fa fa-users"></i></span> 
                                        </div>
                                    </div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->

							</div><!-- /.row -->
                        </div><!-- /.col-sm-6 -->
					</div>
					<div class="hr-line-dashed"></div>
					<h2><?php __('lblPrice'); ?></h2>
					
					<div class="row">
						<div class="col-sm-9 col-xs-9">
							<div class="table-responsive table-responsive-secondary">
                                <table id="pjTbPriceTable" class="table table-striped table-hover">
                                    <thead>
            							<tr>
            								<th><?php __('lblFromInKm'); ?></th>
            								<th><?php __('lblToInKm'); ?></th>
            								<th><?php __('lblPricePerKm'); ?></th>
											<th><?php __('lblStartFee'); ?></th>
											<th><?php __('lblFeePerMints'); ?></th>
            								<th>&nbsp;</th>
            							</tr>
            						</thead>
                                    <tbody>
                                    	
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive table-responsive-secondary -->
						</div><!-- /.col-sm-9 -->
						<div class="col-sm-3 col-xs-3">
							<div class="m-t-lg">
                                <a href="#" class="btn btn-primary btn-outline m-t-xs btnAddPrice"><i class="fa fa-plus"></i> <?php __('btnAddPrice'); ?></a>
                            </div>
						</div>
					</div><!-- /.row -->
					
					<div class="hr-line-dashed"></div>
					 <h2><?php __('lblPriceFromCitytoCity'); ?></h2>                    
					<div class="row">
						<div class="col-sm-8 col-xs-9">
							<div class="table-responsive abc">
							<table id="pjTbCityPriceTable" class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
							<thead>
								<tr>
									<th><?php __('lblFromCity'); ?></th>
									<th><?php __('lblToCity'); ?></th>
									<th><?php __('lblFixedPrice'); ?></th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
							</div><!-- /.table-responsive table-responsive-secondary -->
						</div><!-- /.col-sm-9 -->
						<div class="col-sm-4 col-xs-3">
							<div class="m-t-lg">
								<a href="#" class="btn btn-primary btn-outline m-t-xs btnAddCityPrice"><i class="fa fa-plus"></i> <?php __('btnAddPrice'); ?></a>
							</div>
						</div>
					</div><!-- /.row -->
					
					<div class="hr-line-dashed"></div>

					<h2><?php __('lblPriceFromDateRange'); ?></h2>

					<div class="row">
						<div class="col-sm-8 col-xs-9">
							<div class="table-responsive abc">
								<table id="pjTbDateRangePriceTable" class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
									<thead>
										<tr>
											<th><?php __('lblFromDate'); ?></th>
											<th><?php __('lblToDate'); ?></th>
											<th><?php __('lblDateRangePrice'); ?></th>
											<th>&nbsp;</th>
										</tr>
									</thead>
									<tbody>
								
									</tbody>
								</table>
							</div><!-- /.table-responsive table-responsive-secondary -->
						</div><!-- /.col-sm-9 -->
						<div class="col-sm-4 col-xs-3">
							<div class="m-t-lg">
								<a href="#" class="btn btn-primary btn-outline m-t-xs btnAddDateRangePrice"><i class="fa fa-plus"></i> <?php __('btnAddPrice'); ?></a>
							</div>
						</div>
					</div><!-- /.row -->

					<div class="hr-line-dashed"></div>
					
					<div class="clearfix">
                        <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                            <span class="ladda-label"><?php __('btnSave'); ?></span>
                            <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                        </button>
                        <button type="button" class="btn btn-white btn-lg pull-left btnDeleteShape" style="display:none"><?php __('btnDeleteShape'); ?></button>
                        <a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminFleets&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                    </div><!-- /.clearfix -->
                </form>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>

<table id="pjTbPriceClone" style="display: none">
	<tbody>
		<tr class="pjTbPriceRow" data-index="{INDEX}">
			<td>
				<input type="text" name="start[{INDEX}]" id="start_{INDEX}" class="form-control pj-field-count digits required" maxlength="10" data-rule-smaller_than="#end_{INDEX}" data-msg-smaller_than="<?php __('lblToGreaterThanFrom');?>" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>			</td>
			<td>
				<input type="text" name="end[{INDEX}]" id="end_{INDEX}" class="form-control pj-field-count digits required" maxlength="10" data-rule-not_smaller_than="#start_{INDEX}" data-msg-not_smaller_than="<?php __('lblToGreaterThanFrom');?>" data-msg-digits="<?php __('pj_digits_validation');?>" data-msg-required="<?php __('tr_field_required'); ?>"/>
			</td>
			<td>
				<div class="input-group">
                    <input type="text" name="price[{INDEX}]" class="form-control required number" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>

                    <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                </div>
			</td>
			<td>
				<div class="input-group">
                    <input type="text" name="start_fee_r[{INDEX}]" class="form-control required number" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>

                    <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                </div>
			</td>
			<td>
				<div class="input-group">
                    <input type="text" name="time_rate_per_minute_r[{INDEX}]" class="form-control required number" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>

                    <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
                </div>
			</td>
			
			<td>
				<div class="m-t-xs text-right">
                    <a href="#" class="btn btn-danger btn-outline btn-sm btn-delete lnkRemovePrice" data-index="{INDEX}"><i class="fa fa-trash"></i></a>
                </div>
			</td>
		</tr>
	</tbody>
</table>

<table id="pjTbCityPriceClone" style="display: none">
	<tbody>
		<tr class="pjTbCityPriceRow" data-index="{INDEX}">
			<td>
				<span class="block overflow">
				<select name="from_city[{INDEX}]" id="from_city_{INDEX}" class="pj-form-field w180 pj-field-count required" data-msg-required="<?php __('tr_field_required'); ?>">
						<option value="">-- <?php __('lblChoose');?> --</option>
						<?php foreach ($tpl['city_arr'] as $city) { ?>
						<option value="<?php echo $city['id'];?>"><?php echo pjSanitize::html($city['name']);?></option>
					<?php } ?>
					</select>
				</span>
			</td>
			<td>
				<span class="inline-block">
				<select name="to_city[{INDEX}]" id="to_city_{INDEX}" class="pj-form-field w180 pj-field-count required" data-msg-required="<?php __('tr_field_required'); ?>">
					<option value="">-- <?php __('lblChoose');?> --</option>
					<?php foreach ($tpl['city_arr'] as $city) { ?>
						<option value="<?php echo $city['id'];?>"><?php echo pjSanitize::html($city['name']);?></option>
					<?php } ?>
				</select>
				</span>
			</td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></abbr></span>
					<input type="text" name="price[{INDEX}]" class="pj-form-field  required number w80" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation');?>"/>
				</span>
			</td>
			<td>
				<div class="m-t-xs text-right">
					<a href="#" class="btn btn-danger btn-outline btn-sm btn-delete lnkRemovePrice" data-index="{INDEX}"><i class="fa fa-trash"></i></a>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<table id="pjTbDateRangePriceClone" style="display:none">
	<tbody>
	<tr class="pjTbDateRangePriceRow" data-index="{INDEX}">
		<td style="padding-top: 10px; padding-right: 10px;">
			<div class="input-group">
				<input type="text" name="from_date[{INDEX}]" id="from_date_{INDEX}" class="form-control required datetimepick datetimepickdaterange"
					readonly data-msg-required="<?php __('tr_field_required'); ?>">
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			</div>
		</td>

		<td style="padding-top: 10px; padding-right: 10px;">
			<div class="input-group">
				<input type="text" name="to_date[{INDEX}]" id="to_date_{INDEX}" class="form-control datetimepick required datetimepickdaterange" data-wt="open" readonly="readonly" data-msg-required="<?php __('tr_field_required');?>">
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
			</div>
		</td>

		<td style="padding-top: 10px; padding-right: 10px;">
			<div class="input-group">
				<input type="text" name="price_date[{INDEX}]" class="form-control required number" data-msg-required="<?php __('tr_field_required'); ?>" data-msg-number="<?php __('pj_number_validation'); ?>">
				<span class="input-group-addon">
					<?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?>
				</span>
			</div>
		</td>

		<td class="text-right">
			<a href="#"
			   class="btn btn-danger btn-outline btn-sm lnkRemoveDateRangePrice"
			   data-index="{INDEX}">
				<i class="fa fa-trash"></i>
			</a>
		</td>
	</tr>
	</tbody>
</table>


<script type="text/javascript">
var myLabel = myLabel || {};
<?php if ($tpl['is_flag_ready']) : ?>
var pjCmsLocale = pjCmsLocale || {};
pjCmsLocale.langs = <?php echo $tpl['locale_str']; ?>;
pjCmsLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
<?php endif; ?>
myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
</script>