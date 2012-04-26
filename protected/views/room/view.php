<?php
$this->pageTitle=$model->number.' | Floor '.$model->floor->level.' | '.$model->floor->building->name.' | '.Yii::app()->name;

$this->breadcrumbs=array(
	$model->floor->building->name=>array('building/view','id'=>$model->floor->building->id),
	$model->number,
);

$this->widget('application.extensions.fancybox.EFancyBox', array(
    'target'=>'a.fancy',
    'config'=>array(),
    )
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
					if (data == 'Room added to list' || data == 'Room already in list')
						$('button#add-list').html('Room in List').attr('disabled','disabled');
					else
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
	<div class="status<?php echo ($model->status == 1) ? ' on">In operation' : 'off">Out of operation'; ?></div>
	<h1><?php echo $model->floor->building->name.' - Floor '.$model->floor->level.' - '.$model->number; ?></h1>
	<?php if (Yii::app()->user->checkAccess('updateRoom', Yii::app()->user->id)): ?>
			<div class="admin"><?php echo CHtml::link('Update', array('room/update', 'id'=>$model->id)); ?></div>
	<?php endif; ?>
	<div><?php echo CHtml::link('Go to UWW Reservations Homepage', 'http://reservations.uww.edu'); ?></div>
</div>
<div class="left-column room">
	<div class="images">
		<h2>Front View</h2><span class="info">(Click to view large image.)</span>
		<?php echo CHtml::link(
			CHtml::image(
				Yii::app()->request->baseUrl.'/images/rooms/'.$model->front_image,
				'Room '.$model->number.' front view',
				array('class'=>'front-image')
			),
			Yii::app()->request->baseUrl.'/images/rooms/'.substr($model->front_image,0,-4).'_large'.substr($model->front_image,-4),
			array('class'=>'fancy')
		); ?>
		<?php //echo CHtml::image(Yii::app()->request->baseUrl.'/images/rooms/'.$model->front_image, 'Room '.$model->number.' front view', array('class'=>'front-image')); ?>
		
		<h2>Back View</h2><span class="info">(Click to view large image.)</span>
		<?php echo CHtml::link(
			CHtml::image(
				Yii::app()->request->baseUrl.'/images/rooms/'.$model->back_image,
				'Room '.$model->number.' back view',
				array('class'=>'back-image')
			),
			Yii::app()->request->baseUrl.'/images/rooms/'.substr($model->back_image,0,-4).'_large'.substr($model->back_image,-4),
			array('class'=>'fancy')
		); ?>
		<?php //echo CHtml::image(Yii::app()->request->baseUrl.'/images/rooms/'.$model->back_image, 'Room '.$model->number.' back view', array('class'=>'back-image')); ?>
	</div>
</div>
<div class="right-column room">
	<?php echo CHtml::hiddenField('room_id', $model->id); ?>
	<?php if (!RoomList::hasRoom($model->id)): ?>
		<?php echo CHtml::htmlButton('Add to Room List',array('name'=>'add-list','id'=>'add-list')); ?>
	<?php else: ?>
		<?php echo CHtml::htmlButton('Room in List',array('name'=>'add-list','id'=>'add-list', 'disabled'=>'disabled')); ?>
	<?php endif; ?>
	<div class="details">
		<h2>Room Details</h2>
		<?php if (empty($model->description)): ?>
			<p>No specific details for this room.</p>
		<?php else: ?>
			<p><?php echo CHtml::encode($model->description); ?></p>
		<?php endif; ?>
	</div>
	<?php if (isset($roomFeatures) && count($roomFeatures) > 0): ?>
		<div class="features">
			<h2>Room Features</h2><span class="info">(Hover over feature name for more details.)</span>
			<?php foreach($roomFeatures as $categoryName => $features): ?>
				<h3 class="category"><?php echo $categoryName; ?></h3>
				<ul class="room-features">
					<?php foreach ($features as $featureName => $feature): ?>
						<li>
							<div class="feature"><?php echo $featureName; ?>:
								<?php if (!empty($feature['description'])): ?>
									<span class="description"><?php echo $feature['description']; ?></span>
								<?php endif; ?>
							</div>
							<div class="details"><?php echo $feature['details']->details; ?></div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>