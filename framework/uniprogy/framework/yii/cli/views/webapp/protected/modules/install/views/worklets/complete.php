<h3>Installation Complete</h3>
<p>Application has been successfully installed!</p>
<?php
$reports = wm()->get('install.helper')->getReports();
foreach($reports as $r)
{
	?><div class='box'><?php echo $r; ?></div><?php
}