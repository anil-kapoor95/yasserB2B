<?php
// include_once dirname(__FILE__) . '/elements/header.php';
$SEARCH = @$_SESSION[$controller->defaultStore]['search'];
$passengers = !empty($SEARCH['passengers']) ? $SEARCH['passengers'] : 0;
$luggage = !empty($SEARCH['luggage']) ? $SEARCH['luggage'] : 0;
$distance = !empty($SEARCH['distance']) ? $SEARCH['distance'] : 0;
$durationInMin = !empty($SEARCH['durationInMin']) ? $SEARCH['durationInMin'] : 0;
$time_str = (int) $SEARCH['luggage'] >= 1 ? __('front_taxi_on', true) : __('front_taxi_on_2', true);
$time_str = str_replace("{DATE}", $SEARCH['booking_date'], $time_str);
$time_str = str_replace("{TIME}", date($tpl['option_arr']['o_time_format'], strtotime($SEARCH['booking_time'])), $time_str);
$time_str = str_replace("{PASSENGERS}", $SEARCH['passengers'], $time_str);
$time_str = str_replace("{LUGGAGES}", $SEARCH['luggage'], $time_str);

$hours = floor($durationInMin / 60);
$minutes = $durationInMin % 60;
$durationFormatted = $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ' . $minutes . ' min' . ($minutes != 1 ? 's' : '');
// echo "<pre>"; print_r($SEARCH); echo "</pre>";
?>

