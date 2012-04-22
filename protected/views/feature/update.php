<?php
$this->pageTitle = 'Update Feature: '.$model->name.' | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Features'=>array('index'),
	//$model->name=>array('view','id'=>$model->id),
	'Manage Features'=>array('feature/admin'),
	'Update Feature: '.$model->name,
);

$this->menu=array(
	//array('label'=>'List Feature', 'url'=>array('index')),
	array('label'=>'Create Feature', 'url'=>array('create')),
	//array('label'=>'View Feature', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Feature', 'url'=>array('admin')),
);
?>

<h1>Update Feature <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'categories'=>$categories)); ?>