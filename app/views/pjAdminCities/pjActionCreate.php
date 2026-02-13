

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
				
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCities&amp;action=pjActionCreate" method="post" id="frmCreateCity" class="form pj-form" autocomplete="off">
				<input type="hidden" name="item_create" value="1" />

                    <div class="row">
                        <div class="col-sm-6">
							<?php
							foreach ($tpl['lp_arr'] as $v)
							{
								
							?>
                                <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                                    <label class="control-label"><?php __('lblCityName');?></label>
                                                            
										<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">	
										<?php if ($tpl['is_flag_ready']) : ?>

										<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
										<?php endif; ?>
									</div>
                                </div>
                                <?php
                            }
                            ?>
                           
							
                        </div><!-- /.col-sm-6 -->

                        
                    </div><!-- /.row -->

					<div class="clearfix">
                        <button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in" style="margin-right: 15px;">
                            <span class="ladda-label"><?php __('btnSave'); ?></span>
                            <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                        </button>
                        <button type="button" class="btn btn-white btn-lg pull-left btnDeleteShape" style="display:none"><?php __('btnDeleteShape'); ?></button>
                        <a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminFleets&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                    </div><!-- /.clearfix -->
                </form>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>