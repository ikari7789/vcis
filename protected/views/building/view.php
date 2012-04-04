<?php
$this->breadcrumbs=array(
	'Buildings'=>array('index'),
	$model->name,
);

/*$this->menu=array(
	//array('label'=>'List Building', 'url'=>array('index')),
	array('label'=>'Create Building', 'url'=>array('create')),
	array('label'=>'Update Building', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Building', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Building', 'url'=>array('admin')),
);*/
?>

<h1>View Building #<?php echo $model->name; ?></h1>

<?php /* $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		//'id',
		'name',
		'map_image',
		'create_time',
		'update_time',
	),
)); */ ?>

<?php foreach ($model->floors as $floor) { ?>
	<h2>Floor <?php echo $floor->level; ?></h2>
	<ul>
	<?php foreach ($floor->rooms as $room) {
		echo '<li>'.CHtml::link($room->number, array('room/view', 'id'=>$room->id))."</li>\n";
	} ?>
	</ul>
<?php } ?>