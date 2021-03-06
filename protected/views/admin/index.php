<?php
$this->pageTitle = 'Administrative Tools | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Administrative Tools',
);?>
<div class="content-header">
<h1>Administrative Tools</h1>
</div>
<?php 
$this->widget('zii.widgets.CMenu', array(
	'items'=>array(
		array('label'=>'Manage Buildings', 'url'=>array('building/admin'), 'visible'=>Yii::app()->user->checkAccess('manageBuilding', Yii::app()->user->id)),
		array('label'=>'Manage Rooms', 'url'=>array('room/admin'), 'visible'=>Yii::app()->user->checkAccess('manageRoom', Yii::app()->user->id)),
		array('label'=>'Manage Feature Categories', 'url'=>array('category/admin'), 'visible'=>Yii::app()->user->checkAccess('manageCategory', Yii::app()->user->id)),
		array('label'=>'Manage Features', 'url'=>array('feature/admin'), 'visible'=>Yii::app()->user->checkAccess('manageFeature', Yii::app()->user->id)),
		array('label'=>'Manage Users', 'url'=>array('user/admin'), 'visible'=>Yii::app()->user->checkAccess('manageUser', Yii::app()->user->id)),
		array('label'=>'Manage User Account', 'url'=>array('user/update', 'id'=>Yii::app()->user->id), 'visible'=>(Yii::app()->user->id!=0 && Yii::app()->user->checkAccess('updateOwnUser', Yii::app()->user->id)))
	)
)); ?>