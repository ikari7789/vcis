<?php

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl."/js/building-form.js",CClientScript::POS_HEAD); 

$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Buildings'=>array('index'),
	'Manage Buildings'=>array('building/admin'),
	'Create Building',
);

$this->menu=array(
	//array('label'=>'List Building', 'url'=>array('index')),
	array('label'=>'Manage Building', 'url'=>array('admin')),
);
?>

<h1>Create Building</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'floors'=>$floors)); ?>