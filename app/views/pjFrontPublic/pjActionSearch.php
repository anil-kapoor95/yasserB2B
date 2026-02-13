<?php
//include_once dirname(__FILE__) . '/elements/header.php';
$SEARCH = @$_SESSION[$controller->defaultStore]['search'];
$months = __('months', true);
$short_days = __('short_days', true);
ksort($months);
ksort($short_days);
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;

// echo "<pre>"; print_r($SEARCH); echo "</pre>";
?>
<style type="text/css">
.btn {
    font-weight: 600 !important;
    text-transform: capitalize !important;
}
.btn:focus {
    outline: none !important;
}

.booking-sec-main .col-sm-12.col-xs-12 .xs-0 {
    padding-left: 0;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-pas {
    width: 15%;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-pas input.pjTbs-spinner-result.digits {
    max-width: 52px !important;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-5 .col-xs-12 {
    padding-right: 0;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-6 {
    padding-left: 15px;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-6 .pjTbs-distance input#pjTbsDistanceFiled {
    min-width: 85px !important;

}
.booking-sec-main .col-sm-12.col-xs-12 .xs-6 {
    padding-left: 15px;
    padding-right: 0;
    width: 10.3333%;
}
.booking-sec-main .col-sm-12.col-xs-12 span.input-group-addon {
    color: #feba00 !important;
}
.booking-sec-main .pjTbs-body-actions input.btn.btn-primary {
    background: #feba00 !important;
    width: 100%;
    border-color: #feba00 !important;
}
.booking-sec-main .pjTbs-body-actions input.btn.btn-primary:hover {
    background: #ffc118 !important;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-pic {
    width: 18%;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-5 {
    width: 24%;
}

.booking-sec-main .col-sm-12.col-xs-12 .xs-7 {
    padding-right: 0;
    width: 14.6666%;
    padding-left: 15px;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-7 .pjTbs-body-actions input.btn.btn-primary {
    min-width: unset !important;
    font-size: 13px !important;
    margin-top: 12px;
    height: 40px !important;
}
.booking-sec-main .col-sm-12.col-xs-12 label {
    font-size: 15px !important;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-8.return-date-ai {
    display: flex;
    flex-direction: column;
    width: 10%;
    padding-left: 15px;
    padding-right: 0px;
    margin-top: 10px;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-8.return-date-ai .form-group {
    float: left;
    margin: 0;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-8.return-date-ai label.control-label {
    float: left;
    margin-right: 15px;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-8.return-date-ai .form-group input#pjTbsreturn_date {
    height: 20px;
    width: 19px;
}
.booking-sec-main .col-sm-12.col-xs-12 .date-pick-main.pjTbsReturn_date_time {
    padding: 0;
    width: 70%;
}

.booking-sec-main .pjTbs-box input {
    border-radius: 0px;
}
.booking-sec-main .pjTbs-box .input-group span {
    border-radius: 0 !important;
}
.booking-sec-main .pjTbs-box button.btn {
    border-radius: 0px !important;
}
.booking-sec-main .pjTbs-box input.pjTbs-spinner-result.digits {
    background: #f0f0f0;
}
.booking-sec-main .pjTbs-box {
    border-radius: 0px !important;
}

@media only screen and (min-width: 100px) and (max-width: 767px) {
	.booking-sec-main .col-sm-12.col-xs-12 .xs-pas.xs-3 {
    width: 100% !important;
}
	.booking-sec-main .col-sm-12.col-xs-12 .xs-0 {
  
    padding-right: 15px;
}
	.booking-sec-main .col-sm-12.col-xs-12 .date-pick-main.pjTbsReturn_date_time {
    padding: 0 15px 0 15px;
    width: 100%;
}
	.booking-sec-main .col-sm-12.col-xs-12 .xs-8.return-date-ai {
    
   
    padding-right: 0px;
    margin-top: 0px;
}
 .booking-sec-main .col-sm-12.col-xs-12 .xs-0 {
    padding-left: 15px;
    width: 100% !important;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-pas button.btn {
    width: 28px !important;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-pas input.pjTbs-spinner-result.digits {
    max-width: 33px !important;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-pas {
    width: 50% !important;
}
.booking-sec-main .pjTbs-body-actions input.btn.btn-primary {
  
    width: 100%;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-5 .col-md-6 {
    width: 50%;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-5 .row {
    padding-right: 15px !important;
}
.booking-sec-main .pjTbs-box-title {
  
    padding-left: 15px;
}
span.distance {
    padding-right: 0;
    width: 100%;
    float: left;
}
.pjtbs-car .pjTbs-price {
    width: 100%;
    text-align: center;
}
}
@media only screen and (min-width: 768px) and (max-width: 1024px) {

.booking-sec-main .col-sm-12.col-xs-12 .xs-pic {
    width: 35%;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-3 {
    width: unset;
    float: right;
    display: inline-table;
    padding-right: 0;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-5 {
    width: 45%;
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-8.return-date-ai {
    width: 21%;
   
}
.booking-sec-main .col-sm-12.col-xs-12 .xs-7 {
    
    width: 34%;
   
}
.pjTbsReturn_date_time .date-pick-main-inner {
    width: 46%;
}
.booking-sec-main .col-sm-12.col-xs-12 .date-pick-main.pjTbsReturn_date_time {
  
    width: 66%;
}
	}
	
	@media (max-width: 767.98px) {
  .pjTbs-box, .pjTbs-box .main-searchbar {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
}
	
.return-date-ai .toggle-wrap { overflow: visible; }

.return-date-ai .input-group > span.button-color-ai { display: none !important; }

/* Toggle container */
.return-date-ai .toggle {
  position: relative;
  display: inline-flex;
  align-items: center;
  line-height: 1;
  cursor: pointer;
}

/* Visually hide native checkbox but keep it accessible */
#pjTbsreturn_date {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

/* The visual switch track */
#pjTbsreturn_date + .toggle-ui {
  position: relative;
  display: inline-block;
  width: 58px;
  height: 32px;
  background: #e9ecef;
  border-radius: 9999px !important;             /* force pill shape */
  border: 1px solid rgba(0,0,0,0.08);
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.12);
  transition: background-color .25s ease, box-shadow .25s ease;
  vertical-align: middle;
}

/* The knob */
#pjTbsreturn_date + .toggle-ui::after {
  content: "";
  position: absolute;
  top: 3px;
  left: 3px;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.24);
  transition: transform .25s ease;
}

/* Checked state */
#pjTbsreturn_date:checked + .toggle-ui {
  background: #22c55e;                          /* active green */
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.10);
}

#pjTbsreturn_date:checked + .toggle-ui::after {
  transform: translateX(26px);
}

/* Focus ring for keyboard users */
#pjTbsreturn_date:focus-visible + .toggle-ui {
  outline: 3px solid rgba(34,197,94,0.35);
  outline-offset: 2px;
}

/* Optional: slightly larger on touch devices */
@media (pointer: coarse) {
  #pjTbsreturn_date + .toggle-ui { width: 64px; height: 36px; }
  #pjTbsreturn_date + .toggle-ui::after { width: 30px; height: 30px; }
  #pjTbsreturn_date:checked + .toggle-ui::after { transform: translateX(30px); }
}

</style>
<div class="booking-sec-main">
<div class="e-con-inner">	

<div class="pjTbs-body">
	<form id="pjTbsSearchForm_<?php echo $controller->_get->toString('index');?>" action="#" method="post" class="pjTbsSearchForm">

		<input type="hidden" name="tbs_search" value="1" />
		<input type="hidden" name="o_search_result_redirect_id" id="o_search_result_redirect_id" value="<?php echo isset($tpl['option_arr']['o_search_result_redirect']) ? $tpl['option_arr']['o_search_result_redirect'] : ''; ?>" />

		<input type="hidden" name="from_city" id="from_city_<?php echo $controller->_get->toString('index');?>" value="<?php echo isset($SEARCH['from_city']) ? stripslashes($SEARCH['from_city']) : '';?>" />
		<input type="hidden" name="to_city" id="to_city_<?php echo $controller->_get->toString('index');?>" value="<?php echo isset($SEARCH['to_city']) ? stripslashes($SEARCH['to_city']) : '';?>" />

		<div id="pjTbsCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>" data-fday="<?php echo $week_start;?>"></div>
		<div class="pjTbs-box ">
			
	
			<div class="row">
				<div class="col-sm-12 col-xs-12 main-searchbar">
					<div class="col-sm-2 col-xs-12 xs-0 xs-1 xs-pic">
					<div class="form-group">
						<label class="control-label"><?php __('front_pickup_address');?></label>
	
						<input style="border-radius: 8px;" type="text" id="pickup_address_<?php echo $controller->_get->toString('index');?>" name="pickup_address" value="<?php echo isset($SEARCH['pickup_address']) ? pjSanitize::clean($SEARCH['pickup_address']) : NULL;?>" class="form-control required pjTbsAddress" data-msg-required="<?php __('front_required_field');?>">
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div>
				<div class="col-sm-2 col-xs-12 xs-0 xs-2 xs-pic">
					<div class="form-group">
						<label class="control-label"><?php __('front_dropoff_address');?></label>
	
						<input style="border-radius: 8px;" type="text" id="return_address_<?php echo $controller->_get->toString('index');?>" name="return_address" value="<?php echo isset($SEARCH['return_address']) ? pjSanitize::clean($SEARCH['return_address']) : NULL;?>" class="form-control required pjTbsAddress" data-msg-required="<?php __('front_required_field');?>">
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div>
				  <div class="col-sm-2 col-xs-12 xs-0 xs-pas xs-3">
					<label><?php __('front_passengers');?></label>
					
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon" style="border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important; border-top-right-radius: 0; border-bottom-right-radius: 0">
								<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
							</span>
					
							<div class="btn-group pjTbs-spinner" role="group" aria-label="...">
					            <button type="button" class="btn pjTbs-spinner pjTbs-spinner-down">-</button>
					
								<input type="text" name="passengers" class="pjTbs-spinner-result digits" maxlength="3" value="<?php echo isset($SEARCH['passengers']) ? $SEARCH['passengers'] : 1;?>" data-msg-digits="<?php __('front_digits_validation');?>">
					
								<button type="button" class="btn pjTbs-spinner pjTbs-spinner-up" style="border-top-left-radius: 0px; border-bottom-left-radius: 0px; border-top-right-radius: 8px !important; border-bottom-right-radius: 8px !important">+</button>
							</div>
						</div>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group --><!-- /.col-sm-6 -->
				</div>
				<div class="col-sm-2 col-xs-12 xs-0 xs-pas xs-4" style="display: none;">
					<label><?php __('front_pieces_of_luggage');?></label>
					
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span>
							</span>
							<div class="btn-group pjTbs-spinner" role="group" aria-label="...">
					            <button type="button" class="btn pjTbs-spinner pjTbs-spinner-down">-</button>
								<input type="text" name="luggage" class="pjTbs-spinner-result digits" maxlength="3" value="<?php echo isset($SEARCH['luggage']) ? $SEARCH['luggage'] : NULL;?>" data-msg-digits="<?php __('front_digits_validation');?>">
					
								<button type="button" class="btn pjTbs-spinner pjTbs-spinner-up">+</button>
							</div>
						</div>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div>
				</div>
	
				<div class="col-sm-3 col-xs-12 xs-0 xs-5">
					<div class="row">
						<div class="col-md-6 col-sm-7 col-xs-12">
							<div class="form-group">
							    <label class="control-label">Pick-up Date</label>
								<div class="input-group date-pick" style="border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important; border-top-right-radius: 0; border-bottom-right-radius: 0">
									<span class="input-group-addon" style="border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important; border-top-right-radius: 0; border-bottom-right-radius: 0">
										<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
									</span>
			
									<input type="text" name="booking_date" value="<?php echo isset($SEARCH['booking_date']) ? $SEARCH['booking_date'] : NULL;?>" class="form-control required" readonly="readonly" data-msg-required="<?php __('front_required_field');?>" style="border-top-left-radius: 0px; border-bottom-left-radius: 0px; border-top-right-radius: 8px !important; border-bottom-right-radius: 8px !important"/>
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						</div><!-- /.col-sm-6 -->
	
						<div class="col-md-6 col-sm-5 col-xs-12">
							<div class="form-group">
							    <label class="control-label">Pick-up Time</label>
								<div class="input-group time-pick">
									<span class="input-group-addon" style="border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important; border-top-right-radius: 0; border-bottom-right-radius: 0">
										<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
									</span>
	
									<input type="text" name="booking_time" value="<?php echo isset($SEARCH['booking_time']) ? $SEARCH['booking_time'] : NULL;?>" class="form-control required" readonly data-msg-required="<?php __('front_required_field');?>" style="border-top-left-radius: 0px; border-bottom-left-radius: 0px; border-top-right-radius: 8px !important; border-bottom-right-radius: 8px !important"/>
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						</div><!-- /.col-sm-6 -->
					</div><!-- /.row -->
				</div><!-- /.col-sm-6 -->
				<div class="col-sm-1 col-xs-12 xs-0 xs-8 return-date-ai">
				  <label class="control-label">
				    <span class="glyphicon"></span> Return <?php __('lblreturnbooking'); ?>
				  </label>

				  <div class="form-group">
				    <div class="input-group toggle-wrap">
				      <!-- Keep your original checkbox exactly the same -->
				      <label class="toggle">
				        <input
				          type="checkbox"
				          name="return_status"
				          id="pjTbsreturn_date"
				          value="<?php echo (isset($SEARCH['return_status']) && $SEARCH['return_status'] == 1) ? 1 : 0; ?>" 
				          class="button-color-ai border-color-control"
				          <?php echo (isset($SEARCH['return_status']) && $SEARCH['return_status'] == 1) ? 'checked="checked"' : ''; ?>
				        />
				        <span class="toggle-ui" aria-hidden="true"></span>
				      </label>
				    </div>
				  </div><!-- /.form-group -->
				</div><!-- /.col-sm-1 -->

				 <input type="hidden" id="pjTbsDurationInMinFiled" name="durationInMin" value="<?php echo isset($SEARCH['durationInMin']) ? $SEARCH['durationInMin'] : NULL;?>"/>
						<input type="hidden" id="pjTbsDistanceFiled" name="distance" name="booking_date" value="<?php echo isset($SEARCH['distance']) ? $SEARCH['distance'] : NULL;?>" class="required number" data-msg-required="<?php __('front_required_field');?>" data-msg-number="<?php __('front_number_validation');?>" readonly="readonly"/>


				<!-- <div class="col-sm-1 col-xs-12 xs-0 xs-6">
					<label class="control-label"><?php // __('front_distance');?>:</label>
					<div class="pjTbs-distance">
                        <input type="hidden" id="pjTbsDurationInMinFiled" name="durationInMin" value="<?php // echo isset($SEARCH['durationInMin']) ? $SEARCH['durationInMin'] : NULL;?>"/>
						<input type="text" id="pjTbsDistanceFiled" name="distance" name="booking_date" value="<?php // echo isset($SEARCH['distance']) ? $SEARCH['distance'] : NULL;?>" class="required number" data-msg-required="<?php // __('front_required_field');?>" data-msg-number="<?php // __('front_number_validation');?>" readonly="readonly"/> km</div>
					<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
				</div> -->

				<!-- Retun Address  -->

				<div class="col-sm-12 col-xs-12 date-pick-main pjTbsReturn_date_time" style="<?php echo (isset($SEARCH['return_status']) && $SEARCH['return_status'] == 1) ? 'display: block;' : 'display: none;'; ?>">

					<!--<label class="control-label"><?php __('lblReturnDateTime');?></label>-->
					<div class="row">
						<div class="col-md-6 col-sm-7 col-xs-6 date-pick-main-inner">
						    <label class="control-label">Return Date</label>
							<div class="form-group">
								<div class="input-group date-pick">
								<span class="input-group-addon border-color-control" style="border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important; border-top-right-radius: 0; border-bottom-right-radius: 0">
										<span class="glyphicon glyphicon-calendar button-color-ai" aria-hidden="true"></span>
									</span>		
									<input type="text" name="return_date" value="<?php echo isset($SEARCH['return_date']) ? $SEARCH['return_date'] : NULL;?>" class="form-control border-color-control pjTbReturn_date_val border-color-control" readonly="readonly" data-msg-required="<?php __('front_required_field');?>" style="border-top-left-radius: 0px; border-bottom-left-radius: 0px; border-top-right-radius: 8px !important; border-bottom-right-radius: 8px !important"/>
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
								<span id="mess-slot-unavailable-return" class="mess-slot-unavailable" style="color: #fff; display: none;">
								    <?php echo __('error_booking_unavailable'); ?>
								</span>
								
							</div><!-- /.form-group -->
						</div><!-- /.col-sm-6 -->
						<div class="col-md-6 col-sm-6 col-xs-6 time-pick-main-inner">
						    <label class="control-label">Return Time</label>
							<div class="form-group">
								<div class="input-group time-pick">
									<span class="input-group-addon border-color-control" style="border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important; border-top-right-radius: 0; border-bottom-right-radius: 0">
										<span class="glyphicon glyphicon-time button-color-ai" aria-hidden="true"></span>
									</span>
									<input type="text" name="return_time" value="<?php echo isset($SEARCH['return_time']) ? $SEARCH['return_time'] : NULL;?>" class="form-control border-color-control" data-msg-required="<?php __('front_required_field');?>" style="border-top-left-radius: 0px; border-bottom-left-radius: 0px; border-top-right-radius: 8px !important; border-bottom-right-radius: 8px !important"/>
								</div>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						</div><!-- /.col-sm-5 -->
					</div><!-- /.row -->
				</div><!-- /.col-sm-6 -->
				<!-- End return address -->
				<div class="col-sm-1 col-xs-12 xs-0 xs-7">
				<div class="pjTbs-body-actions">		
				<input value="<?php __('front_btn_book_a_taxi');?>" class="btn btn-primary" type="submit">
			</div>
		</div>
			</div><!-- /.row -->
	
			
	
			<!--<div class="pjTbs-map" id="pjTbsMapCanvas"></div>-->
		</div><!-- /.pjTbs-box -->
	</form>
	
	
</div><!-- /.pjTbs-body -->
</div>
</div>



