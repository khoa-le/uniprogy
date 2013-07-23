<?php
$mailer->From = $email;
$mailer->FromName = $name;
$mailer->Subject = $this->t('{site}: Site Visitor Message', array('{site}' => $site));
?>
Name: <?php echo $name; ?><br />
Email: <?php echo $email; ?><br />
Subject: <?php echo $subject; ?><br />
<br />
<?php echo nl2br($message); ?>