<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
				
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCities&amp;action=pjActionUpdate" method="post" id="frmUpdateCity" class="form pj-form">
				<input type="hidden" name="item_update" value="1" />
				<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />

                    <div class="row">
                        <div class="col-sm-6">
							<?php
							foreach ($tpl['lp_arr'] as $v)
							{
							?>
                                <div class="form-group pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
                                    <label class="control-label"><?php __('lblCityName');?></label>
										<input type="text" id="i18n_name_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][name]" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['name'])); ?>" lang="<?php echo $v['id']; ?>"  data-msg-required="<?php __('tr_field_required');?>"/>
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
                        <a type="button" class="btn btn-white btn-lg pull-right" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCities&action=pjActionIndex"><?php __('btnCancel'); ?></a>
                    </div><!-- /.clearfix -->
                </form>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>
	
	<script type="text/javascript">
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	