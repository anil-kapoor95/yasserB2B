<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true); 
$ct = __('_commission_type', true);
?>

<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-lg-9 col-md-8 col-sm-6">
				<h2><?php __('infoCommissionTitle');?></h2>
			</div>
		</div>
		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoCommissionDesc');?></p>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

	<?php
	$error_code = $controller->_get->toString('err');
	if (!empty($error_code))
	{
		switch ($error_code)
		{
			case in_array($error_code, array('AO06')):
	?>
			<div class="alert alert-success">
				<i class="fa fa-check m-r-xs"></i>
				<strong><?php echo @$titles[$error_code]; ?></strong>
				<?php echo @$bodies[$error_code]?>
			</div>
	<?php
			break;
		}
	}
	?>

	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<form id="frmUpdateOptions"
						action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate"
						method="post"
						class="form-horizontal">

						<input type="hidden" name="options_update" value="1" />
						<input type="hidden" name="tab" value="6" />
						<input type="hidden" name="next_action" value="pjActionCommission" />

						<!-- Commission Type -->
						<div class="form-group">
							<label class="col-sm-3 control-label"><?php __('opt_o_commission_type');?></label>

							<?php $ct = __('_commission_type', true); ?>

							<div class="col-lg-5 col-sm-7">
								<div class="row">
									<div class="col-sm-6">

										<select id="commission_type" class="form-control" name="value-enum-o_commission_type">

											<?php foreach($ct as $k => $v){ ?>

												<option value="percent|fixed::<?php echo $k; ?>"
												<?php echo $tpl['option_arr']['o_commission_type'] == $k ? 'selected="selected"' : null; ?>>

													<?php echo $v; ?>

												</option>

											<?php } ?>

										</select>

									</div>
								</div>
							</div>
						</div>
						<!-- Commission Amount -->
						<div class="form-group">

							<label id="commission_amount_label" class="col-sm-3 control-label">
								<?php __('opt_o_commission_amount');?>
							</label>

							<div class="col-lg-5 col-sm-7">
								<div class="row">
									<div class="col-sm-6">

										<div class="input-group">

											<input id="commission_amount"
												class="form-control number"
												type="text"
												value="<?php echo $tpl['option_arr']['o_commission_amount'];?>"
												name="value-float-o_commission_amount"
												data-msg-number="<?php __('jquery_validation_ARRAY_number', true);?>">

											<span id="commission_icon" class="input-group-addon">
												<i class="fa fa-percent"></i>
											</span>

										</div>

									</div>
								</div>
							</div>
						</div>
						<div class="hr-line-dashed"></div>
						<div class="clearfix">
							<button class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader" data-style="zoom-in">
								<span class="ladda-label"><?php __('plugin_base_btn_save'); ?></span>
								<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
							</button>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>

</div>