<div class="search-result">
	<div class="image">
		<?php echo CHtml::link(
			CHtml::image(
				Yii::app()->request->baseUrl.'/images/rooms/'.$data->room->front_image,
				'Room '.$data->room->number.' front view',
				array('class'=>'list-image')
			),
			Yii::app()->request->baseUrl.'/images/rooms/'.substr($data->room->front_image,0,-4).'_large'.substr($data->room->front_image,-4),
			array('class'=>'fancy')
		); ?>
	</div>
	<div class="info">
		<div class="room info">Room: <?php echo CHtml::link($data->room->number, array('room/view','id'=>$data->room->id)); ?></div>
		<div class="building info">Building: <?php echo $data->room->floor->building->name; ?></div>
		<div class="floor info">Floor: <?php echo $data->room->floor->level; ?></div>
	</div>
</div>