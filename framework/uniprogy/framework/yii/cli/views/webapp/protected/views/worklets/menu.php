<?php
if($this->dropdown)
	app()->controller->widget('uniprogy.extensions.cdropdownmenu.CDropDownMenu',$this->properties);
else
	app()->controller->widget('zii.widgets.CMenu',$this->properties);