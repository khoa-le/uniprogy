<?php $mailer->Subject = $this->t('Registration at {site} almost complete. Verify email.', array('{site}'=>$site)); ?>
<strong>Hi <?php echo $recipient->name; ?>,</strong><br />
<br />
Thank you for registerting with <?php echo $site; ?>.<br />
<br />
There's only one step ahead to complete your registration. Please click on the link below to verify your email:<br />
<?php echo CHtml::link($link,$link); ?><br />
<br />
<?php app()->controller->renderPartial('/system/emails/signature'); ?>