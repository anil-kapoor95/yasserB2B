<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategory&amp;action=pjActionUpdate" method="post" id="frmUpdateCategory">
	<input type="hidden" name="category_update" value="1" />
	<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
    <div class="panel-heading bg-completed">
        <p class="lead m-n"><?php __('infoUpdateCategoryTitle');?></p>
    </div><!-- /.panel-heading -->

    <div class="panel-body">
    	<?php
    	foreach ($tpl['lp_arr'] as $v)
    	{
        	?>
            <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                <label class="control-label"><?php __('lblCategoryName');?></label>
                                        
                <div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?>" data-index="<?php echo $v['id']; ?>">
					<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][category]" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['category'])); ?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">	
					<?php if ($tpl['is_flag_ready']) : ?>
					<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['category']); ?>"></span>
					<?php endif; ?>
				</div>
            </div>
            <?php
        }
        ?>
		<div class="m-t-lg">
            <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                <span class="ladda-label"><?php __('btnSave'); ?></span>
                <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
            </button>
            <button type="button" class="btn btn-white btn-lg pull-right pjFdBtnCancel"><?php __('btnCancel'); ?></button>
        </div><!-- /.clearfix -->
    </div><!-- /.panel-body -->
</form>