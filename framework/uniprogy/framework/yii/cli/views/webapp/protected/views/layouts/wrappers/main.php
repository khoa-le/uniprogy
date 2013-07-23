<?php $isAdmin = app()->user->checkAccess('administrator')
	&& (app()->controller->id == 'admin'
		|| (app()->controller->module && app()->controller->module->name == 'admin')
		|| wm()->get('base.init')->states['admin']); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl; ?>/css/blueprint/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl; ?>/css/blueprint/print.css" type="text/css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl; ?>/blueprint/css/ie.css" media="screen, projection" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl; ?>/css/blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl; ?>/css/main.css" type="text/css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<div class="container" id="page">
	
	<div class="span-24 last prepend-top" id="header">
		<h2 class='column'><?php echo app()->name; ?></h2>
		<?php if(!$isAdmin) { ?>
		<div class='topmenu'><?php
			$this->worklet('base.topMenu');
		?></div>
		<?php } ?>
	</div><!-- header -->
	
	<hr />
	
	<div class="span-24 last" id="mainmenu">
		<?php
		if($isAdmin)
			$this->worklet('admin.menu');
		else
		{
			?><div class='column'><?php	$this->worklet('base.menu'); ?></div>
			<div><?php $this->worklet('user.menu'); ?></div><?php
		}
		?>
	</div><!-- mainmenu -->
	
	<hr />
	
	<?php if(count($this->breadcrumbs)):
	$this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=>$this->breadcrumbs,
		'homeLink'=>$isAdmin
			? CHtml::link($this->t('Home'),array('/admin'))
			: CHtml::link($this->t('Home'),array('/')),
	));
	?><hr /><?php
	endif; ?><!-- breadcrumbs -->
	
	<?php if(app()->user->hasFlash('info')) : ?>
	<div class="span-24 last" id="info"><div class="notice">
		<?php echo app()->user->getFlash('info'); ?>
	</div></div><!-- info -->	
	<?php
	Yii::app()->clientScript->registerScript(
		'hideEffect',
		'$("#info").animate({opacity: 1.0}, 3000).fadeOut("normal");',
		CClientScript::POS_READY
	);
	endif; ?>
	
	<?php echo $content; ?>
	
	<hr />
	
	<div class="span-24 last" id="footer">
		<div class='container'>
			<div class='column'>
				Copyright &copy; <?php echo date('Y'); ?> by <?php echo app()->name; ?>.<br/>
				All Rights Reserved.<br/>
				<?php echo param('poweredBy'); ?>
			</div>
			<div id='footermenu'><?php $this->worklet('base.footerMenu'); ?></div>
		</div>
	</div><!-- footer -->
	
</div><!-- page -->
<?php
if(isset($this->clips['outside']))
	echo $this->clips['outside'];
?>
</body>
</html>