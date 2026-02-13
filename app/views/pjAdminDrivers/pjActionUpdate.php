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

$license_expiry = date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['license_expiry']));
$date_time = date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['dob']));

$licenseFiles = array_values(array_filter($tpl['arr']['files'], function ($file) {
    return $file['file_category'] === 'license';
}));

?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoUpdateDriverTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoUpdateClientDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>
<?php
$u_statarr = __('u_statarr', true)
?>

<div id="dateTimePickerOptionss" style="display:none;" data-timeformat="<?php echo $time_format=='HH:mm' ? 'HH:mm' : 'LT'; ?>" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-dateformat="<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?>" data-format="<?php echo pjUtil::toMomemtJS($tpl['option_arr']['o_date_format']); ?> <?php echo $time_format;?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<?php
    	$error_code = $controller->_get->toString('err');
    	if (!empty($error_code))
    	{
    	    $titles = __('error_titles', true);
    	    $bodies = __('error_bodies', true);
    	    switch (true)
    	    {
    	        case in_array($error_code, array('ADR01', 'ADR03')):
    	            ?>
    				<div class="alert alert-success">
    					<i class="fa fa-check m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
                case in_array($error_code, array('ADR04', 'ADR08')):
    				?>
    				<div class="alert alert-danger">
    					<i class="fa fa-exclamation-triangle m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
    		}
    	}
        
    	?>
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminDrivers&amp;action=pjActionUpdate" method="post" id="frmUpdateDriver" enctype="multipart/form-data">
                    <input type="hidden" name="driver_update" value="1" />
                	<input type="hidden" name="id" value="<?php echo (int) $tpl['arr']['id']; ?>" />
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('lblStatus'); ?></label>
                                <div class="clearfix">
                                    <div class="switch onoffswitch-data pull-left">
                                        <div class="onoffswitch">
                                            <input type="checkbox" class="onoffswitch-checkbox" id="status" name="status" checked>
                                            <label class="onoffswitch-label" for="status">
                                                <span class="onoffswitch-inner" data-on="<?php echo $u_statarr['T'];?>" data-off="<?php echo $u_statarr['F'];?>"></span>
                                                <span class="onoffswitch-switch"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div><!-- /.clearfix -->
                            </div><!-- /.form-group -->
                        </div><!-- /.col-md-3 -->

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('lblBookingTitle'); ?></label>

                                <select name="title" id="title" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                					<option value="">-- <?php __('lblChoose'); ?>--</option>
                					<?php
                					$name_titles = __('personal_titles', true, false);
                					 foreach ($name_titles as $k => $v)
                                            {
                                                ?><option value="<?php echo $k; ?>"<?php echo ($tpl['arr']['title'] == $k) ? ' selected' : ''; ?>><?php echo $v; ?></option><?php
                                            }
                					?>
                				</select>
                            </div>
                        </div><!-- /.col-md-3 -->

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('lblBookingFname'); ?></label>

                                <input type="text" id="first_name" value="<?php echo pjSanitize::html($tpl['arr']['first_name']); ?>" name="first_name" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                            </div>
                        </div><!-- /.col-md-3 -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('lblBookingLname'); ?></label>

                                <input type="text" id="last_name" value="<?php echo pjSanitize::html($tpl['arr']['last_name']); ?>" name="last_name" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                            </div>
                        </div><!-- /.col-md-3 -->
                    </div><!-- /.row -->

                    <div class="hr-line-dashed"></div>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('email'); ?></label>

								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-at"></i></span>
                                	<input type="text" value="<?php echo pjSanitize::html($tpl['arr']['email']); ?>" name="email" id="email" class="form-control email required" placeholder="info@domain.com" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                            </div>
                        </div><!-- /.col-md-3 -->

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('pass'); ?></label>
								
								<div class="input-group">
									
                                	<input type="text" value="<?php echo pjSanitize::html($tpl['arr']['password']); ?>" name="password" id="password" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"><span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                </div>
                            </div>
                        </div><!-- /.col-md-3 -->

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('lblPhone'); ?></label>
								
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                	<input type="text" value="<?php echo pjSanitize::html($tpl['arr']['phone']); ?>" name="phone" id="phone" class="form-control" placeholder="(123) 456-7890">
                                </div>
                            </div>
                        </div><!-- /.col-md-3 -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('lblBookingAddress'); ?></label>
                                <input type="text" value="<?php echo pjSanitize::html($tpl['arr']['address']); ?>" name="address" id="address" class="form-control">
                            </div>
                        </div><!-- /.col-md-3 -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('lblBookingZip'); ?></label>

                                <input type="text" name="zip" id="zip" value="<?php echo pjSanitize::html($tpl['arr']['zip']); ?>" class="form-control" maxlength="255">
                            </div>
                        </div><!-- /.col-md-3 -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('lblBookingState'); ?></label>

                                <input type="text" value="<?php echo pjSanitize::html($tpl['arr']['state']); ?>" name="state" id="state" class="form-control">
                            </div>
                        </div><!-- /.col-md-3 -->
                    
                          <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('driverLicenseNumber'); ?></label>

                                <input type="text" value="<?php echo pjSanitize::html($tpl['arr']['license_number']); ?>" name="license_number" id="license_number" class="form-control">
                            </div>
                        </div><!-- /.col-md-3 -->                   
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('driverLicenseExpiry'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" value="<?php echo $license_expiry; ?>" name="license_expiry" id="license_expiry" class="form-control datetimepick required" data-wt="open" readonly="readonly">
                                </div>
                            </div>
                        </div><!-- /.col-md-3 -->

                    
                        
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('driverNationalIDNumber'); ?></label>

                                <input type="text" value="<?php echo pjSanitize::html($tpl['arr']['national_id_number']); ?>" name="national_id_number" id="national_id_number" class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('driverVehicle_id'); ?></label>

                                <input type="text" value="<?php echo pjSanitize::html($tpl['arr']['vehicle_id']); ?>" name="vehicle_id" id="vehicle_id" class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label">Date of Birth <?php __('lblDriverDOB'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" value="<?php echo $date_time; ?>" name="dob" id="dob" class="form-control datetimepick_dob required" data-wt="open" readonly="readonly" data-format="YYYY-MM-DD">
                                </div>
                            </div>
                        </div><!-- /.col-md-3 -->

                        
                        
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('driverNotes'); ?></label>
                                <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo pjSanitize::html($tpl['arr']['notes']); ?></textarea>
                            </div>
                        </div>
          
                      
                       
                    </div><!-- /.row -->
                    <div class="row">
                         <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="control-label"><?php __('driverLicenseFile'); ?></label>
                                <input type="file" value="" name="license_file" id="license_file" class="form-control">
                               <!-- Download Button (Top-left) -->
                                <div class="file-thumb" style="display: inline-block;position: relative; margin: 5px;">
                                    <img src="<?php echo PJ_UPLOAD_PATH . $licenseFiles[0]['thumb_path']; ?>" class="img-thumbnail" style="width: 80px; height: auto;">
                                                <!-- Download Button Start-->
                                    <a href="<?php echo PJ_UPLOAD_PATH . $licenseFiles[0]['source_path']; ?>" download title="Download" style="position: absolute; top: 0px; left: 0px; background-color: #3498db; color: #fff; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                    <i class="fa fa-cloud-download"></i> </a>
                                </div>
                            </div>
                        </div><!-- /.col-md-3 -->

                        <div class="col-lg-8 col-md-8 col-sm-6">
                            <label for="driver_documents">Upload Other Documents</label>
                            <input type="file" name="driver_documents[]"id="driver_documents" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" multiple>
                            <small class="form-text text-muted">
                                Allowed formats: JPG, PNG, PDF, DOC. You can select multiple files.
                             <?php foreach ($tpl['arr']['files'] as $file): ?>
                                    <?php if ($file['file_category'] !== 'license'): ?>
                                        <div class="file-thumb" id="file-<?php echo $file['id']; ?>" style="display: inline-block;position: relative; margin: 5px;">
                                            <img src="<?php echo PJ_UPLOAD_PATH . $file['source_path']; ?>" class="img-thumbnail" style="width: 80px; height: auto;">
                                              <!-- Download Button Start-->
                                            <a href="<?php echo PJ_UPLOAD_PATH . $file['source_path']; ?>" download title="Download" style="position: absolute; top: 0px;left: 0px; background-color: #3498db; color: #fff; width: 16px; height: 16px;  border-radius: 50%; font-weight: bold; font-size: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.2);transition: background 0.3s ease;"><i class="fa fa-cloud-download"></i></a>
                                             <!-- Download Button End -->
                                            <button value="<?php echo $file['id']; ?>" class="image_btn" style="position: absolute; top: 0px; right: 0px; background-color: #e74c3c; color: #fff; width: 16px; height: 16px; border: none; border-radius: 50%; font-weight: bold; font-size: 16px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: background 0.3s ease;"> &times; </button>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </small>
                        </div>
                    </div>                     
                    <div class="hr-line-dashed"></div>

                    <div class="clearfix">
                        <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                            <span class="ladda-label"><?php __('btnSave'); ?></span>
                            <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                        </button>
                        <a class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminDrivers&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                    </div><!-- /.clearfix -->
                </form>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.email_exists = <?php x__encode('email_taken'); ?>;
myLabel.choose = <?php x__encode('lblChoose'); ?>;
</script>