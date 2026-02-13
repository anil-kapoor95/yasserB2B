<?php
if(isset($tpl['avail_extra_arr']) && !empty($tpl['avail_extra_arr']))
{ 
	?>
	<style type="text/css">


.pjTbs-body form.pjTbsCheckoutForm a {
    color: #feba00 !important;
}
.pjTbs-body form.pjTbsCheckoutForm .pjTbs-body-actions input.btn.btn-primary.btn-block {
    background: #feba00 !important;
     border-color: #feba00 !important;
}
.pjTbs-body form.pjTbsCheckoutForm .pjTbs-body-actions input.btn.btn-primary.btn-block:hover {
    background: #ffbf10 !important;
}
.input-group.time-pick span.input-group-addon span.glyphicon.glyphicon-time {
    color: #feba00;
}
@media only screen and (min-width: 768px) and (max-width: 1024px) {
	.checkbox-ut .col-sm-5.col-xs-12 {
    width: 100%;
}
.row.rowst .col-md-6 {
    width: 100%;
}
	}
</style>
	<div class="pjTbs-box checkbox-ut">
		<div class="pjTbs-box-title"><?php __('front_choose_extras');?></div><!-- /.pjTbs-box-title -->
		
		<ul class="pjTbs-extras">
			<?php
		 
			foreach($tpl['avail_extra_arr'] as $k => $v)
			{
				?>
				<li>
					<div class="row">
						<div class="col-sm-5 col-xs-12">
							<div><label><?php echo pjSanitize::html($v['name']);?></label><input type="hidden"></div>
						    
						    <div class="btn-group pjTbs-spinner" role="group" aria-label="...">
                                <button style="border-top-left-radius: 8px; border-bottom-left-radius: 8px; background: #ffffff;" type="button" class="btn pjTbs-spinner pjTbs-spinner-down" data-target="extra_<?php echo $v['extra_id']; ?>">-</button>
                                <input type="text" 
                                    name="extra_id[<?php echo $v['extra_id'];?>]" 
                                    id="extra_<?php echo $v['extra_id']; ?>" 
                                    class="pjTbs-spinner-result digits form-control text-center pjTbs-spinner-input" 
                                    value="0" 
                                    maxlength="3"
                                    data-price="<?php echo $v['price']; ?>" 
                                    data-per="<?php echo $v['per']; ?>" 
                                    data-msg-digits="<?php __('front_digits_validation'); ?>">
                                <button style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; background: #ffffff;" type="button" class="btn pjTbs-spinner pjTbs-spinner-up" data-target="extra_<?php echo $v['extra_id']; ?>">+</button>
                            </div>
							<!-- <div class="checkbox">
								<label><input type="checkbox" name="extra_id[<?php // echo $v['extra_id'];?>]" value="<?php // echo $v['price'];?>"<?php // echo isset($tpl['extra_id_arr']) ? (in_array($v['extra_id'], $tpl['extra_id_arr']) ? ' checked="checked"' : NULL) : NULL;?> class="pjAvailExtra" data-price="<?php // echo $v['price'];?>" data-per="<?php // echo $v['per']?>"> <?php // echo pjSanitize::html($v['name']);?></label>
							</div> /.checkbox -->
						</div><!-- /.col-sm-6 -->

						<div class="col-sm-4 col-xs-12">
							<span><?php // echo pjCurrency::formatPrice($v['price']) . ($v['per'] == 'person' ? ' ' . __('front_per_person', true) : '');?></span>
						</div><!-- /.col-sm-6 -->
						<?php
						if($v['per'] == 'person')
						{
							$extra_price = $v['price'] * $tpl['passengers'];
							?>
							<div class="col-sm-3 col-xs-12">
								<strong><?php echo pjCurrency::formatPrice($extra_price);?></strong>
							</div><!-- /.col-sm-6 -->
							<?php
						} 
						?>
					</div><!-- /.row -->
				</li>
				<?php
			}
			?>
				
		</ul><!-- /.pjTbs-extras -->
	
	</div><!-- /.pjTbs-car -->
	<?php
} 
?>	

<div class="pjTbs-subtotal">
	<?php

	if($SEARCH['return_status'] == 1 )
	{
				$front_total = $tpl['price_arr']['total']*2;
	}else {
		$front_total = $tpl['price_arr']['total'];
	}

	$front_total += $tpl['price_arr']['daterange_price'];
	$front_total += $tpl['price_arr']['returndate_rangePrice'];

	if(isset($tpl['avail_extra_arr']) && !empty($tpl['avail_extra_arr']) && (float) $tpl['price_arr']['extra'] > 0)
	{ 
		?>
		<p>
			<span><?php __('front_extras');?>:</span>
	
			<span><?php echo pjCurrency::formatPrice($tpl['price_arr']['extra']);?></span>
		</p>
		<?php
	} 
	?>
	<!-- <p>
		
		<span> <?php // __('front_subtotal');?>:</span>

		<span><?php // echo pjCurrency::formatPrice($tpl['price_arr']['subtotal']);?></span>
	</p> -->

	<!-- <p>
		<span><?php // __('front_tax');?>:</span>

		<span><?php // echo pjCurrency::formatPrice($tpl['price_arr']['tax']);?></span>
	</p> -->

	<p>
		<span><?php __('front_total');?>:</span> 

		<span><?php echo pjCurrency::formatPrice($front_total);?></span>&nbsp;<small>(Inclusive 10% VAT)</small>
	</p>


	<!-- <p>
		<span><?php // __('front_deposit_required');?>:</span>

		<span><?php // echo pjCurrency::formatPrice($tpl['price_arr']['deposit']);?></span>
	</p> -->
</div><!-- /.pjTbs-subtotal -->		