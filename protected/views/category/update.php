<?php
$this->pageTitle = 'Update Category: '.$model->name.' | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Categories'=>array('index'),
	//$model->name=>array('view','id'=>$model->id),
	'Manage Feature Categories'=>array('category/admin'),
	'Update Feature Category: '.$model->name,
);

$this->menu=array(
	//array('label'=>'List Category', 'url'=>array('index')),
	array('label'=>'Create Category', 'url'=>array('create')),
	//array('label'=>'View Category', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Category', 'url'=>array('admin')),
);
?>

<h1>Update Category: <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>