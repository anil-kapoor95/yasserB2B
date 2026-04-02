<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoDriversTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoDriversDesc', false, true);?></p>
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
                case in_array($error_code, array('ADR01', 'ADR03')):
                    ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check m-r-xs"></i>
                        <strong><?php echo @$titles[$error_code]; ?></strong>
                        <?php echo @$bodies[$error_code]?>
                    </div>
                    <?php
                    break;
                case in_array($error_code, array('ADR04', 'ADR08')):
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
                        <a href="<?php echo $_SERVER['PHP_SELF'].'?controller=pjAdminSuppliers&action=pjActionDriverCreate'?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddDriver'); ?></a>
                    </div>

                    <div class="col-md-4 col-sm-8">
                        <form action="" method="get" class="form-horizontal frm-filter">
                            <div class="input-group">
                                <input type="text" name="q" placeholder="<?php __('plugin_base_btn_search', false, true); ?>" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <?php
                    $filter = __('filter', true);
                    $u_statarr = __('u_statarr', true);
                    ?>
                    <div class="col-md-4 text-right">
                        <div class="btn-group" role="group" aria-label="...">
                            <button type="button" class="btn btn-primary btn-all active"><?php __('lblAll'); ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="status" data-value="T"><i class="fa fa-check m-r-xs"></i><?php echo $filter['active']; ?></button>
                            <button type="button" class="btn btn-default btn-filter" data-column="status" data-value="F"><i class="fa fa-times m-r-xs"></i><?php echo $filter['inactive']; ?></button>
                        </div>
                    </div>
                </div>

                <div id="grid"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var pjGrid = pjGrid || {};
pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
pjGrid.currentUserId = <?php echo (int) $_SESSION[$controller->defaultUser]['id']; ?>;
pjGrid.hasUpdate = <?php echo pjAuth::factory('pjAdminSuppliers')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasDeleteSingle = <?php echo pjAuth::factory('pjAdminSuppliers')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasDeleteMulti = <?php echo pjAuth::factory('pjAdminSuppliers')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasRevertStatus = <?php echo pjAuth::factory('pjAdminSuppliers')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasExport = <?php echo pjAuth::factory('pjAdminSuppliers')->hasAccess() ? 'true' : 'false';?>;

var myLabel = myLabel || {};
myLabel.thumb_path = <?php x__encode('driverLicenseFile'); ?>;
myLabel.name = <?php x__encode('lblName'); ?>;
myLabel.email = <?php x__encode('email'); ?>;
myLabel.phone = <?php x__encode('lblPhone'); ?>;
myLabel.license_number = <?php x__encode('driverLicenseNumber'); ?>;
myLabel.license_expiry = <?php x__encode('driverLicenseExpiry'); ?>;
myLabel.vehicle_id = <?php x__encode('driverVehicle_id'); ?>;
myLabel.delete_selected = <?php x__encode('delete_selected'); ?>;
myLabel.delete_confirmation = <?php x__encode('delete_confirmation'); ?>;
myLabel.status = <?php x__encode('lblStatus'); ?>;
myLabel.account_locked = <?php x__encode('plugin_base_account_locked'); ?>;
myLabel.yesno = <?php x__encode('plugin_base_yesno'); ?>;

</script>