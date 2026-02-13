<?php
if(!empty($tpl['avail_extra_arr']))
{
    ?>
    <div class="hr-line-dashed"></div>
    <div class="row">
    	<div class="col-lg-6 col-md-6 col-sm-12">
    		<div class="table-responsive table-responsive-secondary">
    			<table class="table table-striped table-hover" id="tblExtras">
					<thead>
    					<tr>
    						
    						<th><?php __('lblExtraName');?></th>
    						<th>Number<?php //__('lblPrice');?></th>
    					</tr>
    				</thead>
					<tbody>
						<?php
						foreach($tpl['avail_extra_arr'] as $k => $v)
						{
						    ?>
							<tr>
								<!-- <td><input type="checkbox" name="extra_id[]" value="<?php // echo $v['extra_id']?>" class="i-checks pjAvailExtra" data-price="<?php // echo $v['price'];?>" data-per="<?php // echo $v['per']?>"/></td> -->
								<td><?php echo pjSanitize::html($v['name']);?></td>
								<td>
									
                                    <input type="number" 
                                    name="extra_id[<?php echo $v['extra_id'];?>]" 
                                    id="extra_<?php echo $v['extra_id']; ?>" 
                                    class="pjTbs-spinner-result digits form-control text-center pjTbs-spinner-input" 
                                    value="0" 
                                    maxlength="3"
                                    data-price="<?php echo $v['price']; ?>" 
                                    data-per="<?php echo $v['per']; ?>" 
                                    data-msg-digits="<?php __('front_digits_validation'); ?>">
                               
								</td>
								
								<!-- <td><?php // echo pjCurrency::formatPrice($v['price']) . ($v['per'] == 'person' ? ' ' . __('lblPerPerson', true) : '');?></td> -->
							</tr>


							<?php
						}
    					?>
					</tbody>
				</table>
    		</div>
    	</div>
    </div>
    <?php
}
?>