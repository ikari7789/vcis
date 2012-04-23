<?php
$this->pageTitle=$model->number.' | Floor '.$model->floor->level.' | '.$model->floor->building->name.' | '.Yii::app()->name;

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
<?php echo $model->floor->level; ?>
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

<div class="content-header">
	<h1><?php echo $model->floor->building->name.' - Floor '.$model->floor->level.' - '.$model->number; ?></h1>
	<?php if (Yii::app()->user->checkAccess('updateRoom', Yii::app()->user->id)): ?>
			<div class="admin"><?php echo CHtml::link('Update', array('room/update', 'id'=>$model->id)); ?></div>
	<?php endif; ?>
</div>
<div class="left-column room">
	<div class="images">
		<h2>Front View</h2>
		<?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/rooms/'.$model->front_image, 'Room '.$model->number.' front view', array('class'=>'front-image')); ?>
		
		<h2>Back View</h2>
		<?php echo CHtml::image(Yii::app()->request->baseUrl.'/images/rooms/'.$model->back_image, 'Room '.$model->number.' back view', array('class'=>'back-image')); ?>
	</div>
</div>
<div class="right-column room">
	<?php echo CHtml::hiddenField('room_id', $model->id); ?>
	<?php echo CHtml::htmlButton('Add to Room List',array('name'=>'add-list','id'=>'add-list')); ?>
	<?php if (isset($model->description)): ?>
		<div class="details">
			<h2>Room Details</h2>
			<p><?php echo CHtml::encode($model->description); ?></p>
		</div>
	<?php endif; ?>
	<?php if (isset($roomFeatures) && count($roomFeatures) > 0): ?>
		<div class="features">
			<h2>Room Features</h2>
			<?php foreach($roomFeatures as $categoryName => $features): ?>
				<h3 class="category"><?php echo $categoryName; ?></h3>
				<ul class="room-features">
					<?php foreach ($features as $featureName => $feature): ?>
						<li>
							<div class="feature"><?php echo $featureName; ?>:</div>
							<div class="details"><?php echo $feature->details; ?></div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>