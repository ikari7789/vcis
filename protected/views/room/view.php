<?php
$this->breadcrumbs=array(
	'Rooms'=>array('index'),
	$model->id,
);

$this->menu=array(
	//array('label'=>'List Room', 'url'=>array('index')),
	array('label'=>'Create Room', 'url'=>array('create')),
	array('label'=>'Update Room', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Room', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Room', 'url'=>array('admin')),
);
?>

<h1><?php echo $model->floor->building->name.' - Floor '.$model->floor->level.' - '.$model->number; ?></h1>

<?php /* $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'number',
		'front_image',
		'back_image',
		'map_image',
		'status',
		'description',
		'floor_id',
		'create_time',
		'update_time',
	),
)); */?>

<div class="images">
	<h3>Front Image</h3>
	<?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/rooms/'.$model->front_image); ?>
	
	<h3>Back Image</h3>
	<?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/rooms/'.$model->back_image); ?>
</div>
<div class="details">
	<h2>Room Features</h2>
	<?php foreach($categories as $category) { ?>
		<?php if (count($category->features) > 0) { ?>
			<h3><?php echo $category->name; ?></h3>
			<ul class="room-details">
			<?php foreach($category->features as $feature) { ?>
					<?php if (isset($roomFeatures[$feature->id])) { ?>
					<li>
						<span class="feature"><?php echo $feature->name; ?></span>
						<span class="details"><?php echo $roomFeatures[$feature->id]['details']; ?></span>
						<?php if ($roomFeatures[$feature->id]['verified'] == 1) { ?>
							<span class="verified">Verified</span>
						<?php } ?>
					</li>
					<?php } ?>
			<?php } ?>
			</ul>
		<?php } ?>
	<?php }	?>
</div>
