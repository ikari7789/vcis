<?php
$this->breadcrumbs=array(
	'Administrative Tools',
);?>
<h1>Administrative Tools</h1>

<?php 
$this->widget('zii.widgets.CMenu', array(
	'items'=>array(
		array('label'=>'Manage Buildings', 'url'=>array('building/admin')),
		array('label'=>'Manage Rooms', 'url'=>array('room/admin')),
		array('label'=>'Manage Feature Categories', 'url'=>array('category/admin')),
		array('label'=>'Manage Features', 'url'=>array('feature/admin')),
		array('label'=>'Manage Users', 'url'=>array('user/admin'), 'visible'=>Yii::app()->user->checkAccess('manageUser', Yii::app()->user->id))
	)
)); ?>