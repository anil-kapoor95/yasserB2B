<?php
// include_once dirname(__FILE__) . '/elements/header.php';
$SEARCH = @$_SESSION[$controller->defaultStore]['search'];
// print_r($SEARCH);
$FORM = @$_SESSION[$controller->defaultForm];
$months = __('months', true);
$short_days = __('short_days', true);
ksort($months);
ksort($short_days);
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;


// $airport_keywords = [
//     'innsbruck airport', 'innsbruck flughafen',
//     'salzburg airport', 'salzburg flughafen',
//     'munich airport', 'münchen flughafen', 'muenchen flughafen',
//     'memmingen airport', 'memmingen flughafen',
//     'zurich airport', 'zürich flughafen', 'zuerich flughafen',
//     'engadin airport', 'samedan st. moritz flughafen',
//     'verona airport', 'verona flughafen',
//     'bolzano airport', 'bozen flughafen',
//     'munich international airport',
//     'munich international airport (MUC)',
//     'munich airport muc',
// 	'munich international airport muc',
// 	'münchen flughafen muc',
// 	'münchen international flughafen muc',
// 	'muenchen flughafen muc',
// 	'muenchen international flughafen muc'
// ];

$airport_keywords = [
    'innsbruck airport', 'innsbruck flughafen',
	'Salzburg airport', 'salzburg flughafen', 'Flughafen Salzburg (SZG)',
	'Innsbrucker Bundesstraße', 'Salzburg', 'salzburg',
	'Flughafen Salzburg', 'salzburg flughafen',
	'munich airport', 'münchen flughafen', 'muenchen flughafen',
	'memmingen airport', 'memmingen flughafen',
	'zurich airport', 'zürich flughafen', 'zuerich flughafen',
	'engadin airport', 'samedan st. moritz flughafen',
	'verona airport', 'verona flughafen',
	'bolzano airport', 'bozen flughafen',
	'munich international airport',
	'munich international airport (muc)',
	'munich airport muc',
	'munich international airport muc',
	'münchen flughafen muc',
	'münchen international flughafen muc',
	'muenchen flughafen muc',
	'muenchen international flughafen muc',
	'Flughafen München (MUC)', 'München-Flughafen',
];
 
// $pickup_lower = strtolower($SEARCH['pickup_address']);
// $return_lower = strtolower($SEARCH['return_address']);
// $has_airport = false;
// foreach ($airport_keywords as $keyword) {
//     if (strpos($pickup_lower, $keyword) !== false || strpos($return_lower, $keyword) !== false) {
//         $has_airport = true;
//         break;
//     }
// }

$pickup_lower = mb_strtolower($SEARCH['pickup_address'], 'UTF-8');
$return_lower = mb_strtolower($SEARCH['return_address'], 'UTF-8');

$pickup_has_airport = false;
$return_has_airport = false;

foreach ($airport_keywords as $keyword) {

    $keyword_lower = mb_strtolower($keyword, 'UTF-8');

    if (!$pickup_has_airport && strpos($pickup_lower, $keyword_lower) !== false) {
        $pickup_has_airport = true;
    }

    if (!$return_has_airport && strpos($return_lower, $keyword_lower) !== false) {
        $return_has_airport = true;
    }
}
?>
<style type="text/css">


.pjTbs-box {
    border-radius: 20px !important;
    background: #F6F6F6 !important;
    padding: 20px !important;
    border: 1px solid #e2e2e2;
    margin-top: 20px !important;
}

.row-flex input.form-control, .row-flex textarea.form-control, .row-flex select.form-control {
    border-radius: 8px;
}
.pjTbs-extras input {
    border-radius: 0px !important;
}


.row-flex span.input-group-addon {
    border-radius: 0 !important;
}	
.row-flex .btn-group.pjTbs-spinner button.btn.pjTbs-spinner {
   border-radius: 0;
}
.row-flex .btn-group.pjTbs-spinner input {
    background: #f0f0f0;
}	
.flag-bx-fld-min {
    position: relative;
}
#pjWrapperTaxiBooking_theme1 .form-control {
    height: 40px;
    font-size: 15px;
    border-color: rgba(0,0,0,0.1);
    padding: 5px;
}
.flag-bx-fld-min input.flag-bx {
    position: absolute;
    width: 41px;
    padding: 5px !important;
}
.flag-bx-fld-min input.flag-fld {
    padding-left: 50px !important;
}

