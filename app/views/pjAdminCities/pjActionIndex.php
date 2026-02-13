<?php
// if (isset($tpl['status']))
// {
// 	$status = __('status', true);
// 	switch ($tpl['status'])
// 	{
// 		case 2:
// 			pjUtil::printNotice(NULL, $status[2]);
// 			break;
// 	}
// } else {
// 	if (isset($_GET['err']))
// 	{
// 		$titles = __('error_titles', true);
// 		$bodies = __('error_bodies', true);
// 		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
// 	}
	?>
	
	<?php // pjUtil::printNotice(__('infoCitiesTitle', true, false), __('infoCitiesDesc', true, false)); ?>
	<!-- <div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminCities" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddCity'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<br class="clear_both" />
	</div> -->
	
	<!-- <div id="grid"></div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.county_name = "<?php // __('lblCityName'); ?>";
	myLabel.delete_selected = "<?php // __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php // __('delete_confirmation'); ?>";
	</script> -->
	<?php
//}
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoCitiesTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoCitiesDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
    	<?php
    	$error_code = $controller->_get->toString('err');
    	if (!empty($error_code))
    	{
    	    $titles = __('error_titles', true);
    	    $bodies = __('error_bodies', true);
    	    switch (true)
    	    {
    	        case in_array($error_code, array('AT01', 'AT02')):
    	            ?>
    				<div class="alert alert-success">
    					<i class="fa fa-check m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
                case in_array($error_code, array('AT09', 'AT10')):
    				?>
    				<div class="alert alert-danger">
    					<i class="fa fa-exclamation-triangle m-r-xs"></i>
    					<strong><?php echo @$titles[$error_code]; ?></strong>
    					<?php echo @$bodies[$error_code]?>
    				</div>
    				<?php
    				break;
    		}
    	}
    	?>
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row m-b-md">
                    <div class="col-md-4">
                    	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCities&amp;action=pjActionCreate" class="btn btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddCity') ?></a>
                    </div><!-- /.col-md-6 -->

                    <div class="col-md-4 col-sm-8">
                        <!-- <form action="" method="get" class="form-horizontal frm-filter">
                            <div class="input-group">
                                <input type="text" name="q" placeholder="<?php // __('plugin_base_btn_search', false, true); ?>" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form> -->
                    </div><!-- /.col-md-3 -->
                </div><!-- /.row -->
				
				<div id="grid"></div>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>
<?php
$filter = __('filter', true, false);
?>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.county_name = "<?php __('lblCityName'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	</script>