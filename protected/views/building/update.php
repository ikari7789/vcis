<?php
$this->pageTitle = 'Update Building: '.$model->name.' | '.Yii::app()->name; 

$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Buildings'=>array('index'),
	//$model->name=>array('view','id'=>$model->id),
	'Manage Buildings'=>array('building/admin'),
	'Update Building: '.$model->name,
);

$this->menu=array(
	//array('label'=>'List Building', 'url'=>array('index')),
	array('label'=>'Create Building', 'url'=>array('create')),
	//array('label'=>'View Building', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Building', 'url'=>array('admin')),
);
?>

<h1>Update Building: <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'floors'=>$floors)); ?>