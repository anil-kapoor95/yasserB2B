<head>
<meta charset="utf-8">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
</head>

<style>
body{
    background:#f5f7fb;
}
.card{
    padding:30px;
    border:1px solid #eee;
    border-radius:8px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}
.text-danger{
    color:#dc3545;
    font-size:13px;
    margin-top:5px;
}

.is-invalid{
    border-color:#dc3545;
}
</style>

<div class="container" style="max-width:700px;margin-top:40px;margin-bottom:40px;">

    <div class="card">

        <h2 style="text-align:center;margin-bottom:25px;"> Supplier Registration Form </h2>

         <div class="alert alert-danger" style="display:none;">
            <ul></ul>
        </div>

        <form method="post" action="#" id="frmSupplierRegister_<?php echo $controller->_get->toString('index');?>">

            <input type="hidden" name="supplier_register" value="1">

            <div class="form-group">
            <label><?php __('plugin_base_lbl_first_name', false, true); ?></label>
            <input type="text" name="first_name" class="form-control"
            value="<?php echo isset($post['first_name']) ? pjSanitize::html($post['first_name']) : ''; ?>">
            </div>

            <div class="form-group">
            <label><?php __('plugin_base_lbl_last_name', false, true); ?></label>
            <input type="text" name="last_name" class="form-control"
            value="<?php echo isset($post['last_name']) ? pjSanitize::html($post['last_name']) : ''; ?>">
            </div>

            <div class="form-group">
            <label><?php __('plugin_base_login_email', false, true); ?></label>
            <input type="email" name="email" class="form-control"
            value="<?php echo isset($post['email']) ? pjSanitize::html($post['email']) : ''; ?>">
            </div>

            <div class="form-group">
            <label><?php __('plugin_base_lbl_phone', false, true); ?></label>
            <input type="text" name="phone" class="form-control"
            value="<?php echo isset($post['phone']) ? pjSanitize::html($post['phone']) : ''; ?>">
            </div>

            <div class="form-group">
            <label><?php __('plugin_base_lbl_company_name', false, true); ?></label>
            <input type="text" name="company_name" class="form-control"
            value="<?php echo isset($post['company_name']) ? pjSanitize::html($post['company_name']) : ''; ?>">
            </div>

            <div class="form-group">
            <label><?php __('plugin_base_lbl_city', false, true); ?></label>
            <input type="text" name="city" class="form-control"
            value="<?php echo isset($post['city']) ? pjSanitize::html($post['city']) : ''; ?>">
            </div>

            <!-- <div class="form-group">
            <label><?php __('plugin_base_lbl_vehicles', false, true); ?></label>
            <input type="number" name="total_vehicles" class="form-control"
            value="<?php echo isset($post['total_vehicles']) ? pjSanitize::html($post['total_vehicles']) : ''; ?>">
            </div> -->


            <!-- <div class="form-group">
                <label class="control-label">Vehicle Category</label>

                <select name="category[]" id="vfront_category" multiple="multiple" size="5" class="form-control" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                    
                    <option value="1">sdddss></option>
                    <option value="1">sdddss></option>
                    <option value="1">sdddss></option>
                    <option value="1">sdddss></option>
                    
                </select>
            </div> -->
                        

            <div class="form-group">
            <label><?php __('plugin_base_login_password', false, true); ?></label>
            <input type="password" name="password" class="form-control">
            </div>

            <div class="form-group">
            <label><?php __('plugin_base_lbl_cnf_pass', false, true); ?></label>
            <input type="password" name="confirm_password" class="form-control">
            </div>

            <div class="row" style="margin-top:20px;">

                <div class="col-sm-6">
                    <input type="submit" value="<?php __('btnRegister');?>" class="btn btn-primary btn-block" >
                </div>

                <div class="col-sm-6 text-center" style="margin-top:10px;">
                    <a href="#!/SupplierLogin">
                        <small><?php __('plugin_base_login'); ?> </small>
                    </a>
                </div>

            </div>

        </form>

    </div>
</div>

