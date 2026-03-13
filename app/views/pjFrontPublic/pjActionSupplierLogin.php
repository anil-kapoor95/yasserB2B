<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<style>
body{
    background:#f5f7fb;
}
</style>

<div class="container" style="max-width:600px;margin-top:60px;margin-bottom:40px;">
    
    <div class="card" style="padding:30px;border:1px solid #eee;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">

        <h2 style="text-align:center;margin-bottom:25px;">Login</h2>

        <div id="alertLoginForm" class="alert alert-danger" style="display:none;"></div>

        <form action="#" method="post" id="frmSupplierLogin" role="form" novalidate="novalidate">

            <input type="hidden" name="supplier_login" value="1">

            <!-- Email -->
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-at"></i>
                    </span>

                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control form-control-lg required"
                           placeholder="<?php __('plugin_base_login_email', false, true); ?>"
                           autocomplete="off"
                           data-msg-required="<?php __('plugin_base_this_field_is_required', false, true); ?>">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-lock"></i>
                    </span>

                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control form-control-lg required"
                           placeholder="<?php __('plugin_base_login_password', false, true); ?>"
                           autocomplete="off"
                           data-msg-required="<?php __('plugin_base_this_field_is_required', false, true); ?>">
                </div>
            </div>

            <!-- Buttons -->
            <div class="form-group text-center" style="margin-top:20px;">
                <button type="submit" class="btn btn-primary btn-lg" style="width:50%;">
                    <?php __('front_btn_login');?>
                </button>
            </div>

            <!-- Create Account Link Below Login -->
            <div class=" text-center" style="margin-top:15px;">
                <a href="#!/SupplierRegister" style="font-weight:bold;">
                   <small> <?php __('plugin_base_link_create_account');?></small>
                </a>
            </div>

        </form>

    </div>

</div>