.pjTbs-service-list-row {
    border-top-: 1px solid white !important;
}
.pjTbs-service-list {
    background: #212121;
    padding: 20px;
    border-radius: 20px;
    color: #ffffff;
}


@media only screen and (min-width: 100px) and (max-width: 767px) {

.row-flex-lft {
    order: 2;
}
.Flight-bx-rt {
    order: 1;
}
.row.row-flex {
    display: flex;
    flex-wrap: wrap;
}
.show-personal-details-first {
    flex-direction: column-reverse;
}
}




</style>
<div class="pjTbs-body">
	<form id="pjTbsCheckoutForm_<?php echo $controller->_get->toString('index');?>" action="#" method="post" class="pjTbsCheckoutForm">
		<input type="hidden" name="lbs_checkout" value="1" />
		<div id="pjTbsCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>" data-fday="<?php echo $week_start;?>"></div>
		
		<div class="pjTbs-service-list">
			<div class="pjTbs-service-list-row">
				<div class="row">
					<div class="col-sm-5 col-xs-12">
						<p><?php __('front_pickup_address');?>:</p>

						<p><strong><?php echo $SEARCH['pickup_address'];?> </strong></p>

						<p><small><?php echo $SEARCH['booking_date'];?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($SEARCH['booking_time']));?></small>

						<?php if (!empty($SEARCH['return_date']) && !empty($SEARCH['return_time'])): ?>
					   <?php __('lblReturnDateTime');?> : <?php echo $SEARCH['return_date'];?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($SEARCH['return_time']));?></p>
					<?php endif; ?>

					</div><!-- /.col-sm-5 -->

					<div class="col-sm-4 col-xs-12">
						<p><?php __('front_dropoff_address');?>:</p>
						
						<p><strong><?php echo $SEARCH['return_address'];?></strong></p>
					</div><!-- /.col-sm-4 -->

					<div class="col-sm-3 col-xs-12">
						<p><?php __('front_distance');?>:</p>
						
						<p><strong><?php echo $SEARCH['distance'];?> km</strong></p>
					</div><!-- /.col-sm-3 -->
				</div><!-- /.row -->
			</div><!-- /.pjTbs-service-list-row -->
			<?php
			$with_str = (int) $SEARCH['luggage'] >= 1 ? __('front_with_desc', true) : __('front_with_desc_2', true);
			$with_str = str_replace("{PASSENGERS}", $SEARCH['passengers'], $with_str);
			$with_str = str_replace("{LUGGAGE}", $SEARCH['luggage'], $with_str);
			
            $duration = isset($SEARCH['durationInMin']) ? (int) $SEARCH['durationInMin'] : 0;
            $ratePerMin = isset($tpl['fleet_arr']['time_rate_per_minute_r']) ? (float) $tpl['fleet_arr']['time_rate_per_minute_r'] : 0;
             $overbooking_cost = $tpl['fleet_arr']['overbooking_cost'] ?? 0;

			$dateRangePrice = $tpl['from_daterange']['price'] ?? 0;

			$returndateRangePrice = $tpl['return_daterange']['price'] ?? 0;
             if (isset($tpl['fleet_price_arr']) && (float)$tpl['fleet_price_arr'] > 0) {
				// $total_price = $tpl['fleet_price_arr']['price'];
				 $priceHikePercent = $tpl['fleet_arr']['price_hike'] ?? 0;
				 $total_price = $tpl['fleet_price_arr']['price'];
				 $hikeAmount = ($total_price * $priceHikePercent) / 100;
				 $total_price += $hikeAmount;
			} else {
				
				$total_price = $tpl['fleet_arr']['start_fee_r'] + $SEARCH['passengers'] * $tpl['fleet_arr']['fee_per_person'] + $tpl['fleet_arr']['price'];
				$total_price += $duration * $ratePerMin;
				}

			
				$allowedBooking = (int) floor((float)$tpl['fleet_arr']['numberof_booking']);
				$totalBooking   = (int) $tpl['totalBooking'];
  					// print_r($allowedBooking );  echo "---"; print_r($totalBooking );
				if ($allowedBooking > 0 && $totalBooking >= $allowedBooking)
				{
				    $total_price += $overbooking_cost;
				}
				
			if($SEARCH['return_status'] == 1 ){
				$total_price = $total_price*2;
			}

			$total_price += $dateRangePrice;
            $total_price += $returndateRangePrice;  

			?>
			<div class="pjTbs-service-list-row">
				<div class="row">
					<div class="col-sm-5 col-xs-12">
						<p><?php __('front_with');?>:</p>
						
						<p><em><?php echo $with_str;?> </em></p>
					</div><!-- /.col-sm-5 -->

					<div class="col-sm-4 col-xs-12">
						<p><?php __('front_ride');?>:</p>
						
						<p><em><?php echo pjSanitize::clean($tpl['fleet_arr']['fleet']);?> </em></p>
					</div><!-- /.col-sm-4 -->

					<div class="col-sm-3 text-right">
						<p><?php __('front_price');?>:</p>
						<input type="hidden" name="net_total" value="<?php echo pjCurrency::formatPrice($total_price)?>">

						<p><strong><?php echo pjCurrency::formatPrice($total_price);?></strong></p>
					</div><!-- /.col-sm-3 -->
				</div><!-- /.row -->
			</div><!-- /.pjTbs-service-list-row -->
		</div><!-- /.pjTbs-service-list -->
		
		<div class="row row-flex show-personal-details-first">
			<div class="col-sm-6 col-xs-12 row-flex-lft">
				<div class="pjTbs-box">
					<div class="pjTbs-box-title"><?php __('front_personal_details');?></div><!-- /.pjTbs-box-title -->
					<?php
					if(!$controller->isFrontLogged())
					{
						$login_message = __('front_login_message', true);
						$login_message = str_replace("{STAG}", '<a href="#" class="pjCssLogin">', $login_message);
						$login_message = str_replace("{ETAG}", '</a>', $login_message);
						?>
						<!-- <div class="row">
							<div class="col-sm-12">
								<div class="form-group"><label><?php // echo $login_message;?></label></div>
							</div>
						</div> -->
						<?php
					}else{
						$logout_message = __('front_logout_message', true);
						$logout_message = str_replace("{STAG}", '<a href="#" class="pjCssLogout">', $logout_message);
						$logout_message = str_replace("{ETAG}", '</a>', $logout_message);
						?>
						<!-- <div class="row">
							<div class="col-sm-12">
								<div class="form-group"><label><?php // echo $logout_message;?></label></div>
							</div>
						</div> -->
						<?php
					}
					$CLIENT = $controller->isFrontLogged() ? $_SESSION[$controller->defaultClient] : array();
					
					if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)))
					{
						?>
						<div class="form-group">
							<label><?php __('front_title'); ?></label>
	
							<select name="c_title" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_title'] == 3) ? ' required' : NULL; ?>" data-msg-required="<?php __('front_required_field');?>">
								<option value="">----</option>
								<?php
								$title_arr = pjUtil::getTitles();
								$name_titles = __('personal_titles', true, false);
								foreach ($title_arr as $v)
								{
									?><option value="<?php echo $v; ?>"<?php echo isset($FORM['c_title']) && $FORM['c_title'] == $v ? ' selected="selected"' : (isset($CLIENT['title']) ? ($CLIENT['title'] == $v ? ' selected="selected"' : NULL ) : NULL); ?>><?php echo $name_titles[$v]; ?></option><?php
								}
								?>
							</select>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					} 
					if (in_array($tpl['option_arr']['o_bf_include_fname'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_fname'); ?></label>
							
							<input type="text" name="c_fname" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_fname'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_fname']) ? pjSanitize::clean($FORM['c_fname']) : (isset($CLIENT['fname']) ? pjSanitize::clean($CLIENT['fname']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_lname'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_lname'); ?></label>
							
							<input type="text" name="c_lname" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_lname'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_lname']) ? pjSanitize::clean($FORM['c_lname']) : (isset($CLIENT['lname']) ? pjSanitize::clean($CLIENT['lname']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3))){
						?>
						<div class="form-group flag-bx-fld">
							<label><?php __('front_phone'); ?></label>
							<div class="flag-bx-fld-min_rems">
							
							<input type="text" name="c_phone" class="flag-fld form-control<?php echo ($tpl['option_arr']['o_bf_include_phone'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_phone']) ? pjSanitize::clean($FORM['c_phone']) : (isset($CLIENT['phone']) ? pjSanitize::clean($CLIENT['phone']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
						</div>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_email'); ?></label>
							
							<input type="text" name="c_email" class="form-control email<?php echo ($tpl['option_arr']['o_bf_include_email'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_email']) ? pjSanitize::clean($FORM['c_email']) : (isset($CLIENT['email']) ? pjSanitize::clean($CLIENT['email']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>" data-msg-email="<?php __('front_email_validation');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_company'); ?></label>
							
							<input type="text" name="c_company" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_company'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_company']) ? pjSanitize::clean($FORM['c_company']) : (isset($CLIENT['company']) ? pjSanitize::clean($CLIENT['company']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_address'); ?></label>
							
							<input type="text" name="c_address" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_address'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_address']) ? pjSanitize::clean($FORM['c_address']) : (isset($CLIENT['address']) ? pjSanitize::clean($CLIENT['address']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_city'); ?></label>
							
							<input type="text" name="c_city" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_city'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_city']) ? pjSanitize::clean($FORM['c_city']) : (isset($CLIENT['city']) ? pjSanitize::clean($CLIENT['city']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_state'); ?></label>
							
							<input type="text" name="c_state" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_state'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_state']) ? pjSanitize::clean($FORM['c_state']) : (isset($CLIENT['state']) ? pjSanitize::clean($CLIENT['state']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3))){
						?>
						<div class="form-group">
							<label><?php __('front_zip'); ?></label>
							
							<input type="text" name="c_zip" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_zip'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_zip']) ? pjSanitize::clean($FORM['c_zip']) : (isset($CLIENT['zip']) ? pjSanitize::clean($CLIENT['zip']) : NULL);?>" data-msg-required="<?php __('front_required_field');?>"/>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))
					{
						?>
						<div class="form-group">
							<label><?php __('front_country'); ?></label>
							
							<select name="c_country" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_country'] == 3) ? ' required' : NULL; ?>" data-msg-required="<?php __('front_required_field');?>">
								<option value="">----</option>
								<?php
								foreach ($tpl['country_arr'] as $v)
								{
									?><option value="<?php echo $v['id']; ?>"<?php echo isset($FORM['c_country']) ? ($FORM['c_country'] == $v['id'] ? ' selected="selected"' : NULL) : (isset($CLIENT['country_id']) ? ($CLIENT['country_id'] == $v['id'] ? ' selected="selected"' : NULL) : NULL) ; ?>><?php echo $v['country_title']; ?></option><?php
								}
								?>
							</select>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)))
					{
						?>
						<div class="form-group">
							<label class="control-label"><?php __('front_notes');?></label>
	
							<textarea name="c_notes" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_notes'] == 3) ? ' required' : NULL; ?>" cols="30" rows="10" data-msg-required="<?php __('front_required_field');?>"><?php echo isset($FORM['c_notes']) ? pjSanitize::clean($FORM['c_notes']) : null;?></textarea>
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<?php
					}
					if($tpl['option_arr']['o_payment_disable'] == 'No')
					{
					    $plugins_payment_methods = pjObject::getPlugin('pjPayments') !== NULL? pjPayments::getPaymentMethods(): array();
					    $haveOnline = $haveOffline = false;
					    foreach ($tpl['payment_titles'] as $k => $v)
					    {
					        if( $k != 'cash' && $k != 'bank' && $k != 'payonline' && $k != 'cardonboard' )
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
					        if( $k == 'cash' || $k == 'bank' || $k == 'payonline' || $k == 'cardonboard')
					        {
					            if( (int) $tpl['payment_option_arr'][$k]['is_active'] == 1)
					            {
					                $haveOffline = true;
					                break;
					            }
					        }
					    }

						?>
						<div class="form-group" style="display: <?php echo ($tpl['option_arr']['o_payment_disable'] == 'Yes') ? 'none' : 'block'; ?>;">
							<label><?php __('front_payment_medthod'); ?></label>
							
							<select id="trPaymentMethod_<?php echo $controller->_get->toString('index');?>" name="payment_method" class="form-control required" data-msg-required="<?php __('front_required_field');?>">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								if ($haveOnline && $haveOffline)
								{
								    ?><optgroup label="Online Payment"><?php
			                    }
			                    foreach ($tpl['payment_titles'] as $k => $v)
			                    {
			                        if($k == 'cash' || $k == 'bank'  || $k == 'payonline' || $k == 'cardonboard'){
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
			                    	<!--<optgroup label="<?php __('script_offline_payment', false, true); ?>">-->
			                    	?><optgroup label="Offline Payment"><?php
			                    }
			                    foreach ($tpl['payment_titles'] as $k => $v)
			                    {
			                        if( $k == 'cash' || $k == 'bank' || $k == 'payonline' || $k == 'cardonboard')
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
							
							<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
						</div><!-- /.form-group -->
						<div class="form-group pjTbsBankWrap" style="display: <?php echo @$FORM['payment_method'] != 'bank' ? 'none' : NULL; ?>">
							<label><?php __('front_bank_account')?></label>
							
							<div class="text-muted"><strong><?php echo nl2br(pjSanitize::html($tpl['bank_account'])); ?></strong></div>
						</div>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_captcha'], array(2, 3)))
					{
						?>
						<div class="form-group">
							<label><?php __('front_captcha'); ?></label>
							<?php
							if($tpl['option_arr']['o_captcha_type_front'] == 'system')
							{
    							?>
    							<div class="row">
    								<div class="col-sm-6 col-xs-12">
    									<div class="form-group">
    										<input type="text" name="captcha" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_captcha'] == 3) ? ' required' : NULL; ?>" autocomplete="off" data-msg-required="<?php __('front_required_field'); ?>" data-msg-remote="<?php __('front_incorrect_captcha');?>"/>
    										<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
    									</div><!-- /.form-group -->
    								</div><!-- /.col-sm-6 -->
    	
    								<div class="col-sm-4 col-xs-12">
    									<img id="pjTbsImage_<?php echo $controller->_get->toString('index')?>" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFrontEnd&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?><?php echo $controller->_get->check('session_id') ? '&session_id=' . $controller->_get->toString('session_id') : NULL;?>" alt="Captcha" style="vertical-align: middle; cursor: pointer;" />
    								</div><!-- /.col-sm-6 -->
								</div><!-- /.row -->
								<?php
							}else {
							    ?>
							    <div id="g-recaptcha" class="g-recaptcha" data-sitekey="<?php echo $tpl['option_arr']['o_captcha_site_key_front'] ?>"></div>
								<input type="hidden" id="recaptcha" name="recaptcha" class="recaptcha<?php echo ($tpl['option_arr']['o_bf_include_captcha'] == 3) ? ' required' : NULL; ?>" autocomplete="off" data-msg-required="<?php __('front_4_v_captcha');?>" data-msg-remote="<?php __('front_4_v_captcha_incorrect');?>"/>
								<?php 
							}
							?>
						</div><!-- /.form-group -->
						<?php
					} 
					?>

					<div class="form-group">
						<div class="checkbox">
							<!--<label><input type="checkbox" name="terms" class="required" data-msg-required="<?php __('front_required_field'); ?>"/>  <?php __('front_agree');?> <a href="#" class="pjTbModalTrigger" data-pj-toggle="modal" data-pj-target="#pjNcbTermModal" data-title="<?php __('front_terms_title');?>"><?php __('front_terms_conditions');?></a></label>-->
							
							<label><input type="checkbox" name="terms" class="required" data-msg-required="<?php __('front_required_field'); ?>"/>  <?php __('front_agree');?> <a href="https://alpenheir.at/terms-and-conditions/" target="_blank" data-title="<?php __('front_terms_title');?>"><?php __('front_terms_conditions');?></a></label>
							
						</div>
						<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
					</div><!-- /.form-group -->
				</div><!-- /.pjTbs-car -->							
			</div><!-- /.col-sm-6 -->

			<div class="col-sm-6 col-xs-12 Flight-bx-rt">
				<?php
				if(in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)) || 
				   in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)) ||
				   in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)) ||
				   in_array($tpl['option_arr']['o_bf_include_termial'], array(2, 3))
				  ){
					?>
					<?php if ($pickup_has_airport): ?>
					<div class="pjTbs-box">
						<div class="pjTbs-box-title"><?php __('front_flight_details');?></div><!-- /.pjTbs-box-title -->
						<div class="form-group">
							<span><?php __('front_flight_details_desc');?></span>
						</div>
						<?php
						if (in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)))
						{
							?>
							<div class="form-group">
								<label><?php __('front_airline'); ?></label>
								
								<input type="text" name="c_airline_company" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_airline_company'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_airline_company']) ? pjSanitize::clean($FORM['c_airline_company']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>

								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
							<?php
						}
						if (in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)))
						{
							?>
							<div class="form-group">
								<label><?php __('front_flight_number'); ?></label>
								
								<input type="text" name="c_flight_number" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_flight_number'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_flight_number']) ? pjSanitize::clean($FORM['c_flight_number']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
							<?php
						}
						
						if (in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)) || in_array($tpl['option_arr']['o_bf_include_terminal'], array(2, 3)))
						{ 
							?>
							<div class="row">
								<?php
								if (in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)))
								{ 
									?>
									<div class="col-md-6 col-sm-7 col-xs-12">
										<div class="form-group">
											<label class="control-label"><?php __('front_flight_time');?></label>
											<div class="input-group time-pick">
												<span class="input-group-addon">
													<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
												</span>
			
												<input type="text" name="c_flight_time" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_flight_time'] == 3) ? ' required' : NULL; ?>" autocomplete="off" value="<?php echo isset($FORM['c_flight_time']) ? pjSanitize::clean($FORM['c_flight_time']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
											</div>
											<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									<?php
								}
								if (in_array($tpl['option_arr']['o_bf_include_terminal'], array(2, 3)))
								{ 
									?>
			
									<div class="col-md-6 col-sm-5 col-xs-12">
										<div class="form-group">
											<label> <?php __('front_terminal'); ?></label>
											
											<input type="text" name="c_terminal" class="form-control<?php echo ($tpl['option_arr']['o_bf_include_terminal'] == 3) ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_terminal']) ? pjSanitize::clean($FORM['c_terminal']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
											<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
										</div><!-- /.form-group -->
									</div><!-- /.col-sm-6 -->
									<?php
								} 
								?>
							</div><!-- /.row -->
							<?php }	?>
					</div>
					<?php endif; ?>
					<?php
				}
				?>

				<!--  Return Flight detail start -->

					<?php // if ( $return_has_airport ): ?>
					<?php if ( (isset($SEARCH['return_status']) && (int)$SEARCH['return_status'] === 1)  || $return_has_airport ): ?>
					<div class="pjTbs-box">
						<div class="pjTbs-box-title">Return <?php __('front_flight_details');?></div><!-- /.pjTbs-box-title -->
						<div class="form-group">
							<span><?php __('front_flight_details_desc');?></span>
						</div>
						
							<div class="form-group">
								<label><?php __('front_departure'); ?></label>
								
								<input type="text" name="c_departure_airline_company" class="form-control" value="<?php echo isset($FORM['c_departure_airline_company']) ? pjSanitize::clean($FORM['c_departure_airline_company']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>

								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						
							<div class="form-group">
								<label><?php __('front_departure_flight_number'); ?></label>
								
								<input type="text" name="c_departure_flight_number" class="form-control" value="<?php echo isset($FORM['c_departure_flight_number']) ? pjSanitize::clean($FORM['c_departure_flight_number']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
								<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
							</div><!-- /.form-group -->
						
							<div class="row">
								<div class="col-md-6 col-sm-7 col-xs-12">
									<div class="form-group">
										<label class="control-label"><?php __('front_flight_departure_time');?></label>
										<div class="input-group time-pick">
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
											</span>
		
											<input type="text" name="c_departure_flight_time" class="form-control" autocomplete="off" value="<?php echo isset($FORM['c_departure_flight_time']) ? pjSanitize::clean($FORM['c_departure_flight_time']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>
										</div>
										<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>
									</div><!-- /.form-group -->
								</div><!-- /.col-sm-6 -->
								
		
								<!--<div class="col-md-6 col-sm-5 col-xs-12">-->
								<!--	<div class="form-group">-->
								<!--		<label> <?php __('front_departurec_terminal'); ?></label>-->
										
								<!--		<input type="text" name="c_departure_terminal" class="form-control" value="<?php echo isset($FORM['c_departure_terminal']) ? pjSanitize::clean($FORM['c_departure_terminal']) : null;?>" data-msg-required="<?php __('front_required_field');?>"/>-->
								<!--		<div class="help-block with-errors"><ul class="list-unstyled"></ul></div>-->
								<!--	</div><!-- /.form-group -->
								<!--</div><!-- /.col-sm-6 -->
							</div><!-- /.row -->
						
					</div>
					<?php endif; ?>
					<?php   // } ?>
				<!-- End return flight detail -->
				<div id="pjTbsPriceBox">
					<?php
					include_once dirname(__FILE__) . '/pjActionGetPrices.php';
					?>
				</div>
								
			</div><!-- /.col-sm-6 -->
		</div><!-- /.row -->
		<div class="pjTbs-body-actions bottom-buttons">
			<div class="row">
				<div class="col-sm-3 col-xs-12">
					<a href="#" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadFleets"><?php __('front_btn_back');?></a>
				</div><!-- /.col-sm-3 -->

				<div class="col-sm-3 col-sm-offset-6 col-xs-12">
					<input type="submit" value="<?php __('front_btn_preview');?>" class="btn btn-primary btn-block" >
				</div><!-- /.col-sm-3 -->
			</div><!-- /.row -->
		</div><!-- /.pjTbs-body-actions -->
	</form>
</div><!-- /.pjTbs-body -->