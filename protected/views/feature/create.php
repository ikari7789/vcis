<?php
$this->pageTitle = 'Create a Feature | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Features'=>array('index'),
	'Manage Features'=>array('feature/admin'),
	'Create Feature',
);

$this->menu=array(
	//array('label'=>'List Feature', 'url'=>array('index')),
	array('label'=>'Manage Feature', 'url'=>array('admin')),
);
?>

<h1>Create Feature</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'categories'=>$categories)); ?>