<style type="text/css">
.pjtbs-car {
    position: relative;
}
.pjtbs-car .pjTbs-car-image img.img-responsive {
    width: 100%;
}
.fleet-container-in-form {
    padding: 8px !important;
}
.map-info-bar {
    background-color: #212121;
    color: #ffffff;
    padding: 15px;
    margin: 20px 0;
    border: 1px solid #000;
    border-radius: 8px;
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
span.distance {
    padding-right: 200px;
}
.pjTbs-car {
    border: 1px solid #e2e2e2;
    border-radius: 8px !important;
    background: #f6f6f6 !important;
}
.pjtbs-car .pjTbs-car-actions input.btn.btn-primary, .pjtbs-car .pjTbs-car-actions input.btn {
    background: #feba00 !important;
    border-color: #feba00 !important;
    color: #00000 !important;
}
.pjtbs-car .pjTbs-car-actions input.btn.btn-primary:hover {
    background: #ffc012 !important;
}
.fleet_page_label {
    position: absolute;
  top: 12px;
  left: 5px;
  background: linear-gradient(135deg, #FEBA00, #FFC857);
  color: #222222;
  font-weight: 600;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.9rem;
  letter-spacing: 0.3px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  font-family: 'Segoe UI', Roboto, sans-serif;
}
.offer-label {
  position: absolute;
  top: 12px;
  right: 12px;
  background: linear-gradient(135deg, #2c3e50, #4ca1af);
  color: #fff;
  font-weight: 500;
  padding: 5px 20px;
  border-radius: 20px;
  font-size: 14px;
  letter-spacing: 0.3px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(4px);
  overflow: hidden;
}

/* ✨ Add shine effect */
.offer-label::before {
  content: "";
  position: absolute;
  top: 0;
  left: -75%;
  width: 50%;
  height: 100%;
  background: linear-gradient(
    120deg,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.4) 50%,
    rgba(255, 255, 255, 0) 100%
  );
  animation: shine 2.5s infinite;
}

@keyframes shine {
  0% {
    left: -75%;
  }
  100% {
    left: 125%;
  }
}
@media only screen and (min-width: 100px) and (max-width: 767px) {
    span.distance {
        padding-right: 0;
        width: 100%;
        float: left;
    }
    .pjtbs-car .pjTbs-price {
        width: 100%;
        text-align: center;
    }
    .fleet_page_label {
        font-size: 14px;
        margin-top: -20px;
    }
    .offer-label {
        top: 40%;
        right: 24%;
        z-index: 99;
    }
}
@media only screen and (min-width: 768px) and (max-width: 1024px) {
    .pjtbs-car .pjTbs-price {
        padding: 3px 6px !important; 
    }
}
</style>

<div class="pjTbs-body .fleet-container-in-form">
<!--    <div class="fleet_page_label">-->
<!--  Book This Winter! Fares are rising 20% — Don’t miss out.-->
<!--</div>-->

    <div class="pjTbs-service-info">
        <div class="row">
            <div class="col-md-12 col-xs-12" style="padding-top: 10px;">
                <?php if (!empty($tpl['fleet_arr'])): ?>
                    <p><?php __('front_taxi_service_from');?> <strong><?php echo pjSanitize::html($SEARCH['pickup_address']); ?></strong> <?php __('front_to_lowercase');?> <strong><?php echo pjSanitize::html($SEARCH['return_address']); ?></strong> <?php echo $time_str;?>
                    <?php if (!empty($SEARCH['return_date']) && !empty($SEARCH['return_time'])): ?>
                     ASDFGH <?php __('lblReturnDateTime');?> : <?php echo $SEARCH['return_date'];?>, <?php echo date($tpl['option_arr']['o_time_format'], strtotime($SEARCH['return_time']));?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p><?php __('lblNoServicesAvailable'); ?></p>
                <?php endif; ?>
            </div></div><input type="hidden" name="o_search_result_redirect_id" id="o_search_result_redirect_id" value="<?php echo isset($SEARCH['o_search_result_redirect_id']) ? $SEARCH['o_search_result_redirect_id'] : 'book-now'; ?>" />
    </div><div class="pjTbs-map" id="pjTbsMapCanvas"></div>
    
    <div class="map-info-bar">
        <span class="distance"><?php __('front_distance');?>: <?php echo $distance; ?> km</span>
        &nbsp;&nbsp;&nbsp;
        <span class="duration">Duration: <?php echo $durationFormatted; ?> </span>
    </div>
    <?php
    if(!empty($tpl['fleet_arr']))
    { 
        foreach($tpl['fleet_arr'] as $k => $v)
        {
            $image = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/250x130.png';
            if(isset($v['thumb_path']) && !empty($v['thumb_path']) && file_exists(PJ_INSTALL_PATH . $v['thumb_path']))
            {
                $image = PJ_INSTALL_URL . $v['thumb_path'];
            }
            ?>
            <div class="pjTbs-car pjTbs-box pjtbs-car" data-id="<?php echo $v['id']; ?>">
                
                <div class="pjTbs-car-title"><?php echo pjSanitize::html($v['fleet']);?>
                
                <!--<div class="offer-label">Book Now & Save 20%</div>-->
                
                </div><div class="row">
                    <div class="col-sm-3 col-xs-12">
                        <div class="pjTbs-car-image">
                            <img src="<?php echo $image;?>" alt="" class="img-responsive">
                        </div></div><div class="col-sm-6 col-xs-12" style="display: none;">
                        <div class="pjTbs-car-desc">
                            <ul class="pjTbs-car-meta">
                                <li><?php __('front_passengers');?>: <?php for($p = 1; $p <= (int) $v['passengers']; $p++) {?><span class="glyphicon glyphicon-user"></span><?php }?> </li>
                                <li><?php __('front_bags');?>: <?php for($p = 1; $p <= (int) $v['luggage']; $p++) {?><span class="glyphicon glyphicon-briefcase"></span><?php }?> </li>
                            </ul><div class="pjTbs-car-info"><?php echo nl2br(pjSanitize::clean($v['description']));?></div></div></div><div class="col-sm-6 col-xs-12" >
                        <div class="pjTbs-car-desc">
                            <ul class="pjTbs-car-meta">
                                <li><span class="glyphicon glyphicon-user"></span> : Up to <?php echo $v['passengers']; ?>  <?php __('front_passengers');?>  </li>
                                <li><span class="glyphicon glyphicon-briefcase"></span> : <?php echo $v['luggage']; ?> <?php __('front_bags');?></li>
                            </ul><div class="pjTbs-car-info"><?php echo nl2br(pjSanitize::clean($v['description']));?></div></div></div><div class="col-sm-3 col-xs-12">
                        <div class="pjTbs-car-actions bottom-buttons">
                            <?php
                             
                             $total = $v['start_fee_r'];
                             $overbooking_cost = $v['overbooking_cost'] ?? 0;
                             $dateRangePrice = isset($tpl['from_daterange'][$v['id']]) ? (float) $tpl['from_daterange'][$v['id']] : 0;
                             $returndateRangePrice = isset($tpl['return_daterange'][$v['id']]) ? (float) $tpl['return_daterange'][$v['id']] : 0;

                            if (isset($tpl['fleet_price_arr'][$v['id']]) && (float)$tpl['fleet_price_arr'][$v['id']] > 0) {
                                // $total = $tpl['fleet_price_arr'][$v['id']];
                                $priceHikePercent = $v['price_hike'] ?? 0;
                                $total = $tpl['fleet_price_arr'][$v['id']];
                                $hikeAmount = ($total * $priceHikePercent) / 100;
                                $total += $hikeAmount;
                                
                            } else {
                               
                                $total += $distance * $v['price'];
                                $total += $durationInMin * $v['time_rate_per_minute_r'];
                                $total += $passengers * $v['fee_per_person'];
                            }

                           // $total += $dateRangePrice;
                          
                            $allowedBooking = (int) ($v['numberof_booking'] ?? 0);
                            $totalBooking = $tpl['totalBooking'][$v['id']] ?? 0;

                            if ($allowedBooking > 0 && $totalBooking >= $allowedBooking)
                                { 
                                    
                                $total += $overbooking_cost;
                             }
                           
                            if($SEARCH['return_status'] == 1 )
                                {
                                    $total = $total*2;
                                }
                               
                               $total += $dateRangePrice;
                               $total += $returndateRangePrice;      
                            ?>
                            <div class="pjTbs-price-holder">
                                <div class="pjTbs-price">
                                    <strong><?php __('front_total');?>: <?php echo pjCurrency::formatPrice($total);?></strong>
                                </div>
                                
                                </div><input type="button" value="<?php __('front_btn_book_a_taxi');?>" data-id="<?php echo $v['id']?>" class="btn btn-primary btn-block pjTbsBtnBookTaxi">
                            
                        </div></div></div></div><?php
        }
    }
    ?>
    <div class="pjTbs-body-actions">
        <br>

        <div class="row">
            <div class="col-sm-3 col-xs-12">
                <a href="#" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadSearch"><?php __('front_btn_back');?></a>
            </div></div></div></div>