<?php $adminUrl = aUrl('/') .'/'. param('adminUrl'); ?>
<p><?php echo $this->t('{module} module has been successfully installed.', array('{module}' => ucfirst($this->module->name))); ?></p>
<p>
<?php echo $this->t('Admin Console: {link}', 
	array('{link}'=>CHtml::link($adminUrl,$adminUrl))); ?><br />
<?php echo $this->t('Admin Login: {login}', array('{login}'=>$email)); ?><br />
<?php echo $this->t('Admin Password: {password}', array('{password}'=>$password)); ?><br />
</p>
<p><strong>SETUP YOUR CRON JOB</strong></p>
<p>If your server allows you to setup cron jobs, please use details below.<br />
If not, you can run your crons manually from Admin Console -> Tools.</p>
<p>
	Cron command: wget --quiet --delete-after <?php echo str_replace('http://','http://'.$email.':'.$password.'@',$adminUrl.'/cron'); ?><br />
	Time Period: */10 * * * *
</p>