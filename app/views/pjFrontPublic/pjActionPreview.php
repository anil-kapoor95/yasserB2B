
<?php
// include_once dirname(__FILE__) . '/elements/header.php';
$SEARCH = @$_SESSION[$controller->defaultStore]['search'];
$FORM = @$_SESSION[$controller->defaultForm];

?>

<style>
    .pjTbs-box {
    border-radius: 20px !important;
    background: #F6F6F6 !important;
    padding: 20px !important;
    border: 1px solid #e2e2e2;
    margin-top: 20px !important;
}
</style>

<div class="pjTbs-body">
	<form id="pjTbsPreviewForm_<?php echo $controller->_get->toString('index');?>" action="#" method="post">
		<input type="hidden" name="tbs_preview" value="1" />
		<div class="pjTbs-box">
			<div class="pjTbs-box-title"><?php __('front_your_enquiry');?></div><!-- /.pjTbs-box-title -->
	
			<ul class="pjTbs-extras">
				<li>
					<div class="row">
						<div class="col-md-6 col-xs-12">
							<em><?php echo pjSanitize::clean($tpl['fleet_arr']['fleet']);?></em>
						</div><!-- /.col-md-6 -->
	
						<div class="col-md-4 col-xs-8">
							<?php echo $SEARCH['booking_date']?>, <?php echo $SEARCH['booking_time']?>
						</div><!-- /.col-md-4 -->

						<?php if (!empty($SEARCH['return_date']) && !empty($SEARCH['return_time'])): ?>
						<div class="col-md-4 col-xs-8">
					  	 <?php __('lblReturnDateTime');?> : <?php echo $SEARCH['return_date'];?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($SEARCH['return_time']));?>
					  	 </div><!-- /.col-md-4 -->

						<?php endif; ?>

						<?php
						
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

						// if($tpl['totalBooking'] > 10){
						// 		$total_price += $overbooking_cost;
						// 	}

						$allowedBooking = (int) floor((float)$tpl['fleet_arr']['numberof_booking']);
                        $totalBooking   =  (int) ($tpl['totalBooking']);

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
						<div class="col-md-2 col-xs-4 text-right">
							<strong><?php echo pjCurrency::formatPrice($total_price)?></strong>
						</div><!-- /.col-md-2 -->
					</div><!-- /.row -->
				</li>
				<!-- <li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php //__('front_extra_price');?></em>
						</div>
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
							<strong><?php // echo pjCurrency::formatPrice($tpl['price_arr']['extra']);?></strong>
						</div>
					</div>
				</li> -->
				<!-- <li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php // __('front_subtotal');?></em>
						</div>
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right asdf">
							<strong><?php // echo pjCurrency::formatPrice($tpl['price_arr']['subtotal']);?></strong>
						</div>
					</div>
				</li> -->
				
				<!-- <li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php // __('front_tax');?></em>
						</div>
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
							<strong><?php // echo pjCurrency::formatPrice($tpl['price_arr']['tax']);?></strong>
						</div>
					</div>
				</li> -->
			<?php 
				if($SEARCH['return_status'] == 1 )
				{
					$front_total = $tpl['price_arr']['total']*2;
					$front_deposit = $tpl['price_arr']['deposit']*2;
				}
				else{
					$front_total = $tpl['price_arr']['total'];
					$front_deposit = $tpl['price_arr']['deposit'];
				}

				$front_total += $tpl['price_arr']['daterange_price'];
				$front_total += $tpl['price_arr']['returndate_rangePrice'];
				$front_deposit += $tpl['price_arr']['daterange_price'];
				$front_deposit += $tpl['price_arr']['returndate_rangePrice'];
				
				?>
				<li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php __('front_total');?></em>
						</div><!-- /.col-md-6 -->
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right Asd">
							<strong><?php echo pjCurrency::formatPrice($front_total); ?></strong> &nbsp;<small>(Inclusive 10% VAT)</small>
						</div><!-- /.col-md-2 -->
					</div><!-- /.row -->
				</li>
				<?php if($FORM['payment_method'] != 'bank' && $FORM['payment_method'] != 'cash'): ?>
					<li>
					    <div class="row">
					        <div class="col-xs-6">
					            <em><?php __('front_deposit_required'); ?></em>
					        </div><!-- /.col-md-6 -->

					        <div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
					            <strong><?php echo pjCurrency::formatPrice($front_deposit); ?></strong>
					        </div><!-- /.col-md-2 -->
					    </div><!-- /.row -->
					</li>
					<?php endif; ?>

				<!-- <li>
					<div class="row">
						<div class="col-xs-6">
							<em><?php // __('front_deposit_required');?></em>
						</div>
	
						<div class="col-md-2 col-md-offset-4 col-xs-6 text-right">
							<strong><?php // echo pjCurrency::formatPrice($front_deposit); ?></strong>
						</div>
					</div>
				</li> -->
			</ul>
		</div>
	
		<div class="pjTbs-box">
			<div class="pjTbs-box-title"><?php __('front_booking_details');?></div><!-- /.pjTbs-box-title -->
	
			<div class="pjTbs-personal-details">
				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<p>
							<span><?php __('front_pickup_address');?></span>
	
							<strong><?php echo pjSanitize::html($SEARCH['pickup_address']);?></strong>
						</p>
					</div><!-- /.col-sm-6 -->
					<div class="col-sm-6 col-xs-12">
						<p>
							<span><?php __('front_dropoff_address');?></span>
	
							<strong><?php echo pjSanitize::html($SEARCH['return_address']);?></strong>
						</p>
					</div><!-- /.col-sm-6 -->
					<div class="col-sm-6 col-xs-12">
						<p>
							<span><?php __('front_passengers');?></span>
	
							<strong><?php echo $tpl['passengers'];?></strong>
						</p>
					</div><!-- /.col-sm-6 -->
					<?php
					if((int) $SEARCH['luggage'] > 0) 
					?>
					<!--<div class="col-sm-6 col-xs-12">-->
					<!--	<p>-->
					<!--		<span><?php __('front_pieces_of_luggage');?></span>-->
	
					<!--		<strong><?php echo $SEARCH['luggage'];?></strong>-->
					<!--	</p>-->
					<!--</div> /.col-sm-6 -->
					<?php
					if($tpl['option_arr']['o_payment_disable'] == 'No' && isset($FORM['payment_method']) && $FORM['payment_method'] != '')
					{
					    $payment_methods = $tpl['payment_titles'];
						?>
						<div class="<?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == 'bank' ? 'col-sm-6' : 'col-sm-12';?> col-xs-12">
							<p>
								<span><?php __('front_payment_medthod'); ?></span>
		
								<strong><?php echo $payment_methods[$FORM['payment_method']];?></strong>
							</p>
						</div><!-- /.col-sm-6 -->
						
						<div style="display: <?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == 'bank' ? 'none' : 'none'; ?>">
							<div class="col-sm-6 col-xs-12">
								<p>
									<span><?php __('front_bank_account'); ?></span>
			
									<strong>
										<?php echo nl2br(pjSanitize::html($tpl['bank_account'])); ?>
									</strong>
								</p>
							</div><!-- /.col-sm-6 -->
						</div>
						<?php
					} 
					?>
				</div>
			</div>
	
			<br>
	
			<div class="pjTbs-box-title"><?php __('front_personal_details');?></div><!-- /.pjTbs-box-title -->
			
			<div class="pjTbs-personal-details">
				<div class="row">
					<?php
					if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)) && isset($FORM['c_title']) && $FORM['c_title'] != '')
					{ 
						$title = NULL;
						$name_titles = __('personal_titles', true, false);
						if(isset($FORM['c_title']) && $FORM['c_title'] != '')
						{
							$title = $FORM['c_title'];
						}
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_title'); ?>:</span>
		
								<strong><?php echo $name_titles[$title];?></strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_fname'], array(2, 3)) && isset($FORM['c_fname']) && $FORM['c_fname'] != ''){
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_fname'); ?></span>
								<strong>
									<?php echo isset($FORM['c_fname']) ? pjSanitize::clean($FORM['c_fname']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_lname'], array(2, 3)) && isset($FORM['c_lname']) && $FORM['c_lname'] != ''){
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_lname'); ?></span>
								<strong>
									<?php echo isset($FORM['c_lname']) ? pjSanitize::clean($FORM['c_lname']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)) && isset($FORM['c_phone']) && $FORM['c_phone'] != ''){
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_phone'); ?></span>
								<strong>
									<?php echo isset($FORM['c_phone']) ? pjSanitize::clean($FORM['c_phone']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)) && isset($FORM['c_email']) && $FORM['c_email'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_email'); ?></span>
								<strong>
									<?php echo isset($FORM['c_email']) ? pjSanitize::clean($FORM['c_email']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)) && isset($FORM['c_company']) && $FORM['c_company'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_company'); ?></span>
								<strong>
									<?php echo isset($FORM['c_company']) ? pjSanitize::clean($FORM['c_company']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3)) && isset($FORM['c_address']) && $FORM['c_address'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_address'); ?></span>
								<strong>
									<?php echo isset($FORM['c_address']) ? pjSanitize::clean($FORM['c_address']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)) && isset($FORM['c_city']) && $FORM['c_city'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_city'); ?></span>
								<strong>
									<?php echo isset($FORM['c_city']) ? pjSanitize::clean($FORM['c_city']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)) && isset($FORM['c_state']) && $FORM['c_state'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_state'); ?></span>
								<strong>
									<?php echo isset($FORM['c_state']) ? pjSanitize::clean($FORM['c_state']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)) && isset($FORM['c_country']) && $FORM['c_country'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_zip'); ?></span>
								<strong>
									<?php echo isset($FORM['c_zip']) ? pjSanitize::clean($FORM['c_zip']) : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)) && isset($FORM['c_country']) && $FORM['c_country'] != ''){ 
						?>
						<div class="col-sm-6 col-xs-12">
							<p>
								<span><?php __('front_country'); ?></span>
								<strong>
									<?php echo !empty($tpl['country_arr']) ? $tpl['country_arr']['country_title'] : null;?>
								</strong>
							</p>
						</div><!-- /.col-sm-6 -->
						<?php
					}
					?>
				</div><!-- /.row -->
				<?php
				if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)) && isset($FORM['c_notes']) && $FORM['c_notes'] != ''){ 
					?>
					<p>
						<span><?php __('front_notes'); ?></span>
		
						<strong><?php echo isset($FORM['c_notes']) ? nl2br(pjSanitize::clean($FORM['c_notes'])) : null;?></strong>
					</p>
					<?php
				}
				if(in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)) ||
						in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)) ||
						in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)) ||
						in_array($tpl['option_arr']['o_bf_include_termial'], array(2, 3))
				){
					?>
					<!--<div class="pjTbs-box-title"><?php __('front_flight_details');?></div><!-- /.pjTbs-box-title -->
			
					<div class="pjTbs-personal-details">
						<div class="row">
							<?php
							if (in_array($tpl['option_arr']['o_bf_include_airline_company'], array(2, 3)) && isset($FORM['c_airline_company']) && $FORM['c_airline_company'] != ''){
								?>
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_airline_company'); ?></span>
										<strong>
											<?php echo isset($FORM['c_airline_company']) ? pjSanitize::clean($FORM['c_airline_company']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								<?php
							}
							if (in_array($tpl['option_arr']['o_bf_include_flight_number'], array(2, 3)) && isset($FORM['c_flight_number']) && $FORM['c_flight_number'] != ''){ 
								?>
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_flight_number'); ?></span>
										<strong>
											<?php echo isset($FORM['c_flight_number']) ? pjSanitize::clean($FORM['c_flight_number']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								<?php
							}
							if (in_array($tpl['option_arr']['o_bf_include_flight_time'], array(2, 3)) && isset($FORM['c_flight_time']) && $FORM['c_flight_time'] != ''){ 
								?>
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_flight_time'); ?></span>
										<strong>
											<?php echo isset($FORM['c_flight_time']) ? pjSanitize::clean($FORM['c_flight_time']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								<?php
							}
							if (in_array($tpl['option_arr']['o_bf_include_terminal'], array(2, 3)) && isset($FORM['c_terminal']) && $FORM['c_terminal'] != ''){
								?>
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_terminal'); ?></span>
										<strong>
											<?php echo isset($FORM['c_terminal']) ? pjSanitize::clean($FORM['c_terminal']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								<?php
							} 

							if($SEARCH['return_status'] == 1){


							?>

							<!-- Return start Here -->


								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('lblDepartureAirlineCompany'); ?></span>
										<strong>
											<?php echo isset($FORM['c_departure_airline_company']) ? pjSanitize::clean($FORM['c_departure_airline_company']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_departure_flight_number'); ?></span>
										<strong>
											<?php echo isset($FORM['c_departure_flight_number']) ? pjSanitize::clean($FORM['c_departure_flight_number']) : null;?>
										</strong>
									</p>
								</div> <!-- /.col-sm-6 -->
							
								<div class="col-sm-6 col-xs-12">
									<p>
										<span><?php __('front_flight_departure_time'); ?></span>
										<strong>
											<?php echo isset($FORM['c_departure_flight_time']) ? pjSanitize::clean($FORM['c_departure_flight_time']) : null;?>
										</strong>
									</p>
								</div><!-- /.col-sm-6 -->
								
								<!--<div class="col-sm-6 col-xs-12">-->
								<!--	<p>-->
								<!--		<span><?php // __('front_departurec_terminal'); ?></span>-->
								<!--		<strong>-->
								<!--			<?php // echo isset($FORM['c_departure_terminal']) ? pjSanitize::clean($FORM['c_departure_terminal']) : null;?>-->
								<!--		</strong>-->
								<!--	</p>-->
								<!--</div> /.col-sm-6 -->
							<?php } ?>
							
							<!-- Return End Here -->

							<?php 
							$hasExtras = isset($FORM['extra_id']) && is_array($FORM['extra_id']) &&
							    !empty(array_filter($FORM['extra_id'], function($q) { return (int)$q > 0; }));

							if ($hasExtras): ?>
							    <div class="col-sm-6 col-xs-12">
							        <span><?php __('front_choose_extras'); ?></span>
							        <div class="extra-summary-list">
							            <?php foreach ($tpl['avail_extra_arr'] as $v): ?>
							                <?php
							                $qty = isset($FORM['extra_id'][$v['extra_id']]) ? (int)$FORM['extra_id'][$v['extra_id']] : 0;
							                if ($qty > 0):
							                ?>
							                    <div class="extra-item">
							                        <strong><?php echo pjSanitize::html($v['name']); ?>:</strong> <?php echo $qty; ?>
							                    </div>
							                <?php endif; ?>
							            <?php endforeach; ?>
							        </div>
							    </div>
							<?php endif; ?>

						</div>
					</div>
					<?php
				} 
				?>
			</div><!-- /.pjTbs-personal-details -->
		</div>
	
		<div class="pjTbs-body-actions bottom-buttons">
			<div class="row">
				<div class="col-sm-3 col-xs-12">
					<a href="#" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadCheckout"><?php __('front_btn_back');?></a>
				</div><!-- /.col-sm-3 -->
	
				<div class="col-sm-3 col-sm-offset-6 col-xs-12">
					<input type="submit" value="<?php __('front_btn_confirm');?>" class="btn btn-primary btn-block" >
				</div><!-- /.col-sm-3 -->
			</div><!-- /.row -->
		</div><!-- /.pjTbs-body-actions -->
	</form>
</div><!-- /.pjTbs-body -->