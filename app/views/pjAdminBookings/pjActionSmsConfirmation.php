<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
    ?>
    <form action="" method="post" id="frmSmsConfirmation" autocomplete="off" data-locale="<?php echo $tpl['arr']['locale_id']; ?>">
        <input type="hidden" name="send_sms_confirmation" value="1" />
        <input type="hidden" name="phone" value="<?php echo $tpl['arr']['phone']; ?>" />
        <input type="hidden" name="locale_id" value="<?php echo $controller->getLocaleId(); ?>" />

        <div class="form-group">
            <label class="control-label"><?php __('booking_message') ?></label>

            <textarea name="message" class="form-control required" rows="4"><?php echo stripslashes(@$tpl['arr']['message']); ?></textarea>
        </div>
    </form>
	<?php
}
?>