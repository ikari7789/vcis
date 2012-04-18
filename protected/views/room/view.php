<?php
$this->breadcrumbs=array(
	$model->floor->building->name=>array('building/view','id'=>$model->floor->building->id),
	$model->number,
);

$this->menu=array(
	//array('label'=>'List Room', 'url'=>array('index')),
	array('label'=>'Create Room', 'url'=>array('create')),
	array('label'=>'Update Room', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Room', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Room', 'url'=>array('admin')),
);
?>

<?php Yii::app()->clientScript->registerScript('room',"
	$(document).ready(function() {
		// add to list code
		$('button#add-list').on('click', function() {
			$.ajax({
				type: 'POST',
				url: '".CController::createUrl('list/add')."',
				data: 'room_id='+$('#room_id').val(),
				success: function(data) {
					alert(data);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert('Error saving to list');
				}
			});
		});
	});
"); ?>

<h1><?php echo $model->floor->building->name.' - Floor '.$model->floor->level.' - '.$model->number; ?></h1>

<div class="left-room">
	<div class="images">
		<h3>Front Image</h3>
		<?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/rooms/'.$model->front_image); ?>
		
		<h3>Back Image</h3>
		<?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/rooms/'.$model->back_image); ?>
	</div>
</div>
<div class="right-room">
	<div class="details">
		<?php if (isset($model->description)): ?>
			<h2>Room Details</h2>
			<p><?php echo CHtml::encode($model->description); ?></p>
		<?php endif; ?>
		<h2>Room Features</h2>
		<?php echo CHtml::hiddenField('room_id', $model->id); ?>
		<?php foreach($categories as $category) { ?>
			<?php if (count($category->features) > 0) { ?>
				<h3><?php echo $category->name; ?></h3>
				<ul class="room-details">
				<?php foreach($category->features as $feature) { ?>
						<?php if (isset($roomFeatures[$feature->id])) { ?>
						<li>
							<span class="feature"><?php echo $feature->name; ?></span>
							<span class="details"><?php echo $roomFeatures[$feature->id]['details']; ?></span>
							<?php if ($roomFeatures[$feature->id]['verification_time'] != '0000-00-00 00:00:00') { ?>
								<span class="verified">Verified on <?php echo CHtml::encode($roomFeatures[$feature->id]['verification_time']); ?></span>
							<?php } ?>
						</li>
						<?php } ?>
				<?php } ?>
				</ul>
			<?php } ?>
		<?php }	?>
	</div>
	<?php echo CHtml::htmlButton('Add to Room List',array('name'=>'add-list','id'=>'add-list')); ?>
</div>