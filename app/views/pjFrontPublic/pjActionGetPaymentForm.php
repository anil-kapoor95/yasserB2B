<?php
// include_once dirname(__FILE__) . '/elements/header.php'; 

$front_messages = __('front_messages', true, false);
?>
 <style>
        .pjTbs-body.thanks-bx {
    text-align: center;
}
.pjTbs-body.thanks-bx span.glyphicon.glyphicon-ok {
    background: #3c763d;
    color: #fff;
    font-size: 70px;
    padding: 31px;
    border-radius: 50%;
    margin-bottom: 30px;
}
.pjTbs-body.thanks-bx .pjTbs-body-actions {
    text-align: center;
    margin: unset;
}
.pjTbs-body.thanks-bx .pjTbs-body-actions a.btn.btn-secondary.btn-block.pjTbsBtnBack {
    display: inline;
    background: #feba00 !important;
    border-color: #feba00  !important;
    padding: 10px 30px;
}
.pjTbs-body.thanks-bx .pjTbs-body-actions a.btn.btn-secondary.btn-block.pjTbsBtnBack:hover {
    display: inline;
    background: #f7bd20 !important;
    
}
    </style>
<div class="pjTbs-body thanks-bx">
    <?php
    
    if (!empty($tpl['arr']['payment_method']))
    {
        
        if(isset($tpl['params']['plugin']) && !empty($tpl['params']['plugin']))
        {
            $payment_messages = __('payment_plugin_messages');
            ?>
            <p class="text-success text-center"><?php echo isset($payment_messages[$tpl['arr']['payment_method']]) ? $payment_messages[$tpl['arr']['payment_method']]: $front_messages[1];?></p>
            <?php
            if (pjObject::getPlugin($tpl['params']['plugin']) !== NULL)
            {
                $controller->requestAction(array('controller' => $tpl['params']['plugin'], 'action' => 'pjActionForm', 'params' => $tpl['params']));
            }
        }else{
            switch ($tpl['arr']['payment_method'])
                    {
                        case 'bank':
                        case 'creditcard':
                        case 'cash':
                        case 'cardonboard':
                        case 'payonline':
                        default:
                            $system_msg = str_replace("[STAG]", "<a href='#' class='alert-link fdStartOver'>", $front_messages[4]);
                            $system_msg = str_replace("[ETAG]", "</a>", $system_msg);

                            $thankyoupage = trim($tpl['option_arr']['o_thankyou_page']); 

                            if (!empty($thankyoupage)) {
                                // Redirect to thank you page
                                echo "<script>window.location.href = '" . htmlspecialchars($thankyoupage, ENT_QUOTES) . "';</script>";
                            } else {
                                ?>
                                <span class="glyphicon glyphicon-ok"></span>
                                <p class="text-success text-center"><?php echo $system_msg; ?></p>

                                <div class="pjTbs-body-actions bottom-buttons">
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12">
                                            <a href="#" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadSearch">
                                                <?php __('front_btn_back');?> to Home
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                    }

           /**
            switch ($tpl['arr']['payment_method'])
            {
                case 'bank':
                case 'creditcard':
                case 'cash':
                case 'cardonboard':
                case 'payonline':
                default:
                    $system_msg = str_replace("[STAG]", "<a href='#' class='alert-link fdStartOver'>", $front_messages[4]);
                    $system_msg = str_replace("[ETAG]", "</a>", $system_msg);

                    $thankyoupage = $tpl['option_arr']['o_thankyou_page']; 
                                 
                    ?>

                  <span class="glyphicon glyphicon-ok"></span>
                    <p class="text-success text-center"> <?php echo $system_msg;?></p>

                    <div class="pjTbs-body-actions bottom-buttons">
                        <br>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <a href="#" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadSearch"><?php __('front_btn_back');?> to Home</a>
                            </div><!-- /.col-sm-3 -->
                        </div><!-- /.row -->
                    </div><!-- /.pjTbs-body-actions -->

                    <?php
            } */
        }
    }else{
        $system_msg = str_replace("[STAG]", "<a href='#' class='alert-link fdStartOver'>", $front_messages[4]);
        $system_msg = str_replace("[ETAG]", "</a>", $system_msg);
        ?>
        
        <span class="glyphicon glyphicon-ok"></span>
                    <p class="text-success text-center"> <?php echo $system_msg;?></p>

                    <div class="pjTbs-body-actions">
                        <br>
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <a href="#" class="btn btn-secondary btn-block pjTbsBtnBack" data-load="loadSearch"><?php __('front_btn_back');?> to Home</a>
                            </div><!-- /.col-sm-3 -->
                        </div><!-- /.row -->
                    </div><!-- /.pjTbs-body-actions -->
        <?php
    }
    ?>
</div>