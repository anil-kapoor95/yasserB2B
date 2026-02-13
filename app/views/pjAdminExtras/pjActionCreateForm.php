<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionCreate" method="post" id="frmCreateExtra">
	<input type="hidden" name="extra_create" value="1" />
    <div class="panel-heading bg-completed">
        <p class="lead m-n"><?php __('infoAddExtraTitle');?></p>
    </div><!-- /.panel-heading -->

    <div class="panel-body">
    	<?php
    	foreach ($tpl['lp_arr'] as $v)
    	{
        	?>
            <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                <label class="control-label"><?php __('lblExtraName');?></label>
                                        
                <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
					<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">	
					<?php if ($tpl['is_flag_ready']) : ?>
					<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
					<?php endif; ?>
				</div>
            </div>
            <?php
        }
        ?>
        <div class="form-group">
            <label class="control-label"><?php __('lblPrice'); ?></label>
        
            <div class="input-group">
                <input type="text" name="price" class="form-control text-right number required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-number="<?php __('pj_number_validation', false, true);?>">

                <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency']); ?></span> 
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label"><?php __('lblPricePerPerson'); ?></label>

            <div class="switch">
                <div class="onoffswitch onoffswitch-data">
                    <input type="checkbox" class="onoffswitch-checkbox" name="price_per_person" id="price_per_person">
                    <label class="onoffswitch-label" for="price_per_person">
                        <span class="onoffswitch-inner" data-on="<?php __('_yesno_ARRAY_T', false, true)?>" data-off="<?php __('_yesno_ARRAY_F', false, true)?>"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
        </div><!-- /.form-group -->
        
		<div class="m-t-lg">
            <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                <span class="ladda-label"><?php __('btnSave'); ?></span>
                <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
            </button>
            <button type="button" class="btn btn-white btn-lg pull-right pjFdBtnCancel"><?php __('btnCancel'); ?></button>
        </div><!-- /.clearfix -->
    </div><!-- /.panel-body -->
</form>