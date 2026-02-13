<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
	?>
    <form action="" method="post" id="frmConfirmation" autocomplete="off" data-locale="<?php echo $tpl['arr']['locale_id']; ?>">
        <input type="hidden" name="send_confirmation" value="1" />
        <input type="hidden" name="to" value="<?php echo $tpl['arr']['to']; ?>" />
        <input type="hidden" name="from" value="<?php echo $tpl['arr']['from']; ?>" />
        <input type="hidden" name="locale_id" value="<?php echo $controller->getLocaleId(); ?>" />

        <div class="form-group">
            <label class="control-label"><?php __('booking_subject') ?></label>
			
            <input type="text" name="subject" class="form-control required" value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['subject'])); ?>" />
        </div>

        <div class="form-group">
            <label class="control-label"><?php __('booking_message') ?></label>

            <textarea name="message" class="form-control required mceEditor" style="width: 400px; height: 260px;"><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['message'])); ?></textarea>
        </div>
    </form>
	<?php
}
?>