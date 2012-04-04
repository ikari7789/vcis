<?php
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Rooms'=>array('index'),
	//$model->name=>array('view','id'=>$model->id),
	'Manage Rooms'=>array('room/admin'),
	'Update Room: '.$model->number,
);

$this->menu=array(
	//array('label'=>'List Room', 'url'=>array('index')),
	array('label'=>'Create Room', 'url'=>array('create')),
	//array('label'=>'View Room', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Room', 'url'=>array('admin')),
);
?>

<h1>Update Room <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'buildings'=>$buildings,
	'floors'=>$floors,
	'categories'=>$categories,
	'roomFeatures'=>$roomFeatures,
)); ?>