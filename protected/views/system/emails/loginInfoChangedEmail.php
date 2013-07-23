<?php $mailer->Subject = $this->t('Login info changed on {site}', array('{site}' => $site)); ?>
<strong>Hi <?php echo $recipient->name; ?>,</strong><br />
<br />
Your login info has been changed recently.<br />
New login info:<br />
<br />
Email: <?php echo $email; ?><br />
Password: <?php echo $password; ?><br />
<br />
<?php app()->controller->renderPartial('/system/emails/signature'); ?>