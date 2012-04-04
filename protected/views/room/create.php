<?php
$this->breadcrumbs=array(
	'Administrative Tools'=>array('admin/index'),
	//'Rooms'=>array('index'),
	'Manage Rooms'=>array('room/admin'),
	'Create Room',
);

$this->menu=array(
	//array('label'=>'List Room', 'url'=>array('index')),
	array('label'=>'Manage Room', 'url'=>array('admin')),
);
?>

<h1>Create Room</h1>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'buildings'=>$buildings,
	'floors'=>$floors,
	'categories'=>$categories,
)); ?>