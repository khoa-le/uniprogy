<?php $mailer->Subject = $this->t('Password reset link'); ?>
<strong>Hi <?php echo $recipient->name; ?>,</strong><br />
<br />
Please click the link below to reset your password:<br />
<?php echo CHtml::link($link,$link); ?><br />
<br />
<?php app()->controller->renderPartial('/system/emails/signature'); ?>